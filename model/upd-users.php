<?php 

session_start();

include "../class/Sql.php";

$idcia		= $_POST["idcia"];
$nome 		= $_POST["nome"];
$login		= $_POST["login"];
$email		= $_POST["email"];
$telefone 	= $_POST["telefone"];
//$senha		= md5($_POST["senha"]);

$status		= (isset($_POST["status"])) ? 1 : 0;
$admin		= (isset($_POST["admin"])) ? 1 : 0;
$superuser	= (isset($_POST["superuser"])) ? 1 : 0;

$iduser = $_SESSION['iduser'];

$conn=new Sql();

$result= $conn->sql( basename(__FILE__),
					"UPDATE adm_users 
					    SET idcia = $idcia, name = '$nome', login = '$login', email = '$email', celphone = $telefone, status = $status, admin = $admin, superuser = $superuser
					  WHERE iduser=$iduser");

/* abaixo faz update na senha (precisa ver isso antes)
$result= $conn->sql( basename(__FILE__),
					"UPDATE adm_users 
					    SET idcia = $idcia, name = '$nome', login = '$login', email = '$email', celphone = $telefone, password = '$senha', status = $status, admin = $admin, superuser = $superuser
					  WHERE iduser=$iduser");
*/

header("Location: \users");


?>