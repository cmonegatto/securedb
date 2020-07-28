<?php 

include "class/Sql.php";
include "function/utils.php";

$user_name = $data['username'];

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

$result= $conn->sql(basename(__FILE__), 
					"SELECT count(*) as qtd FROM adm_logins_to_kill WHERE username = :USERNAME", array(":USERNAME" => $user_name));

if ($result[0]['QTD']>0):
    $result= $conn->sql(basename(__FILE__), 
                        "DELETE FROM adm_logins_to_kill WHERE username = :USERNAME", array(":USERNAME" => $user_name));
else:
    $result= $conn->sql(basename(__FILE__), 
                        "INSERT INTO adm_logins_to_kill values (:USERNAME)", array(":USERNAME" => $user_name));

endif;

//header("Location: \admlogins");
header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
exit;	
              