<?php
/*
  This page takes in a csv file and AD user name
  and passwords. Only when credentials is verified
  does the file get uploaded. Once uploaded the 
  display of the file is done by massverify.php
*/

include "./Parts/Main.php";
include "./authenticate.php";

session_start();

_page_top("Mass Upload", "");
?>
<h2>Mass Upload via CSV</h2>

<?php
if ($_FILES) {
	// Where the file is going to be placed 
	$target_path = "uploads/";
	$target_path = $target_path . basename( $_FILES['uploadedfile']['name']);

	if(login_user($_POST['user'],$_POST['pswd'])) {
		if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {
			$_SESSION['$target_file'] = $target_path;
			header("Location: massverify.php");
			unset ($_FILES);
			die();
		} else{
			echo "There was an error uploading the file, please try again!";
		}
	} else {
		echo "User ID and/or Password is incorrect and/or Not a member of DSLINUX!";
	}
} else {
	echo "<form action=\"mass.php\" enctype=\"multipart/form-data\" method=\"POST\" style=\"width:350px\">\n";
	echo "<fieldset><legend>Please specify a file for mass upload:</legend>\n";
	echo "<input type=\"file\" name=\"uploadedfile\" /><br />\n";
	echo "<br />\n";
	echo "<fieldset><legend>1DC Login REQUIRED:</legend>\n";
	echo "1DC User: <input type=\"text\" name=\"user\" /><br />\n";
	echo "Password: <input type=\"password\" name=\"pswd\" /></fieldset><br />\n";	
	echo "<input type=\"submit\" name=\"submit\" value=\"Upload\" /></fieldset></form>\n";
	
	echo "<p>An example file with headings, that need to stay in place, is <a href=\"./Example.csv\">HERE</a></p>\n";
}

_page_bot();
?>
