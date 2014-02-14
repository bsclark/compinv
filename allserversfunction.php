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

//Page specific suff
include "./pagetopbot.php";
$pagename = "Server List - All Function";
$rpage = "./allserversfunction.php";

//Page header
ptop();

//Formulate the query
if ($HTTP_POST_VARS["Sort"] == "ByName" or $HTTP_POST_VARS["Sort"] == "" or $HTTP_POST_VARS["Sort"] == "none"):
  $sql = "SELECT server_id, servername, platform.osname AS osname, patchlvl, production, supported, critical, " .
         "category.catname AS catname, customer.cname AS cname, customer.customerid AS customerid, " .
         "f1.fdescription AS primaryfunction, " .
	 "f2.fdescription AS secfunction " .
         "FROM server_info LEFT JOIN platform ON server_info.osplatform=platform.platformid LEFT JOIN category ON " .
         "category.catid=server_info.category LEFT JOIN customer ON customer.customerid=server_info.primarycustcontact " .
	 "LEFT JOIN function AS f1 ON f1.functionid=server_info.primfunction LEFT JOIN function AS f2 ON " .
	 "f2.functionid=server_info.secfunction ORDER BY servername ASC";
endif;

//Execute the query and put result in $result
$result = mysql_query($sql, $db) or die ("Unable to execute query for login: " . mysql_error());

echo "<table border=\"1\" class=\"all\">\n";
echo " <tr bgcolor=\"#999999\"><th>Server Name</th><th>OS</th><th>Patch Lvl</th><th>Production</th><th>Supported?</th>" .
     "<th>Critical?</th><th>Category</th><th>Primary Contact</th><th>Primary Function</th><th>Secondary Function</th><tr>\n";

$rowcount = "0";
while ($row = mysql_fetch_array($result)):
  if ($rowcount == "0"):
    echo "<tr>\n";
  else:
    echo "<tr bgcolor=\"#ccccff\">\n";
  endif;

  echo "  <td><a href=\"./serverdisplay.php?sname=" . $row["server_id"] . "\">" . $row["servername"] . "</a></td>\n" .
       "  <td>" . $row["osname"] . "</td>\n" .
       "  <td>" . $row["patchlvl"] . "</td>\n" .
       "  <td>" . $row["production"] . "</td>\n" .
       "  <td>" . $row["supported"] . "</td>\n" .
       "  <td>" . $row["critical"] . "</td>\n" .
       "  <td>" . $row["catname"] . "</td>\n" .
       "  <td><a href=\"./custdetails.php?cid=" . $row["customerid"] . "\">" . $row["cname"] . "</a></td>\n" .
       "  <td>" . $row["primaryfunction"] . "</td>\n" .
       "  <td>" . $row["secfunction"] . "</td>\n" .
       " </tr>\n";

  if ($rowcount == "0"):
   $rowcount = "1";
  else:
   $rowcount = "0";
  endif;
endwhile;

//Bottom to table
echo "</table>\n";

//Bottom of page
pbottom();

//Close Database Connection
mysql_close($db);
?>
