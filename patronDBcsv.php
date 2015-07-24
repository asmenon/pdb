<?php
{
    error_reporting(0);

    $tdate = date('Y-m-d');


    require("patrondb/secretsauce.php");
    $con      = mysql_connect($hostname,$username,$password);
    @mysql_select_db($database) or die ("Unable to select db");

    $sql = mysql_query("SELECT * from DONORS order by name");

    // Fetch Record from Database

    $columns_total = mysql_num_fields($sql);

    // Get The Field Name

    for ($i = 0; $i < $columns_total; $i++) {
	$heading = mysql_field_name($sql, $i);
	$output .= '"'.$heading.'",';
   }
   $output .="\n";

   // Get Records from the table

   while ($row = mysql_fetch_array($sql)) {
     for ($i = 0; $i < $columns_total; $i++) {
       $output .='"'.$row["$i"].'",';
     }
     $output .="\n";
   }

    // Download the file

   $filename = "patronDB-${tdate}.csv";
   header('Content-type: application/csv');
   header('Content-Disposition: attachment; filename='.$filename);

    echo $output;
    mysql_close($con);
    exit;
}

?>
