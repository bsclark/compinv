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
$pagename = "Server List - All Support";
$rpage = "./allserverssupport.php";

//Page header
ptop();

//Formulate the query
if ($HTTP_POST_VARS["Sort"] == "ByName" or $HTTP_POST_VARS["Sort"] == "" or $HTTP_POST_VARS["Sort"] == "none"):
  $sql = "SELECT si.server_id AS serverid, si.servername AS servername, c.catname AS catname, p.osname AS osname, " .
         "m.supportdate AS supportdate, " .
	 "m.monitored AS monitored, mt.typename AS monitortype, bt.typename AS backuptype, b.bfrequency AS bfrequency, " .
         "b.backedup AS backedup, b.retain AS retain, b.databackuped AS databackuped " .
         "FROM server_info AS si LEFT JOIN category AS c ON si.category=c.catid " .
         "LEFT JOIN platform AS p ON si.osplatform=p.platformid " .
         "LEFT JOIN monitor AS m ON si.server_id=m.serverid " .
         "LEFT JOIN monitortype AS mt ON m.monitortype=mt.monitortypeid " .
         "LEFT JOIN backup AS b ON si.server_id=b.serverid " . 
	 "LEFT JOIN backuptype AS bt ON b.backuptype=bt.backuptypeid " .
	 "ORDER BY si.servername ASC";
endif;

//Execute the query and put result in $result
$result = mysql_query($sql, $db) or die ("Unable to execute query for login: " . mysql_error());

echo "<table border=\"1\" class=\"all\">\n";
echo " <tr bgcolor=\"#999999\"><th>Server Name</th><th>Category</th><th>OS</th><th>Support Date</th><th>Monitored ? " .
     "</th><th>Monitor Type</th><th>Backedup?</th><th>Backup Type</th><th>Frequency</th><th>Retain</th>" .
     "<th>Data Backedup</th></td>\n";

$rowcount = "0";
while ($row = mysql_fetch_array($result)):
  if ($rowcount == "0"):
    echo "<tr>\n";
  else:
    echo "<tr bgcolor=\"#ccccff\">\n";
  endif;

  echo "  <td><a href=\"./serverdisplay.php?sname=" . $row["serverid"] . "\">" . $row["servername"] . "</a></td>\n" .
       "  <td>" . $row["catname"] . "</td>\n" .
       "  <td>" . $row["osname"] . "</td>\n" .
       "  <td>" . $row["supportdate"] . "</td>\n" .
       "  <td>" . $row["monitored"] . "</td>\n" .
       "  <td>" . $row["monitortype"] . "</td>\n" .
       "  <td>" . $row["backedup"] . "</td>\n" .
       "  <td>" . $row["backuptype"] . "</td>\n" .
       "  <td>" . $row["bfrequency"] . "</td>\n" .
       "  <td>" . $row["retain"] . "</td>\n" .
       "  <td>" . $row["databackuped"] . "</td>\n" .
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
