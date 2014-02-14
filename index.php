<?php
/*
+------------+-----------------+------+-----+---------+----------------+
| Field      | Type            | Null | Key | Default | Extra          |
+------------+-----------------+------+-----+---------+----------------+
| userid     | smallint(6)     |      | PRI | NULL    | auto_increment |
| username   | varchar(50)     | YES  |     | NULL    |                |
| userpasswd | varchar(20)     | YES  |     | NULL    |                |
| usernotes  | tinytext        | YES  |     | NULL    |                |
| useradmin  | set('No','Yes') | YES  |     | NULL    |                |
+------------+-----------------+------+-----+---------+----------------+
*/

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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "DTD/xhtml1-transitional.dtd">
<html>
<head>
<title>Server List</title>
<link href="MainStyle.css" rel="stylesheet" type="text/css" />
</head>

<body>

<p><h1>Server List</h1></p>
<hr>
<p>All Servers Function - <a href="./allserversfunction.php">Here</a>
<br>All Servers Location - <a href="./allserverslocation.php">Here</a>
<br>All Servers Support - <a href="./allserverssupport.php">Here</a>
<br>All Servers Hardware - <a href="./allservershardware.php">Here</a></p>

<?php
if ($loggedinadmin == "Yes"):
  echo "<hr>\n";
  echo "<p>\n";
  echo "<h3>Server Admin Pages</h3>\n";
  echo "Server Info Administration - <a href=\"server_info.php\">Here</a><br>\n";
  echo "Server Backup Administration - <a href=\"server-back.php\">Here</a><br>\n";
  echo "Server Monitor Administration - <a href=\"server-mont.php\">Here</a><br>\n";
  echo "Server Hardware Administration - <a href=\"serverspec.php\">Here</a><br>\n";
  echo "Server Software Administration - <a href=\"serversoftware.php\">Here</a><br>\n";
  echo "Server History  Administration - <a href=\"server-history.php\">Here</a><br>\n";
  echo "</p>\n";
  echo "<hr>\n";
  echo "<p>\n";
  echo "<h3>Database Admin Pages</h3>\n";
  echo "Admin User Administration - <a href=\"uadmin.php\">Here</a><br>\n";
  echo "Customer Administration - <a href=\"custadmin.php\">Here</a><br>\n";
  echo "Function Administration - <a href=\"fadmin.php\">Here</a><br>\n";
  echo "Category Administration - <a href=\"cadmin.php\">Here</a><br>\n";
  echo "Platform Administration - <a href=\"padmin.php\">Here</a><br>\n";
  echo "Make Administration - <a href=\"makeadmin.php\">Here</a><br>\n";
  echo "Model Administration - <a href=\"modeladmin.php\">Here</a><br>\n";
  echo "Software Administration - <a href=\"sadmin.php\">Here</a><br>\n";
  echo "Room Administration - <a href=\"roomadmin.php\">Here</a><br>\n";
  echo "Rack Administration - <a href=\"rackadmin.php\">Here</a><br>\n";
  echo "Backup Type Administration - <a href=\"backuptypeadmin.php\">Here</a><br>\n";
  echo "Monitor Type Administration - <a href=\"monitortypeadmin.php\">Here</a><br>\n";
  echo "</p>";
endif;

//Page bottom
pbottom();
?>
</body>
</html>
