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
mysql> describe softwareonserver;
+------------+--------------+------+-----+---------+----------------+
| Field      | Type         | Null | Key | Default | Extra          |
+------------+--------------+------+-----+---------+----------------+
| onserverid | mediumint(9) |      | PRI | NULL    | auto_increment |
| serverid   | smallint(6)  | YES  |     | NULL    |                |
| softwareid | smallint(6)  | YES  |     | NULL    |                |
| dateadd    | date         | YES  |     | NULL    |                |
+------------+--------------+------+-----+---------+----------------+
*/

include "./pagetopbot.php";
$pagename = "Server Software";
$rpage = "./serversoftware.php";

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
  echo " <tr><td align=\"center\"><select tabindex=\"0\" name=\"serverid\" size=\"6\">\n";

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
  if ($HTTP_POST_VARS["serverid"] == "" or $HTTP_POST_VARS["serverid"] == "none"):
    echo "<b>ERROR:</b> You Must Select a Server.<br><br>\n";
    page_return();
  else:
    //Page header
    ptop();
    formtop();

    //Formulate the query
    $sql = "SELECT onserverid, dateadd, software.softname AS softname, " .
           "software.softversion AS softversion " .
	   "FROM softwareonserver JOIN software WHERE software.softwareid=softwareonserver.softwareid AND " .
           "serverid=\"" . $HTTP_POST_VARS["serverid"] . "\"";
    $sql2 = "SELECT servername FROM server_info WHERE server_id=\"" . $HTTP_POST_VARS["serverid"] . "\"";

    //Execute the query and put result in $result
    $result = mysql_query($sql, $db) or die ("Unable to execute query for login: " . mysql_error());
    $result2 = mysql_query($sql2, $db) or die ("Unable to execute query for login: " . mysql_error());

    //Get infro from query
    $serverrow = mysql_fetch_array($result2);

    //Create Software Lisitings
    echo " <tr><th colspan=\"2\">" . $serverrow["servername"] . "</th></tr>\n";
    echo " <tr><td colspan=\"2\"><table border=\"1\">\n";
    echo "  <tr bgcolor=\"lightblue\"><th>Software</th><th>Date of Install</th></tr>\n";

    while ($row = mysql_fetch_array($result)):
      //Create software list
      echo "    <tr><td><input type=\"checkbox\" name=\"onserverid[]\" value=\"" . $row["onserverid"] . "\">" .
	   $row["softname"] . " " . $row["softversion"] . "</td><td align=\"center\">" . $row["dateadd"]. "</td></tr>\n";
    endwhile;
    echo " </table></td></tr>\n";

    echo "  <input type=\"hidden\" name=\"serverid\" value=\"" . $HTTP_POST_VARS["serverid"] . "\">\n";

    //Form buttons
    echo " <tr><td colspan=\"2\" align=\"right\"><input type=\"reset\" value=\"Reset\"><input type=\"submit\" name=\"Action\" value=\"Cancel\"><input type=\"submit\" name=\"Action\" value=\"Delete\"><input type=\"submit\" name=\"Action\" value=\"Add\"></td></tr>\n";

    //Page footer
    formbottom();
    pbottom();
  endif;
elseif ($HTTP_POST_VARS["Action"] == "Delete"):
  //How many to delete
  $noin = sizeof ($HTTP_POST_VARS["onserverid"]);

  if ($noin == 0):
    echo "<b>ERROR:</b> No software selected for deletion.<br>\n";
    page_return();
  else:
    $i = 0;
    while ($i < $noin):
      //Formulate the query
      $sql = "DELETE FROM softwareonserver WHERE serverid=\"" . $HTTP_POST_VARS["serverid"] .
  	     "\" AND onserverid=\"" .  $HTTP_POST_VARS["onserverid"][$i] . "\"";
    
      //Run SQL
      $result = mysql_query($sql, $db) or die ("Unable to execute query for login: " . mysql_error());

      $i = $i + 1;
    endwhile;

    page_return();
  endif;
elseif ($HTTP_POST_VARS["Action"] == "Add"):
    //Page header
    ptop();
    formtop();

    //Formulate the query
    $sql = "SELECT softwareid, softname, softversion FROM software ORDER BY softname ASC";

    //Execute the query and put result in $result
    $result = mysql_query($sql, $db) or die ("Unable to execute query for login: " . mysql_error());

    //Create add software box
    echo " <tr><td>Add Software:</td><td><select multiple name=\"newonserverid[]\" size=\"4\">\n";
    while ($srow = mysql_fetch_array($result)):
         echo "    <option value=\"" . $srow["softwareid"] . "\">" . $srow["softname"] . $srow["softversion"] . "</option>\n";    endwhile;
    echo " </select></td></tr>\n";

    //Install date
    echo " <tr><td>Install Date:</td><td><input type=\"text\" size=\"12\" name=\"dateadd\" value=\"" . date ("Y-m-d") . "\"></td></tr>\n";

    echo "  <input type=\"hidden\" name=\"serverid\" value=\"" . $HTTP_POST_VARS["serverid"] . "\">\n";

    //Form buttons
    echo " <tr><td colspan=\"2\" align=\"right\"><input type=\"reset\" value=\"Reset\"><input type=\"submit\" name=\"Action\" value=\"Cancel\"><input type=\"submit\" name=\"Action\" value=\"Continue\"></td></tr>\n";

    //Page footer
    formbottom();
    pbottom();
elseif ($HTTP_POST_VARS["Action"] == "Continue"):
  //How many to add
  $noin = sizeof ($HTTP_POST_VARS["newonserverid"]);

  if ($noin == 0):
    echo "<b>ERROR:</b> No software selected to add.<br>\n";
    page_return();
  else:
    $i = 0;
    while ($i < $noin):
      //Formulate the query
      $sql = "INSERT INTO softwareonserver SET serverid=\"" . $HTTP_POST_VARS["serverid"] . "\", softwareid=\"" .
             $HTTP_POST_VARS["newonserverid"][$i] . "\", dateadd=\"" . $HTTP_POST_VARS["dateadd"] . "\"";

      //Run SQL
      $result = mysql_query($sql, $db) or die ("Unable to execute query for login: " . mysql_error());

      $i = $i + 1;
    endwhile;

    page_return();
  endif;
elseif ($HTTP_POST_VARS["Action"] == "Cancel"):
  page_return();
endif;

//Close Database Connection
mysql_close($db);
?>
