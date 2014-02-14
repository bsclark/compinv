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
mysql> describe history;
+-------------+-------------+------+-----+---------+----------------+
| Field       | Type        | Null | Key | Default | Extra          |
+-------------+-------------+------+-----+---------+----------------+
| historyid   | int(11)     |      | PRI | NULL    | auto_increment |
| serverid    | smallint(6) | YES  |     | NULL    |                |
| dateofnote  | date        | YES  |     | NULL    |                |
| historynote | text        | YES  |     | NULL    |                |
| changemgtno | varchar(20) | YES  |     | NULL    |                |
| whoid       | smallint(6) | YES  |     | NULL    |                |
+-------------+-------------+------+-----+---------+----------------+
*/

include "./pagetopbot.php";
$pagename = "Server History";
$rpage = "./server-history.php";

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
  $sql = "SELECT server_id, servername FROM server_info";

  //Execute the query and put result in $result
  $result = mysql_query($sql, $db) or die ("Unable to execute query for login: " . mysql_error());

  //Create function box
  echo " <tr><td align=\"center\"><select tabindex=\"0\" name=\"server_id\" size=\"6\">\n";

  //Get infro from query and create form
  while ($row = mysql_fetch_array($result)):
    echo "  <option value=\"" . $row["server_id"] . "\">" . $row["servername"] . "</option>\n";
  endwhile;

  echo " </select></td></tr>\n";
  echo " <tr><td><input type=\"reset\"><input type=\"submit\" name=\"Action\" value=\"Cancel\"><input type=\"submit\" name=\"Action\" value=\"Select\"></td></tr>\n";

  //Page footer
  formbottom();
  pbottom();
elseif ($HTTP_POST_VARS["Action"] == "Select"):
  //Determine if they selected anything
  if ($HTTP_POST_VARS["server_id"] == "" or $HTTP_POST_VARS["server_id"] == "none"):
    echo "<b>ERROR:</b> You Must Select a Server.<br><br>\n";
    page_return();
  else:
    //Page header
    ptop();
    formtop();

    //Formulate the query
    $sql = "SELECT historyid, dateofnote FROM history WHERE serverid=\"" . $HTTP_POST_VARS["server_id"] . "\"";

    //Execute the query and put result in $result
    $result = mysql_query($sql, $db) or die ("Unable to execute query for login: " . mysql_error());

    //Create function box
    echo " <tr><td align=\"center\"><select tabindex=\"0\" name=\"historyid\" size=\"6\">\n";

    //Get infro from query and create form
    while ($row = mysql_fetch_array($result)):
      echo "  <option value=\"" . $row["historyid"] . "\">" . $row["dateofnote"] . "</option>\n";
    endwhile;

    echo " </select></td></tr>\n";
    echo "<input type=\"hidden\" name=\"serverid\" value=\"" . $HTTP_POST_VARS["server_id"] . "\">\n";

    //Form buttons
    echo " <tr><td colspan=\"2\" align=\"right\"><input type=\"reset\" value=\"Reset\"><input type=\"submit\" name=\"Action\" value=\"Cancel\"><input type=\"submit\" name=\"Action\" value=\"New\"><input type=\"submit\" name=\"Action\" value=\"Continue\"></td></tr>\n";

    //Page footer
    formbottom();
    pbottom();
  endif;
elseif ($HTTP_POST_VARS["Action"] == "Continue"):
  //Determine if they selected anything
  if ($HTTP_POST_VARS["historyid"] == "" or $HTTP_POST_VARS["historyid"] == "none"):
    echo "<b>ERROR:</b> You Must Select a date.<br><br>\n";
    page_return();
  else:
    //Page header
    ptop();
    formtop();

    //Formulate the query
    $sql = "SELECT server_info.servername, historyid, dateofnote, historynote, changemgtno, user.username FROM history LEFT JOIN server_info ON server_info.server_id=history.serverid LEFT JOIN user ON user.userid=history.whoid WHERE historyid=\"" . $HTTP_POST_VARS["historyid"] . "\"";

    //Execute the query and put result in $result
    $result = mysql_query($sql, $db) or die ("Unable to execute query for login: " . mysql_error());

    //Get infro from query
    $mainrow = mysql_fetch_array($result);

    //Display servername
    echo " <tr><td>Server Name:</td><td>" . $mainrow["servername"] . "</td></tr>\n";

    //Create dateofnote box
    echo " <tr><td>Date:</td><td><input type=\"text\" size=\"12\" name=\"dateofnote\" value=\"" . $mainrow["dateofnote"] . "\"></td></tr>\n";

    //Create historynote box
    echo " <tr><td>Note:</td><td><textarea cols=\"60\" rows=\"6\" name=\"historynote\">" .
         $mainrow["historynote"] . "</textarea></td></tr>\n";

    //Create changemgtno box
    echo " <tr><td>Change Management #:</td><td><input type=\"text\" size=\"22\" name=\"changemgtno\" value=\"" . $mainrow["changemgtno"] . "\"></td></tr>\n";

    //Display Admin who left/changed note last 
    echo " <tr><td>Admin Modified By:</td><td>" . $mainrow["username"] . "</td></tr>\n";

    echo "<input type=\"hidden\" name=\"historyid\" value=\"" . $mainrow["historyid"] . "\">\n";

    //Form buttons
    echo " <tr><td colspan=\"2\" align=\"right\"><input type=\"reset\" value=\"Reset\"><input type=\"submit\" name=\"Action\" value=\"Cancel\"><input type=\"submit\" name=\"Action\" value=\"Update\"></td></tr>\n";

    //Page footer
    formbottom();
    pbottom();
  endif;
