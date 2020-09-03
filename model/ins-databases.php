<?php 

session_start();

include "../class/Sql.php";
include "../function/utils.php";

$idcat		= $_POST["idcat"];
$aliasdb	= $_POST["aliasdb"];
$dbname		= $_POST["dbname"];
$hostname	= $_POST["hostname"];
$port		= $_POST["port"];
$player		= $_POST["player"];
$username	= $_POST["username"];
$password	= encrypt_decrypt('encrypt', $_POST["password"]);

$conn=new Sql();


$result= $conn->sql( basename(__FILE__), 
					 "SELECT IDCIA
					    FROM ADM_CATEGORIES
					   WHERE idcat = :IDCAT",
					  array(":IDCAT"=> $idcat)
				   );

$idcia = $result[0]['IDCIA'];


$result= $conn->sql( basename(__FILE__), 
					 "INSERT INTO adm_databases (idcat, idcia, aliasdb, dbname, hostname, port, player, username, password)
					  VALUES (:IDCAT, :IDCIA, :ALIASDB, :DBNAME, :HOSTNAME, :PORT, :PLAYER, :USERNAME, :PASSWORD)",
					  array(":IDCAT"=> $idcat,
					  		":IDCIA"=> $idcia,
							":ALIASDB"=> $aliasdb,
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