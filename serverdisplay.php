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

//Formulate the query
$sql = "SELECT si.servername AS servername, ss.ipaddress AS ipaddress, p.osname AS osname, si.patchlvl AS patchlvl,  " .
       "si.production AS production, si.supported AS supported, si.critical AS critical, c.catname AS catname, " .
       "cu.cname AS cname, f1.fdescription AS primfunction, f2.fdescription AS secfunction, si.datecreated AS onlinedate, " .
       "si.serialnum AS sn, si.tag AS tag, m.makename AS make, mo.modelname AS model, " .
       "si.racked AS racked, r.rackname AS rack, ro.roomname AS room, m1.supportdate AS suppdate, " .
       "m1.monitored AS monitored, mt.typename AS montype, b.backedup AS backedup, bt.typename AS backtype, " .
       "b.bfrequency AS frequency, b.retain AS retain, b.databackuped AS databackuped, ss.biosdate AS biosdate, " .
       "ss.firmware AS frimware, ss.cpu AS cpu, ss.ram AS ram, ss.diskspace AS diskspace, ss.totalnics AS totalnics, " .
       "ss.nictype AS nictype, ss.nicinuse AS nicinuse, ss.networkcomments AS ncomments " .
       "FROM server_info AS si LEFT JOIN serverspec AS ss ON si.server_id=ss.serverid " .
       "LEFT JOIN platform AS p ON si.osplatform=p.platformid " .
       "LEFT JOIN category AS c ON si.category=c.catid " .
       "LEFT JOIN customer AS cu ON si.primarycustcontact=cu.customerid " .
       "LEFT JOIN function AS f1 ON si.primfunction=f1.functionid " .
       "LEFT JOIN function AS f2 ON si.secfunction=f2.functionid " .
       "LEFT JOIN make AS m ON si.make=m.makeid " .
       "LEFT JOIN model AS mo ON si.model=mo.modelid " .
       "LEFT JOIN rack AS r ON si.rackid=r.rackid " .
       "LEFT JOIN room AS ro ON si.roomid=ro.roomid " .
       "LEFT JOIN monitor AS m1 ON m1.serverid=si.server_id " .
       "LEFT JOIN monitortype AS mt ON m1.monitortype=mt.monitortypeid " .
       "LEFT JOIN backup AS b ON b.serverid=si.server_id " .
       "LEFT JOIN backuptype AS bt ON b.backuptype=bt.backuptypeid " .
       "WHERE si.server_id=\"" . $HTTP_GET_VARS["sname"] . "\"";

//Execute the query and put result in $result
$result = mysql_query($sql, $db) or die ("Unable to execute query for login: " . mysql_error());

//Get infro from query
$mainrow = mysql_fetch_array($result);

//Page specific suff
include "./pagetopbot.php";
$pagename = "Server List - Server Display: " . $mainrow["servername"];
$rpage = "./serverdisplay.php";

//Page header
ptop();

echo "<table border=\"1\" align=\"center\">\n";
echo "<tr bgcolor=\"#99999\"><th colspan=\"2\" align=\"center\">" . $mainrow["servername"] . "</th></tr>\n";
echo "<tr><td>IP Address:</td><td>" . $mainrow["ipaddress"] . "</td></tr>\n";
echo "<tr><td>OS:</td><td>" . $mainrow["osname"] . "</td></tr>\n";
echo "<tr><td>Patch Lvl:</td><td>" . $mainrow["patchlvl"] . "</td></tr>\n";
echo "<tr><td>Production:</td><td>" . $mainrow["production"] . "</td></tr>\n";
echo "<tr><td>Supported:</td><td>" . $mainrow["supported"] . "</td></tr>\n";
echo "<tr><td>Critical:</td><td>" . $mainrow["critical"] . "</td></tr>\n";
echo "<tr><td>Category:</td><td>" . $mainrow["catname"] . "</td></tr>\n";
echo "<tr><td>Primary Customer:</td><td>" . $mainrow["cname"] . "</td></tr>\n";
echo "<tr><td>Primary Function:</td><td>" . $mainrow["primfunction"] . "</td></tr>\n";
echo "<tr><td>Secondary Function:</td><td>" . $mainrow["secfunction"] . "</td></tr>\n";
echo "<tr><td>Online Date:</td><td>" . $mainrow["onlinedate"] . "</td></tr>\n";
echo "<tr><td>S/N:</td><td>" . $mainrow["sn"] . "</td></tr>\n";
echo "<tr><td>Tag:</td><td>" . $mainrow["tag"] . "</td></tr>\n";
echo "<tr><td>Make:</td><td>" . $mainrow["make"] . "</td></tr>\n";
echo "<tr><td>Model:</td><td>" . $mainrow["model"] . "</td></tr>\n";
echo "<tr bgcolor=\"ffff66\"><td>Racked:</td><td>" . $mainrow["racked"] . "</td></tr>\n";
echo "<tr bgcolor=\"ffff66\"><td>Rack Location:</td><td>" . $mainrow["rack"] . "</td></tr>\n";
echo "<tr bgcolor=\"ffff66\"><td>Room:</td><td>" . $mainrow["room"] . "</td></tr>\n";
echo "<tr bgcolor=\"#cc66ff\"><td>Support Date:</td><td>" . $mainrow["suppdate"] . "</td></tr>\n";
echo "<tr bgcolor=\"#cc66ff\"><td>Monitored?:</td><td>" . $mainrowi["monitored"] . "</td></tr>\n";
echo "<tr bgcolor=\"#cc66ff\"><td>Monitor Type:</td><td>" . $mainrow["montype"] . "</td></tr>\n";
echo "<tr bgcolor=\"#cc66ff\"><td>Backedup?:</td><td>" . $mainrow["backedup"] . "</td></tr>\n";
echo "<tr bgcolor=\"#cc66ff\"><td>Backup Type:</td><td>" . $mainrow["backtype"] . "</td></tr>\n";
echo "<tr bgcolor=\"#cc66ff\"><td>Frequency:</td><td>" . $mainrow["frequency"] . "</td></tr>\n";
echo "<tr bgcolor=\"#cc66ff\"><td>Retain:</td><td>" . $mainrow["retain"] . "</td></tr>\n";
echo "<tr bgcolor=\"#cc66ff\"><td>Data Backedup:</td><td>" . $mainrow["databackuped"] . "</td></tr>\n";
echo "<tr bgcolor=\"lightgreen\"><td>BIOS Date:</td><td>" . $mainrow["biosdate"] . "</td></tr>\n";
echo "<tr bgcolor=\"lightgreen\"><td>Firmware:</td><td>" . $mainrow["firmware"] . "</td></tr>\n";
echo "<tr bgcolor=\"lightgreen\"><td>CPU:</td><td>" . $mainrow["cpu"] . "</td></tr>\n";
echo "<tr bgcolor=\"lightgreen\"><td>RAM:</td><td>" . $mainrow["ram"] . "</td></tr>\n";
echo "<tr bgcolor=\"lightgreen\"><td>Total Diskspace:</td><td>" . $mainrow["diskspace"] . "</td></tr>\n";
echo "<tr bgcolor=\"lightgreen\"><td>Total NICs:</td><td>" . $mainrow["totalnics"] . "</td></tr>\n";
echo "<tr bgcolor=\"lightgreen\"><td>NIC Type:</td><td>" . $mainrow["nictype"] . "</td></tr>\n";
echo "<tr bgcolor=\"lightgreen\"><td>NIC In Use:</td><td>" . $mainrow["nicinuse"] . "</td></tr>\n";
echo "<tr bgcolor=\"lightgreen\"><td>Network Comments:</td><td>" . $mainrow["ncomments"] . "</td></tr>\n";
echo "</table>\n";

