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
mysql> describe server_info;
+--------------------+-----------------------+------+-----+---------+----------------+
| Field              | Type                  | Null | Key | Default | Extra          |
+--------------------+-----------------------+------+-----+---------+----------------+
| server_id          | smallint(6)           |      | PRI | NULL    | auto_increment |
| servername         | varchar(15)           | YES  |     | NULL    |                |
| osplatform         | smallint(6)           | YES  |     | NULL    |                |
| patchlvl           | varchar(15)           | YES  |     | NULL    |                |
| production         | set('No','Yes','N/A') | YES  |     | NULL    |                |
| supported          | set('No','Yes','N/A') | YES  |     | NULL    |                |
| critical           | set('No','Yes','N/A') | YES  |     | NULL    |                |
| category           | smallint(6)           | YES  |     | NULL    |                |
| primarycustcontact | smallint(6)           | YES  |     | NULL    |                |
| primfunction       | smallint(6)           | YES  |     | NULL    |                |
| secfunction        | smallint(6)           | YES  |     | NULL    |                |
| datecreated        | date                  | YES  |     | NULL    |                |
| serialnum          | varchar(80)           | YES  |     | NULL    |                |
| tag                | varchar(10)           | YES  |     | NULL    |                |
| make               | smallint(6)           | YES  |     | NULL    |                |
| model              | smallint(6)           | YES  |     | NULL    |                |
| racked             | set('No','Yes')       | YES  |     | NULL    |                |
| rackid             | smallint(6)           | YES  |     | NULL    |                |
| roomid             | smallint(6)           | YES  |     | NULL    |                |
+--------------------+-----------------------+------+-----+---------+----------------+
*/

include "./pagetopbot.php";
$pagename = "Server Information";
$rpage = "./server_info.php";

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
  echo " <tr><td><input type=\"reset\"><input type=\"submit\" name=\"Action\" value=\"New\"><input type=\"submit\" name=\"Action\" value=\"Cancel\"><input type=\"submit\" name=\"Action\" value=\"Delete\"><input type=\"submit\" name=\"Action\" value=\"Select\"></td></tr>\n";

  //Page footer
  formbottom();
  pbottom();
elseif ($HTTP_POST_VARS["Action"] == "Select"):
  //Page header
  ptop();
  formtop();

  //Formulate queries
  $sql = "SELECT servername, osplatform, patchlvl, production, supported, critical, category, primarycustcontact, " .
         "primfunction, secfunction, datecreated, serialnum, tag, make, model, racked, rackid, roomid " .
         "FROM server_info WHERE server_id=\"" . $HTTP_POST_VARS["server_id"] . "\"";
  $sql2 = "SELECT platformid, osname FROM platform ORDER BY osname ASC";
  $sql3 = "SELECT catid, catname FROM category ORDER BY catname ASC";
  $sql4 = "SELECT customerid ,cname FROM customer ORDER BY cname ASC";
  $sql5 = "SELECT functionid, fdescription FROM function ORDER BY fdescription ASC";
  $sql6 = "SELECT rackid, rackname FROM rack ORDER BY rackname ASC";
  $sql7 = "SELECT roomid, roomname FROM room ORDER BY roomname ASC";
  $sql8 = "SELECT makeid, makename FROM make ORDER BY makename ASC";
  $sql9 = "SELECT modelid, modelname FROM model ORDER BY modelname ASC";

  //Execute the query and put result in $result
  $result = mysql_query($sql, $db) or die ("Unable to execute query for user info: "  . mysql_error());
  $result2 = mysql_query($sql2, $db) or die ("Unable to execute query for user info: "  . mysql_error());
  $result3 = mysql_query($sql3, $db) or die ("Unable to execute query for user info: "  . mysql_error());
  $result4 = mysql_query($sql4, $db) or die ("Unable to execute query for user info: "  . mysql_error());
  $result5 = mysql_query($sql5, $db) or die ("Unable to execute query for user info: "  . mysql_error());
  $result5a = mysql_query($sql5, $db) or die ("Unable to execute query for user info: "  . mysql_error());
  $result6 = mysql_query($sql6, $db) or die ("Unable to execute query for user info: "  . mysql_error());
  $result7 = mysql_query($sql7, $db) or die ("Unable to execute query for user info: "  . mysql_error());
  $result8 = mysql_query($sql8, $db) or die ("Unable to execute query for user info: "  . mysql_error());
  $result9 = mysql_query($sql9, $db) or die ("Unable to execute query for user info: "  . mysql_error());
 
