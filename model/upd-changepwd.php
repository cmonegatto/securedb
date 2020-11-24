<?php 

session_start();

include "../class/Sql.php";

$pwd      = md5($_POST["senha"]);
$pwd2     = md5($_POST["senha2"]);

$idcia      = $_SESSION['s_idcia'];
$iduser     = $_SESSION['s_iduser'];

if ( $pwd !== $pwd2):
	$_SESSION['msg'] = 'a senha digitada não confere, tente novamente!';
	header("Location: \changepwd");
	exit;
endif;


$conn=new Sql();

$result= $conn->sql( basename(__FILE__),
					"UPDATE adm_users 
					    SET password = :SENHA
					  WHERE iduser = :IDUSER",
                      array(":SENHA"=> $pwd,
                            ":IDUSER"=> $iduser)
					);
	 
$_SESSION['msg'] = 'Atualização de senha realizada com sucesso!';

header("Location: \changepwd");


?>