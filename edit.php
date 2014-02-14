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

_page_top("Edit", "");

?>
<h2>Edit Server Information</h2>

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
