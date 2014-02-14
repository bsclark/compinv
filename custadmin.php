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
mysql> describe customer;
+------------+-------------+------+-----+---------+----------------+
| Field      | Type        | Null | Key | Default | Extra          |
+------------+-------------+------+-----+---------+----------------+
| customerid | smallint(6) |      | PRI | NULL    | auto_increment |
| cname      | varchar(50) | YES  |     | NULL    |                |
| cdskphone  | varchar(20) | YES  |     | NULL    |                |
| cmobphone  | varchar(20) | YES  |     | NULL    |                |
| cothrphone | varchar(20) | YES  |     | NULL    |                |
| cemail     | varchar(50) | YES  |     | NULL    |                |
| cnotes     | tinytext    | YES  |     | NULL    |                |
+------------+-------------+------+-----+---------+----------------+
*/

include "./pagetopbot.php";
$pagename = "Customer";

function formtop() {
  echo "<form enctype=\"multipart/form-data\" method=\"post\" action=\"./custadmin.php\">\n";
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
  $sql = "SELECT customerid, cname FROM customer ORDER BY cname ASC";

  //Execute the query and put result in $result
  $result = mysql_query($sql, $db) or die ("Unable to execute query for login: " . mysql_error());

  //Create select box
  echo " <tr><td align=\"center\"><select tabindex=\"0\" name=\"customerid\" size=\"6\">\n";

  //Get infro from query and create form
  while ($row = mysql_fetch_array($result)):
    echo "  <option value=\"" . $row["customerid"] . "\">" . $row["cname"] . "</option>\n";
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
  $sql = "SELECT customerid, cname, cdskphone, cmobphone, cothrphone, cemail, cnotes " .
          "FROM customer WHERE customerid=\"" . $HTTP_POST_VARS["customerid"] . "\"";

  //Execute the query and put result in $result
  $result = mysql_query($sql, $db) or die ("Unable to execute query for user info: "  . mysql_error());
 
//Get infro from query
  $row = mysql_fetch_array($result);

  //Create table and fill in values
  echo " <tr><td>Name:</td><td><input type=\"text\" size=\"52\" name=\"cname\" value=\"" . $row["cname"] . "\"></td></tr>\n";
  echo " <tr><td>Desk Phone:</td><td><input type=\"text\" size=\"22\" name=\"cdskphone\" value=\"" . $row["cdskphone"] . "\"></td></tr>\n";
  echo " <tr><td>Mobile Phone:</td><td><input type=\"text\" size=\"22\" name=\"cmobphone\" value=\"" . $row["cmobphone"] . "\"></td></tr>\n";
  echo " <tr><td>Other Phone:</td><td><input type=\"text\" size=\"22\" name=\"cothrphone\" value=\"" . $row["cothrphone"] . "\"></td></tr>\n";
  echo " <tr><td>Email:</td><td><input type=\"text\" size=\"52\" name=\"cemail\" value=\"" . $row["cemail"] . "\"></td></tr>\n";
  echo " <tr><td>Notes:</td><td><textarea cols=\"60\" rows=\"6\" name=\"cnotes\">" . $row["cnotes"] . "</textarea></td></tr>\n";
  echo " <tr><td colspan=\"2\" align=\"right\"><input type=\"hidden\" name=\"customerid\" value=\"" . $HTTP_POST_VARS["customerid"] . "\"><input type=\"reset\" value=\"Reset\"><input type=\"submit\" name=\"Action\" value=\"Cancel\"><input type=\"submit\" name=\"Action\" value=\"Update\"></td></tr>\n";

  //Page footer
  formbottom();
  pbottom();
elseif ($HTTP_POST_VARS["Action"] == "Update"):
  //Formulate the query
    $sql = "UPDATE customer SET cname = \"" . $HTTP_POST_VARS["cname"] .
        "\", cdskphone = \"" . $HTTP_POST_VARS["cdskphone"] .
        "\", cmobphone = \"" . $HTTP_POST_VARS["cmobphone"] .
        "\", cothrphone = \"" . $HTTP_POST_VARS["cothrphone"] .
        "\", cemail = \"" . $HTTP_POST_VARS["cemail"] .
        "\", cnotes = \"" . $HTTP_POST_VARS["cnotes"] .
        "\" WHERE customerid = \"" . $HTTP_POST_VARS["customerid"] . "\"";

  //Execute the query and put result in $result
  $result = mysql_query($sql, $db) or die ("Unable to execute query for user info: "  . mysql_error());

  page_return();
elseif ($HTTP_POST_VARS["Action"] == "Cancel"):
  page_return();
elseif ($HTTP_POST_VARS["Action"] == "New"):
  //Page header
  ptop();
  formtop();

  //Create User
  echo " <tr><td>Name:</td><td><input type=\"text\" size=\"52\" name=\"cname\"></td></tr>\n";
  echo " <tr><td>Desk Phone:</td><td><input type=\"text\" size=\"22\" name=\"cdskphone\"></td></tr>\n";
  echo " <tr><td>Mobile Phone:</td><td><input type=\"text\" size=\"22\" name=\"cmobphone\"></td></tr>\n";
  echo " <tr><td>Other Phone:</td><td><input type=\"text\" size=\"22\" name=\"cothrphone\"></td></tr>\n";
  echo " <tr><td>Email:</td><td><input type=\"text\" size=\"22\" name=\"cemail\"></td></tr>\n";
  echo " <tr><td>Notes:</td><td><textarea cols=\"60\" rows=\"6\" name=\"cnotes\"></textarea></td></tr>\n";
  echo " <tr><td colspan=\"2\" align=\"right\"><input type=\"reset\" value=\"Reset\"><input type=\"submit\" name=\"Actio
n\" value=\"Cancel\"><input type=\"submit\" name=\"Action\" value=\"Create\"></td></tr>\n";

  //Page footer
  formbottom();
  pbottom();
elseif ($HTTP_POST_VARS["Action"] == "Create"):
  if ($HTTP_POST_VARS["cname"] == "" or $HTTP_POST_VARS["cdskphone"] == "" or $HTTP_POST_VARS["cmobphone"] == ""):
    //Page header
    ptop();

    echo "A name,desk and mobile phone must be filled out!<br>\n";
    echo "<a href=\"./custadmin.php\">Back</a>\n";
    
    //Page footer
    pbottom();
  else:
    //Formulate the query
    $sql = "INSERT INTO customer SET cname=\"" . $HTTP_POST_VARS["cname"] . "\", cdskphone=\"" .
           $HTTP_POST_VARS["cdskphone"] . "\", cmobphone=\"" . $HTTP_POST_VARS["cmobphone"] . "\", cothrphone=\"" . 
           $HTTP_POST_VARS["cothrphone"] . "\", cemail=\"" . $HTTP_POST_VARS["cemail"] .
           "\", cnotes=\"" .$HTTP_POST_VARS["cnotes"] . "\"";

    //Execute the query and put result in $result
    $result = mysql_query($sql, $db) or die ("Unable to execute query for user info: "  . mysql_error());

    page_return();
  endif;
elseif ($HTTP_POST_VARS["Action"] == "Delete"):
  //Formulate the query
  $sql = "DELETE FROM customer WHERE customerid = \"" . $HTTP_POST_VARS["customerid"] . "\"";

  //Execute the query and put result in $result
  $result = mysql_query($sql, $db) or die ("Unable to execute query for user info: "  . mysql_error());

  page_return();
endif;

//Close Database Connection
mysql_close($db);
?>
