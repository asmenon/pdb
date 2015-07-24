<?php

//
//
//

function printTabs($fil) {
    

           printContents ("dark-orange-menu.html");
	   echo "<P><P>";

}


function closeTabs(){

	echo "</div>\n";
	echo "</div>\n";
}
function closeFileTabs($fh){

fwrite ($fh, "</div>\n");
fwrite ($fh, "</div>\n");
}


// For Google SEO
// -------------------------------------------------------------------------
function printPageTitle($title){



}


//-----------------------------------------------------------------
function printHeaders($title,$css,$js,$img){
  echo "<html>\n";
  echo "<head>\n";
  echo "<STYLE type=\"text/css\">\n";
  echo "a:link {text-decoration: none ; color:#3333cc;}\n";	
  echo "a:visited {text-decoration: none ; color:#3333cc;}\n";
  echo " a:active {text-decoration: none; color:#3333ff;}\n";	
  echo "a:hover {text-decoration: underline; background-color: #cccccc; color: red}\n";
  echo "</STYLE>\n";

  echo "<script type=\"text/javascript\" src=\"dark-orange-menu.files/dmenu.js\"></script>\n";
  echo "<noscript><link type=\"text/css\" href=\"dark-orange-menu.files/style.css\" rel=\"stylesheet\"></noscript>\n";
  echo " <meta http-equiv=\"content-type\" content=\"text/html; charset=UTF-8\" /> \n";
  echo " <title>CFAA - Colorado Fine Arts Association</title> \n";
  echo " <style type=\"text/css\" media=\"screen\"> \n";
  echo " body {\n";
	echo " margin:5px 0px; padding:5px;\n"; 
	echo " text-align:center;\n";
  echo " }\n";
	
  echo " #Content {\n";
	echo " width:900px;\n";
	echo " margin:0px auto; \n";
	echo " text-align:left; \n";
	echo " padding:5px;\n";
	echo " border:1px #888;\n";
	echo " background-color:#fff;\n";
  echo " }\n";
echo " </style> \n";




echo "<script type=\"text/javascript\">\n";
echo "dateFuture1 = new Date(2011,9,21,16,00,00);\n";
echo "function GetCount(ddate,iid){\n";
echo "	 dateNow = new Date();	\n";
echo "	 amount = ddate.getTime() - dateNow.getTime();	\n";
echo "	 delete dateNow;\n";
echo "	 if(amount < 0){\n";
echo "	 	   document.getElementById(iid).innerHTML=\"Now!\";\n";
echo "		   }\n";
echo "		   else{\n";
echo "			days=0;out=\"\";\n";
echo "				amount = Math.floor(amount/1000);\n";
echo "				       days=Math.floor(amount/86400);\n";
echo "					amount=amount%86400;\n";
echo "						if(days != 0){out += days +\" \"+((days==1)?\"day\":\"days till Abhishek Concert. Reserve Your Seats Now\")+\", \";}\n";
echo "							out = out.substr(0,out.length-2);\n";
echo "							    document.getElementById(iid).innerHTML=out;\n";
echo "								setTimeout(function(){GetCount(ddate,iid)}, 1000);\n";
echo "								}\n";
echo "}\n";
echo "window.onload=function(){\n";
echo "	GetCount(dateFuture1, 'countbox1');\n";
echo "};\n";
echo "</script>\n";



 if ($_SERVER['QUERY_STRING'] == "BuyTickets" || $_SERVER['QUERY_STRING'] == 'BharatSundar' || $_SERVER['QUERY_STRING'] == 'BuyTickets-Priya' || $_SERVER['QUERY_STRING'] == 'BuyTickets-Lavanya' || $_SERVER['QUERY_STRING'] == 'BuyTickets-Nithyashree' || $_SERVER['QUERY_STRING'] == "CFAA-DVD" || $_SERVER['QUERY_STRING'] == 'BuyRMTU_DVD' || $_SERVER['QUERY_STRING'] == 'RegisterAjit' || $_SERVER['QUERY_STRING'] == 'tghtickets' || $_SERVER['QUERY_STRING'] == 'BuyTickets-Sowmya' || $_SERVER['QUERY_STRING'] == 'BuyTickets-Kalapini' || $_SERVER['QUERY_STRING'] == 'registerforrmtu2014.php' || $_SERVER['QUERY_STRING'] == 'registerforcompetitions2014.php' || $_SERVER['QUERY_STRING'] == 'BuyTickets-RMTU-2013' || $_SERVER['QUERY_STRING'] == 'BuyTickets-Shashank-2013'){
 echo "<script  id='googlecart-script' type='text/javascript' src='https://checkout.google.com/seller/gsc/v2_2/cart.js?mid=731972828001953' integration='jscart-wizard' post-cart-to-sandbox='false' currency='USD' 
productWeightUnits='LB'></script>\n";
}
  echo  "</head>";
}





// Prints the page Header and SubHeader




