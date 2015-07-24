<?php session_start();

{
    error_reporting(0);
    require("patrondb/utils.inc");
    require("patrondb/secretsauce.php");
    $tdate = date('Y-m-d H:i:s');


    global $authorized_editors;
    global $authorized_password;

    if (in_array($_GET['lname'],$authorized_editors)  && $_GET['lpass'] == $authorized_password){
	$_SESSION['user'] = $_GET['lname'];
    }

    if ($_SESSION['user'] == ''){
	echo "<script>location.replace(\"index.php\");</script>";
    }

    $con          = mysql_connect($hostname,$username,$password);
    @mysql_select_db($database) or die ("Unable to select db");
    $_selfURL     = "patronDB.php";
    $_notifyURL   = "patronNotify.php";
    $_detailsURL  = "pdbDetails.php";
    $alpha_filter = $_GET['f'];



    //--Stylesheets and header info--//

    include("patrondb/patronDB_Header.html");

    $notify = $_POST['notify'];

    $notification_emails = array();

    $notification_email_list  = $_POST['email_list'];
    $esubject                 = $_POST['email_subject'];
    $ebody                    = $_POST['email_body'];

    $query  = "SELECT id FROM DONORS";
    if ($query != ''){		
	$res_funcQry = mysql_query($query);
	$num_funcQry = mysql_num_rows($res_funcQry);
	$i = 0;
	if ($num_funcQry > 0){
	    while ($i < $num_funcQry){
		$n = mysql_result($res_funcQry, $i, "id");
		$email = $_POST["notify${n}"];
		if ($email != ''){
		    array_push($notification_emails, "$email");
		}
		$i++;
	    }
	}
	echo "<div align=center>\n";
	echo "<h4><a href=\"javascript:history.back();\">Back</a> | <a href=\"patronDB.php\">Donor Database</a> </h4><BR>\n";
	echo "<h2>Patron Email Notification System</h2>";
	echo "<form action=patronNotify.php method=post>\n";
	echo "<table class=ptables>\n";
	if (!$notification_email_list){
	    $notification_email_list = implode(',',$notification_emails);
	}
	echo "<tr><td class=pcells>Emails (Bcc)</td><td colspan=5 class=pcellswrap>$notification_email_list</td></tr>";
	echo "<input type=hidden name=email_list value=\"$notification_email_list\">\n";
	echo "<tr><td class=pcells>From</td class=pcells><td colspan=5 class=pcells>CFAA Board of Directors</td></tr>";
	echo "<tr><td class=pcells>Subject</td class=pcells><td colspan=5 class=pcells><input type=text value=\"$esubject\" name=email_subject size=60></td></tr>";
	echo "<tr><td class=pcells>Message</td><td class=pcells colspan=5 ><textarea name=email_body cols=80 rows=10 value=\"$ebody\" placeholder=\"Type Your Message Here. Use HTML Characters as none of the other control characters including newlines are preserved by design.... Always Send a Test Email to Yourself !\">$ebody</textarea></td></tr>";
	echo "<tr><td size=+2 colspan=5 align=center><input name=notify type=submit value=Notify></td></tr>";
	echo "</table>";
	echo "</div>";
    }

    if ($notify == 'Notify' && $notification_email_list != '' && $esubject != '' && $ebody != ''){
	$mailHead = "From: Colorado Fine Arts Association <info@coloradofinearts.org> \r\nContent-Type: text/html\r\n";
	$mailHead .= "Cc: info@coloradofinearts.org,treasurer@coloradofinearts.org" . "\r\n";
	$mailHead .= "Bcc: $notification_email_list" . "\r\n";
//	mail("$notification_email_list","$esubject","<P>$ebody",$mailHead);
	mail("info@coloradofinearts.org","$esubject","<P>$ebody",$mailHead);
	echo "<div align=center><i>$notification_email_list emailed with the requested message</i></center><P>";
    }



}
