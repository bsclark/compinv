<?php
/*

*/
session_start();

include "./Parts/Main.php";
include "./authenticate.php";

if(login_user($_SESSION['user'],$_SESSION['pswd'])) {
	if($_SESSION['access'] == 1) {
		$db = mysql_connect("localhost","inventory","<passws>")
			or die("Unable to connect to server: " . mysql_error());
		
		mysql_select_db("db_inventory", $db) or die ("Unable to select database: " . mysql_error());

		echo "<pre>\n";
		print_r($_SESSION['createdsql']);
		echo "</pre>\n";
		
		foreach ($_SESSION['createdsql'] as $ins_line) {
			$result = mysql_query($ins_line, $db) or die ("Unable to execute query for login: " . mysql_error());
		}
		
		mysql_free_result($result);
		mysql_close($db);
		
		session_unset();
		session_destroy();
		
		header("Location: index.php");
		die();
	} else{
		echo "There was an error uploading to the database, please try again!";
	}
} else {
	echo "User ID and/or Password is incorrect and/or Not a member of <group name from ldap>!";
}
session_unset();
session_destroy();
?>
