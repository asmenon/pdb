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


    global $authorized_users;
    global $authorized_password;

    if (in_array($_GET['lname'],$authorized_users)  && $_GET['lpass'] == $authorized_password){
	$_SESSION['user'] = $_GET['lname'];
    }
   

    if ($_SESSION['user'] == ''){
	echo "<script>location.replace(\"index.php\");</script>";
    }
    else {
	$esubject = "CFAA FinDB Login";
	$ebody    = $_SESSION['user'] . " logged into Budget DB on " . $tdate;
	$mailHead = "From: Colorado Fine Arts Association <info@coloradofinearts.org> \r\nContent-Type: text/html\r\n";
	$mailHead .= "Cc: info@coloradofinearts.org,treasurer@coloradofinearts.org" . "\r\n";
	$mailHead .= "Bcc: $notification_email_list" . "\r\n";
	mail("president@coloradofinearts.org","$esubject","<P>$ebody",$mailHead);
    }

    $con          = mysql_connect($hostname,$username,$password);
    @mysql_select_db($database) or die ("Unable to select db");
    $mode          = $_GET['t'];

    $_submit         = $_POST['submit'];
  
    
    if ($_submit == 'Add'){
         $_b_event        = $_POST['b_event'];
         $_b_item         = $_POST['b_item'];
         $_b_date         = $_POST['b_date'];
         $_b_poc          = $_POST['b_poc'];
         $_b_revenue      = $_POST['b_revenue'];
         $_b_expense      = $_POST['b_expense'];
         $_b_id           = $_POST['b_id'];
         $add_query       = "INSERT INTO BUDGET VALUES ($_b_id,\"$_b_item\",\"$_b_poc\",\"$_b_mode\",\"$_b_event\",\"$_b_date\",\"$_b_revenue\",\"$_b_expense\",NOW());";
         echo "<div align=center>\n";
       	 if (!mysql_query($add_query)){
	    echo "<i>ERROR: Could not execute $add_query</i><br>";
	    $ustat = 'Failed';
	}
	else {
	    echo "<i>Added requested Transaction: $_b_id</i><BR>";
	}
	echo "</div>";
    }
    if ($_submit == 'Update'){
         $_b_event        = $_POST['b_event'];
         $_b_item         = $_POST['b_item'];
         $_b_date         = $_POST['b_date'];
         $_b_poc          = $_POST['b_poc'];
         $_b_revenue      = $_POST['b_revenue'];
         $_b_expense      = $_POST['b_expense'];
         $_b_id           = $_POST['b_id'];
         $add_query       = "UPDATE BUDGET set b_event=\"$_b_event\", b_item=\"$_b_item\", b_date=\"$_b_date\", b_poc=\"$_b_poc\", b_revenue=$_b_revenue, b_expense=$_b_expense where b_id=$_b_id;";
         echo "<div align=center>\n";
       	 if (!mysql_query($add_query)){
	    echo "<i>ERROR: Could not execute $add_query</i><br>";
	    $ustat = 'Failed';
	}
	else {
	    echo "<i>Updated requested Transaction: $_b_id</i><BR>";
	}
	echo "</div>";
    }
    //--Stylesheets and header info--//

    include("patrondb/budgetDB_Header.html");

    // Transactions
    // Add, Edit/Update, View, Summarize
    $tdate = date('Y-m-d H:i:s');
    if ($mode == 'Add'){
	printTitle("Add a New Budget Line Item");
	AddBudget();
    }
    else if ($mode == 'Edit') {
        $b_id = $_GET['b_id'];
	printTitle("Edit Budget Line Item $b_id");
	EditBudget($b_id);
    }

    printTitle("All Available Budgets");
    printBudgets();
    


}
function EditBudget($b_id)
{
    echo "<form action=budgets.php method=post>\n";
    echo "<table class=ptables>\n";

    echo "<tr><td class=pcells>Budget Line ID</td><td class=pcells>$b_id</td></tr>";
    echo "<input type=hidden name=b_id value=$b_id>\n";
    $b_event = runQuery("SELECT b_event from BUDGET where b_id=$b_id",'b_event');
    $b_date = runQuery("SELECT b_date from BUDGET where b_id=$b_id",'b_date');
    $b_item = runQuery("SELECT b_item from BUDGET where b_id=$b_id",'b_item');
    $b_poc = runQuery("SELECT b_poc from BUDGET where b_id=$b_id",'b_poc');
    $b_revenue = runQuery("SELECT b_revenue from BUDGET where b_id=$b_id",'b_revenue');
    $b_expense = runQuery("SELECT b_expense from BUDGET where b_id=$b_id",'b_expense');

    echo "<tr><td class=pcells>Budget Event</td><td class=pcells><input type=text name=b_event size=40 value=\"$b_event\"></td></tr>";
    echo "<tr><td class=pcells>Budget Event Date</td><td class=pcells><input type=text name=b_date size=40 value=\"$b_date\"></td></tr>";
    echo "<tr><td class=pcells>Budget Item</td><td class=pcells><input type=text name=b_item size=40 value=\"$b_item\"></td></tr>";
    echo "<tr><td class=pcells>Budget POC</td><td class=pcells><input type=text name=b_poc size=40 value=\"$b_poc\"></td></tr>";
    echo "<tr><td class=pcells>Projected Income</td><td class=pcells><input type=text name=b_revenue  size=40 value=\"$b_revenue\"></td></tr>";
    echo "<tr><td class=pcells>Expected Expense</td><td class=pcells><input type=text name=b_expense size=40 value=\"$b_expense\"></td></tr>";
    echo "<tr><td class=pcells colspan=2><input type=submit value=Update name=submit></td></tr>";

    echo "</select>";
    echo "</td></tr>";

    echo "</form>";
    echo "</table>";

}
function AddBudget()
{
    echo "<form action=budgets.php method=post>\n";
    echo "<table class=ptables>\n";

    $blid = runQuery("SELECT b_id from BUDGET order by b_id DESC limit 1",'b_id');
    $blid++;
    echo "<tr><td class=pcells>Budget Line ID</td><td class=pcells>$blid</td></tr>";
    echo "<input type=hidden name=b_id value=$blid>\n";
    echo "<tr><td class=pcells>Budget Event</td><td class=pcells><input type=text name=b_event size=40></td></tr>";
    echo "<tr><td class=pcells>Budget Event Date</td><td class=pcells><input type=text name=b_date size=40></td></tr>";
    echo "<tr><td class=pcells>Budget Item</td><td class=pcells><input type=text name=b_item size=40></td></tr>";
    echo "<tr><td class=pcells>Budget POC</td><td class=pcells><input type=text name=b_poc size=40></td></tr>";
    echo "<tr><td class=pcells>Projected Income</td><td class=pcells><input type=text name=b_revenue  size=40></td></tr>";
    echo "<tr><td class=pcells>Expected Expense</td><td class=pcells><input type=text name=b_expense size=40></td></tr>";
    echo "<tr><td class=pcells colspan=2><input type=submit value=Add name=submit></td></tr>";

    echo "</select>";
    echo "</td></tr>";

    echo "</form>";
    echo "</table>";
}
function printBudgets()
{
    $events = buildArrayFromQuery("SELECT distinct(b_event) from BUDGET order by b_date",'b_event');
    foreach ($events as $event){
	printTitle("$event");
        $balance = 0;
        $res = mysql_query("SELECT * from BUDGET where b_event=\"$event\" order by b_revenue DESC");
        $num = mysql_num_rows($res);
        $i = 0;
        echo "<table class=ptables>\n";
        echo "<tr><td class=pcellheads>Budget Item</td><td class=pcellheads>POC</td><td class=pcellheads>Due Date</td><td class=pcellheads>Revenue</td><td class=pcellheads>Expense</td><td class=pcellheads>Balance</td></tr>";
        while ($i < $num){
          $b_id = mysql_result($res, $i, "b_id");
          $b_item = mysql_result($res, $i, "b_item");
          $b_rev = mysql_result($res, $i, "b_revenue");
          $b_poc = mysql_result($res, $i, "b_poc");
          $b_date = mysql_result($res, $i, "b_date");
          $b_exp = mysql_result($res, $i, "b_expense");
          if ($b_rev >0) {
              echo "<tr><td class=pcells>$b_item</td><td class=pcells>$b_poc</td><td class=pcells>$b_date</td><td class=pcells>$b_rev</td><td>&nbsp;</td>";
              $balance = $balance + $b_rev;
          }
          else {
             echo "<tr><td class=pcells>$b_item</td><td class=pcells>$b_poc</td><td class=pcells>$b_date</td><td class=pcells>&nbsp;</td><td class=pcells>($b_exp)</td>";
            
             $balance = $balance - $b_exp;
          }
          $pbalance = money_format("%.2i",$balance);
          echo "<td class=pcells>$pbalance <a href=\"budgets.php?t=Edit&b_id=$b_id\">Edit</a></td></tr>";
          $i++;
        }
        echo "</table>";
        
    }
}

function printTitle($t)
{
    echo "<div align=center><h2>$t</h2></div><P>";
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

