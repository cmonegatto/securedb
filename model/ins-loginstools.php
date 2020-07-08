<?php 

session_start();

include "../class/Sql.php";
include "../function/utils.php";

$iddb	= $_SESSION['iddb'];
$idcat	= $_SESSION['idcat'];

$tools = $_POST["tools"];


$conn=new Sql();


$result= $conn->sql( basename(__FILE__), "SELECT hostname, username, password, dbname, port, player
											FROM adm_databases
										   WHERE iddb = $iddb");

$localhost	= $result[0]['hostname'];
$user	    = $result[0]['username'];
$password	= encrypt_decrypt('decrypt', $result[0]['password']);
$dbname		= $result[0]['dbname'];
$port		= $result[0]['port'];
$player		= $result[0]['player'];


$conn=new Sql($player, $localhost, $user, $password, $dbname, $port);


$result= $conn->sql( basename(__FILE__), 
					 "INSERT INTO adm_logins_tools values (:TOOLS)", array(":TOOLS"=> $tools)
				   );


    header("Location: \loginstools");
	exit;	


?>