//Get infro from query
  $mainrow = mysql_fetch_array($result);

  //Create table and fill in values
  echo " <tr><td>Server Name:</td><td><input type=\"text\" size=\"52\" name=\"servername\" value=\"" . $mainrow["servername"] . "\"></td></tr>\n";

  //Create platform select box
  echo " <tr><td>OS Platform:</td><td><select name=\"osplatform\" size=\"1\">\n";
  while ($row = mysql_fetch_array($result2)):
     if ($row["platformid"] == $mainrow["osplatform"]):
       echo "    <option selected value=\"" . $row["platformid"] . "\">" . $row["osname"] . "</option>\n";
     else:
       echo "    <option value=\"" . $row["platformid"] . "\">" . $row["osname"] . "</option>\n";
     endif;
  endwhile;
  echo " </select></td></tr>\n";

  //Create patch info box
  echo " <tr><td>Patch Level:</td><td><input type=\"text\" size=\"17\" name=\"patchlvl\" value=\"" . $mainrow["patchlvl"] . "\"></td></tr>\n";

  //Create production select box
  echo " <tr><td>Production:</td><td><select tabindex=\"0\" name=\"production\" size=\"1\">\n";
  if ($mainrow["production"] == "Yes"):
     echo "    <option selected value=\"Yes\">Yes</option>\n";
     echo "    <option value=\"No\">No</option>\n";
     echo "    <option value=\"N/A\">N/A</option>\n";
  elseif ($mainrow["production"] == "No"):
     echo "    <option value=\"Yes\">Yes</option>\n";
     echo "    <option selected value=\"No\">No</option>\n";
     echo "    <option value=\"N/A\">N/A</option>\n";
  elseif ($mainrow["production"] == "N/A"):
     echo "    <option value=\"Yes\">Yes</option>\n";
     echo "    <option value=\"No\">No</option>\n";
     echo "    <option selected value=\"N/A\">N/A</option>\n";
  else:
     echo "    <option value=\"Yes\">Yes</option>\n";
     echo "    <option value=\"No\">No</option>\n";
     echo "    <option value=\"N/A\">N/A</option>\n";
  endif;
  echo " </select></td></tr>\n";

  //Create supported select box
  echo " <tr><td>Supported:</td><td><select tabindex=\"0\" name=\"supported\" size=\"1\">\n";
  if ($mainrow["supported"] == "Yes"):
     echo "    <option selected value=\"Yes\">Yes</option>\n";
     echo "    <option value=\"No\">No</option>\n";
     echo "    <option value=\"N/A\">N/A</option>\n";
  elseif ($mainrow["supported"] == "No"):
     echo "    <option value=\"Yes\">Yes</option>\n";
     echo "    <option selected value=\"No\">No</option>\n";
     echo "    <option value=\"N/A\">N/A</option>\n";
  elseif ($mainrow["supported"] == "N/A"):
     echo "    <option value=\"Yes\">Yes</option>\n";
     echo "    <option value=\"No\">No</option>\n";
     echo "    <option selected value=\"N/A\">N/A</option>\n";
  else:
     echo "    <option value=\"Yes\">Yes</option>\n";
     echo "    <option value=\"No\">No</option>\n";
     echo "    <option value=\"N/A\">N/A</option>\n";
  endif;
  echo " </select></td></tr>\n";

  //Create critical select box
  echo " <tr><td>Critical:</td><td><select tabindex=\"0\" name=\"critical\" size=\"1\">\n";
  if ($mainrow["critical"] == "Yes"):
     echo "    <option selected value=\"Yes\">Yes</option>\n";
     echo "    <option value=\"No\">No</option>\n";
     echo "    <option value=\"N/A\">N/A</option>\n";
  elseif ($mainrow["critical"] == "No"):
     echo "    <option value=\"Yes\">Yes</option>\n";
     echo "    <option selected value=\"No\">No</option>\n";
     echo "    <option value=\"N/A\">N/A</option>\n";
  elseif ($mainrow["critical"] == "N/A"):
     echo "    <option value=\"Yes\">Yes</option>\n";
     echo "    <option value=\"No\">No</option>\n";
     echo "    <option selected value=\"N/A\">N/A</option>\n";
  else:
     echo "    <option value=\"Yes\">Yes</option>\n";
     echo "    <option value=\"No\">No</option>\n";
     echo "    <option value=\"N/A\">N/A</option>\n";
  endif;
  echo " </select></td></tr>\n";

  //Create category select box
  echo " <tr><td>Category:</td><td><select name=\"category\" size=\"1\">\n";
  while ($row = mysql_fetch_array($result3)):
     if ($row["catid"] == $mainrow["category"]):
       echo "    <option selected value=\"" . $row["catid"] . "\">" . $row["catname"] . "</option>\n";
     else:
       echo "    <option value=\"" . $row["catid"] . "\">" . $row["catname"] . "</option>\n";
     endif;
  endwhile;
  echo " </select></td></tr>\n";

  //Create customer select box
  echo " <tr><td>Primary Contact:</td><td><select name=\"primarycustcontact\" size=\"1\">\n";
  while ($row = mysql_fetch_array($result4)):
     if ($row["customerid"] == $mainrow["primarycustcontact"]):
       echo "    <option selected value=\"" . $row["customerid"] . "\">" . $row["cname"] . "</option>\n";
     else:
       echo "    <option value=\"" . $row["customerid"] . "\">" . $row["cname"] . "</option>\n";
     endif;
  endwhile;
  echo " </select></td></tr>\n";

  //Create primary function select box
  echo " <tr><td>Primary Function:</td><td><select name=\"primfunction\" size=\"1\">\n";
  while ($row = mysql_fetch_array($result5)):
     if ($row["functionid"] == $mainrow["primfunction"]):
       echo "    <option selected value=\"" . $row["functionid"] . "\">" . $row["fdescription"] . "</option>\n";
     else:
       echo "    <option value=\"" . $row["functionid"] . "\">" . $row["fdescription"] . "</option>\n";
     endif;
  endwhile;
  echo " </select></td></tr>\n";

  //Create secondary function select box
  echo " <tr><td>Secondary Function:</td><td><select name=\"secfunction\" size=\"1\">\n";
  while ($row = mysql_fetch_array($result5a)):
     if ($row["functionid"] == $mainrow["secfunction"]):
       echo "    <option selected value=\"" . $row["functionid"] . "\">" . $row["fdescription"] . "</option>\n";
     else:
       echo "    <option value=\"" . $row["functionid"] . "\">" . $row["fdescription"] . "</option>\n";
     endif;
  endwhile;
  echo " </select></td></tr>\n";

  //Create date box
  echo " <tr><td>Online Date:</td><td><input type=\"text\" size=\"10\" name=\"datecreated\" value=\"" . $mainrow["datecreated"] . "\"></td></tr>\n";

  //Create the racked radio boxes
  echo " <tr><td>Racked:</td><td>";
  if ($mainrow["racked"] == "Yes"):
    echo "<input type=\"radio\" name=\"racked\" value=\"Yes\" checked=\"on\">Yes<input type=\"radio\" name=\"racked\" value=\"No\">No</td></tr>\n";
  else:
    echo "<input type=\"radio\" name=\"racked\" value=\"Yes\">Yes<input type=\"radio\" name=\"racked\" value=\"No\" checked=\"on\">No</td></tr>\n";
  endif;

  //Create rack select box
  echo " <tr><td>Rack:</td><td><select name=\"rackid\" size=\"1\">\n";
  while ($row = mysql_fetch_array($result6)):
    if ($row["rackid"] == $mainrow["rackid"]):
      echo "    <option selected value=\"" . $row["rackid"] . "\">" . $row["rackname"] . "</option>\n";
    else:
      echo "    <option value=\"" . $row["rackid"] . "\">" . $row["rackname"] . "</option>\n";
    endif;
  endwhile;
  echo " </select></td></tr>\n";

  //Create room select box
  echo " <tr><td>Room:</td><td><select name=\"roomid\" size=\"1\">\n";
  while ($row = mysql_fetch_array($result7)):
    if ($row["roomid"] == $mainrow["roomid"]):
      echo "    <option selected value=\"" . $row["roomid"] . "\">" . $row["roomname"] . "</option>\n";
    else:
      echo "    <option value=\"" . $row["roomid"] . "\">" . $row["roomname"] . "</option>\n";
    endif;
  endwhile;
  echo " </select></td></tr>\n";

  //Create serial number box
  echo " <tr><td>Serial Number:</td><td><input type=\"text\" size=\"52\" name=\"serialnum\" value=\"" . $mainrow["serialnum"] . "\"></td></tr>\n";

  //Create tag boxes
  echo " <tr><td>Tag:</td><td><input type=\"text\" size=\"12\" name=\"tag\" value=\"" . $mainrow["tag"] . "\"></td></tr>\n";

  //Create make select box
  echo " <tr><td>Make:</td><td><select name=\"make\" size=\"1\">\n";
  while ($row = mysql_fetch_array($result8)):
    if ($row["makeid"] == $mainrow["make"]):
      echo "    <option selected value=\"" . $row["makeid"] . "\">" . $row["makename"] . "</option>\n";
    else:
      echo "    <option value=\"" . $row["makeid"] . "\">" . $row["makename"] . "</option>\n";
    endif;
  endwhile;
  echo " </select></td></tr>\n";

  //Create model select box
  echo " <tr><td>Model:</td><td><select name=\"model\" size=\"1\">\n";
  while ($row = mysql_fetch_array($result9)):
    if ($row["modelid"] == $mainrow["model"]):
      echo "    <option selected value=\"" . $row["modelid"] . "\">" . $row["modelname"] . "</option>\n";
    else:
      echo "    <option value=\"" . $row["modelid"] . "\">" . $row["modelname"] . "</option>\n";
    endif;
  endwhile;
  echo " </select></td></tr>\n";

  //Form buttons
  echo " <tr><td colspan=\"2\" align=\"right\"><input type=\"hidden\" name=\"server_id\" value=\"" . $HTTP_POST_VARS["server_id"] . "\"><input type=\"reset\" value=\"Reset\"><input type=\"submit\" name=\"Action\" value=\"Cancel\"><input type=\"submit\" name=\"Action\" value=\"Update\"></td></tr>\n";

  //Page footer
  formbottom();
  pbottom();