function printBodyHeaders ($top, $second)
{

    echo "<body bgcolor=#000000>\n";
	echo "<div id=\"fb-root\"></div>\n";
echo "<script>(function(d, s, id) {\n";
  echo "var js, fjs = d.getElementsByTagName(s)[0];\n";
  echo "if (d.getElementById(id)) {return;}\n";
  echo "js = d.createElement(s); js.id = id;\n";
  echo "js.src = \"//connect.facebook.net/en_US/all.js#xfbml=1\";\n";
  echo "fjs.parentNode.insertBefore(js, fjs);\n";
echo "}(document, 'script', 'facebook-jssdk'));</script>\n";



    echo "<body><div id=Content>\n";
    echo "<table width=100%><tr>";
    echo "<td valign=top style=\"padding-left:100px\">";
    echo "<a href=\"http://coloradofinearts.org\"><img align=center src=\"customLogo.gif\" border=0>\n";
    echo "</td>";
	echo "<td align=left valign=top style=\"font-size: 16pt; font-weight: bold; color: darkblue; font-family: Trebuchet MS,Arial Narrow\">
Colorado Fine Arts Association (CFAA)<br>\n";
      echo "<div style=\"font-size: 8pt;color:#FF8200;font-weight:normal;font-family: Trebuchet MS,Arial Narrow\">Enriching Minds Through Enduring Arts</div>\n";
	echo "<p><div style=\"font-size: 9pt; color: #000000; font-weight:normal;font-family: Trebuchet MS,Arial Narrow\">A 501(c)(3), Tax Exempt, Non Profit Organization<br>\n";
	echo "Tax ID: 27-0325446";
    echo "<br><a href=\"http://colfinearts.blogspot.com\" title=\"CFAA Blogs\" target=\"_blank\"><img src=\"images/blogger.jpg\"></a>&nbsp;<a href=\"http://twitter.com/colfinearts\" title=\"Follow Us On Twitter!\" target=\"_blank\"><img src=\"pics/icon-twit.png\" border=0><a>&nbsp;<a href=\"http://www.facebook.com/pages/Colorado-Fine-Arts-Association/142216239170877\" title=\"Join us at Facebook!\" target=\"_blank\"><img src=\"pics/icon-fb.png\" border=0></a><br>";

echo "<!-- AddThis Button BEGIN -->\n";
echo "<div class=\"addthis_toolbox addthis_default_style \">\n";
echo "<a class=\"addthis_button_facebook_like\" fb:like:layout=\"button_count\"></a>\n";
echo "<a class=\"addthis_button_tweet\"></a>\n";
echo "<a class=\"addthis_button_google_plusone\" g:plusone:size=\"medium\"></a>\n";
echo "<a class=\"addthis_counter addthis_pill_style\"></a>\n";
echo "</div>\n";
echo "<script type=\"text/javascript\" src=\"http://s7.addthis.com/js/250/addthis_widget.js#pubid=ra-4ea33af50d9ee1f8\"></script>\n";
echo "<!-- AddThis Button END -->\n";
echo "</div></td>";
    echo "</tr></table>";
}





//

// Typically on the bottom of a page you need some navigation links

// This function prints that line

//

function printTails ($bottomList) {	

  echo "<table align=center valign=bottom width=100%>";

  echo "<tr bgcolor=><td align=center class=fixedlinings>&nbsp;\n";

  $tabs = "";

  foreach ($bottomList as $entry){

    if ($tabs){

      echo " | ";

    }

    else {

      $tabs = 1;

    }

    echo $entry, "\n";

  }

  echo "&nbsp;</td></tr>";



}



function runQuery($query,$tag){

    $res_funcQry = mysql_query($query);

    $num_funcQry = mysql_num_rows($res_funcQry);

    $i = 0;

    while ($i < $num_funcQry){

	$val = mysql_result($res_funcQry, $i, "$tag");

	$i++;

    }

    return $val;

}


function printEndHtml()

{

  $uri = $_SERVER['QUERY_STRING'];

  $uri = str_replace ("&","|",$uri);

// echo "<P><div align=center style=\"font-size: 12pt; font-weight: bold; color: red; font-family: Trebuchet MS,Arial Narrow\" id=\"countbox1\"></div>\n";
// echo "<div align=center style=\"font-size: 14pt; font-weight: normal; color: red; font-family: Trebuchet MS,Arial Narrow\"><a href=\"index.php?BuyRhapsodyTickets\">Reserve Your Rhapsody 2011 Seats</a></div>\n";


    echo "<p style=\"border-bottom: 1px dotted #000000; width:100%;\">\n";
    echo " <div align=center class=fixedsmaller>";

     printContents ("footer.html");

    echo "</body>\n";

    echo "</html>\n";

}






function printContents($url){

	$fh = fopen($url, 'r');

        if ($fh){  

	    while (!feof($fh)){

		$lx = fgets($fh,1048576);

		echo $lx;

	    }

	    fclose($fh);

        }



}

function printFileContents($url){

         echo "<div style=\"width:900px ; align=center ; font-size: 9pt; font-family: Trebuchet MS,Arial Narrow\">\n";
	 echo "<table><tr><td style=\"width:900px ; padding-left:100px; padding-right:100px; align=center ; background:#ffffff; font-size: 9pt; font-family: Trebuchet MS,Arial Narrow\">\n";

	$fh = fopen($url, 'r');

        if ($fh){  

	    while (!feof($fh)){

		$lx = fgets($fh,1048576);

		echo $lx;

	    }

	    fclose($fh);

        }
	echo "</td></tr></table>";
	echo "</div>";


}


?>






