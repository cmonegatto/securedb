<?php 


include "class/Sql.php";


$iduser = $data['iduser'];

$str = 'CAedfh%120';
$pwd = str_shuffle($str);


$conn=new Sql();

$result= $conn->sql( basename(__FILE__),
					"UPDATE adm_users 
					    SET password = md5(:SENHA)
					  WHERE iduser = :IDUSER",
                      array(":SENHA"=> $pwd,
                            ":IDUSER"=> $iduser)
					);
	 
$_SESSION['msg'] = 'A senha do usuário foi reinicializada para: ' . $pwd;

header("Location: /users");
exit;


?>