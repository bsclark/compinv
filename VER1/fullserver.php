<?php
/*

*/

include './Parts/Main.php';

_page_top("Server Inventory","");

//---------------------------------------
//DB Connect
//---------------------------------------
  $db = mysql_connect("localhost","inventory","<db user pass>")
        or die("Unable to connect to server: " . mysql_error());

  mysql_select_db("db_inventory", $db) or die ("Unable to select database: " . mysql_error());
//---------------------------------------

$sql = "SELECT * FROM inv_main WHERE id=\"" . $_GET['sid'] . "\"";

$result = mysql_query($sql, $db) or die ("Unable to execute query for login: " . mysql_error());

echo "<table class=\"serverlisting\">\n";
  echo "<thead><tr>";
	echo "<th>Host Name</th>";
	echo "<th>Domain Name</th>";
	echo "<th>Physical</th>";
	echo "<th>System Type</th>";
	echo "<th>Maintanice Window</th>";
	echo "<th>Audit Level</th>";
	echo "<th>PCI Data</th>";
	echo "<th>Server Install Date</th>";
	echo "<th>Use</th>";
	echo "<th>OS</th>";
	echo "<th>Patch Level</th>";
	echo "<th>Last Patch</th>";
	echo "<th>LDAP?</th>";
	echo "<th>SAN?</th>";
	echo "<th>Last Fail Over Date</th>";
	echo "<th>Location</th>";
	echo "<th>Security Zone</th>";
	echo "<th>Rack</th>";
	echo "<th>IP Address</th>";
	echo "<th>Backup IP</th>";
	echo "<th>Other IPs</th>";
	echo "<th>Jumpserver</th>";
	echo "<th>Remote Console</th>";
	echo "<th>SN</th>";
	echo "<th>VCNAME</th>";
	echo "<th>Model</th>";
	echo "<th>Maintance Date</th>";
	echo "<th>EOL</th>";
	echo "<th># CPUs</th>";
	echo "<th>CPU Speed</th>";
	echo "<th>Memory</th>";
	echo "<th>Inventory Update</th>";
echo "</tr></thead><tbody>\n";

while ($row = mysql_fetch_array($result)) {
    echo "<tr>";
	echo "<td><a href=\"./fullserver.php?sid=" . $row["id"] . "\">" . $row["servername"] . "</a></td>";
	echo "<td>" . $row["domainname"] . "</td>";
	echo "<td>" . $row["physical"] . "</td>";
	echo "<td>" . $row["systemtype"] . "</td>";
	echo "<td>" . $row["mainwindow"] . "</td>";
	echo "<td>" . $row["audit"] . "</td>";
	echo "<td>" . $row["pcidata"] . "</td>";
	echo "<td>" . $row["serverinstalldate"] . "</td>";
	echo "<td>" . $row["serveruse"] . "</td>";
	echo "<td>" . $row["operatingsys"] . "</td>";
	echo "<td>" . $row["ospatchlvl"] . "</td>";
	echo "<td>" . $row["lastospatch"] . "</td>";
	echo "<td>" . $row["ldap"] . "</td>";
	echo "<td>" . $row["connectsan"] . "</td>";
	echo "<td>" . $row["failovertestdate"] . "</td>";
	echo "<td>" . $row["location"] . "</td>";
	echo "<td>" . $row["securityzone"] . "</td>";
	echo "<td>" . $row["rack"] . "</td>";
	echo "<td>" . $row["pub_ipaddress"] . "</td>";
	echo "<td>" . $row["backup_ipaddress"] . "</td>";
	echo "<td>" . $row["other_ipaddress"] . "</td>";
	echo "<td>" . $row["jumpserver"] . "</td>";
	echo "<td>" . $row["remoteconsole"] . "</td>";
	echo "<td>" . $row["serialnumber"] . "</td>";
	echo "<td>" . $row["vcname"] . "</td>";
	echo "<td>" . $row["model"] . "</td>";
	echo "<td>" . $row["maincontactdate"] . "</td>";
	echo "<td>" . $row["endofservicedate"] . "</td>";
	echo "<td>" . $row["cpucount"] . "</td>";
	echo "<td>" . $row["cpuspeed"] . "</td>";
	echo "<td>" . $row["memory"] . "</td>";
	echo "<td>" . $row["inventoryupdate"] . "</td>";
	echo "</tr>";
}

echo "</tbody></table><br />\n";  
  
_page_bot();

//---------------------------------------
//Free mysql result
//---------------------------------------
mysql_free_result($result);
//---------------------------------------

//---------------------------------------
//Close Database Connection
//---------------------------------------
mysql_close($db);
//---------------------------------------
?>
