<?php session_start();

{
    error_reporting(0);
    require("patrondb/utils.inc");
    require("patrondb/secretsauce.php");

    $tdate = date('Y-m-d H:i:s');

    setlocale(LC_MONETARY, 'en_US');

    global $authorized_password;
    global $authorized_users;
    if (in_array($_GET['lname'],$authorized_users)  && $_GET['lpass'] == $authorized_password){
	$_SESSION['user'] = $_GET['lname'];
    }

    if ($_SESSION['user'] == ''){
	echo "<script>location.replace(\"index.php\");</script>";
    }
    else {
	$esubject = "CFAA FinDB Login";
	$ebody    = $_SESSION['user'] . " logged into Receivables DB on " . $tdate;
	$mailHead = "From: Colorado Fine Arts Association <info@coloradofinearts.org> \r\nContent-Type: text/html\r\n";
	$mailHead .= "Cc: info@coloradofinearts.org,treasurer@coloradofinearts.org" . "\r\n";
	$mailHead .= "Bcc: $notification_email_list" . "\r\n";
	mail("president@coloradofinearts.org","$esubject","<P>$ebody",$mailHead);
    }

    $con          = mysql_connect($hostname,$username,$password);
    @mysql_select_db($database) or die ("Unable to select db");
    $_selfURL     = "patronDB.php";
    $_notifyURL   = "patronNotify.php";
    $_detailsURL  = "pdbDetails.php";
    $alpha_filter = $_GET['f'];
    $type         = $_GET['t'];

    //--Stylesheets and header info--//

    include("patrondb/financeDB_Header.html");

    // Transactions
    // Add, Edit/Update, View, Summarize

    $add = $_POST['Add'];
    $upd = $_POST['Update'];


    if ($type == 'Del'){
	$id = $_GET['id'];
	if ($id > 0){ 
	    $q = "DELETE FROM f_receivalbes where r_id=$id";
	    echo "<div align=center>";
	    if (!mysql_query($q)){
		echo "<i>ERROR: Could not DELETE Receivable ID $id</i><br>";
		$ustat = 'Failed';
	    }
	    else {
		echo "<i>Deleted requested Receivalbe: $id</i><BR>";
	    }
	    echo "</div>";
	}
    }
    else if ($type == 'Edit'){
	$id = $_GET['id'];
	$q = "SELECT * FROM f_receivables where r_id=$id";
	$res = mysql_query($q);
	$num = mysql_num_rows($res);
	$i = 0;
	while ($i < $num){
	    $id  = mysql_result($res, $i, "r_id");
	    $c_id = mysql_result($res, $i, "r_cid");
	    $cat = runQuery("SELECT c_name from f_categories where c_id=$c_id",'c_name');
	    $date = mysql_result($res, $i, "r_date");
	    $rdate = mysql_result($res, $i, "r_rdate");
	    $name = mysql_result($res, $i, "r_from");
	    $amt  = mysql_result($res, $i, "r_amount");
	    $i++;
	}
	printTitle("Edit an Existing Receivable");
	echo "<form action=receivables.php method=post>\n";
	echo "<table class=ptables>\n";
	addFormRow('From','text','Entity Name',$name);
	addFormRow('Date','text','YYYY-MM-DD',$date);
	addFormRow('Rdate','text','YYYY-MM-DD',$rdate);
	addSelectRows('Category','f_categories','c_name',$cat);
	addFormRow('Amount','text','Dollar Amount',$amt);
	addFormRow('Update','submit','');
	echo "</table>";
	echo "<input type=hidden name=id value=$id>\n";
	echo "</form>";
    }
    else if ($type == 'Add'){
	global $authorized_editors;
	if (in_array($_SESSION['user'],$authorized_editors)){
	    printTitle("Add a Receivable");
	    echo "<form action=receivables.php method=post>\n";
	    echo "<table class=ptables>\n";
	    addFormRow('From','text','Entity Name');
	    addFormRow('Date','text','YYYY-MM-DD');
	    addFormRow('Rdate','text','YYYY-MM-DD',$date);
	    addSelectRows('Category','f_categories','c_name');
	    addFormRow('Amount','text','Dollar Amount');
	    addFormRow('Add','submit','');
	    echo "</table>";
	    echo "</form>";
	}
	else { echo "<table class=ptables>\n";
	       echo "<tr><td style=\"font-size:10px;font-color:#aa4433\">User not authorized to add receivables</td></tr>";
	       echo "</table>"; 
	}
    }
    else if ($add == 'Add'){
	$_name = $_POST['from'];
	$_date = $_POST['date'];
	$_rdate = $_POST['rdate'];
	$_amt = $_POST['amount'];
	$_desc = $_POST['description'];
	$id = runQuery("SELECT r_id from f_receivables order by r_id desc limit 1",'r_id');
	$id = $id + 1;
	$cid = $_POST['category'];
	$q = "INSERT INTO f_receivables VALUES ($id,$cid,\"$_date\",\"$_rdate,\",$_amt,\"$_name\",NOW());";
	echo "<div align=center>\n";
	if (!mysql_query($q)){
	    echo "<i>ERROR: Could not execute $q</i><br>";
	    $ustat = 'Failed';
	}
	else {
	    echo "<i>Added requested Receivable: $id ($q)</i><BR>";
	}
	echo "</div>";
    }
    else if ($upd == 'Update'){
	$_id = $_POST['id'];
	$_date = $_POST['date'];
	$_rdate = $_POST['rdate'];
	$_name = $_POST['from'];
	$_amt = $_POST['amount'];
	$cid = $_POST['category'];
	$q = "UPDATE f_receivables set r_from=\"$_name\",r_cid=$cid,r_date=\"$_date\",r_rdate=\"$_rdate\",r_amount=$_amt where r_id=$_id;";
	echo "<div align=center>\n";
	if (!mysql_query($q)){
	    echo "<i>ERROR: Could not execute $q</i><br>";
	    $ustat = 'Failed';
	}
	else {
	    echo "<i>Updated requested Receivable: $_id ($q)</i><BR>";
	}
	echo "</div>";
    }

    printTitle("View Receivables");
    echo "<table class=ptables>\n";
    $q = "SELECT * FROM f_receivables order by r_date";
    $whereclause = "";
    $res = mysql_query($q);
    $num = mysql_num_rows($res);
    $i = 0;
    printHeaderRow();
    while ($i < $num){
	$id  = mysql_result($res, $i, "r_id");
	$c_id = mysql_result($res, $i, "r_cid");
	$cat = runQuery("SELECT c_name from f_categories where c_id=$c_id",'c_name');
	$date = mysql_result($res, $i, "r_date");
	$rdate = mysql_result($res, $i, "r_rdate");
	$name = mysql_result($res, $i, "r_from");
	$amt  = mysql_result($res, $i, "r_amount");
	$bal = $bal + $amt;
	addRowData($id,$date,$cat,$name,$rdate,$amt,$bal);
	$i++;
    }
    echo "</table>";



}
function printTitle($t)
{
    echo "<div align=center><h2>$t</h2></div><P>";
}
function addSelectRows($t,$table,$column,$default)
{
    echo "<tr><td class=pcells>Category</td><td class=pcells>";
    $entries = buildArrayFromQuery("SELECT DISTINCT $column FROM $table","$column");
    sort($entries);
    echo "<select name=category>\n";
    if ($default){
	$cid = runQuery("SELECT c_id from $table where c_name=\"$default\"",'c_id');
	echo "<option name=\"$default\" value=$cid selected>$default</option>\n";
    }
    else {
	echo "<option name=\"Select One\">Select One</option>\n";
    }
    foreach ($entries as $entry){
	$cid = runQuery("SELECT c_id from $table where c_name=\"$entry\"",'c_id');
	echo "<option name=$entry value=$cid>$entry</option>";
    }
    echo "</select>";
    echo "</td></tr>";
}

