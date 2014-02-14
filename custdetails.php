<?php
$HTTP_GET_VARS['auth'] = false;  //Assume user is not authenticated

//Connect to MySQL
$db = mysql_connect("localhost","dbuser","dbuser pass") or die("Unable to connect to server: " . mysql_error());

//Select database on MySQL server
mysql_select_db("serverlist", $db) or die ("Unable to select database: " . mysql_error());

if (isset($HTTP_SERVER_VARS['PHP_AUTH_USER']) &&
        isset($HTTP_SERVER_VARS['PHP_AUTH_PW'])):

  //Formulate the query
  $sql = "SELECT userid, useradmin FROM user where username=\"" . $HTTP_SERVER_VARS['PHP_AUTH_USER'] . "\" AND userpasswd = \"" . $HTTP_SERVER_VARS['PHP_AUTH_PW'] . "\"";

  //Execute the query and put result in $result
  $result = mysql_query($sql, $db) or die ("Unable to execute query for login: " . mysql_error());

  //Get number of rows in $result
  $num = mysql_numrows($result) or die ("Username or password doesn't exist or is wrong: " . mysql_error());

  //Get infro from query
  $row =  mysql_fetch_array($result);
  $loggedinuserid = $row["userid"];
  $loggedinadmin = $row["useradmin"];

  //Free mysql result
  mysql_free_result($result);

//  if ($num !=0 and $loggedinadmin == "Yes"):
  if ($num !=0):
    //A matching row was found
    $HTTP_GET_VARS['auth'] = true;
  endif;
endif;

if (! $HTTP_GET_VARS['auth']):
  header("HTTP/1.0 401 Unauthorized");
  header("WWW-Authenticate: Basic realm=\"Server List\"");
  echo "Authorization Required.";
  exit;
endif;

//Formulate the query
$sql = "SELECT cname, cdskphone, cmobphone, cothrphone, cemail, cnotes FROM customer " . 
       "WHERE customerid=\"" . $HTTP_GET_VARS["cid"] . "\"";

//Execute the query and put result in $result
$result = mysql_query($sql, $db) or die ("Unable to execute query for login: " . mysql_error());

//Get infro from query
$mainrow = mysql_fetch_array($result);

//Page specific suff
include "./pagetopbot.php";
$pagename = "Server List - Customer Details: " . $mainrow["servername"];
$rpage = "./custdetails.php";

//Page header
ptop();

echo "<table border=\"1\" align=\"center\">\n";
echo "<tr bgcolor=\"#99999\"><th colspan=\"2\" align=\"center\">Customer Details</th></tr>\n";
echo "<tr><td>Customer Name:</td><td>" . $mainrow["cname"] . "</td></tr>\n";
echo "<tr><td>Desk Phone:</td><td>" . $mainrow["cdskphone"] . "</td></tr>\n";
echo "<tr><td>Mobile Phone:</td><td>" . $mainrow["cmobphone"] . "</td></tr>\n";
echo "<tr><td>Other Phone:</td><td>" . $mainrow["cothrphone"] . "</td></tr>\n";
echo "<tr><td>E-Mail:</td><td>" . $mainrow["cemail"] . "</td></tr>\n";
echo "<tr><td>Notes:</td><td>" . $mainrow["cnotes"] . "</td></tr>\n";
echo "</table>\n";

//Bottom of page
pbottom();

//Close Database Connection
mysql_close($db);
?>
