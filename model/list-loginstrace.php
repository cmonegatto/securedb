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


if ($player=='OCI'):

    $result= $conn->sql( basename(__FILE__), 
                        "SELECT id_login, to_char(datetime, 'dd/mm/yy hh24:mi:ss') as date_time, username, osuser, machine, terminal, program, module, spid
                            FROM adm_logins_trace
                            ORDER BY datetime DESC"
                        );
            
           
elseif ($player=='SQLSRV'):

    // NÃO HÁ RESULTADOS DE TRACE PARA SQL SERVER - ESTÁ DESABILITADO, NÃO VAI ENTRAR NESSE IF
    $result= $conn->sql( basename(__FILE__), 
                        "SELECT ID_LOGIN, format(DATETIME,'dd/MM/yyyy HH:mm:ss') as DATE_TIME, USERNAME, OSUSER, MACHINE, TERMINAL, PROGRAM, MODULE, SPID
                            FROM adm_logins_trace
                            ORDER BY datetime DESC"
                        );

endif;            


foreach ($result as $key => $value) {

    echo "<td>".$result[$key]['ID_LOGIN']."</td>";
    echo "<td>".$result[$key]['DATE_TIME']."</td>";
    echo "<td>".$result[$key]['USERNAME']."</td>";
    echo "<td>".$result[$key]['OSUSER']."</td>";
    echo "<td>".$result[$key]['MACHINE']."</td>";
    echo "<td>".$result[$key]['TERMINAL']."</td>";
    echo "<td>".$result[$key]['PROGRAM']."</td>";
    echo "<td>".$result[$key]['MODULE']."</td>";
    echo "<td>".$result[$key]['SPID']."</td>";

    echo "</tr>";

};


?>