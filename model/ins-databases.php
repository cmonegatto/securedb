<?php 

session_start();

include "../class/Sql.php";
include "../function/utils.php";

$idcat		= $_POST["idcat"];
$dbname		= $_POST["dbname"];
$hostname	= $_POST["hostname"];
$username	= $_POST["username"];
$password	= encrypt_decrypt('encrypt', $_POST["password"]);

$conn=new Sql();

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



$result= $conn->sql( basename(__FILE__), 
					 "INSERT INTO adm_databases (idcat, dbname, hostname, username, password)
					  VALUES (:IDCAT, :DBNAME, :HOSTNAME, :USERNAME, :PASSWORD)",
					  array(":IDCAT"=> $idcat,
					  		":DBNAME"=> $dbname,
					  		":HOSTNAME"=> $hostname,
							":USERNAME"=> $username,
							":PASSWORD"=> $password
					  		)
				   );


    header("Location: \databases/insert");
	exit;	


?>