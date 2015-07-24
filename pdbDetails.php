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

    $con      = mysql_connect($hostname,$username,$password);
    @mysql_select_db($database) or die ("Unable to select db");

    $_selfURL    = "patronDB.php";
    $_detailsURL = "pdbDetails.php";

    $flds = array(
	'id' => 'ID',
	'short_name' => 'Short Name',
	'year' => 'Year When The Last Donation Was Made',
	'patron_since' => 'Patron Since',
	'name' => 'Name',
	'spouse_name' => 'Spouse Name',
	'active' => 'Is this Patron Currently Active',
	'email' => 'Email Address',
	'phone' => 'Telephone Number',
	'address' => 'Address',
	'addressline2' => 'Address Line II',
	'city' => 'City',
	'zip' => '5 Digit Zip',
	'donation' => 'Donation Amount',
	'donationmode' => 'Donation Mode',
	'matchingdollar' => 'Dollar Amount From Employer Matching',
	'employer' => 'Employer Name',
	'total_donation' => 'Total Donation Till Date',
	'dat' => 'Date of Last Donation',
	'enddat' => 'Current Donation Period End Date',
	'description' => 'Notes about this Patron',
	'upd' => 'Details Last Updated on',
	);
    $cflds = array(
	'id' => 'Internal ID',
	'short_name' => 'Short Name',
	'year' => 'YYYY',
	'patron_since' => 'YYYY',
	'name' => 'No CSVs',
	'spouse_name' => 'Spouse Name',
	'active' => '1 or 0 - 1 for Active 0 for Inactive',
	'email' => 'Full Emails',
	'phone' => 'No - Just Number',
	'address' => 'No CSVs',
	'addressline2' => 'No CSVs',
	'city' => 'Name of the City',
	'zip' => '5 Digit Zip Code',
	'donation' => 'Exact Dollar Amount',
	'donationmode' => 'Paypal/Check/Cash',
	'matchingdollar' => 'Dollar Amount',
	'employer' => 'For Matching',
	'total_donation' => 'Dollar Amount',
	'dat' => 'YYYY-MM-DD',
	'enddat' => 'YYYY-MM-DD',
	'description' => 'Notes',
	'upd' => '',
	);

    //--Stylesheets and header info--//
    include("patrondb/patronDB_Header.html");

    //--Title of the Patron Table--//
    $id   = $_GET['id'];
    $mode = $_GET['mode'];

    //--Find Update Values-//


    if ($_POST['submit'] == 'Update Patron Database Information' || $_POST['submit'] == 'Add This New Entry To Patron Database'){
	$id = $_POST['id'];
	$mode = 'Edit';
	if ($id > 0){ 
	    $short_name   = $_POST['short_name'];
	    $patron_since = $_POST['patron_since'];
	    $name         = $_POST['name'];
	    $spousename   = $_POST['spouse_name'];
	    $year         = $_POST['year'];
	    $email        = $_POST['email'];
	    $phone        = $_POST['phone'];
	    $address      = $_POST['address'];
	    $addressline2 = $_POST['addressline2'];
	    $city         = $_POST['city'];
	    $zip          = $_POST['zip'];
	    $active       = $_POST['active'];
	    $donation     = $_POST['donation'];
	    $donationmode = $_POST['donationmode'];
	    $matchingdollar = $_POST['matchingdollar'];
	    $employer       = $_POST['employer'];
	    $total_donation = $_POST['total_donation'];
	    $dat            = $_POST['dat'];
	    $enddat         = $_POST['enddat'];
	    $description    = $_POST['description'];

	    if ($_POST['submit'] == 'Add This New Entry To Patron Database'){
		$uquery = "INSERT INTO DONORS VALUES ($id,\"$short_name\",\"$year\",\"$patron_since\",\"$name\",\"$spousename\",\"$email\",\"$phone\",\"$address\",\"$addressline2\",\"$city\",\"$zip\",\"$donation\",\"$donationmode\",\"$matchingdollar\",\"$employer\",\"$total_donation\",\"$dat\",\"$enddat\",\"$active\",\"$description\",NOW());";
	    }
	    else {
		$uquery         = "UPDATE DONORS SET short_name=\"$short_name\",year=\"$year\",patron_since=\"$patron_since\",name=\"$name\",spouse_name=\"$spousename\",email=\"$email\",phone=\"$phone\",address=\"$address\",donation=\"$donation\",donationmode=\"$donationmode\",matchingdollar=\"$matchingdollar\",employer=\"$employer\",total_donation=\"$total_donation\",dat=\"$dat\",enddat=\"$enddat\",addressline2=\"$addressline2\",city=\"$city\",zip=\"$zip\",active=\"$active\",description=\"$description\" where id=$id";
	    }
	    if (!mysql_query($uquery)){
		echo "ERROR: Could not execute $uquery<br>";
		$ustat = 'Failed';
	    }
	    else { $ustat = 'Passed'; }

	    $to      = 'president@coloradofinearts.org';
	    $subject = 'Patron DB Update';
	    $message = "$ustat: => $uquery";
	    $headers = 'From: info@coloradofinearts.org' . "\r\n" .
		'Reply-To: info@coloradofinearts.org' . "\r\n" .
		'X-Mailer: PHP/' . phpversion();
	    mail($to, $subject, $message, $headers);
	}
    }


    echo "<div align=center><h3>Details of Donor ID # $id [$tdate]</h3>";
    $newid = runQuery("SELECT id from DONORS order by id DESC limit 1",'id');
    $newid = $newid + 1;

    if ($mode == 'Edit' || $mode == 'Add'){
	if ($mode == 'Add'){
	    echo "<h4> <a href=\"javascript:history.back();\">Back</a> | <a href=\"patronDB.php\">Donor Database</a> </h4></div>";
	}	
	else {
	    echo "<h4> <a href=\"javascript:history.back();\">Back</a> | <a href=\"patronDB.php\">Donor Database</a> | <a href=\"pdbDetails.php?id=$id\">Edited Record</a> | <a href=\"pdbDetails.php?id=$newid&mode=Add\">Add an Entry</a> </h4></div>";
	}
    }
    else {
	echo "<h4><a href=\"javascript:history.back();\">Back</a> | <a href=\"patronDB.php\">Donor Database</a> | <a href=\"pdbDetails.php?id=$id&mode=Edit\">Edit Details</a> | <a href=\"pdbDetails.php?id=$newid&mode=Add\">Add an Entry</a> </h4></div> \n";
    }
    echo "<table class=ptables>\n";



    //--Generate Query--//

    if ($id > 0){
	if ($mode == 'Edit' || $mode == 'Add'){
	    echo "<form action=pdbDetails.php method=post>\n";
	}

	if ($mode == 'Add') { $id = $id - 1; }
	$sql   = "SELECT * from DONORS WHERE id=$id";
	if ($mode == 'Add') { $id = $id + 1; }
	$query = mysql_query($sql); 
	$columns = mysql_num_fields($query); 

	for($i = 1; $i < $columns; $i++) { 
	    $fieldName = mysql_field_name($query,$i);
	    while($row = mysql_fetch_assoc($query,$i)){
		foreach($row as $column=>$value) {
		    if (($mode == 'Edit'||$mode == 'Add') && $column != 'id' && $column != 'upd'){
			if ($column == 'description'){
			    if ($mode == 'Add'){
				echo "<tr><td class=pcells>$flds[$column]</td><td colspan=3 class=pcells><textarea name=$column cols=60 rows=4 placeholder=\"$cflds[$column]\"></textarea></td></tr>";
			    }
			    else {
				echo "<tr><td class=pcells>$flds[$column]</td><td colspan=3 class=pcells><textarea name=$column cols=60 rows=4>$value</textarea></td></tr>";
			    }
			}
			else {
			    if ($mode == 'Add'){
				echo "<tr><td class=pcells>$flds[$column]</td><td colspan=3 class=pcells><input type=text name=$column size=60 placeholder=\"$cflds[$column]\" value=\"\"></td></tr>";
			    }
			    else {
				echo "<tr><td class=pcells>$flds[$column]</td><td colspan=3 class=pcells><input type=text name=$column size=60 value=\"$value\"><font size=-2>($cflds[$column])</font></td></tr>";
			    }
			}
		    }		    
		    else {
			if ($mode == 'Add' && $column == 'id'){
			    echo "<tr><td class=pcells>$flds[$column]</td><td colspan=3 class=pcells>$id</td></tr>";
			}
			else if ($column == 'active'){
			    if ($value == 1) {
				echo "<tr><td class=pcells>$flds[$column]</td><td colspan=3 class=pcells><font color=green>Active</font></td></tr>";
			    }
			    else {
				echo "<tr><td class=pcells>$flds[$column]</td><td colspan=3 class=pcells><font color=red>Inactive</font></td></tr>";
			    }
			}
			else {
			    echo "<tr><td class=pcells>$flds[$column]</td><td colspan=3 class=pcells>$value</td></tr>";
			}
		    }
		}
	    }
	}
	if ($mode == 'Edit' || $mode == 'Add'){
	    echo "<tr><td colspan=4 align=center>\n";
	    echo "<input type=hidden name=id value=$id>\n";
	    if ($mode == 'Add'){
		echo "<input type=submit name=submit value=\"Add This New Entry To Patron Database\">\n";
	    }
	    else {
		echo "<input type=submit name=submit value=\"Update Patron Database Information\">\n";
	    }
	    echo "</form>\n";
	    echo "</td></tr>";
	}
    }
    else {
	echo "<script>history.back();</script>";
    }
    echo "</table>";
    include("patrondb/patronDB_Footer.html");
    mysql_close($con);
}


?>
