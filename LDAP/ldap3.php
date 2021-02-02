<?php

    $username = 'einstain';
    $password = 'password';
    $server = 'ldap.forumsys.com';
    $domain = '@forumsys.com';
    $port = 389;

    $connection = ldap_connect($server, $port);
    if (!$connection) {
        exit('Connection failed');
    }

    // Help talking to AD
    ldap_set_option($connection , LDAP_OPT_PROTOCOL_VERSION, 3);
    ldap_set_option($connection , LDAP_OPT_REFERRALS, 0);

    echo $username.$domain;

    //$bind = @ldap_bind($connection, $username.$domain, $password);
    $bind = @ldap_bind($connection, $username.$domain, $password);
    if (!$bind) {
        exit('Binding failed');
    }

    // This is where you can do your work
    echo 'Hello from LDAP';

    ldap_close($connection );

?>
