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



if ($player=='OCI'):
	$result= $conn->sql(basename(__FILE__), 
	"UPDATE  adm_logins 
		SET  FLAG = DECODE(FLAG,'*',NULL,'*')
	  WHERE id_login = :ID",
	array(":ID" => $id));
	
elseif ($player == 'SQLSRV'):
	$result= $conn->sql(basename(__FILE__), 
	"UPDATE  adm_logins 
		SET  FLAG = CASE WHEN FLAG = '*' THEN NULL ELSE '*' END 
	  WHERE id_login = :ID",
	array(":ID" => $id));

elseif ($player == 'MYSQL'):	
	$result= $conn->sql(basename(__FILE__), 
	"UPDATE  adm_logins 
		SET  FLAG = if(flag='*', NULL, '*')
	  WHERE id_login = :ID",
	array(":ID" => $id));
	
endif;







header("Location: \admlogins");
exit;	
                
?>                