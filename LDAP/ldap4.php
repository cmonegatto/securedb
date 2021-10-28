<?php

//$username = 'newton';
//$password = 'password';
//$server = 'ldap';
//$domain = '@forumsys.com';
//$port = 389;

    $ldap_server = "ldap.forumsys.com";
    $dominio = "@forumsys.com"; //Dominio local ou global
    //$user = "newton"; //.$dominio;
    $user = "uid=newton, dc=example,dc=com";
    $ldap_porta = "389";
    $ldap_pass   = 'password';
    $ldapcon = ldap_connect($ldap_server, $ldap_porta) or die("Could not connect to LDAP server.");

    //ldap_set_option($ldapcon, LDAP_OPT_PROTOCOL_VERSION, 3);

    if ($ldapcon){

        // binding to ldap server
        //$ldapbind = ldap_bind($ldapconn, $user, $ldap_pass);

        $bind = ldap_bind($ldapcon, $user, $ldap_pass);

        // verify binding
        if ($bind) {
            echo "LDAP bind successful…";
        } else {
            echo "LDAP bind failed…";
        }

    }

?>