function buildArrayFromQuery($qry,$field){

    $array  = array();
    $res_funcQry = mysql_query($qry);
    $num_funcQry = mysql_num_rows($res_funcQry);
    $i = 0;
    $functions = array();
    while ($i < $num_funcQry){
       $func_name = mysql_result($res_funcQry, $i, "$field");
       array_push($array,"$func_name");
       $i++;
    }
    return $array;
}

function addFormRow ($name, $type, $ph,$data)
{
    $lname = strtolower($name);

    if ($data == '') { $fld = 'placeholder'; $fval = $ph; } else { $fld= 'value' ; $fval = $data; }

    if ($type == 'text'){
	echo "<tr><td class=pcells>$name</td><td><input type=$type size=40 name=$lname $fld=\"$data\"></td></tr>\n";
    }
    else if ($type == 'textarea'){
	echo "<tr><td class=pcells>$name</td><td><textarea cols=40 rows=5 name=$lname>$data</textarea></tr>\n";
    }
    else if ($type == 'checkbox'){
	if ($data == 1){
	    echo "<tr><td class=pcells>$name</td><td><input type=$type name=$lname value=1 checked></td></tr>\n";
	}
	else {
	    echo "<tr><td class=pcells>$name</td><td><input type=$type name=$lname></td></tr>\n";
	}
    }
    else if ($type == 'submit'){
	echo "<tr><td class=pcells colspan=2><input type=submit name=$name value=$name></td></tr>\n";
    }
}
function printHeaderRow()
{
    echo "<tr>\n";
    echo "<td class=pcellheads>Date</td>";
    echo "<td class=pcellheads colspan=2>Receivable</td>";
    echo "<td class=pcellheads>Category</td>";
    echo "<td class=pcellheads>Receivable Date</td>";
    echo "<td class=pcellheads>Dollar Amount</td>";
    echo "<td class=pcellheads>Balance</td>";
    echo "<td class=pcellheads>Actions</td>";
    echo "</tr>";
}
function addRowData($id,$dat,$cat,$nam,$rdate,$amt,$bal)
{
    echo "<tr>\n";
    echo "<td class=pcells>$dat</td>";
    echo "<td class=pcells colspan=2>$nam</td>";
    echo "<td class=pcells>$cat</td>";
    echo "<td class=pcells>$rdate</td>";
    echo "<td class=pcells>$$amt</td>";
    echo "<td class=pcells><strong>",money_format("%.2i",$bal),"</strong></td>";
    global $authorized_editors;
    if (in_array($_SESSION['user'],$authorized_editors)){
	echo "<td class=pcells><a href=\"receivables.php?t=Del&id=$id\">Delete</a> | <a href=\"receivables.php?t=Edit&id=$id\">Edit</a></td>";
    }
    else {
	echo "<td class=pcells><a href=\"#\">Delete</a> | <a href=\"#\">Edit</a></td>";
    }
    echo "</tr>";
}