elseif ($HTTP_POST_VARS["Action"] == "Update"):
  //Formulate the query
    $sql = "UPDATE server_info SET servername = \"" . $HTTP_POST_VARS["servername"] .
        "\", osplatform = \"" . $HTTP_POST_VARS["osplatform"] .
        "\", patchlvl = \"" . $HTTP_POST_VARS["patchlvl"] .
        "\", production = \"" . $HTTP_POST_VARS["production"] .
        "\", supported = \"" . $HTTP_POST_VARS["supported"] .
        "\", critical = \"" . $HTTP_POST_VARS["critical"] .
        "\", category = \"" . $HTTP_POST_VARS["category"] .
        "\", primarycustcontact = \"" . $HTTP_POST_VARS["primarycustcontact"] .
        "\", primfunction = \"" . $HTTP_POST_VARS["primfunction"] .
        "\", secfunction = \"" . $HTTP_POST_VARS["secfunction"] .
        "\", datecreated = \"" . $HTTP_POST_VARS["datecreated"] .
        "\", serialnum = \"" . $HTTP_POST_VARS["serialnum"] .
        "\", tag = \"" . $HTTP_POST_VARS["tag"] .
        "\", make = \"" . $HTTP_POST_VARS["make"] .
        "\", model = \"" . $HTTP_POST_VARS["model"] .
        "\" WHERE server_id = \"" . $HTTP_POST_VARS["server_id"] . "\"";

  //Execute the query and put result in $result
  $result = mysql_query($sql, $db) or die ("Unable to execute query for user info: "  . mysql_error());

  page_return();
