<?php


    $ldap_server = "ldap.forumsys.com";
    $dominio = "@forumsys.com"; //Dominio local ou global
    //$user = "uid=newton, dc=example,dc=com";

    $user = "einstein"; //.$dominio;
    $user = "uid=$user, DC=EXAMPLE, DC=com";


    $ldap_porta = "389";
    $ldap_pass   = 'password';
    $ldapcon = ldap_connect($ldap_server, $ldap_porta) or die("Could not connect to LDAP server.");

    ldap_set_option($ldapcon, LDAP_OPT_PROTOCOL_VERSION, 3);

    echo $ldapcon;
    echo "<br/>";
    echo $user;
    echo "<br/>";
    echo $ldap_pass;
    echo "<br/>";
    echo "<br/>";

    if ($ldapcon){


        $bind = @ldap_bind($ldapcon, $user, $ldap_pass);

        if ($bind) {
            echo "LDAP bind successful…";
        } else {
            echo "LDAP bind failed…";
        }
        echo $bind;

    }


    echo "<br/>";
    echo "------------------------------------------------";
    echo "<br/>";



    $ldap_server = "adgoogle.phcnet.usp.br";
    $dominio = "";
    $user = "securedb".$dominio;
    //$user = "uid=$user, DC=HCFMUSP, DC=local";
    $ldap_porta = "389";
    $ldap_pass   = 'securedb@2021';
    $ldapcon = ldap_connect($ldap_server, $ldap_porta) or die("Could not connect to LDAP server.");

    ldap_set_option($ldapcon, LDAP_OPT_PROTOCOL_VERSION, 3);

    echo $ldapcon;
    echo "<br/>";
    echo $user;
    echo "<br/>";
    echo $ldap_pass;
    echo "<br/>";
    echo "<br/>";

    if ($ldapcon){

        $bind = @ldap_bind($ldapcon, $user, $ldap_pass);

        if ($bind) {
            echo "LDAP bind successful…";

                // $filter = "(cn=Albert Einstein)";
                // $filter = "(sn=freitas)";
                //$filter = "(samaccountname=marcelo.freitas)";
                $filter = "(samaccountname=Leandro.lana)";
                $result = ldap_search($ldapcon,"dc=HCFMUSP, dc=local",$filter) or exit("Unable to search");
                $entries = ldap_get_entries($ldapcon, $result);

                if (count($entries)>1):

                   print "<pre>";
                   //print_r ($entries);
                   echo($entries[0]['samaccountname'][0]) .'</br>';
                   echo($entries[0]['displayname'][0]) .'</br>';
                endif;


        } else {
            echo "LDAP bind failed…";
        }

    }

?>

