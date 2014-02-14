<?php
/*
This will be used as a online scipt to create a list 
of user add commands
*/

include "./Parts/Main.php";

_page_top("Create UserAdd", "");

function Random_Password($length) { 
    srand(date("U")); 
    $possible_charactors = "abcdefghijklmnopqrstuvwxyz1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ"; 
    $string = ""; 
    while(strlen($string)<$length) { 
        $string .= substr($possible_charactors, rand()%(strlen($possible_charactors)),1); 
    } 
    return($string); 
} 

if (!isset($_GET['create'])) {
?>
<h2>Create UserAdd Commands</h2>
<p>Fill in the boxes and hit create. The resulting page will be a list of useradd commands you can then cut-n-paste
into a ssh window to create a new user.</p>

<form action="./create_user.php">
  <table>
       <tr><td colspan="4"><b>Create User</b></td></tr>
       <tr>
	      <td>Name<textarea cols="30" rows="5" name="U_name"></textarea></td>
		  <td>LAN ID<textarea cols="15" rows="5" name="U_lanid"></textarea></td>
		  <td>BN #<textarea cols="10" rows="5" name="U_bn_no"></textarea></td>
	   </tr>
	   <tr><td><input type="checkbox" name="passwd_reset">Just Reset Password for user list.</td></tr>
	   <tr><td><input type="submit" name="create" value="Create"></td></tr>
</table></form>

<?php
} elseif (isset($_GET['passwd_reset'])) {
	$lanids = preg_split( "/[\r\n]+|,/", trim($_GET['U_lanid']) );
		
	$i=0;
	foreach ($lanids as $lanid ){
		$lanid_arr[$i] = $lanid;
		$i++;
	}
		
	echo "<code>\n";
		
	$i = 0;
	while ($i < count($lanid_arr)):
		echo "echo " . strtolower($lanid_arr[$i]) . ":" . Random_Password(9) . 
			"|chpasswd;passwd -e " . strtolower($lanid_arr[$i]) . ";faillog -u ". strtolower($lanid_arr[$i]) . 
			" -r<br/>\n";
		$i++;
	endwhile;
		
	echo "</code>\n";		
} else { 
	if (isset($_GET['U_name'])){
		$names = preg_split( "/[\r\n]+|,/", trim($_GET['U_name']) );
		$lanids = preg_split( "/[\r\n]+|,/", trim($_GET['U_lanid']) );
		$bn_nos = preg_split( "/[\r\n]+|,/", trim($_GET['U_bn_no']) );

		$i=0;
		foreach ($names as $name ){
			$name_arr[$i] = $name;
			$i++;
		}
		
		$i=0;
		foreach ($lanids as $lanid ){
			$lanid_arr[$i] = $lanid;
			$i++;
		}
		
		$i=0;
		foreach ($bn_nos as $bn_no ){
			$bn_no_arr[$i] = $bn_no;
			$i++;
		}
		
		echo "<code>\n";
		
		$i = 0;
		while ($i < count($name_arr)):
			echo "useradd -u " . $bn_no_arr[$i] . " -g users -m -c \"" . ucwords(strtolower($name_arr[$i])) . "\" " . strtolower($lanid_arr[$i]) . ";echo " . strtolower($lanid_arr[$i]) . ":" . Random_Password(9) . "|chpasswd;passwd -e " . strtolower($lanid_arr[$i]) . "<br/>\n";
			$i++;
		endwhile;
		
		echo "</code>\n";		
    }
}
_page_bot();
?>
