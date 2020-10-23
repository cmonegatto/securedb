<?php 

session_start();

include "../class/Sql.php";
include "../function/utils.php";

$iddb = $_SESSION['iddb'];

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
					    FROM adm_categories
					   WHERE idcat = :IDCAT",
					  array(":IDCAT"=> $idcat)
				   );

$idcia = $result[0]['IDCIA'];

$result= $conn->sql( basename(__FILE__), 
					 "UPDATE adm_databases
						 SET idcat = :IDCAT, idcia = :IDCIA, aliasdb = :ALIASDB, dbname = :DBNAME, hostname = :HOSTNAME, port = :PORT, player := :PLAYER, username = :USERNAME, password = :PASSWORD
					   WHERE iddb = :IDDB",
					  array(":IDDB"=> $iddb,
							":IDCAT"=> $idcat,
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


    header("Location: \databases");
	exit;	

?>