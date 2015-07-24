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
	$ebody    = $_SESSION['user'] . " logged into Payables DB on " . $tdate;
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
	    $q = "DELETE FROM f_payables where p_id=$id";
	    echo "<div align=center>";
	    if (!mysql_query($q)){
		echo "<i>ERROR: Could not DELETE Payable ID $id</i><br>";
		$ustat = 'Failed';
	    }
	    else {
		echo "<i>Deleted requested Payable: $id</i><BR>";
	    }
	    echo "</div>";
	}
    }
    else if ($type == 'Edit'){
	$id = $_GET['id'];
	$q = "SELECT * FROM f_payables where p_id=$id";
	$res = mysql_query($q);
	$num = mysql_num_rows($res);
	$i = 0;
	while ($i < $num){
	    $id  = mysql_result($res, $i, "p_id");
	    $c_id = mysql_result($res, $i, "p_cid");
	    $cat = runQuery("SELECT c_name from f_categories where c_id=$c_id",'c_name');
	    $date = mysql_result($res, $i, "p_date");
	    $pdate = mysql_result($res, $i, "p_pdate");
	    $name = mysql_result($res, $i, "p_to");
	    $amt  = mysql_result($res, $i, "p_amount");
	    $i++;
	}
	printTitle("Edit an Existing Payable");
	echo "<form action=payables.php method=post>\n";
	echo "<table class=ptables>\n";
	addFormRow('To','text','Entity Name',$name);
	addFormRow('Date','text','YYYY-MM-DD',$date);
	addFormRow('Pdate','text','YYYY-MM-DD',$pdate);
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
	    printTitle("Add a Payable");
	    echo "<form action=payables.php method=post>\n";
	    echo "<table class=ptables>\n";
	    addFormRow('To','text','Entity Name');
	    addFormRow('Date','text','YYYY-MM-DD');
	    addFormRow('Pdate','text','YYYY-MM-DD',$date);
	    addSelectRows('Category','f_categories','c_name');
	    addFormRow('Amount','text','Dollar Amount');
	    addFormRow('Add','submit','');
	    echo "</table>";
	    echo "</form>";
	}
	else { echo "<table class=ptables>\n";
	       echo "<tr><td style=\"font-size:10px;font-color:#aa4433\">User not authorized to add payables</td></tr>";
	       echo "</table>"; 
	}
    }
    else if ($add == 'Add'){
	$_name = $_POST['to'];
	$_date = $_POST['date'];
	$_pdate = $_POST['pdate'];
	$_amt = $_POST['amount'];
	$_desc = $_POST['description'];
	$id = runQuery("SELECT p_id from f_payables order by p_id desc limit 1",'p_id');
	$id = $id + 1;
	$cid = $_POST['category'];
	$q = "INSERT INTO f_payables VALUES ($id,$cid,\"$_date\",\"$_pdate,\",$_amt,\"$_name\",NOW());";
	echo "<div align=center>\n";
	if (!mysql_query($q)){
	    echo "<i>ERROR: Could not execute $q</i><br>";
	    $ustat = 'Failed';
	}
	else {
	    echo "<i>Added requested Payable: $id ($q)</i><BR>";
	}
	echo "</div>";
    }
    else if ($upd == 'Update'){
	$_id = $_POST['id'];
	$_date = $_POST['date'];
	$_pdate = $_POST['pdate'];
	$_name = $_POST['to'];
	$_amt = $_POST['amount'];
	$cid = $_POST['category'];
	$q = "UPDATE f_payables set p_to=\"$_name\",p_cid=$cid,p_date=\"$_date\",p_rdate=\"$_pdate\",p_amount=$_amt where p_id=$_id;";
	echo "<div align=center>\n";
	if (!mysql_query($q)){
	    echo "<i>ERROR: Could not execute $q</i><br>";
	    $ustat = 'Failed';
	}
	else {
	    echo "<i>Updated requested Payable: $_id ($q)</i><BR>";
	}
	echo "</div>";
    }

    printTitle("View Payables");
    echo "<table class=ptables>\n";
    $q = "SELECT * FROM f_payables order by p_date";
    $whereclause = "";
    $res = mysql_query($q);
    $num = mysql_num_rows($res);
    $i = 0;
    printHeaderRow();
    while ($i < $num){
	$id  = mysql_result($res, $i, "p_id");
	$c_id = mysql_result($res, $i, "p_cid");
	$cat = runQuery("SELECT c_name from f_categories where c_id=$c_id",'c_name');
	$date = mysql_result($res, $i, "p_date");
	$pdate = mysql_result($res, $i, "p_pdate");
	$name = mysql_result($res, $i, "p_to");
	$amt  = mysql_result($res, $i, "p_amount");
	$bal = $bal + $amt;
	addRowData($id,$date,$cat,$name,$pdate,$amt,$bal);
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
    echo "<td class=pcellheads colspan=2>Payable</td>";
    echo "<td class=pcellheads>Category</td>";
    echo "<td class=pcellheads>Payable Date</td>";
    echo "<td class=pcellheads>Dollar Amount</td>";
    echo "<td class=pcellheads>Balance</td>";
    echo "<td class=pcellheads>Actions</td>";
    echo "</tr>";
}
function addRowData($id,$dat,$cat,$nam,$pdate,$amt,$bal)
{
    echo "<tr>\n";
    echo "<td class=pcells>$dat</td>";
    echo "<td class=pcells colspan=2>$nam</td>";
    echo "<td class=pcells>$cat</td>";
    echo "<td class=pcells>$pdate</td>";
    echo "<td class=pcells>$$amt</td>";
    echo "<td class=pcells><strong>",money_format("%.2i",$bal),"</strong></td>";
    global $authorized_editors;
    if (in_array($_SESSION['user'],$authorized_editors)){
	echo "<td class=pcells><a href=\"payables.php?t=Del&id=$id\">Delete</a> | <a href=\"payables.php?t=Edit&id=$id\">Edit</a></td>";
    }
    else {
	echo "<td class=pcells><a href=\"#\">Delete</a> | <a href=\"#\">Edit</a></td>";
    }
    echo "</tr>";
}
