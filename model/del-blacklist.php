<?php 

include "class/Sql.php";
include "function/utils.php";

$id = $data['id'];


$iddb	= (!isset($_POST['iddb']))?$_SESSION['iddb']:$_POST['iddb'];
$idcat	= (!isset($_POST['idcat']))?$_SESSION['idcat']:$_POST['idcat'];

$conn=new Sql();


$result= $conn->sql( basename(__FILE__), "SELECT hostname, username, password, dbname, port, player
											FROM adm_databases
                                           WHERE iddb = :IDDB",
                                           array(":IDDB" => $iddb));

$localhost	= $result[0]['hostname'];
$user	    = $result[0]['username'];
$password	= encrypt_decrypt('decrypt', $result[0]['password']);
$dbname		= $result[0]['dbname'];
$port		= $result[0]['port'];
$player		= $result[0]['player'];


$conn=new Sql($player, $localhost, $user, $password, $dbname, $port);

if (isset($_SESSION['msg']) && strlen($_SESSION['msg'])>0 ):
    header("Location: \admloginslog/0/0");
	exit;	
endif;

$datetime = date('d/m/Y H:i:s');

if ($player=='OCI'):
        // $datetimestr = "to_char('$datetime','dd-mm-yyyy hh24:mi:ss')";
        $datetimestr = 'SYSDATE';
elseif ($player == 'SQLSRV'):
        // $datetimestr = "format('$datetime','dd/MM/yyyy HH:mm:ss')";
        $datetimestr = 'GETDATE()';
elseif ($player == 'MYSQL'):
        // $datetimestr = "DATETIME('$datetime','dd/MM/yyyy HH:mm:ss')";
        $datetimestr = 'NOW()';
endif;


$loginname = strtoupper($_SESSION['s_login']);


$result= $conn->sql( basename(__FILE__),     
    "INSERT INTO ADM_LOGINS_AUD ( ACTION, ACTION_BY, ACTION_DATE, USERNAME, OSUSER, MACHINE )
     SELECT 'BLACK-NO', '$loginname', $datetimestr, USERNAME, OSUSER, MACHINE
       FROM adm_logins_locked
      WHERE id=:ID", array(":ID"=> $id)
    );

$result= $conn->sql(basename(__FILE__), 
                    "DELETE FROM adm_logins_locked WHERE id=:ID", array(":ID"=> $id)
                   );






header("Location: \blacklist");
exit;	
                
?>                