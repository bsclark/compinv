<?php
/*

*/

include './Parts/Main.php';

_page_top("Server Inventory","");

//---------------------------------------
//DB Connect
//---------------------------------------
  $db = mysql_connect("localhost","inventory","<db user name>")
        or die("Unable to connect to server: " . mysql_error());

  mysql_select_db("db_inventory", $db) or die ("Unable to select database: " . mysql_error());
//---------------------------------------

$sql = "SELECT COUNT(*) FROM inv_main";
$result = mysql_query($sql, $db) or die ("Unable to execute query for login: " . mysql_error());
$servercount = mysql_fetch_array($result);

$sql = "SELECT COUNT(*) FROM inv_main WHERE physical=\"V\"";
$result = mysql_query($sql, $db) or die ("Unable to execute query for login: " . mysql_error());
$novirtual = mysql_fetch_array($result);

$sql = "SELECT COUNT(*) FROM inv_main WHERE physical=\"P\"";
$result = mysql_query($sql, $db) or die ("Unable to execute query for login: " . mysql_error());
$nophysical = mysql_fetch_array($result);

$sql = "SELECT COUNT(*) FROM inv_main WHERE physical IS NULL";
$result = mysql_query($sql, $db) or die ("Unable to execute query for login: " . mysql_error());
$unknown_physical = mysql_fetch_array($result);

$sql = "SELECT COUNT(*) FROM inv_main WHERE serverinstalldate BETWEEN \"" . date('Y-m-d', strtotime("-30 days")) . "\" AND \"" . date('Y-m-d') . "\"";
$result = mysql_query($sql, $db) or die ("Unable to execute query for login: " . mysql_error());
$nothritydays = mysql_fetch_array($result);

echo "<h3>D' Server List</h3>\n";
echo "<ul>\n";
echo "  <li>Total Servers: " . $servercount['COUNT(*)'] . "</li>\n";
echo "  <li>New Servers in last 30 days: " . $nothritydays['COUNT(*)'] . "</li>\n";
echo "  <li># Servers Virtual: " . $novirtual['COUNT(*)'] . "</li>\n";
echo "  <li># Servers Physical: " . $nophysical['COUNT(*)'] . "</li>\n";
echo "  <li># Servers P/V Unknown: " . $unknown_physical['COUNT(*)'] . "</li>\n";
echo "</ul>\n";

?>
<form action="./results.php">
  <table>
       <tr><td colspan="4"><b>Search by</b></td></tr>
       <tr>
	      <td>Host Name<input type="text" name="S_servername"></td>
		  <td>IP Address<input type="text" name="S_ipaddress"></td>
		  <td>Application<input type="text" name="S_appname"></td>
		  <td><input type="submit" name="search" value="Search"></td>
	   </tr>
</table></form>

<form action="./edit.php">
   <table class="serverlisting">
      <thead><tr>
         <!--<th>Edit</th>-->
	     <th>Host Name</th>
	     <th>Domain Name</th>
	     <th>Use</th>
	     <th>OS</th>
	     <th>Patch Level</th>
	     <th>Location</th>
	     <th>Security Zone</th>
	     <th>IP Address</th>
	     <th>Backup IP</th>
	     <th>Other IPs</th>
	     <th>Jumpserver</th>
	     <th>Inventory Update</th>
      </tr></thead><tbody>

<?php
$sql = "SELECT id, servername, domainname, serveruse, operatingsys, location, ospatchlvl, securityzone, pub_ipaddress, backup_ipaddress, other_ipaddress, jumpserver, inventoryupdate FROM inv_main ORDER BY servername";
$result = mysql_query($sql, $db) or die ("Unable to execute query for login: " . mysql_error());

$rowcolor = 0;

while ($row = mysql_fetch_array($result)) {
    if ($rowcolor == "1") {
	    echo "<tr class=\"alt\">";
		$rowcolor = 0;
	} else {
		echo "<tr>";
		$rowcolor = 1;
	}
	//echo "<td class=\"checkbox\"><input type=\"checkbox\" name=\"sid\" value=\"" . $row["id"] . "\"></td>";
	echo "<td><a href=\"./fullserver.php?sid=" . $row["id"] . "\">" . $row["servername"] . "</a></td>";
	echo "<td>" . $row["domainname"] . "</td>";
	echo "<td>" . $row["serveruse"] . "</td>";
	echo "<td>" . $row["operatingsys"] . "</td>";
	echo "<td>" . $row["ospatchlvl"] . "</td>";
	echo "<td>" . $row["location"] . "</td>";
	echo "<td>" . $row["securityzone"] . "</td>";
	echo "<td>" . $row["pub_ipaddress"] . "</td>";
	echo "<td>" . $row["backup_ipaddress"] . "</td>";
	echo "<td>" . $row["other_ipaddress"] . "</td>";
	echo "<td>" . $row["jumpserver"] . "</td>";
	echo "<td>" . $row["inventoryupdate"] . "</td>";
	echo "</tr>";
}

//echo "</tbody></table><br /><input type=\"submit\" value=\"Edit\"></form>\n";
echo "</tbody></table></form>\n";
  
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
