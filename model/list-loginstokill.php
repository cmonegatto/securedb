<?php 

include_once "class/Sql.php";
include "function/utils.php";

//$iddb  = $data['iddb'];
//$idcat = $data['idcat'];

//$iddb  = $_SESSION['iddb'];
//$idcat = $_SESSION['idcat'];


$iddb	= (!isset($_POST['iddb']))?$_SESSION['iddb']:$_POST['iddb'];
$idcat	= (!isset($_POST['idcat']))?$_SESSION['idcat']:$_POST['idcat'];


$conn=new Sql();


$result= $conn->sql( basename(__FILE__), "SELECT hostname, username, password, dbname, port, player
											FROM adm_databases
										   WHERE iddb = $iddb");

$localhost	= $result[0]['hostname'];
$username	= $result[0]['username'];
$password	= encrypt_decrypt('decrypt', $result[0]['password']);
$dbname		= $result[0]['dbname'];
$port		= $result[0]['port'];
$player		= $result[0]['player'];


$conn=new Sql($player, $localhost, $username, $password, $dbname, $port);

if (isset($_SESSION['msg']) && strlen($_SESSION['msg'])>0 ):
    header("Location: \admloginslog/0/0");
	exit;	
endif;


$result= $conn->sql( basename(__FILE__), 
                    "SELECT u.username,0 as admtrigger, decode(k.username,null,0,1) as tokill
                       FROM dba_users u
                       LEFT JOIN adm_logins_to_kill k
                         ON u.username = k.username
                      WHERE account_status='OPEN'
                      MINUS
                    (
                     SELECT grantee as username, 0 as admtrigger, 0 as tokill
                       FROM dba_role_privs 
                      WHERE granted_role = 'DBA'
                      UNION
                     SELECT u.username, 0 as admtrigger, 0 as tokill
                       FROM dba_users u 
                      WHERE u.account_status='OPEN'
                        AND EXISTS
                            ( SELECT grantee 
                                FROM  dba_sys_privs 
                                WHERE privilege = 'ADMINISTER DATABASE TRIGGER'
                                  AND grantee = u.username
                            )
                    )
                    UNION
                    SELECT grantee as username, 1 as admtrigger, 0 as tokill
                      FROM dba_role_privs 
                     WHERE granted_role = 'DBA'
                     UNION
                    SELECT u.username, 1 as admtrigger, 0 as tokill
                      FROM dba_users u 
                     WHERE u.account_status='OPEN'
                       AND EXISTS
                           ( SELECT grantee 
                               FROM  dba_sys_privs 
                              WHERE privilege = 'ADMINISTER DATABASE TRIGGER'
                                AND grantee = u.username
                            )
                     ORDER BY username"
                    );
            


foreach ($result as $key => $value) {
    
    $username = $result[$key]['USERNAME'];
    $msg = "";

    if ($result[$key]['TOKILL'] && !$result[$key]['ADMTRIGGER']):
        echo "<tr class='tokill'>";
        echo "<td><a href='\admlogins/lockuser/$username'><i class='fa fa-lock'></i></a></td>";
        $msg = 'usuário habilitado para KILL SESSION';        

    elseif (!$result[$key]['TOKILL'] && !$result[$key]['ADMTRIGGER']):
        echo "<tr class='notokill'>";
        echo "<td><a href='\admlogins/lockuser/$username'><i class='fa fa-unlock'></i></a></td>";
        $msg = 'usuário DESABILITADO para kill session ';

    else:
        echo "<tr class='admtrigger'>";
        echo "<td></td>";		
        $msg = 'usuário com privilégio ADMINISTER DATABASE TRIGGER';

    endif;


    echo "<td>".$username."</td>";
    echo "<td>".$msg."</td>";

    
    echo "</tr>";

};


?>