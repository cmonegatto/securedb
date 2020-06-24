<?php 

include_once "class/Sql.php";
include "function/utils.php";

$iddb  = $data['iddb'];
$idcat = $data['idcat'];

$conn=new Sql();

$result= $conn->sql( basename(__FILE__), "SELECT hostname, username, password, dbname
											FROM adm_databases
										   WHERE iddb = $iddb");

$localhost = $result[0]['hostname'];
$username = $result[0]['username'];
$password = encrypt_decrypt('decrypt', $result[0]['password']);
$dbname = $result[0]['dbname'];

$conn=new Sql("oci", $localhost, $username, $password, $dbname, 1521); // trocar a porta pela tabela no cadastro.


if (isset($_SESSION['msg']) && strlen($_SESSION['msg'])>0 ):
    header("Location: \admlogins/0/0");
	exit;	
endif;



if (!$conn):
	$_SESSION['msg'] = "Database não disponível, verifique os dados de conexão";
	die();
endif;






if (isset($_SESSION['iddb']) && $_SESSION['iddb'] >0 ) :

	//$_SESSION['iddb'] = 0;

	$result= $conn->sql( basename(__FILE__), 
						"SELECT count(*) as qtd, username, osuser, machine, program, killed
							FROM adm_logins_log
						GROUP BY  username, osuser, machine, program, killed
						ORDER BY 1 desc"
						
						);
			  

	foreach ($result as $key => $value) {
		
		if ($result[$key]['USERNAME'] == 'ADMINISTRADOR'):
			echo 
			"<tr class='dif'>";
		else:
			"<tr>";
		endif;


		echo "<td><a href='\databases/update/1/1'><i class='fa fa-pencil'></i></a></td>";
		echo "<td><a href='\databases/delete/1'><i class='fa fa-trash'></i></a></td>";
  		echo "<td>".$result[$key]['QTD']."</td>";
		echo "<td>".$result[$key]['USERNAME']."</td>";
		echo "<td>".$result[$key]['OSUSER']."</td>";
		echo "<td>".$result[$key]['MACHINE']."</td>";
		echo "<td>".$result[$key]['PROGRAM']."</td>";
		echo "<td>".$result[$key]['KILLED']."</td>";
		echo "<td><a href='\databases/delete/1'><i class='fa fa-unlock'></i></a></td>


		</tr>";

	};

endif;

?>