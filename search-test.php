<?php

function login_user ($user, $pswd) {
$ldaphost = "<some server>";
$ldapdomain = "<ldap domain>";
$ldap_user_group = "<ldap group allowed to login>";

$ldap = ldap_connect($ldaphost, 389) or die( "Could not connect!" );

// verify user and password
if($bd = @ldap_bind($ad, "$user@$ldapdomain", $pswd)) {
	// valid
	// check for proper group
	$dn = "dc=<domain name>, dc=com";
	$filter = "(sAMAccountName=" . $user . ")";
	$result = ldap_search($ldap, $dn, $filter, $attrs) or exit ("Unable to search LDAP server");
	$entries = ldap_get_entries($ldap, $result);
	ldap_unbind($ldap);
	
	// check group
	foreach($entries[0]['memberof' as $ldapgrp) {
		if (strpos($ldapgrp, $ldap_user_group)) $access = 1;
	}
	
	if ($access !=0) {
		// session variables
		$_SESSION['user'] = $user;
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