<?php
/*
This will be used as a online scipt to create a list 
of user add commands
*/

include "./Parts/Main.php";

_page_top("Base", "");


?>
<h2>Create UserAdd Commands</h2>
<p>Fill in the boxes and hit create. The resulting page will be a list of useradd commands you can then cut-n-paste
into a ssh window to create a new user.</p>

<?php
if (!isset($_POST['submit'])) { // if page is not submitted to itself echo the form
?>

<form action="./create_user.php">
  <table>
       <tr><td colspan="4"><b>Search by</b></td></tr>
       <tr>
	      <td>Name<textarea cols="30" rows="5" name="U_name"></textarea></td>
		  <td>LAN ID<textarea cols="15" rows="5" name="U_lanid"></textarea></td>
		  <td>BN #<textarea cols="10" rows="5" name="U_bn_no"></textarea></td>
		  <td><input type="submit" name="create" value="Create"></td>
	   </tr>
</table></form>

<?php
} else { 


}


_page_bot();
?>
