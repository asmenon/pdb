<?php session_start();

{
    error_reporting(0);
    require("patrondb/utils.inc");
    $tdate = date('Y-m-d H:i:s');


    $database = 'colora47_ticketing';
    $username = 'colora47_ajayc';
    $hostname = 'localhost';
    $password = 'lila0000';

    if (($_GET['lname'] == 'ramesh' || $_GET['lname'] == 'ajay') && $_GET['lpass'] == 'cfaa$2015$team'){
	$_SESSION['user'] = $_GET['lname'];
    }

    if ($_SESSION['user'] == ''){
	echo "<script>location.replace(\"accounts.php\");</script>";
    }

    $con          = mysql_connect($hostname,$username,$password);
    @mysql_select_db($database) or die ("Unable to select db");
    $_selfURL     = "patronDB.php";
    $_notifyURL   = "patronNotify.php";
    $_detailsURL  = "pdbDetails.php";
    $alpha_filter = $_GET['f'];
    $type         = $_GET['t'];

    //--Stylesheets and header info--//

    include("patrondb/patronDB_Header.html");

    // Transactions
    // Add, Edit/Update, View, Summarize


    if ($type == 'Add'){
	printTitle("Add a Transaction");
	echo "<form action=transactions.php method=post>\n";
	echo "<table class=ptables>\n";
	addFormRow('Transaction','text','Transaction Name');
	addFormRow('Date','text','YYYY-MM-DD');
	addFormRow('From','text','Recipient');
	addFormRow('Description','textarea','Details of the Transaction');
	addFormRow('Credit','checkbox','');
	addFormRow('Add','submit','');
	echo "</table>";
	echo "</form>";
    }
    else if ($type == 'View'){
	printTitle("View Transactions");
	echo "<table class=ptables>\n";
	$q = "SELECT * FROM f_transactions";
	$whereclause = "";
	$res = mysql_query($q);
	$num = mysql_num_rows($res);
	$i = 0;
	while ($i < $num){
	    $id  = mysql_result($res, $i, "t_id");
	    $c_id = mysql_result($res, $i, "t_cid");
	    $cat = runQuery("SELECT c_name from f_categories where c_id=$c_id",'c_name');
	    $date = mysql_result($res, $i, "t_date");
	    $name = mysql_result($res, $i, "t_name");
	    $amt  = mysql_result($res, $i, "t_amount");
	    $credit = mysql_result($res, $i, "t_credit");
	    if ($credit) {
		addRowData($date,$name,$cat,$amt,'');
	    }
	    else {
		addRowData($date,$name,$cat,'',$amt);
	    }
	}
	echo "</table>";
    }

}
function printTitle($t)
{
    echo "<div align=center><h2>$t</h2></div><P>";
}
function addFormRow ($name, $type, $ph)
{
    $lname = strtolower($name);
    if ($type == 'text'){
	echo "<tr><td class=pcells>$name</td><td><input type=$type size=40 name=$lname placeholder=\"$ph\"></td></tr>";
    }
    else if ($type == 'textarea'){
	echo "<tr><td class=pcells>$name</td><td><textarea cols=40 rows=5 name=$lname placeholder=\"$ph\"></textarea></tr>";
    }
    else if ($type == 'checkbox'){
	echo "<tr><td class=pcells>$name</td><td><input type=$type name=$lname></td></tr>";
    }
    else if ($type == 'submit'){
	echo "<tr><td class=pcells colspan=2><input type=submit name=$name value=$name></td></tr>";
    }
}
function addRowData($dat,$nam,$cat,$cre,$deb)
{
    echo "<tr>\n";
    echo "<td class=pcells>$dat</td>";
    echo "<td class=pcells>$nam</td>";
    echo "<td class=pcells>$cat</td>";
    echo "<td class=pcells>$cre</td>";
    echo "<td class=pcells>$deb</td>";
    echo "</tr>";
}
