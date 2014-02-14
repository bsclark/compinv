<?php
/*

*/

include "./Parts/Main.php";

//---------------------------------------
//DB Connect
//---------------------------------------
  $db = mysql_connect("localhost","inventory","<db user pass>")
        or die("Unable to connect to server: " . mysql_error());

  mysql_select_db("db_inventory", $db) or die ("Unable to select database: " . mysql_error());
//---------------------------------------

//$sql = "SELECT * FROM inv_main";

//$result = mysql_query($sql, $db) or die ("Unable to execute query for login: " . mysql_error());

//while ($row = mysql_fetch_array($result)):
//endwhile;

_page_top("Base", "");

?>
<h2>BASE</h2>
<p>This is the main text area of the page.</p>
<p>Lorem ipsum dolor sit amet, horreo Athenagora eius sed. 
Oculos calamitatibus civitatis ut libertatem adhuc memores fuisset insuper dedisti in deinde 
duas particularis ad suis. Petentibus respiciens loco sed quod ait regem Ardalio nos. Disce 
Apollonius ut casus inferioribus civitatis ut casus adprehendens melius circa quia. Potentiam 
suos exteriores iuvenis est Apollonius in fuerat est in modo ad quia ei sed eu.</p>

<?php


_page_bot();
//---------------------------------------
//Free mysql result
//---------------------------------------
//  mysql_free_result($result);
//---------------------------------------

//---------------------------------------
//Close Database Connection
//---------------------------------------
  mysql_close($db);
//---------------------------------------
?>
