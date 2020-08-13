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
					    SET idcia = :IDCIA, name = :NOME, login = :LOGIN, email = :EMAIL, celphone = :TELEFONE, status = :STATUS, admin = :ADMIN, superuser = :SUPERUSER
					  WHERE iduser = :IDUSER",
					  array(":IDUSER"=> $iduser,
					  		":IDCIA"=> $idcia,
					  		":NOME"=> $nome,
							":LOGIN"=> $login,
							":EMAIL"=> $email,
							":TELEFONE"=> $telefone,
							":STATUS"=> $status,
							":ADMIN"=> $admin,
							":SUPERUSER"=> $superuser
							)
					);
	 

header("Location: \users");


?>