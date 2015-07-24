<?php session_start();

{
    error_reporting(0);
    require("patrondb/utils.inc");
    $tdate = date('Y-m-d H:i:s');

    setlocale(LC_MONETARY, 'en_US');

    $database = 'colora47_ticketing';
    $username = 'colora47_ajayc';
    $hostname = 'localhost';
    $password = 'lila0000';

    if (($_GET['lname'] == 'ramesh' || $_GET['lname'] == 'ajay') && $_GET['lpass'] == 'cfaa$2015$team'){
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
    $type         = $_GET['t'];

    //--Stylesheets and header info--//

    include("patrondb/financeDB_Header.html");

    // Transactions
    // Add, Edit/Update, View, Summarize
    $tdate = date('Y-m-d H:i:s');
    printTitle("Current Financial Statements as of $tdate");
    $net = printIncomeStatement();
    printBalanceStatements($net);

}
function printTitle($t)
{
    echo "<div align=center><h2>$t</h2></div><P>";
}

function printIncomeStatement()
{

    $carryover = runQuery("SELECT t_amount from f_transactions where t_name='Carryover from 2014'",'t_amount');
    $revenues = sprintf("%.2f",runQuery("Select SUM(t_amount) as amt from f_transactions where t_id > 1 and t_date like '%2015%' and t_credit=0",'amt'));
    $expenses = sprintf("%.2f",runQuery("Select SUM(t_amount) as amt from f_transactions where t_id > 1 and t_date like '%2015%' and t_credit=1",'amt'));
    $patronincome = sprintf("%.2f",runQuery("Select sum(donation) as amt from DONORS where dat like '%2015%'",'amt'));

    $net = $carryover + $revenues - $expenses;

    echo "<table class=ptables>\n";
    printRow("Mini Income Statement",'','','H');
    printRow("Carryover from 2014",'',$carryover);
    printRow("Income 2015",'',$revenues);
    printRow("Patron Income 2015",'',$patronincome);
    printRow("Expenses 2015",$expenses,'');
    printRow("Net Income",'',$net,'H');
    echo "</table>";
    return $net;

}
function printBalanceStatements($net)
{

    $receipts = sprintf("%.2f",runQuery("Select SUM(r_amount) as amt from f_receivables where r_date like '%2015%'",'amt'));
    $payables = sprintf("%.2f",runQuery("Select SUM(p_amount) as amt from f_payables where p_date like '%2015%'",'amt'));

    $gross = $net + $receipts - $payables;

    echo "<table class=ptables>\n";
    printRow("Mini Balance Sheet",'','','H');
    printRow("Cash and Equivalents",'',$net);
    printRow("Accounts Receivables",'',$receipts);
    printRow("Accounts Payables",$payables,'');
    printRow("Net Assets",'',$gross,'H');
    echo "</table>";

}
function printRow($title,$i1,$i2,$mode)
{


    if ($mode == 'H') { $style = 'pcellsstrong'; } else { $style = 'pcells'; }
    $colspan=3;
    echo "<tr>\n";
    if (!$i1 && !$i2){
	$colspan=5;
    }
    echo "<td class=$style colspan=$colspan>$title</td>";
    if ($colspan == 3){
	if ($i1){ 
	    echo "<td class=$style><font color=red>(",money_format("%.2i",$i1),")</font></td>";
	}
	else { 	echo "<td class=$style></td>"; }
	if ($i2){
	    echo "<td class=$style>",money_format("%.2i",$i2),"</td>";
	}
	else {
	    echo "<td class=$style></td>";
	}
    }
    echo "</tr>\n";
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