elseif ($HTTP_POST_VARS["Action"] == "Cancel"):
  page_return();
elseif ($HTTP_POST_VARS["Action"] == "New"):
  //Page header
  ptop();
  formtop();

  //Formulate queries
  $sql = "SELECT customerid, cname FROM customer ORDER BY cname ASC";
  $sql2 = "SELECT functionid, fdescription FROM function ORDER BY fdescription ASC";
  $sql3 = "SELECT catid, catname FROM category ORDER BY catname ASC";
  $sql4 = "SELECT platformid, osname FROM platform ORDER BY osname ASC";
  $sql5 = "SELECT makeid, makename FROM make ORDER BY makename ASC";
  $sql6 = "SELECT modelid, modelname FROM model ORDER BY modelname ASC";
  $sql7 = "SELECT rackid, rackname FROM rack ORDER BY rackname ASC";
  $sql8 = "SELECT roomid, roomname FROM room ORDER BY roomname ASC";

  //Execute the query and put result in $result
  $result = mysql_query($sql, $db) or die ("Unable to execute query for user info: "  . mysql_error());
  $result2 = mysql_query($sql2, $db) or die ("Unable to execute query for user info: "  . mysql_error());
  $result2a = mysql_query($sql2, $db) or die ("Unable to execute query for user info: "  . mysql_error());
  $result3 = mysql_query($sql3, $db) or die ("Unable to execute query for user info: "  . mysql_error());
  $result4 = mysql_query($sql4, $db) or die ("Unable to execute query for user info: "  . mysql_error());
  $result5 = mysql_query($sql5, $db) or die ("Unable to execute query for user info: "  . mysql_error());
  $result6 = mysql_query($sql6, $db) or die ("Unable to execute query for user info: "  . mysql_error());
  $result7 = mysql_query($sql7, $db) or die ("Unable to execute query for user info: "  . mysql_error());
  $result8 = mysql_query($sql8, $db) or die ("Unable to execute query for user info: "  . mysql_error());

  //Create table and fill in values
  echo " <tr><td>Server Name:</td><td><input type=\"text\" size=\"52\" name=\"servername\"></td></tr>\n";

  //Create platform select box
  echo " <tr><td>OS Platform:</td><td><select name=\"osplatform\" size=\"1\">\n";
  while ($row = mysql_fetch_array($result4)):
     echo "    <option value=\"" . $row["platformid"] . "\">" . $row["osname"] . "</option>\n";
  endwhile;
  echo " </select></td></tr>\n";

  echo " <tr><td>Patch Level:</td><td><input type=\"text\" size=\"17\" name=\"patchlvl\"></td></tr>\n";

  //Create production select box
  echo " <tr><td>Production:</td><td><select name=\"production\" size=\"1\">\n";
     echo "    <option value=\"Yes\">Yes</option>\n";
     echo "    <option value=\"No\">No</option>\n";
     echo "    <option selected value=\"N/A\">N/A</option>\n";
  echo " </select></td></tr>\n";

  //Create supported select box
  echo " <tr><td>Supported:</td><td><select name=\"supported\" size=\"1\">\n";
     echo "    <option value=\"Yes\">Yes</option>\n";
     echo "    <option value=\"No\">No</option>\n";
     echo "    <option selected value=\"N/A\">N/A</option>\n";
  echo " </select></td></tr>\n";

  //Create select critical  box
  echo " <tr><td>Critical:</td><td><select name=\"critical\" size=\"1\">\n";
     echo "    <option value=\"Yes\">Yes</option>\n";
     echo "    <option value=\"No\">No</option>\n";
     echo "    <option selected value=\"N/A\">N/A</option>\n";
  echo " </select></td></tr>\n";

  //Create category select box
  echo " <tr><td>Category:</td><td><select name=\"category\" size=\"1\">\n";
  while ($row = mysql_fetch_array($result3)):
     echo "    <option value=\"" . $row["catid"] . "\">" . $row["catname"] . "</option>\n";
  endwhile;
  echo " </select></td></tr>\n";

  //Create primary customer select box
  echo " <tr><td>Primary Contact:</td><td><select name=\"primarycustcontact\" size=\"1\">\n";
  while ($row = mysql_fetch_array($result)):
     echo "    <option value=\"" . $row["customerid"] . "\">" . $row["cname"] . "</option>\n";
  endwhile;
  echo " </select></td></tr>\n";

  //Create primary function select box
  echo " <tr><td>Primary Function:</td><td><select name=\"primfunction\" size=\"1\">\n";
  while ($row = mysql_fetch_array($result2)):
     echo "    <option value=\"" . $row["functionid"] . "\">" . $row["fdescription"] . "</option>\n";
  endwhile;
  echo " </select></td></tr>\n";

  //Create sec function select box
  echo " <tr><td>Secondary Function:</td><td><select name=\"secfunction\" size=\"1\">\n";
  while ($row = mysql_fetch_array($result2a)):
     echo "    <option value=\"" . $row["functionid"] . "\">" . $row["fdescription"] . "</option>\n";
  endwhile;
  echo " </select></td></tr>\n";

  //Auto fill in create date
  echo " <tr><td>Online Date:</td><td><input type=\"text\" size=\"10\" name=\"datecreated\" value=\"" . date ("Y-m-d") . "\"></td></tr>\n";
  
  //Create the racked radio boxes
  echo " <tr><td>Racked:</td><td><input type=\"radio\" name=\"racked\" value=\"Yes\">Yes<input type=\"radio\" name=\"racked\" value=\"No\">No</td></tr>\n";

  //Create rack select box
  echo " <tr><td>Rack:</td><td><select name=\"rackid\" size=\"1\">\n";
  while ($row = mysql_fetch_array($result7)):
     echo "    <option value=\"" . $row["rackid"] . "\">" . $row["rackname"] . "</option>\n";
  endwhile;
  echo " </select></td></tr>\n";

  //Create room select box
  echo " <tr><td>Room:</td><td><select name=\"roomid\" size=\"1\">\n";
  while ($row = mysql_fetch_array($result8)):
     echo "    <option value=\"" . $row["roomid"] . "\">" . $row["roomname"] . "</option>\n";
  endwhile;
  echo " </select></td></tr>\n";

  //Create serial number box
  echo " <tr><td>Serial Number:</td><td><input type=\"text\" size=\"52\" name=\"serialnum\"></td></tr>\n";

  //Create tag boxes
  echo " <tr><td>Tag:</td><td><input type=\"text\" size=\"12\" name=\"tag\"></td></tr>\n";

  //Create make select box
  echo " <tr><td>Make:</td><td><select name=\"make\" size=\"1\">\n";
  while ($row = mysql_fetch_array($result5)):
     echo "    <option value=\"" . $row["makeid"] . "\">" . $row["makename"] . "</option>\n";
  endwhile;
  echo " </select></td></tr>\n";

  //Create model select box
  echo " <tr><td>Model:</td><td><select name=\"model\" size=\"1\">\n";
  while ($row = mysql_fetch_array($result6)):
     echo "    <option value=\"" . $row["modelid"] . "\">" . $row["modelname"] . "</option>\n";
  endwhile;
  echo " </select></td></tr>\n";

  //Create the form buttons
  echo " <tr><td colspan=\"2\" align=\"right\"><input type=\"reset\" value=\"Reset\"><input type=\"submit\" name=\"Action\" value=\"Cancel\"><input type=\"submit\" name=\"Action\" value=\"Create\"></td></tr>\n";

  //Page footer
  formbottom();
  pbottom();