elseif ($HTTP_POST_VARS["Action"] == "New"):
  //Page header
  ptop();
  formtop();

  //Formulate the query
  $sql = "SELECT servername FROM server_info WHERE server_id=\"" . $HTTP_POST_VARS["serverid"] . "\"";

  //Execute the query and put result in $result
  $result = mysql_query($sql, $db) or die ("Unable to execute query for login: " . mysql_error());

  //Get infro from query
  $mainrow = mysql_fetch_array($result);

  //Display servername
  echo " <tr><td>Server Name:</td><td>" . $mainrow["servername"] . "</td></tr>\n";

  //Create dateofnote box
  echo " <tr><td>Date:</td><td><input type=\"text\" size=\"12\" name=\"dateofnote\" value=\"" . date ("Y-m-d") . "\"></td></tr>\n";

  //Create historynote box
  echo " <tr><td>Note:</td><td><textarea cols=\"60\" rows=\"6\" name=\"historynote\"></textarea></td></tr>\n";

  //Create changemgtno box
  echo " <tr><td>Change Management #:</td><td><input type=\"text\" size=\"22\" name=\"changemgtno\"></td></tr>\n";

  echo "<input type=\"hidden\" name=\"serverid\" value=\"" . $HTTP_POST_VARS["serverid"] . "\">\n";

  //Form buttons
  echo " <tr><td colspan=\"2\" align=\"right\"><input type=\"reset\" value=\"Reset\"><input type=\"submit\" name=\"Action\" value=\"Cancel\"><input type=\"submit\" name=\"Action\" value=\"Add\"></td></tr>\n";

  //Page footer
  formbottom();
  pbottom();
elseif ($HTTP_POST_VARS["Action"] == "Add"):
  if ($HTTP_POST_VARS["historynote"] == "" AND $HTTP_POST_VARS["changemgtno"] == ""):
    echo "<b>ERROR:</b> You Must Enter Data.<br><br>\n";
    page_return();
  else:
    //Formulate the query
    $sql = "INSERT INTO history SET dateofnote= \"" . $HTTP_POST_VARS["dateofnote"] .
        "\", historynote = \"" . $HTTP_POST_VARS["historynote"] .
        "\", serverid = \"" . $HTTP_POST_VARS["serverid"] .
        "\", changemgtno = \"" . $HTTP_POST_VARS["changemgtno"] .
        "\", whoid = \"" . $loggedinuserid . "\"";

    //Execute the query and put result in $result
    $result = mysql_query($sql, $db) or die ("Unable to execute query for user info: "  . mysql_error());

    page_return();
  endif;
elseif ($HTTP_POST_VARS["Action"] == "Update"):
  //Formulate the query
    $sql = "UPDATE history SET dateofnote= \"" . $HTTP_POST_VARS["dateofnote"] .
        "\", historynote = \"" . $HTTP_POST_VARS["historynote"] .
        "\", changemgtno = \"" . $HTTP_POST_VARS["changemgtno"] .
        "\", whoid = \"" . $$loggedinuserid .
        "\" WHERE historyid = \"" . $HTTP_POST_VARS["historyid"] . "\"";

  //Execute the query and put result in $result
  $result = mysql_query($sql, $db) or die ("Unable to execute query for user info: "  . mysql_error());

  page_return();
elseif ($HTTP_POST_VARS["Action"] == "Cancel"):
  page_return();
endif;

//Close Database Connection
mysql_close($db);
?>
