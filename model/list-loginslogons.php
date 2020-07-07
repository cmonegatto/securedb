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


$result= $conn->sql( basename(__FILE__), 
                    "SELECT id_login, to_char(datetime, 'dd/mm/yy hh24:mi:ss') as date_time, username, osuser, machine, terminal, program, module
                        FROM adm_logins_logons ll
                        ORDER BY datetime DESC"
                    );
            


foreach ($result as $key => $value) {
    

    echo "<td>".$result[$key]['ID_LOGIN']."</td>";
    echo "<td>".$result[$key]['DATE_TIME']."</td>";
    echo "<td>".$result[$key]['USERNAME']."</td>";
    echo "<td>".$result[$key]['OSUSER']."</td>";
    echo "<td>".$result[$key]['MACHINE']."</td>";
    echo "<td>".$result[$key]['TERMINAL']."</td>";
    echo "<td>".$result[$key]['PROGRAM']."</td>";
    echo "<td>".$result[$key]['MODULE']."</td>";

    echo "</tr>";

};


?>