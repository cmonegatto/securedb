<?php 

include_once "class/Sql.php";
include "function/utils.php";

//$id_log  = $data['id_log'];

//$iddb  = $data['iddb'];
//$idcat = $data['idcat'];

//$iddb  = $_SESSION['iddb'];
//$idcat = $_SESSION['idcat'];


$iddb	= (!isset($_POST['iddb']))?$_SESSION['iddb']:$_POST['iddb'];
$idcat	= (!isset($_POST['idcat']))?$_SESSION['idcat']:$_POST['idcat'];


$conn=new Sql();


$result= $conn->sql( basename(__FILE__), "SELECT hostname, username, password, dbname, port, player
											FROM adm_databases
                       WHERE iddb = :IDDB",
										   array(":IDDB" => $iddb));                       

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



if ($player == 'OCI'):

    //tratativa da ordenação para Oracle caso parâmetro lockNoRule esteja ON ou OFF
    if (! $_SESSION['s_lockNoRule']):
        $orderby = "order by 4, 3 desc,2,1";
    else:
        $orderby = "order by 3 desc,2,1";
    endif;

    $result= $conn->sql( basename(__FILE__), 
                     "SELECT DISTINCT u.username, adm.admtrigger, k.tokill, l.admlogins
                        FROM dba_users u, 
                            (select username, 1 tokill from adm_logins_to_kill) k,
                            (select username, 1 admlogins from adm_logins) l,
                            ( SELECT u1.username, 1 admtrigger, 0 tokill
                                        FROM dba_users u1,
                                            ( SELECT grantee username, 1 as admtrigger, 0 as tokill
                                                FROM  dba_sys_privs 
                                                WHERE privilege = 'ADMINISTER DATABASE TRIGGER'
                                                UNION
                                            SELECT distinct grantee username, 1 as admtrigger, 0 as tokill
                                            FROM  dba_role_privs 
                                            WHERE granted_role in (select grantee from dba_sys_privs where privilege = 'ADMINISTER DATABASE TRIGGER')                                         
                                            ) x
                            
                            WHERE u1.account_status='OPEN'
                                and u1.username = x.username     
                                ) adm
                      WHERE u.account_status='OPEN'
                        and u.username = adm.username(+)
                        and u.username = k.username(+)
                        and u.username = l.username(+) " . $orderby
                        );

elseif ($player == 'SQLSRV'):

     //tratativa da ordenação para Oracle caso parâmetro lockNoRule esteja ON ou OFF
    if (! $_SESSION['s_lockNoRule']):
        $orderby = "ORDER by 4 desc,3 desc, 1";
    else:
        $orderby = "ORDER by 3 desc, 1";
    endif;


    $result= $conn->sql( basename(__FILE__),  
                    /*
                     "SELECT name USERNAME, 0 ADMTRIGGER, 0 TOKILL, 0 ADMLOGINS from sys.database_principals where type_desc='SQL_USER'
                      EXCEPT
                     (SELECT lower(username) username, 0 admtrigger, 0 tokill, 0 admlogins from adm_logins
                       UNION
                      SELECT lower(username) username, 0 admtrigger, 0 tokill, 0 admlogins from ADM_LOGINS_TO_KILL
                     )
                       UNION 
                      SELECT lower(username) username, 0 admtrigger, 1 tokill, 0 admlogins from ADM_LOGINS_TO_KILL
                       UNION
                      SELECT lower(username) username, 0 admtrigger, 0 tokill, 1 admlogins from adm_logins
                      EXCEPT
                      SELECT lower(username) username, 0 admtrigger, 0 tokill, 1 admlogins from ADM_LOGINS_TO_KILL
                     "
                        );
                    */

                     "SELECT * FROM 
                     (
                        -- SELECT name USERNAME, 0 ADMTRIGGER, 0 TOKILL, 0 ADMLOGINS from sys.database_principals where type_desc='SQL_USER'
                        SELECT name USERNAME, 0 ADMTRIGGER, 0 TOKILL, 0 ADMLOGINS from sys.server_principals where type_desc='SQL_LOGIN' and  is_disabled=0
                        EXCEPT
                        (SELECT lower(username) username, 0 admtrigger, 0 tokill, 0 admlogins from adm_logins
                        UNION
                        SELECT lower(username) username, 0 admtrigger, 0 tokill, 0 admlogins from ADM_LOGINS_TO_KILL
                        )
                        UNION 
                        SELECT lower(username) username, 0 admtrigger, 1 tokill, 0 admlogins from ADM_LOGINS_TO_KILL
                        UNION
                        SELECT lower(username) username, 0 admtrigger, 0 tokill, 1 admlogins from adm_logins
                        EXCEPT
                        SELECT lower(username) username, 0 admtrigger, 0 tokill, 1 admlogins from ADM_LOGINS_TO_KILL
                     ) as x
                      WHERE x.username is not null " . $orderby
                        );



endif;

/*
    tokill       : (1) O registro já está na tabela adm_logins_to_kill (ícone unlock) - (0) ainda não está (ícone lock)
    admlogins    : (1) Existe regra, portanto pode permitir o LOCK (0) Não há regra, não permitir LOCK, ou seja, enviar para ADM_LOGINS_TO_KILL
    admtrigger   : (1) Para banco ORACLE - Usuários com GRANT ADMINISTER DATABASE TRIGGER não podem ser cortados (0) Não tem esse grant, pode sofrer KILL SESSION
*/

foreach ($result as $key => $value) {
    
    $username = $result[$key]['USERNAME'];
    $user_name = str_replace('\\', '*', $result[$key]['USERNAME']);

    $msg = "";

    if ($result[$key]['TOKILL'] && !$result[$key]['ADMTRIGGER']):
        echo "<tr class='tokill'>";
        echo "<td style='text-align:center'><a href='\admlogins/lockuser/$user_name'><i class='fa fa-lock'></i></a></td>";
        $msg = 'KILL de sessão ON';
        //$msg = 'usuário HABILITADO para KILL SESSION';        


    elseif (!$result[$key]['TOKILL'] && !$result[$key]['ADMTRIGGER'] && $result[$key]['ADMLOGINS'] OR ($_SESSION['s_lockNoRule'] && !$result[$key]['ADMTRIGGER'])  ):
        echo "<tr class='notokill'>";
        echo "<td style='text-align:center'><a href='\admlogins/lockuser/$user_name'><i class='fa fa-unlock'></i></a></td>";
        $msg = 'KILL de sessão OFF';
        //$msg = 'usuário DESABILITADO para KILL SESSION';

    elseif (!$result[$key]['ADMLOGINS'] && !$result[$key]['ADMTRIGGER']):
        echo "<tr class='admtrigger'>";
        echo "<td></td>";		
        $msg = 'Não há regra definida para esse usuário';

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