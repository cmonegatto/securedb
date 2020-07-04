<?php 

session_start();

include "../class/Sql.php";
include "../function/utils.php";

$idcat		= $_POST["idcat"];
$dbname		= $_POST["dbname"];
$hostname	= $_POST["hostname"];
$port		= $_POST["port"];
$player		= $_POST["player"];
$username	= $_POST["username"];
$password	= encrypt_decrypt('encrypt', $_POST["password"]);

$conn=new Sql();
/*
// checa duplicidade
$result= $conn->sql( basename(__FILE__), 
					 "SELECT count(*) as qtd
					    FROM adm_databases
					   WHERE idcat = :IDCAT
					     AND dbname = :DBNAME",
					  array(":IDCAT"=> $idcat,
					  		":DBNAME"=> $dbname
					  )
				   );

if ($result[0]["qtd"] > 0):
	$_SESSION['msg']="Registro já existe!";
endif;

if (isset($_SESSION['msg']) && strlen($_SESSION['msg'])>0 ):
    header("Location: \databases/insert");
	exit;	
endif;

*/

$result= $conn->sql( basename(__FILE__), 
					 "INSERT INTO adm_databases (idcat, dbname, hostname, port, player, username, password)
					  VALUES (:IDCAT, :DBNAME, :HOSTNAME, :PORT, :PLAYER, :USERNAME, :PASSWORD)",
					  array(":IDCAT"=> $idcat,
					  		":DBNAME"=> $dbname,
					  		":HOSTNAME"=> $hostname,
					  		":PORT"=> $port,
					  		":PLAYER"=> $player,
							":USERNAME"=> $username,
							":PASSWORD"=> $password
					  		)
				   );


    header("Location: \databases/insert");
	exit;	


?>