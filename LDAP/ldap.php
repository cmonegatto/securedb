<?php


$server = 'ldap.forumsys.com';
//$domain = '@forumsys.com';
$port = 389;


	//$ldap_dn = "uid=".$_POST["username"].$domain.",dc=example,dc=com";
	$ldap_dn = "uid=".$_POST["username"].",dc=example,dc=com";
	$ldap_password = $_POST["password"];

    echo $ldap_dn."</br>";


	$ldap_con = ldap_connect($server, $port);
	ldap_set_option($ldap_con, LDAP_OPT_PROTOCOL_VERSION, 3);
	
	if(ldap_bind($ldap_con, $ldap_dn, $ldap_password)) {

		echo "Bind successful!";
	  
	} else {
		echo "Invalid user/pass or other errors!";
	}
	
	
?>


