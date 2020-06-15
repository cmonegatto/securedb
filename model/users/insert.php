<?php 

include_once '../conexao.php';

$idcia		= $_POST["idcia"];
$nome 		= $_POST["nome"];
$login		= $_POST["login"];
$email		= $_POST["email"];
$telefone 	= $_POST["telefone"];
$senha		= $_POST["senha"];

$status		= (isset($_POST["status"])) ? 1 : 0;
$admin		= (isset($_POST["admin"])) ? 1 : 0;
$superuser	= (isset($_POST["superuser"])) ? 1 : 0;


$sql = "INSERT INTO adm_users (idcia, name, login, password, email, celphone, status, admin, superuser)
             VALUES ('$idcia', '$nome', '$login', '$senha', '$email', $telefone, $status, $admin, $superuser)";

$queryUpdate = $link->query($sql);


if (!$queryUpdate):
	echo "erro no INSERT! <br>";
	echo $sql;
endif;


if( mysqli_affected_rows($link) >0):
    header("Location: \users/insert");
	exit;	
endif;


?>