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
$dtregister = date('Y-m-d H:i:s');

/* Parâmetros INI 

hostname	: nome do servidor HOST que hospeda MYSQL
username	: usuário de acesso ao banco de dados
password	: senha
dbname		: nome do banco
db			: Drive do banco de dados que hospeda o metadados. "MYSQL"
lockNoRule	: (on/off) 
				- default off - só permite lock em usuários com regras definidas...
				- ON para aceitar locar usuario do banco mesmo sem nenhuma regra
				- OFF - só permite lock em usuários com regras definidas...
*/

	$_SESSION['s_hostname'] = $ini['database']['hostname'];
	$_SESSION['s_username'] = $ini['database']['username'];
	$_SESSION['s_password'] = encrypt_decrypt('decrypt', $ini['database']['password']);
	$_SESSION['s_dbname'] 	= $ini['database']['dbname'];
	$_SESSION['s_db'] 		= $ini['database']['db'];


	if (isset($ini['database']['lockNoRule'])):
		$_SESSION['s_lockNoRule'] = $ini['database']['lockNoRule'] == 1 ? 1 : 0;
	else:
		$_SESSION['s_lockNoRule'] = 0;
	endif;

	$expiration_time = $ini['session']['expiration_time'];

	if (! $expiration_time):
		$_SESSION['s_limite_session'] = 3600; // se não encontrar definição no INI padrão 1h
	else:
		$_SESSION['s_limite_session'] = $expiration_time;
	endif;


endif;	



$email = addslashes($_POST["email"]);
$senha = md5($_POST["senha"]);

$role = "";

$conn=new Sql();

if (isset($_SESSION['msg'])):
	header("Location: /");
endif;	



$result= $conn->sql( basename(__FILE__), 
					 "SELECT * FROM adm_users
					   WHERE email = :EMAIL 
					     AND password = :PASSWORD",
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
	$_SESSION['s_nameuser'] = $result[0]['name'];
	$_SESSION['s_celphone'] = $result[0]['celphone'];
	$_SESSION['s_login'] = $result[0]['login'];
	$_SESSION['s_time'] = time();
	
	if ($result[0]['superuser']):
		$role = "<span style='color:gray'> (Super)</span>";
	elseif ($result[0]['admin']):
		$role = "<span style='color:gray'> (Admin)</span>";

	endif;

	$_SESSION['s_shortnameuser'] = substr($result[0]['name'], 0, strpos($result[0]['name'] . ' ', ' ')) . $role; //primeiro nome

elseif (isset($result[0]['email']) && !$result[0]['status']):
	$_SESSION['msg'] ='Esse usuário está inativo!';
else :
	$_SESSION['msg'] ='Usuário ou senha inválida!';
endif;

$result= $conn->sql( basename(__FILE__), 
					 "INSERT INTO adm_login_activity (dtregister, user, username, ip, status) values ('$dtregister', :USER, :USERNAME, :IP, :STATUS)",
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
