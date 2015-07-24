<?php session_start();
{
    error_reporting(0);
    require("patrondb/utils.inc");
    require("patrondb/secretsauce.php");
    $tdate = date('Y-m-d H:i:s');

    setlocale(LC_MONETARY, 'en_US');

    $database = 'colora47_ticketing';
    $username = 'colora47_ajayc';
    $hostname = 'localhost';
    $password = 'lila0000';

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
	$ebody    = $_SESSION['user'] . " logged into Transactions DB on " . $tdate;
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
	    $q = "DELETE FROM f_transactions where t_id=$id";
	    echo "<div align=center>";
	    if (!mysql_query($q)){
		echo "<i>ERROR: Could not DELETE Transaction ID $id</i><br>";
		$ustat = 'Failed';
	    }
	    else {
		echo "<i>Deleted requested Transaction: $id</i><BR>";
	    }
	    echo "</div>";
	}
    }
    else if ($type == 'Edit'){
	$id = $_GET['id'];
	$q = "SELECT * FROM f_transactions where t_id=$id";
	$res = mysql_query($q);
	$num = mysql_num_rows($res);
	$i = 0;
	while ($i < $num){
	    $id  = mysql_result($res, $i, "t_id");
	    $c_id = mysql_result($res, $i, "t_cid");
	    $cat = runQuery("SELECT c_name from f_categories where c_id=$c_id",'c_name');
	    $date = mysql_result($res, $i, "t_date");
	    $name = mysql_result($res, $i, "t_name");
	    $src = mysql_result($res, $i, "t_entity");
	    $mode = mysql_result($res, $i, "t_mode");
	    $event = mysql_result($res, $i, "t_event");
	    $amt  = mysql_result($res, $i, "t_amount");
	    $credit = mysql_result($res, $i, "t_credit");
	    $desc = mysql_result($res, $i, "t_desc");
	    $i++;
	}
	printTitle("Edit an Existing Transaction");
	echo "<form action=transactions.php method=post>\n";
	echo "<table class=ptables>\n";
	addFormRow('Transaction','text','Transaction Name',$name);
	addFormRow('Date','text','YYYY-MM-DD',$date);
	addSelectRows('Category','f_categories','c_name',$cat);
	addFormRow('Entity','text','Source',$src);
	addFormRow('Mode','text','Payment Mode',$mode);
	addFormRow('Event','text','Event',$event);
	addFormRow('Amount','text','Dollar Amount',$amt);
	addFormRow('Description','textarea','Details of the Transaction',$desc);
	addFormRow('Credit','checkbox','',$credit);
	addFormRow('Update','submit','');
	echo "</table>";
	echo "<input type=hidden name=id value=$id>\n";
	echo "</form>";
    }
    else if ($type == 'Add'){
      global $authorized_editors;
      if (in_array($_SESSION['user'],$authorized_editors)){
	printTitle("Add a Transaction");
	echo "<form action=transactions.php method=post>\n";
	echo "<table class=ptables>\n";
	addFormRow('Transaction','text','Transaction Name');
	addFormRow('Date','text','YYYY-MM-DD');
	addSelectRows('Category','f_categories','c_name');
	addFormRow('Entity','text','Recipient','Source/Recipient');
	addFormRow('Mode','text','Mode','Payment Mode');
	addFormRow('Event','text','Event','Event Name');
	addFormRow('Amount','text','Dollar Amount');
	addFormRow('Description','textarea','Details of the Transaction');
	addFormRow('Credit','checkbox','');
	addFormRow('Add','submit','');
	echo "</table>";
	echo "</form>";
     }
     else { echo "<table class=ptables>\n";
          echo "<tr><td style=\"font-size:10px;font-color:#aa4433\">User not authorized to add transactions</td></tr>";
          echo "</table>"; }
    }
    else if ($add == 'Add'){
	$_name = $_POST['transaction'];
	$_date = $_POST['date'];
	$_from = $_POST['entity'];
	$_event = $_POST['event'];
	$_mode = $_POST['mode'];
	$_amt = $_POST['amount'];
	$_desc = $_POST['description'];
	$_credit = $_POST['credit'];
	if ($_credit == 'Checked' || $_credit == 'on') { $_credit = 1; } else {$_credit = 0; }
	$id  = runQuery("SELECT t_id from f_transactions order by t_id desc limit 1",'t_id');
	$id  = $id + 1;
	$cid = $_POST['category'];
	$q   = "INSERT INTO f_transactions VALUES ($id,$cid,\"$_name\",\"$_date\",\"$_user\",\"$_mode\",\"$_event\",\"\",$_amt,\"$_desc\",$_credit,NOW());";
	echo "<div align=center>\n";
	if (!mysql_query($q)){
	    echo "<i>ERROR: Could not execute $q</i><br>";
	    $ustat = 'Failed';
	}
	else {
	    echo "<i>Added requested Transaction: $id</i><BR>";
	}
	echo "</div>";
    }
    else if ($upd == 'Update'){
	$_id = $_POST['id'];
	$_name   = $_POST['transaction'];
	$_date   = $_POST['date'];
	$_from   = $_POST['entity'];
	$_mode   = $_POST['mode'];
	$_event  = $_POST['event'];
	$_amt    = $_POST['amount'];
	$_desc   = $_POST['description'];
	$_credit = $_POST['credit'];
	if ($_credit == 1 or $_credit == 'on') { $_credit = 1; } else {$_credit = 0; }
	$cid     = $_POST['category'];
	$q = "UPDATE f_transactions set t_name=\"$_name\",t_cid=$cid,t_date=\"$_date\",t_entity=\"$_from\",t_mode=\"$_mode\",t_event=\"$_event\",t_amount=$_amt,t_desc=\"$_desc\",t_credit=$_credit where t_id=$_id;";
	echo "<div align=center>\n";
	if (!mysql_query($q)){
	    echo "<i>ERROR: Could not execute $q</i><br>";
	    $ustat = 'Failed';
	}
	else {
	    echo "<i>Updated requested Transaction: $_id ($q)</i><BR>";
	}
	echo "</div>";
    }

    printTitle("View Transactions");
    $whereclause = array();
    echo "<table class=ptables>\n";
    $q = "SELECT * FROM f_transactions ";
    $f_t = $_GET['f_transaction'];
    $f_c = $_GET['f_category'];
    $f_s = $_GET['f_source'];
    $f_m = $_GET['f_mode'];
    $f_e = $_GET['f_event'];
    if ($f_t) { array_push ($whereclause,"t_name like \"%$f_t%\""); }
    if ($f_s) { array_push ($whereclause,"t_entity like \"%$f_s%\""); }
    if ($f_m) { array_push ($whereclause,"t_mode like \"%$f_m%\""); }
    if ($f_e) { array_push ($whereclause,"t_event like \"%$f_e%\""); }
    if ($f_c) { 
       $cid = runQuery("SELECT c_id from f_categories where c_name like \"%$f_c%\" limit 1",'c_id');
       array_push ($whereclause,"t_cid = $cid"); 
    }
    if ($whereclause[0] != ''){
	if ($whereclause[1] != ''){
	    $where = implode (' and ', $whereclause);
	}
	else {
	    $where = $whereclause[0];
	}
	$q .= " WHERE $where ";
    }
    $q .= " order by t_date";
    $res         = mysql_query($q);
    $num         = mysql_num_rows($res);
    $i           = 0;
    printHeaderRow();
    echo "<form action=transactions.php method=get>\n";
    echo "<tr>";
    echo "<td></td>";
    echo "<td class=pcells colspan=2><input type=text name=f_transaction size=15></td>\n";
    echo "<td class=pcells><input type=text name=f_category size=15></td>\n";
    echo "<td class=pcells><input type=text name=f_source size=15></td>\n";
    echo "<td class=pcells><input type=text name=f_mode size=15></td>\n";
    echo "<td class=pcells><input type=text name=f_event size=15></td>\n";
    echo "<td colspan=4 align=right> <input type=submit name=Filter></td>";
    echo "</tr>";
    echo "</form>";
    while ($i < $num){
	$id  = mysql_result($res, $i, "t_id");
	$c_id   = mysql_result($res, $i, "t_cid");
	$cat    = runQuery("SELECT c_name from f_categories where c_id=$c_id",'c_name');
	$date   = mysql_result($res, $i, "t_date");
	$name   = mysql_result($res, $i, "t_name");
	$mode   = mysql_result($res, $i, "t_mode");
	$event  = mysql_result($res, $i, "t_event");
	$from   = mysql_result($res, $i, "t_entity");
	$amt    = mysql_result($res, $i, "t_amount");
	$credit = mysql_result($res, $i, "t_credit");
	if ($credit) {
	    $bal    = $bal - $amt;
	    addRowData($id,$date,$name,$from,$mode,$event,$cat,$amt,'',$bal);
	}
	else {
	    $bal    = $bal + $amt;
	    addRowData($id,$date,$name,$from,$mode,$event,$cat,'',$amt,$bal);
	}
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
    echo "<td class=pcellheads colspan=2>Transaction</td>";
    echo "<td class=pcellheads>Category</td>";
    echo "<td class=pcellheads>Source</td>";
    echo "<td class=pcellheads>Mode</td>";
    echo "<td class=pcellheads>Event</td>";
    echo "<td class=pcellheads>Credit</td>";
    echo "<td class=pcellheads>Debit</td>";
    echo "<td class=pcellheads>Balance</td>";
    echo "<td class=pcellheads>Actions</td>";
    echo "</tr>";
}
function addRowData($id,$dat,$nam,$frm,$mod,$event,$cat,$cre,$deb,$bal)
{
    echo "<tr>\n";
    echo "<td class=pcells>$dat</td>";
    echo "<td class=pcells colspan=2>$nam</td>";
    echo "<td class=pcells>$cat</td>";
    echo "<td class=pcells>$frm</td>";
    echo "<td class=pcells>$mod</td>";
    echo "<td class=pcells>$event</td>";
    if ($cre > 0){ 
	echo "<td class=pcells><font color=red>($$cre)</font><td class=pcells></td>";
    }
    else if ($deb > 0){ 
	echo "<td class=pcells></td><td class=pcells>$$deb</td>";
    }
    else {
	echo "<td colspan=2 class=pcells>&nbsp</td>";
    }
    echo "<td class=pcells><strong>",money_format("%.2i",$bal),"</strong></td>";
    global $authorized_editors;
    if (in_array($_SESSION['user'],$authorized_editors)){
	echo "<td class=pcells><a href=\"transactions.php?t=Del&id=$id\">Delete</a> | <a href=\"transactions.php?t=Edit&id=$id\">Edit</a></td>";
    } else { echo "<td class=pcells><a href=\"#\">Delete</a> | <a href=\"#\">Edit</a></td>"; }
    echo "</tr>";
}
