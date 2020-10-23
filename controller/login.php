<?php 

session_start();

include "../class/Sql.php";
include "../function/utils.php";



if (! file_exists("../config.ini")):
	//echo "O arquivo config.ini não foi encontrado!";
	$_SESSION['msg'] ='O arquivo de parâmetros não foi encontrado!';	
	header("Location: /");
else:

$ini = parse_ini_file('../config.ini', true);

/*
	$_SESSION['s_hostname'] = $ini['database']['hostname'];
	$_SESSION['s_username'] = encrypt_decrypt('decrypt', $ini['database']['username']);
	$_SESSION['s_password'] = encrypt_decrypt('decrypt', $ini['database']['password']);
	$_SESSION['s_dbname'] 	= encrypt_decrypt('decrypt', $ini['database']['dbname']);
	$_SESSION['s_db'] 		= $ini['database']['db'];
*/
	$_SESSION['s_hostname'] = $ini['database']['hostname'];
	$_SESSION['s_username'] = $ini['database']['username'];
	$_SESSION['s_password'] = $ini['database']['password'];
	$_SESSION['s_dbname'] 	= $ini['database']['dbname'];
	$_SESSION['s_db'] 		= $ini['database']['db'];
	
endif;	



$email = $_POST["email"];
$senha = md5($_POST["senha"]);

$role = "";

$conn=new Sql();

$result= $conn->sql( basename(__FILE__), 
					 "SELECT * FROM adm_users
					   WHERE email = :EMAIL and password = :PASSWORD",
					  array(":EMAIL"=> $email,
					  		":PASSWORD"=> $senha
					  )
				   );



if (isset($result[0]['email']) && $result[0]['status']): 
	$_SESSION['s_idcia'] = $result[0]['idcia'];
	$_SESSION['s_iduser'] = $result[0]['iduser'];
	$_SESSION['s_emailuser'] = $result[0]['email'];
	$_SESSION['s_admin'] = $result[0]['admin'];
	$_SESSION['s_superuser'] = $result[0]['superuser'];
	
	if ($result[0]['superuser']):
		$role = "<span style='color:gray'> (Super)</span>";
	elseif ($result[0]['admin']):
		$role = "<span style='color:gray'> (Admin)</span>";

	endif;

	$_SESSION['s_nameuser'] = substr($result[0]['name'], 0, strpos($result[0]['name'], ' ')) . $role; //primeiro nome

elseif (isset($result[0]['email']) && !$result[0]['status']):
	$_SESSION['msg'] ='Esse usuário está inativo!';
else :
	$_SESSION['msg'] ='Usuário ou senha inválida!';
endif;

$result= $conn->sql( basename(__FILE__), 
					 "INSERT INTO adm_login_activity (user, username, ip, status) values (:USER, :USERNAME, :IP, :STATUS)",
					  array( ":USER"=> $email,
					  		 ":USERNAME"=> getenv("USERNAME"),
							 ":IP"=> getenv("REMOTE_ADDR"),
							 ":STATUS"=> (isset($_SESSION['msg']))?$_SESSION['msg']:"success"
							)
				   );

//var_dump ($_SESSION);
//die();

header("Location: /");

?>
