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

  if ($num !=0 and $loggedinadmin == "Yes"):
//  if ($num !=0):
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

/*
mysql> describe backup;
+--------------+-----------------+------+-----+---------+----------------+
| Field        | Type            | Null | Key | Default | Extra          |
+--------------+-----------------+------+-----+---------+----------------+
| backupid     | smallint(6)     |      | PRI | NULL    | auto_increment |
| backuptype   | smallint(6)     | YES  |     | NULL    |                |
| databackuped | tinytext        | YES  |     | NULL    |                |
| bfrequency   | varchar(40)     | YES  |     | NULL    |                |
| retain       | varchar(10)     | YES  |     | NULL    |                |
| backedup     | set('No','Yes') | YES  |     | NULL    |                |
| serverid     | smallint(6)     | YES  |     | NULL    |                |
+--------------+-----------------+------+-----+---------+----------------+
*/

include "./pagetopbot.php";
$pagename = "Server Backup";
$rpage = "./server-back.php";

function formtop() {
  echo "<form enctype=\"multipart/form-data\" method=\"post\" action=\"" . $rpage . "\">\n";
  echo "<table align=\"center\">\n";
}

function formbottom() {
  echo "</form>\n";
  echo "</table>\n";
}

function page_return() {
  ptop();
  echo "Transaction completed/Canceled. <a href=\"./\">Home</a>";
  pbottom();
}

if ($HTTP_POST_VARS['Action'] == "" or $HTTP_POST_VARS['Action'] == "None"):
  //Page header
  ptop();
  formtop();

  //Formulate the query
  $sql = "SELECT backupid, server_info.servername AS servername FROM backup JOIN server_info WHERE backup.serverid=server_info.server_id";

  //Execute the query and put result in $result
  $result = mysql_query($sql, $db) or die ("Unable to execute query for login: " . mysql_error());

  //Create function box
  echo " <tr><td align=\"center\"><select tabindex=\"0\" name=\"backupid\" size=\"6\">\n";

  //Get infro from query and create form
  while ($row = mysql_fetch_array($result)):
    echo "  <option value=\"" . $row["backupid"] . "\">" . $row["servername"] . "</option>\n";
  endwhile;

  echo " </select></td></tr>\n";
  echo " <tr><td><input type=\"reset\"><input type=\"submit\" name=\"Action\" value=\"Cancel\"><input type=\"submit\" name=\"Action\" value=\"Select\"></td></tr>\n";

  //Page footer
  formbottom();
  pbottom();
elseif ($HTTP_POST_VARS["Action"] == "Select"):
  //Page header
  ptop();
  formtop();

  //Formulate queries
  $sql = "SELECT server_info.servername AS servername, backupid, backuptype, databackuped, bfrequency, " .
 	 "retain, backedup, serverid " .
	 "FROM backup JOIN server_info WHERE server_info.server_id=backup.serverid AND backupid=\"" .
	 $HTTP_POST_VARS["backupid"] . "\"";
  $sql2 = "SELECT backuptypeid, typename FROM backuptype ORDER BY typename ASC";

  //Execute the query and put result in $result
  $result = mysql_query($sql, $db) or die ("Unable to execute query for user info: "  . mysql_error());
  $result2 = mysql_query($sql2, $db) or die ("Unable to execute query for user info: "  . mysql_error());
 
  //Get infro from query
  $mainrow = mysql_fetch_array($result);

  //Display servername
  echo " <tr><td>Server Name:</td><td>" . $mainrow["servername"] . "</td></tr>\n";

  //Create the backedup radio boxes
  echo " <tr><td>Backedup:</td><td>";
  if ($mainrow["backedup"] == "No"):
    echo "<input type=\"radio\" name=\"backedup\" value=\"Yes\">Yes<input type=\"radio\" name=\"backedup\" value=\"No\" checked=\"on\">No</td></tr>\n";
  else:
    echo "<input type=\"radio\" name=\"backedup\" value=\"Yes\" checked=\"on\">Yes<input type=\"radio\" name=\"backedup\" value=\"No\">No</td></tr>\n";
  endif;

  //Create backup type select box
  echo " <tr><td>Backup Type</td><td><select name=\"backuptype\" size=\"1\">\n";
  while ($row = mysql_fetch_array($result2)):
     if ($row["backuptypeid"] == $mainrow["backuptype"]):
       echo "    <option selected value=\"" . $row["backuptypeid"] . "\">" . $row["typename"] . "</option>\n";
     else:
       echo "    <option value=\"" . $row["backuptypeid"] . "\">" . $row["typename"] . "</option>\n";
     endif;
  endwhile;
  echo " </select></td></tr>\n";

  //Create data backedup box
  echo " <tr><td>Data Backedup:</td><td><textarea cols=\"60\" rows=\"6\" name=\"databackuped\">" . $mainrow["databackuped"] .
       "</textarea></td></tr>\n";

  //Create frequency box
  echo " <tr><td>Backup Frequency:</td><td><input type=\"text\" size=\"42\" name=\"bfrequency\" value=\"" . 
       $mainrow["bfrequency"] . "\"></td></tr>\n";

  //Create retain box
  echo " <tr><td>Retained:</td><td><input type=\"text\" size=\"12\" name=\"retain\" value=\"" . $mainrow["retain"] . "\"></td></tr>\n";

  //Form buttons
  echo " <tr><td colspan=\"2\" align=\"right\"><input type=\"hidden\" name=\"backupid\" value=\"" . $HTTP_POST_VARS["backupid"] . "\"><input type=\"reset\" value=\"Reset\"><input type=\"submit\" name=\"Action\" value=\"Cancel\"><input type=\"submit\" name=\"Action\" value=\"Update\"></td></tr>\n";

  //Page footer
  formbottom();
  pbottom();
elseif ($HTTP_POST_VARS["Action"] == "Update"):
  //Formulate the query
    $sql = "UPDATE backup SET backuptype = \"" . $HTTP_POST_VARS["backuptype"] .
        "\", databackuped = \"" . $HTTP_POST_VARS["databackuped"] .
        "\", bfrequency = \"" . $HTTP_POST_VARS["bfrequency"] .
        "\", retain = \"" . $HTTP_POST_VARS["retain"] .
        "\", backedup = \"" . $HTTP_POST_VARS["backedup"] .
        "\" WHERE backupid = \"" . $HTTP_POST_VARS["backupid"] . "\"";

  //Execute the query and put result in $result
  $result = mysql_query($sql, $db) or die ("Unable to execute query for user info: "  . mysql_error());

  page_return();
elseif ($HTTP_POST_VARS["Action"] == "Cancel"):
  page_return();
endif;

//Close Database Connection
mysql_close($db);
?>
