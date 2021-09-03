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

    
    $orderby = "order by 1";

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

    $orderby = "order by 1";

    $result= $conn->sql( basename(__FILE__),  
                     "SELECT DISTINCT u.name as USERNAME, 0 as ADMTRIGGER, k.TOKILL, l.ADMLOGINS
                        from sys.server_principals u
                        left outer join (select username, 1 tokill from adm_logins_to_kill) k
                             on u.name COLLATE Latin1_General_CI_AS= k.username
                        left outer join (select username, 1 admlogins from adm_logins) l
                             on u.name COLLATE Latin1_General_CI_AS= l.username
                       where is_disabled=0
                         and type <>'R'
                         and name NOT LIKE 'NT%SERVI%'
                         and type_desc not in ('WINDOWS_GROUP')
                         and name NOT LIKE '##%'" . $orderby
                        );


elseif ($player == 'MYSQL'):

    $orderby = "order by 1";

    $result= $conn->sql( basename(__FILE__),  
                    "SELECT DISTINCT u.user as USERNAME, 0 as ADMTRIGGER, k.TOKILL as TOKILL, l.ADMLOGINS as ADMLOGINS
                       from mysql.user u
                       left outer join (select username, 1 tokill from adm_logins_to_kill) k
                            on u.user = k.username
                       left outer join (select username, 1 admlogins from adm_logins) l
                            on u.user = l.username " . $orderby
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

    //if ($result[$key]['TOKILL'] && !$result[$key]['ADMTRIGGER']):
    if ($result[$key]['TOKILL'] ):
        echo "<tr class='tokill'>";
        echo "<td style='text-align:center'><a href='\admlogins/lockuser/$user_name'><i class='fa fa-lock'></i></a></td>";
        $msg = 'KILL de sessão ON';
        //$msg = 'usuário HABILITADO para KILL SESSION';        


    //elseif (!$result[$key]['TOKILL'] && !$result[$key]['ADMTRIGGER'] && $result[$key]['ADMLOGINS'] OR ($_SESSION['s_lockNoRule'] && !$result[$key]['ADMTRIGGER'])  ):
    elseif (!$result[$key]['TOKILL'] && $result[$key]['ADMLOGINS'] OR ($_SESSION['s_lockNoRule'] )  ):
        echo "<tr class='notokill'>";
        echo "<td style='text-align:center'><a href='\admlogins/lockuser/$user_name'><i class='fa fa-unlock'></i></a></td>";
        $msg = 'KILL de sessão OFF';
        //$msg = 'usuário DESABILITADO para KILL SESSION';

    //elseif (!$result[$key]['ADMLOGINS'] && !$result[$key]['ADMTRIGGER']):
    elseif (!$result[$key]['ADMLOGINS'] ):
        echo "<tr class='admtrigger'>";
        echo "<td></td>";		
        $msg = 'Não há regra definida para esse usuário';

    /*
    else:
        echo "<tr class='admtrigger'>";
        echo "<td></td>";		
        $msg = 'usuário com privilégio ADMINISTER DATABASE TRIGGER';
    */
    endif;
    
    
    if ($result[$key]['ADMLOGINS']):
        echo "<td style='text-align:center'><i td style='color:white'; class='fa fa-list'></i></td>";
    else:
        echo "<td></td>";
    endif;


    //echo "<td></td>";
    echo "<td>".$username."</td>";
    echo "<td>".$msg."</td>";

    
    echo "</tr>";

};


?>