elseif ($HTTP_POST_VARS["Action"] == "Create"):
  if ($HTTP_POST_VARS["servername"] == ""):
    //Page header
    ptop();

    echo "Server Name Must be filled out!<br>\n";
    echo "<a href=\"" . $rpage . "\">Back</a>\n";
    
    //Page footer
    pbottom();
  else:
    //Formulate the query
    $sql = "INSERT INTO server_info SET servername=\"" . $HTTP_POST_VARS["servername"] .
        "\", osplatform = \"" . $HTTP_POST_VARS["osplatform"] . 
        "\", patchlvl = \"" . $HTTP_POST_VARS["patchlvl"] . 
        "\", production = \"" . $HTTP_POST_VARS["production"] . 
        "\", supported = \"" . $HTTP_POST_VARS["supported"] . 
        "\", critical = \"" . $HTTP_POST_VARS["critical"] . 
        "\", category = \"" . $HTTP_POST_VARS["category"] . 
        "\", primarycustcontact = \"" . $HTTP_POST_VARS["primarycustcontact"] . 
        "\", primfunction = \"" . $HTTP_POST_VARS["primfunction"] . 
        "\", secfunction = \"" . $HTTP_POST_VARS["secfunction"] . 
        "\", datecreated = \"" . $HTTP_POST_VARS["datecreated"] . 
        "\", serialnum = \"" . $HTTP_POST_VARS["serialnum"] . 
        "\", tag = \"" . $HTTP_POST_VARS["tag"] . 
        "\", make = \"" . $HTTP_POST_VARS["make"] . 
        "\", model = \"" . $HTTP_POST_VARS["model"] . 
        "\", racked = \"" . $HTTP_POST_VARS["racked"] . 
        "\", rackid = \"" . $HTTP_POST_VARS["rackid"] . 
        "\", roomid = \"" . $HTTP_POST_VARS["roomid"] . "\"";

    //Execute the query and put result in result
    $result = mysql_query($sql, $db) or die ("Unable to execute query for user info: "  . mysql_error());
 
    //Populate other needed tables off new server_id
    $serverid = mysql_insert_id();

    //Add entries to the backup, monitor, softwareonserver, history and serverspec tables;
    $sql2 = "INSERT INTO backup SET serverid=\"" . $serverid . "\"";
    $sql3 = "INSERT INTO monitor SET serverid=\"" . $serverid . "\"";
