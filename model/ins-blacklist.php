<?php 

session_start();

include "../class/Sql.php";
include "../function/utils.php";


$iddb	= $_SESSION['iddb'];
$idcat	= $_SESSION['idcat'];

$username = $_POST["username"];
$osuser   = $_POST["osuser"];
$machine  = $_POST["machine"];


if (!($username.$osuser.$machine)):
    $_SESSION['msg'] = 'Preencha ao menos um dos três primeiros campos!';
   header("Location: \blacklist");
   exit;
endif;

$username   = !$username ? NULL : $username;
$osuser     = !$osuser   ? NULL : $osuser;
$machine    = !$machine  ? NULL : $machine;


$conn=new Sql();


$result= $conn->sql( basename(__FILE__), "SELECT hostname, username, password, dbname, port, player
											FROM adm_databases
										   WHERE iddb = :IDDB", array(":IDDB" => $iddb));


$localhost	= $result[0]['hostname'];
$user	    = $result[0]['username'];
$password	= encrypt_decrypt('decrypt', $result[0]['password']);
$dbname		= $result[0]['dbname'];
$port		= $result[0]['port'];
$player		= $result[0]['player'];


$conn=new Sql($player, $localhost, $user, $password, $dbname, $port);


$datetime = date('d/m/Y H:i:s');

if ($player=='OCI'):
        // $datetimestr = "to_char('$datetime','dd-mm-yyyy hh24:mi:ss')";
        $datetimestr = 'SYSDATE';
elseif ($player == 'SQLSRV'):
        // $datetimestr = "format('$datetime','dd/MM/yyyy HH:mm:ss')";
        $datetimestr = 'GETDATE()';
elseif ($player == 'MYSQL'):
        // $datetimestr = "DATETIME('$datetime','dd/MM/yyyy HH:mm:ss')";
        $datetimestr = 'NOW()';
endif;


$loginname = strtoupper($_SESSION['s_login']);


$result= $conn->sql( basename(__FILE__), 
    "INSERT INTO adm_logins_locked (datetime, username, osuser, machine) 
          VALUES ($datetimestr, :USERNAME, :OSUSER, :MACHINE)", array(":USERNAME"=> $username, ":OSUSER"=> $osuser, ":MACHINE"=> $machine)
    );

$result= $conn->sql( basename(__FILE__),     
    "INSERT INTO adm_logins_aud ( ACTION, ACTION_BY, ACTION_DATE, USERNAME, OSUSER, MACHINE )
    VALUES ('BLACK-YES', '$loginname', $datetimestr, :USERNAME, :OSUSER, :MACHINE)", array(":USERNAME"=> $username, ":OSUSER"=> $osuser, ":MACHINE"=> $machine)
    );



/*
if ($player == 'OCI'):


    $result= $conn->sql( basename(__FILE__), 
                        "INSERT INTO adm_logins_locked (datetime, username, osuser, machine) 
                                VALUES (sysdate, :USERNAME, :OSUSER, :MACHINE)", 
                        array(":USERNAME"=> $username, ":OSUSER"=> $osuser, ":MACHINE"=> $machine)
                    );

elseif ($player == 'SQLSRV'):

    $result= $conn->sql( basename(__FILE__), 
                        "INSERT INTO adm_logins_locked (datetime, username, osuser, machine) 
                                VALUES (getdate(), :USERNAME, :OSUSER, :MACHINE)", 
                        array(":USERNAME"=> $username, ":OSUSER"=> $osuser, ":MACHINE"=> $machine)
                    );


elseif ($player == 'MYSQL'):

    $result= $conn->sql( basename(__FILE__), 
                        "INSERT INTO adm_logins_locked (datetime, username, osuser, machine) 
                                VALUES (now(), :USERNAME, :OSUSER, :MACHINE)", 
                        array(":USERNAME"=> $username, ":OSUSER"=> $osuser, ":MACHINE"=> $machine)
                    );
                    
endif;
*/

header("Location: \blacklist");
exit;	


?>