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
										   WHERE iddb = :IDDB",
										   array(":IDDB" => $iddb));

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



if ($player == 'OCI'):
    
    $result= $conn->sql( basename(__FILE__), "SELECT ID, to_char(DATETIME, 'DD/MM/YYYY HH24:MI:SS') DATETIME, DATETIME DATETIME2, USERNAME, OSUSER, MACHINE 
                                                FROM adm_logins_locked
                                            ORDER BY datetime2 desc");

elseif ($player == 'SQLSRV'):
    $result= $conn->sql( basename(__FILE__), "SELECT ID, format(DATETIME,'dd/MM/yyyy HH:mm:ss') as DATETIME, DATETIME as DATETIME2, USERNAME, OSUSER, MACHINE 
                                                FROM adm_logins_locked
                                            ORDER BY datetime2 desc");

elseif ($player == 'MYSQL'):
    $result= $conn->sql( basename(__FILE__), "SELECT ID, date_format(DATETIME, '%Y/%m/%d %H:%i:%s') as DATETIME, DATETIME as DATETIME2, USERNAME, OSUSER, MACHINE 
                                                FROM adm_logins_locked
                                            ORDER BY datetime2 desc");

endif;


foreach ($result as $key => $value) {
	
	$id         = $result[$key]['ID'];
	$datetime   = $result[$key]['DATETIME'];
	$username   = $result[$key]['USERNAME'];
	$osuser     = $result[$key]['OSUSER'];
	$machine    = $result[$key]['MACHINE'];

    //echo "<td style='text-align:center'><i class='fa fa-unlock x'></i></a></td>";


	echo "</tr>";
	echo "<td style='text-align:center'><a href='\blacklist/delete/$id'><i class='fa fa-trash'></i></a></td>";
	echo "<td>".$datetime."</td>";
	echo "<td>".$username."</td>";
	echo "<td>".$osuser."</td>";
	echo "<td>".$machine."</td>";
	echo "</tr>";

};


?>