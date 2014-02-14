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

if ($_GET['S_servername'] != "") {
    $sql = "SELECT * FROM inv_main WHERE servername LIKE \"%" . $_GET['S_servername'] . "%\"";
} elseif ($_GET['S_ipaddress']) {
    $sql = "SELECT * FROM inv_main WHERE pub_ipaddress LIKE \"%" . $_GET['S_ipaddress'] . 
	       "%\" OR backup_ipaddress LIKE \"%" . $_GET['S_ipaddress'] .
		   "%\" OR other_ipaddress LIKE \"%" . $_GET['S_ipaddress'] . "%\"";
} elseif ($_GET['S_appname']) {
    $sql = "SELECT * FROM inv_main WHERE servername LIKE \"%" . $_GET['S_appname'] . "%\"";
} else {
   echo "HUH?";
}

//echo $sql;
$result = mysql_query($sql, $db) or die ("Unable to execute query for login: " . mysql_error());

while ($row = mysql_fetch_array($result)) {
  echo $row['servername'] . "<br />";
}

_page_top("Base", "");

echo "<h2>Search by Results</h2>\n";

echo 





_page_bot();
//---------------------------------------
//Free mysql result
//---------------------------------------
  mysql_free_result($result);
//---------------------------------------

//---------------------------------------
//Close Database Connection
//---------------------------------------
  mysql_close($db);
//---------------------------------------
?>