echo "<br><br>\n";

//Softare Data
echo "<table border=\"1\" align=\"center\">\n";
echo "<tr bgcolor=\"#99999\"><th colspan=\"2\" align=\"center\">" . $mainrow["servername"] . 
     " Installed Software</th></tr>\n";
echo "<tr  bgcolor=\"#ff9900\"><th>Software</th><th>Date Installed</th></tr>\n";

//SQL
$sql = "SELECT s.softname AS softname, sos.dateadd AS dateadd FROM softwareonserver AS sos " .
       "LEFT JOIN software AS s ON sos.softwareid=s.softwareid WHERE sos.serverid=\"" . $HTTP_GET_VARS["sname"] . "\"";

//Execute the query and put result in $result
$result = mysql_query($sql, $db) or die ("Unable to execute query for login: " . mysql_error());

$rowcount = "0";
while ($row = mysql_fetch_array($result)):
  if ($rowcount == "0"):
    echo "<tr><td>" . $row["softname"] . "</td><td>" . $row["dateadd"] . "</td></tr>\n";
  else:
    echo "<tr bgcolor=\"#ccccff\"><td>" . $row["softname"] . "</td><td>" . $row["dateadd"] . "</td></tr>\n";
  endif;

  if ($rowcount == "0"):
   $rowcount = "1";
  else:
   $rowcount = "0";
  endif;
endwhile;
echo "</table>\n";

echo "<br><br>\n";

//History data
echo "<table border=\"1\" align=\"center\">\n";
echo "<tr bgcolor=\"#99999\"><th colspan=\"6\" align=\"center\">" . $mainrow["servername"] . " History/Notes</th></tr>\n";

//SQL
$sql = "SELECT h.dateofnote AS dateofnote, h.historynote AS historynote, h.changemgtno AS changemgtno, " . 
       "u.username AS whoid FROM history AS h LEFT JOIN user AS u ON h.whoid=u.userid " . 
       "WHERE serverid=\"" . $HTTP_GET_VARS["sname"] . "\"";

//Execute the query and put result in $result
$result = mysql_query($sql, $db) or die ("Unable to execute query for login: " . mysql_error());

$rowcount = "0";
while ($row = mysql_fetch_array($result)):
  if ($rowcount == "0"):
    echo "<tr><td>Date:</td><td>" . $row["dateofnote"] . "</td><td>Change Management #:</td><td>" . 
         $row["changemgtno"] . "</td><td>Note By:</td><td>" . $row["whoid"] . "</td></tr>\n";
    echo "<tr><td>Note:</td><td colspan=\"5\">" . $row["historynote"] . "</td></tr>\n";
  else:
    echo "<tr bgcolor=\"#ccccff\"><td>Date:</td><td>" . $row["dateofnote"] . 
         "</td><td>Change Management #:</td><td>". $row["changemgtno"] . "</td>" .
         "<td>Note By:</td><td>" . $row["whoid"] . "</td></tr>\n";
    echo "<tr bgcolor=\"#ccccff\"><td>Note:</td><td colspan=\"5\">" . $row["historynote"] . "</td></tr>\n";
  endif;

  if ($rowcount == "0"):
   $rowcount = "1";
  else:
   $rowcount = "0";
  endif;
endwhile;
echo "</table>\n";

//Bottom of page
pbottom();

//Close Database Connection
mysql_close($db);
?>
