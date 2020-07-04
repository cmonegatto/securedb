<?php 

session_start();

include "../class/Sql.php";

$idcia		= $_POST["idcia"];
$nome 		= $_POST["nome"];
$login		= $_POST["login"];
$email		= $_POST["email"];
$telefone 	= $_POST["telefone"];
$senha		= md5($_POST["senha"]);

$status		= (isset($_POST["status"])) ? 1 : 0;
$admin		= (isset($_POST["admin"])) ? 1 : 0;
$superuser	= (isset($_POST["superuser"])) ? 1 : 0;


$conn=new Sql();

$result= $conn->sql( basename(__FILE__), 
					 "INSERT INTO adm_users (idcia, name, login, password, email, celphone, status, admin, superuser)
					  VALUES (:IDCIA, :NOME, :LOGIN, :SENHA, :EMAIL, :TELEFONE, :STATUS, :ADMIN, :SUPERUSER)",
					  array(":IDCIA"=> $idcia,
					  		":NOME"=> $nome,
							":LOGIN"=> $login,
							":SENHA"=> $senha,
							":EMAIL"=> $email,
							":TELEFONE"=> $telefone,
							":STATUS"=> $status,
							":ADMIN"=> $admin,
							":SUPERUSER"=> $superuser

					  )
				   );


header("Location: \users/insert");
exit;	


?>