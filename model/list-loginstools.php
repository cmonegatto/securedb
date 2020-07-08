<?php 

include_once "class/Sql.php";
include "function/utils.php";

//$iddb  = $data['iddb'];
//$idcat = $data['idcat'];

//$iddb  = $_SESSION['iddb'];
//$idcat = $_SESSION['idcat'];


$iddb	= (!isset($_POST['iddb']))?$_SESSION['iddb']:$_POST['iddb'];
$idcat	= (!isset($_POST['idcat']))?$_SESSION['idcat']:$_POST['idcat'];


$conn=new Sql();


$result= $conn->sql( basename(__FILE__), "SELECT hostname, username, password, dbname, port, player
											FROM adm_databases
										   WHERE iddb = $iddb");

$localhost	= $result[0]['hostname'];
$username	= $result[0]['username'];
$password	= encrypt_decrypt('decrypt', $result[0]['password']);
$dbname		= $result[0]['dbname'];
$port		= $result[0]['port'];
$player		= $result[0]['player'];


$conn=new Sql($player, $localhost, $username, $password, $dbname, $port);

if (isset($_SESSION['msg']) && strlen($_SESSION['msg'])>0 ):
    header("Location: \admloginslog/0/0");
	exit;	
endif;

//if (isset($_SESSION['iddb']) && $_SESSION['iddb'] >0 ) :

	$result= $conn->sql( basename(__FILE__), 
						"SELECT *
						   FROM adm_logins_tools"
						);
			  



	foreach ($result as $key => $value) {
		
        $program  = $result[$key]['PROGRAM'];
        $id 	  = str_replace('%', '*', $program);
    
		echo "</tr>";
		echo "<td><a href='\loginstools/delete/$id'><i class='fa fa-trash'></i></a></td>";
		echo "<td>".$program."</td>";
		echo "</tr>";

	};

//endif;

?>