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
mysql> describe room;
+-----------+-------------+------+-----+---------+----------------+
| Field     | Type        | Null | Key | Default | Extra          |
+-----------+-------------+------+-----+---------+----------------+
| roomid    | smallint(6) |      | PRI | NULL    | auto_increment |
| roomname  | varchar(50) | YES  |     | NULL    |                |
| roomnotes | tinytext    | YES  |     | NULL    |                |
+-----------+-------------+------+-----+---------+----------------+
*/

include "./pagetopbot.php";
$pagename = "Room";
$rpage = "./roomadmin.php";

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
  echo "Transaction completed. <a href=\"./\">Home</a>";
  pbottom();
}

if ($HTTP_POST_VARS['Action'] == "" or $HTTP_POST_VARS['Action'] == "None"):
  //Page header
  ptop();
  formtop();

  //Formulate the query
  $sql = "SELECT roomid, roomname FROM room ORDER BY roomname ASC";

  //Execute the query and put result in $result
  $result = mysql_query($sql, $db) or die ("Unable to execute query for login: " . mysql_error());

  //Create function box
  echo " <tr><td align=\"center\"><select tabindex=\"0\" name=\"roomid\" size=\"6\">\n";

  //Get infro from query and create form
  while ($row = mysql_fetch_array($result)):
    echo "  <option value=\"" . $row["roomid"] . "\">" . $row["roomname"] . "</option>\n";
  endwhile;

  echo " </select></td></tr>\n";
  echo " <tr><td><input type=\"reset\"><input type=\"submit\" name=\"Action\" value=\"New\"><input type=\"submit\" name=\"Action\" value=\"Cancel\"><input type=\"submit\" name=\"Action\" value=\"Delete\"><input type=\"submit\" name=\"Action\" value=\"Select\"></td></tr>\n";

  //Page footer
  formbottom();
  pbottom();
elseif ($HTTP_POST_VARS["Action"] == "Select"):
  //Page header
  ptop();
  formtop();

  //Formulate queries
  $sql = "SELECT roomid, roomname, roomnotes FROM room WHERE roomid=\"" . $HTTP_POST_VARS["roomid"] . "\"";

  //Execute the query and put result in $result
  $result = mysql_query($sql, $db) or die ("Unable to execute query for user info: "  . mysql_error());
 
//Get infro from query
  $row = mysql_fetch_array($result);

  //Create table and fill in values
  echo " <tr><td>" . $pagename . ":</td><td><input type=\"text\" size=\"52\" name=\"roomname\" value=\"" . $row["roomname"] . "\"></td></tr>\n";
  echo " <tr><td>Notes:</td><td><textarea cols=\"60\" rows=\"6\" name=\"roomnotes\">" . $row["roomnotes"] . "</textarea></td></tr>\n";
  echo " <tr><td colspan=\"2\" align=\"right\"><input type=\"hidden\" name=\"roomid\" value=\"" . $HTTP_POST_VARS["roomid"] . "\"><input type=\"reset\" value=\"Reset\"><input type=\"submit\" name=\"Action\" value=\"Cancel\"><input type=\"submit\" name=\"Action\" value=\"Update\"></td></tr>\n";

  //Page footer
  formbottom();
  pbottom();
elseif ($HTTP_POST_VARS["Action"] == "Update"):
  //Formulate the query
    $sql = "UPDATE room SET roomname = \"" . $HTTP_POST_VARS["roomname"] .
        "\", roomnotes = \"" . $HTTP_POST_VARS["roomnotes"] .
        "\" WHERE roomid = \"" . $HTTP_POST_VARS["roomid"] . "\"";

  //Execute the query and put result in $result
  $result = mysql_query($sql, $db) or die ("Unable to execute query for user info: "  . mysql_error());

  page_return();
elseif ($HTTP_POST_VARS["Action"] == "Cancel"):
  page_return();
elseif ($HTTP_POST_VARS["Action"] == "New"):
  //Page header
  ptop();
  formtop();

  //Create Category
  echo " <tr><td>" . $pagename . ":</td><td><input type=\"text\" size=\"52\" name=\"roomname\" value=\"" . $row["roomname"] . "\"></td></tr>\n";
  echo " <tr><td>Notes:</td><td><textarea cols=\"60\" rows=\"6\" name=\"roomnotes\">" . $row["roomnotes"] . "</textarea></td></tr>\n";
  echo " <tr><td colspan=\"2\" align=\"right\"><input type=\"reset\" value=\"Reset\"><input type=\"submit\" name=\"Actio
n\" value=\"Cancel\"><input type=\"submit\" name=\"Action\" value=\"Create\"></td></tr>\n";

  //Page footer
  formbottom();
  pbottom();
elseif ($HTTP_POST_VARS["Action"] == "Create"):
  if ($HTTP_POST_VARS["roomname"] == ""):
    //Page header
    ptop();

    echo "Category must be filled out!<br>\n";
    echo "<a href=\"" . $rpage . "\">Back</a>\n";
    
    //Page footer
    pbottom();
  else:
    //Formulate the query
    $sql = "INSERT INTO room SET roomname=\"" . $HTTP_POST_VARS["roomname"] .
        "\", roomnotes = \"" . $HTTP_POST_VARS["roomnotes"] . "\"";

    //Execute the query and put result in $result
    $result = mysql_query($sql, $db) or die ("Unable to execute query for user info: "  . mysql_error());

    page_return();
  endif;
elseif ($HTTP_POST_VARS["Action"] == "Delete"):
  //Formulate the query
  $sql = "DELETE FROM room WHERE roomid = \"" . $HTTP_POST_VARS["roomid"] . "\"";

  //Execute the query and put result in $result
  $result = mysql_query($sql, $db) or die ("Unable to execute query for user info: "  . mysql_error());

  page_return();
endif;

//Close Database Connection
mysql_close($db);
?>
