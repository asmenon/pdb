<?php session_start();

{
    error_reporting(0);
    require("patrondb/utils.inc");
    require("patrondb/secretsauce.php");
    $tdate = date('Y-m-d H:i:s');


    $database = 'colora47_ticketing';
    $username = 'colora47_ajayc';
    $hostname = 'localhost';
    $password = 'lila0000';

    global $authorized_password;
    global $authorized_users;
    global $authorized_editors;
    if (in_array($_POST['lname'],$authorized_users)  && $_POST['lpass'] == $authorized_password){
	$_SESSION['user'] = $_POST['lname'];
    }

    if (in_array($_GET['lname'],$authorized_users)  && $_GET['lpass'] == $authorized_password){
	$_SESSION['user'] = $_GET['lname'];
    }


    if ($_SESSION['user'] == ''){
	echo "<script>location.replace(\"index.php\");</script>";
    }
    else {
	$esubject = "CFAA FinDB Login";
	$ebody    = $_SESSION['user'] . " logged into Patron DB on " . $tdate;
	$mailHead = "From: Colorado Fine Arts Association <info@coloradofinearts.org> \r\nContent-Type: text/html\r\n";
	$mailHead .= "Cc: info@coloradofinearts.org,treasurer@coloradofinearts.org" . "\r\n";
	$mailHead .= "Bcc: $notification_email_list" . "\r\n";
	mail("president@coloradofinearts.org","$esubject","<P>$ebody",$mailHead);
    }

    $con      = mysql_connect($hostname,$username,$password);
    @mysql_select_db($database) or die ("Unable to select db");

    $_selfURL    = "patronDB.php";
    $_notifyURL    = "patronNotify.php";
    $_detailsURL = "pdbDetails.php";
    $alpha_filter = $_GET['f'];



    //--Stylesheets and header info--//

    include("patrondb/patronDB_Header.html");
    


    $table_headers = array('ID','Name','Donation Amount','Patron Level','Donation Date','Expiry Date','Actions','Notify');
    $sort_headers  = array('id','name','donation','','dat','enddat');
    $colspan = count($table_heads);
    $id = 0;

    //--Title of the Patron Table--//
    echo "<div align=center><h3>CFAA Donor Database [$tdate]</h3>";
    $newid = runQuery("SELECT id from DONORS order by id DESC limit 1",'id');
    $newid = $newid + 1;
    if (in_array($_SESSION['user'],$authorized_editors)){
       echo "<h4><a href=\"pdbDetails.php?id=$newid&mode=Add\">Add a New Donor</a> | <a href=\"patronDBcsv.php\">Download Patron DB as CSV</a></h4>";
    }
    else {
        echo "<h4><a href=\"#\">Add a New Donor</a> | <a href=\"patronDBcsv.php\">Download Patron DB as CSV</a></h4>";
    }
    printPatronStats();
    echo "<table class=ptables>\n";
    echo "<tr>";

    printPaginationIndex($alpha_filter);

    //--Generate Query--//

    if ($_GET['sort'] == '') { $sortorder = 'name'; }
    else { $sortorder = strtolower($_GET['sort']);  }
    $whereClause = "";
    $whereArray  = array ();

    if ($alpha_filter != ''){
	if ($alpha_filter == 'Active'){
	    array_push ($whereArray, " active=1 ");
	}
	else if ($alpha_filter == 'Inactive'){
	    array_push ($whereArray, " active=0 ");
	}
	else {
	    array_push ($whereArray, " name like \"$alpha_filter%\" ");
	}
    }

    foreach ($sort_headers as $sh){
       if ($_GET["$sh"] != "") { 
	   if ($sh == 'name'){
	       array_push($whereArray,"name like \"%$_GET[$sh]%\" or email like \"%$_GET[$sh]%\" or spouse_name like \"%$_GET[$sh]%\" "); 
	   }
	   else {
	       array_push($whereArray,"$sh like \"%$_GET[$sh]%\""); 
	   }
       }
    }

    if ($whereArray[0] != '') { $whereClause = 'WHERE ';}
    $whereClause .= join (' AND ', $whereArray);

    $whereClause .= " ORDER BY $sortorder";
    $query = "SELECT * from DONORS $whereClause";    
    
    //--Print Rows--//
    $table_columns = array('id','name','donation','level','dat','enddat');
    if ($query != ''){ 
	$res_funcQry = mysql_query($query);
	$num_funcQry = mysql_num_rows($res_funcQry);
	$i = 0;
	if ($num_funcQry > 0){
	    foreach ($table_headers as $th){
		echo "<td class=pcellheads>$th</td>";
	    }

	    echo "</tr><tr>";
	    echo "<form action=$_selfURL method=get>\n";
	    foreach ($sort_headers as $th){
		if ($th != ''){
		    echo "<td class=pcells><input type=radio name=sort value=$th></td>";
		}
		else {
		    echo "<td class=pcells>&nbsp;</td>";
		}
	    }
	    echo "</tr><tr>";

	    foreach ($sort_headers as $th){
		if ($th != ''){
		    echo "<td class=pcells><input type=text name=$th value=\"$_GET[$th]\"></td>"; 
		}
		else {
		    echo "<td class=pcells>&nbsp;</td>";
		}
	    }
	    echo "<td class=pcells><input type=submit value=\"FilterSort\"></td>";
	    echo  "</form>";
	    echo "<form action=$_notifyURL method=post>\n";
	    echo "<td class=pcells><input type=submit value=\"Notify\"></td></tr>";
	    
	}

	while ($i < $num_funcQry){
	    $active = mysql_result($res_funcQry, $i, "active");
	    echo "<tr>";

	    foreach ($table_columns as $tc){
                if ($tc == 'level') { 
                  echo generateLevel(mysql_result($res_funcQry, $i, "donation"),$active); 
                }
                else { 
		   if ($tc == 'name'){
		      $email = mysql_result($res_funcQry, $i, "email");
		      if ($active){
			  ${$tc} = "<td class=pcells><a href=\"mailto:$email\">" . mysql_result($res_funcQry, $i, "$tc") . "</a></td>";
		      }
		      else {
			  ${$tc} = "<td class=pcellsgray><a href=\"mailto:$email\">" . mysql_result($res_funcQry, $i, "$tc") . "</a></td>";
		      }
		   }
		   else {
		       if ($active){
			   ${$tc} = "<td class=pcells>" . mysql_result($res_funcQry, $i, "$tc") . "</td>";
		       }
		       else {
			   ${$tc} = "<td class=pcellsgray>" . mysql_result($res_funcQry, $i, "$tc") . "</td>";
		       }
		   }
		   echo "${$tc}";
                }
	    }
	    $id = mysql_result($res_funcQry, $i, "id");
            global $authorized_editors;
	    if (in_array($_SESSION['user'],$authorized_editors)){
	      if ($active){
		 echo "<td class=pcells><a href=\"${_detailsURL}?id=$id\">Details</a></td>";	
		 echo "<td class=pcells><input type=checkbox name=\"notify${id}\" value=\"$email\"></td>";
	      }
	      else {
		echo "<td class=pcellsgray><a href=\"${_detailsURL}?id=$id\">Details</a></td>";	
		echo "<td class=pcellsgray><input type=checkbox name=\"notify${id}\" value=\"$email\"></td>";
	      }
            }
            else {
              if ($active){
		 echo "<td class=pcells><a href=\"#\">Details</a></td>";	
		 echo "<td class=pcells>X</td>";
	      }
	      else {
		echo "<td class=pcellsgray><a href=\"#\">Details</a></td>";	
		echo "<td class=pcellsgray>X</td>";
	      }
            }
	    echo "</tr>\n";
	    $i++;
	}
    }
    echo "</form>";
    echo "</table>";
    include("patrondb/patronDB_Footer.html");
    mysql_close($con);
}
function generateLevel($don,$active)
{
   $level  = 'Gold';
   if ($don > 249 && $don < 500)       {       $level = 'Gold';      }
   else if ($don > 499 && $don < 2500) {       $level = 'Platinum';   }
   else if ($don > 2500 && $don < 5000){       $level = 'VIP';      }
   else if ($don > 4999)               {       $level = 'Lifetime'; }
   if ($active){
       return "<td class=pcells>$level</td>";
   }
   else {
       return "<td class=pcellsgray>$level</td>";
   }
}
function printPatronStats()
{
    $tot_patrons      = runQuery("SELECT COUNT(id) ccn from DONORS",'ccn');    
    $tot_active_patrons = runQuery("SELECT COUNT(id) ccn from DONORS WHERE  active = 1",'ccn');    
    $tot_paid_patrons = runQuery("SELECT COUNT(id) ccn from DONORS WHERE donation > 0",'ccn');    
    $tot_active_gold_patrons = runQuery("SELECT COUNT(id) ccn from DONORS WHERE active=1 and donation < 499",'ccn');    
    $tot_gold_patrons = runQuery("SELECT COUNT(id) ccn from DONORS WHERE donation < 499",'ccn');    
    $tot_active_plat_patrons = runQuery("SELECT COUNT(id) ccn from DONORS WHERE donation > 499 and donation < 1000 and active=1",'ccn');    
    $tot_active_bus_patrons = runQuery("SELECT COUNT(id) ccn from DONORS WHERE short_name='Business' and active=1",'ccn');    
    $tot_plat_patrons = runQuery("SELECT COUNT(id) ccn from DONORS WHERE donation > 499 and donation < 1000",'ccn');    
    $tot_life_patrons = runQuery("SELECT COUNT(id) ccn from DONORS WHERE total_donation > 4999",'ccn');    
    $tot_active_prem_patrons = runQuery("SELECT COUNT(id) ccn from DONORS WHERE total_donation > 1000 and active=1",'ccn');    
    $tot_prem_patrons = runQuery("SELECT COUNT(id) ccn from DONORS WHERE total_donation > 1000",'ccn');    
    $tot_2014_patrons = runQuery("SELECT COUNT(id) ccn from DONORS WHERE enddat like \"%2014-12%\"",'ccn');    
    $tot_2015_patrons = runQuery("SELECT COUNT(id) ccn from DONORS WHERE enddat like \"%2016-12%\"",'ccn');    
    $tot_2016_patrons = runQuery("SELECT COUNT(id) ccn from DONORS WHERE enddat not like \"%2016-12%\" ",'ccn');    
    
    echo "<table class=ptables>\n";
    echo "<tr><td class=pcellheads></td><td class=pcellheads>Total</td><td class=pcellheads>Active</td></tr>";
    echo "<tr><td class=pcells>Patrons</td><td class=pcells>$tot_patrons</td><td class=pcells>$tot_active_patrons</td></tr>";
    echo "<tr><td class=pcells>Paid Patrons</td><td class=pcells>$tot_paid_patrons</td><td></td></tr>";
    echo "<tr><td class=pcells>Gold Patrons</td><td class=pcells>$tot_gold_patrons</td><td class=pcells>$tot_active_gold_patrons</td></tr>";
    echo "<tr><td class=pcells>Platinum Patrons</td><td class=pcells>$tot_plat_patrons</td><td class=pcells>$tot_active_plat_patrons</td></tr>";
    echo "<tr><td class=pcells>Premium Patrons</td><td class=pcells>$tot_prem_patrons</td><td class=pcells>$tot_active_prem_patrons</td></tr>";
    echo "<tr><td class=pcells>Active Business Patrons</td><td></td><td class=pcells>$tot_active_bus_patrons</td></tr>";
    echo "<tr><td class=pcells>Lifetime (Business & Personal > USD5k) Patrons</td><td class=pcells>$tot_life_patrons</td></tr>";
    echo "<tr><td class=pcells>Renewals Expired in Dec 2014</td><td class=pcells>$tot_2014_patrons</td></tr>";
    echo "<tr><td class=pcells>Renewals Expiring in Dec 2016</td><td class=pcells>$tot_2015_patrons</td></tr>";
    echo "<tr><td class=pcells>Renewals Expiring before Dec 2016</td><td class=pcells>$tot_2016_patrons</td></tr>";
    echo "<tr><td colspan=2 class=pcells>&nbsp;</td></tr>";
    echo "</table>";
    
}
function printPaginationIndex($i)
{
    echo "<div class=\"pagination clearfix\">\n";
    if ($i == 'All' || $i == ''){
	echo "&nbsp;<strong>All</strong>";
    }
    else {
	    echo "&nbsp;<a href=\"patronDB.php\">All</a>";
    }

    if ($i == 'Active'){
	echo "&nbsp;<strong>Active</strong>";
    }
    else {
	    echo "&nbsp;<a href=\"patronDB.php?f=Active\">Active</a>";
    }

    if ($i == 'Inactive'){
	echo "&nbsp;<strong>Inactive</strong>";
    }
    else {
	    echo "&nbsp;<a href=\"patronDB.php?f=Inactive\">Inactive</a>";
    }


    foreach (range(A,Z) as $x){
	if ($i == $x){
	    echo "&nbsp;<strong>$x</strong>";
	}
	else {
	    echo "&nbsp;<a href=\"patronDB.php?f=$x\">$x</a>";
	}
    }
    echo "</div>";
}
?>