//    $sql4 = "INSERT INTO softwareonserver SET serverid=\"" . $serverid . "\"";
    $sql5 = "INSERT INTO history SET historynote=\"Initial insert into database.\", whoid=\"" . $loggedinuserid .
            "\", serverid=\"" . $serverid . "\"";
    $sql6 = "INSERT INTO serverspec SET serverid=\"" . $serverid . "\"";
    
    $result2 = mysql_query($sql2, $db) or die ("Unable to execute query for user info: "  . mysql_error());
    $result3 = mysql_query($sql3, $db) or die ("Unable to execute query for user info: "  . mysql_error());
//    $result4 = mysql_query($sql4, $db) or die ("Unable to execute query for user info: "  . mysql_error());
    $result5 = mysql_query($sql5, $db) or die ("Unable to execute query for user info: "  . mysql_error());
    $result6 = mysql_query($sql6, $db) or die ("Unable to execute query for user info: "  . mysql_error());

    page_return();
  endif;
elseif ($HTTP_POST_VARS["Action"] == "Delete"):
  //Delete all traces of the server from all tables
  $sql = "DELETE FROM server_info WHERE server_id = \"" . $HTTP_POST_VARS["server_id"] . "\"";
  $sql2 = "DELETE FROM backup WHERE serverid=\"" . $HTTP_POST_VARS["server_id"] . "\"";
  $sql3 = "DELETE FROM monitor WHERE serverid=\"" . $HTTP_POST_VARS["server_id"] . "\"";
  $sql4 = "DELETE FROM softwareonserver WHERE serverid=\"" . $HTTP_POST_VARS["server_id"] . "\"";
  $sql5 = "DELETE FROM history WHERE serverid=\"" . $HTTP_POST_VARS["server_id"] . "\"";
  $sql6 = "DELETE FROM serverspec WHERE serverid=\"" . $HTTP_POST_VARS["server_id"] . "\"";

  //Execute the query and put result in $result
  $result = mysql_query($sql, $db) or die ("Unable to execute query for user info: "  . mysql_error());
  $result = mysql_query($sql2, $db) or die ("Unable to execute query for user info: "  . mysql_error());
  $result = mysql_query($sql3, $db) or die ("Unable to execute query for user info: "  . mysql_error());
  $result = mysql_query($sql4, $db) or die ("Unable to execute query for user info: "  . mysql_error());
  $result = mysql_query($sql5, $db) or die ("Unable to execute query for user info: "  . mysql_error());
  $result = mysql_query($sql6, $db) or die ("Unable to execute query for user info: "  . mysql_error());

  page_return();
endif;

//Close Database Connection
mysql_close($db);
?>
