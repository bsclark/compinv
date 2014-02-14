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
mysql> describe serverspec;
+-----------------+----------------------+------+-----+---------+----------------+
| Field           | Type                 | Null | Key | Default | Extra          |
+-----------------+----------------------+------+-----+---------+----------------+
| specid          | int(11)              |      | PRI | NULL    | auto_increment |
| serverid        | smallint(6)          | YES  |     | NULL    |                |
| biosdate        | date                 | YES  |     | NULL    |                |
| firmware        | varchar(20)          | YES  |     | NULL    |                |
| ram             | varchar(10)          | YES  |     | NULL    |                |
| diskspace       | varchar(20)          | YES  |     | NULL    |                |
| totalnics       | varchar(5)           | YES  |     | NULL    |                |
| nictype         | set('Single','Dual') | YES  |     | NULL    |                |
| nicinuse        | varchar(5)           | YES  |     | NULL    |                |
| networkcomments | tinytext             | YES  |     | NULL    |                |
| cpu             | varchar(30)          | YES  |     | NULL    |                |
| ipaddress       | varchar(30)          | YES  |     | NULL    |                |
+-----------------+----------------------+------+-----+---------+----------------+
*/

include "./pagetopbot.php";
$pagename = "Server Hardware";
$rpage = "./serverspec.php";

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
  $sql = "SELECT specid, server_info.servername AS servername FROM serverspec JOIN server_info WHERE serverspec.serverid=server_info.server_id";

  //Execute the query and put result in $result
  $result = mysql_query($sql, $db) or die ("Unable to execute query for login: " . mysql_error());

  //Create function box
  echo " <tr><td align=\"center\"><select tabindex=\"0\" name=\"specid\" size=\"6\">\n";

  //Get infro from query and create form
  while ($row = mysql_fetch_array($result)):
    echo "  <option value=\"" . $row["specid"] . "\">" . $row["servername"] . "</option>\n";
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
  $sql = "SELECT server_info.servername AS servername, specid, cpu, ipaddress, biosdate, firmware, ram, diskspace, " .
	 "totalnics, nictype, nicinuse, networkcomments, serverid " .
	 "FROM serverspec JOIN server_info WHERE server_info.server_id=serverspec.serverid AND specid=\"" .
	 $HTTP_POST_VARS["specid"] . "\"";

  //Execute the query and put result in $result
  $result = mysql_query($sql, $db) or die ("Unable to execute query for user info: "  . mysql_error());
 
  //Get infro from query
  $mainrow = mysql_fetch_array($result);

  //Display servername
  echo " <tr><td>Server Name:</td><td>" . $mainrow["servername"] . "</td></tr>\n";

  //Create cpu box
  echo " <tr><td>CPU:</td><td><input type=\"text\" size=\"32\" name=\"cpu\" value=\"" . $mainrow["cpu"] . "\"></td></tr>\n";

  //Create ip address box
  echo " <tr><td>IP Address:</td><td><input type=\"text\" size=\"32\" name=\"ipaddress\" value=\"" . $mainrow["ipaddress"] . "\"></td></tr>\n";

  //Create biosdate box
  echo " <tr><td>BIOS Date:</td><td><input type=\"text\" size=\"12\" name=\"biosdate\" value=\"" . $mainrow["biosdate"] . "\"></td></tr>\n";

  //Create firmware box
  echo " <tr><td>Firmware:</td><td><input type=\"text\" size=\"22\" name=\"firmware\" value=\"" . $mainrow["firmware"] . "\"></td></tr>\n";

  //Create ram box
  echo " <tr><td>RAM:</td><td><input type=\"text\" size=\"12\" name=\"ram\" value=\"" . $mainrow["ram"] . "\"></td></tr>\n";

  //Create diskspace box
  echo " <tr><td>Total Diskspace:</td><td><input type=\"text\" size=\"22\" name=\"diskspace\" value=\"" . $mainrow["diskspace"] . "\"></td></tr>\n";

  //Create totalnics box
  echo " <tr><td>Total NICS:</td><td><input type=\"text\" size=\"7\" name=\"totalnics\" value=\"" . $mainrow["totalnics"] . "\"></td></tr>\n";

  //Create the nic type radio boxes
  echo " <tr><td>NIC  Type:</td><td>";
  if ($mainrow["nictype"] == "Dual"):
    echo "<input type=\"radio\" name=\"nictype\" value=\"Single\">Single<input type=\"radio\" name=\"nictype\" value=\"Dual\" checked=\"on\">Dual</td></tr>\n";
  else:
    echo "<input type=\"radio\" name=\"nictype\" value=\"Single\" checked=\"on\">Single<input type=\"radio\" name=\"nictype\" value=\"Dual\">Dual</td></tr>\n";
  endif;

  //Create nicinuse box
  echo " <tr><td>NICs In Use:</td><td><input type=\"text\" size=\"7\" name=\"nicinuse\" value=\"" . $mainrow["nicinuse"] . "\"></td></tr>\n";

  //Create network comments backedup box
  echo " <tr><td>Network Comments:</td><td><textarea cols=\"60\" rows=\"6\" name=\"networkcomments\">" . 
       $mainrow["networkcomments"] . "</textarea></td></tr>\n";

  //Form buttons
  echo " <tr><td colspan=\"2\" align=\"right\"><input type=\"hidden\" name=\"specid\" value=\"" . $HTTP_POST_VARS["specid"] . "\"><input type=\"reset\" value=\"Reset\"><input type=\"submit\" name=\"Action\" value=\"Cancel\"><input type=\"submit\" name=\"Action\" value=\"Update\"></td></tr>\n";

  //Page footer
  formbottom();
  pbottom();
elseif ($HTTP_POST_VARS["Action"] == "Update"):
  //Formulate the query
    $sql = "UPDATE serverspec SET biosdate = \"" . $HTTP_POST_VARS["biosdate"] .
        "\", cpu = \"" . $HTTP_POST_VARS["cpu"] .
        "\", ipaddress = \"" . $HTTP_POST_VARS["ipaddress"] .
        "\", firmware = \"" . $HTTP_POST_VARS["firmware"] .
        "\", ram = \"" . $HTTP_POST_VARS["ram"] .
        "\", diskspace = \"" . $HTTP_POST_VARS["diskspace"] .
        "\", totalnics = \"" . $HTTP_POST_VARS["totalnics"] .
        "\", nictype = \"" . $HTTP_POST_VARS["nictype"] .
        "\", nicinuse = \"" . $HTTP_POST_VARS["nicinuse"] .
        "\", networkcomments = \"" . $HTTP_POST_VARS["networkcomments"] .
        "\" WHERE specid = \"" . $HTTP_POST_VARS["specid"] . "\"";

  //Execute the query and put result in $result
  $result = mysql_query($sql, $db) or die ("Unable to execute query for user info: "  . mysql_error());

  page_return();
elseif ($HTTP_POST_VARS["Action"] == "Cancel"):
  page_return();
endif;

//Close Database Connection
mysql_close($db);
?>
