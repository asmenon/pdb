<?php session_start();

    error_reporting(0);
    require("patrondb/utils.inc");
    include("patrondb/patronDB_Header.html");

    if ($_SESSION['user'] == 'ramesh' || $_SESSION['user'] == 'ajay' || $_SESSION['user'] == 'indira'){
	echo "<script>location.replace(\"patronDB.php\");</script>";
    }

    echo "<form action=patronDB.php method=post>\n";
    echo "<div align=center>";
    echo "<table class=ptables>\n";
    echo "<tr><td class=pcells align=right>User ID</td><td><input type=text name=lname size=20></td></tr>";

    echo "<tr><td class=pcells align=right>Password</td><td><input type=password name=lpass size=20></td></tr>";
    echo "<tr><td class=pcells colspan=2><input type=submit name=submit value=Login></td></tr>";
    echo "</table>";
    echo "</form>";
    include("patrondb/patronDB_Footer.html");
?>