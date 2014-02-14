<?php
function login_user ($user, $pswd) {
$ldaphost = "<fqdn of ldap host";
$ldapdomain = "<some ldap domain>";
$ldap_user_group = "<ldap group that can access>";

$ldap = ldap_connect($ldaphost, 389) or die("Could not connect to AD!");

ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);

// verify user and password
if($bd = @ldap_bind($ldap, "$user@$ldapdomain", $pswd)) {
	// valid
	// check for proper group
	$dn = "dc=1dc, dc=com";
	$attrs = array("memberof");
	$filter = "sAMAccountName=$user";
	$result = ldap_search($ldap, $dn, $filter, $attrs) or exit ("Unable to search LDAP server");
	$entries = ldap_get_entries($ldap, $result);
	ldap_unbind($ldap);

	// check group
	foreach($entries[0]['memberof'] as $ldapgrp) {
		if (strpos($ldapgrp, $ldap_user_group)) $access = 1;
	}
	
	if ($access !=0) {
		// session variables
		$_SESSION['user'] = $user;
		$_SESSION['pswd'] = $pswd;
		$_SESSION['access'] = $access;
		return true;
	} else {
		// deny
		return false;
	}
} else {
	// bad juju
	return false;
}
}
?>
