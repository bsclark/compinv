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
$pagename = "Server List - All Hardware";
$rpage = "./allservershardware.php";

//Page header
ptop();

//Formulate the query
if ($HTTP_POST_VARS["Sort"] == "ByName" or $HTTP_POST_VARS["Sort"] == "" or $HTTP_POST_VARS["Sort"] == "none"):
  $sql = "SELECT si.server_id AS serverid, si.servername AS servername, m.makename AS make, mo.modelname AS model, " .
	 "si.serialnum AS serialnum, " .
         "ss.cpu AS cpu, ss.firmware AS firmware, ss.ram AS ram, ss.diskspace AS diskspace, ss.totalnics AS totalnics, " .
	 "ss.nictype AS nictype, ss.nicinuse AS nicinuse, si.tag AS tag " .
         "FROM server_info AS si LEFT JOIN make AS m ON si.make=m.makeid " .
	 "LEFT JOIN model AS mo ON si.model=mo.modelid " .
	 "LEFT JOIN serverspec AS ss ON ss.serverid=si.server_id " .
 	 "ORDER BY servername ASC";
endif;

//Execute the query and put result in $result
$result = mysql_query($sql, $db) or die ("Unable to execute query for login: " . mysql_error());

echo "<table border=\"1\" class=\"all\">\n";
echo " <tr bgcolor=\"#999999\"><th>Server Name</th><th>Make</th><th>Model</th><th>S/N</th><th>" .
     "CPU</th><th>Firmware</th><th>RAM</th><th>Total Diskspace</th><th>Total NICS</th><th>NIC Type" .
     "</th><th>NICs In Use</th><th>Tag</th></td>\n";

$rowcount = "0";
while ($row = mysql_fetch_array($result)):
  if ($rowcount == "0"):
    echo "<tr>\n";
  else:
    echo "<tr bgcolor=\"#ccccff\">\n";
  endif; 

  echo "  <td><a href=\"./serverdisplay.php?sname=" . $row["serverid"] . "\">" . $row["servername"] . "</a></td>\n" .
       "  <td>" . $row["make"] . "</td>\n" .
       "  <td>" . $row["model"] . "</td>\n" .
       "  <td>" . $row["serialnum"] . "</td>\n" .
       "  <td>" . $row["cpu"] . "</td>\n" .
       "  <td>" . $row["firmware"] . "</td>\n" .
       "  <td>" . $row["ram"] . "</td>\n" .
       "  <td>" . $row["diskspace"] . "</td>\n" .
       "  <td>" . $row["totalnics"] . "</td>\n" .
       "  <td>" . $row["nictype"] . "</td>\n" .
       "  <td>" . $row["nicinuse"] . "</td>\n" .
       "  <td>" . $row["tag"] . "</td>\n" .
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
