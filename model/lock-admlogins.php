<?php 

include "class/Sql.php";
include "function/utils.php";


$user_name = str_replace('*', '\\', $data["username"]);

$iddb	= (!isset($_POST['iddb']))?$_SESSION['iddb']:$_POST['iddb'];
$idcat	= (!isset($_POST['idcat']))?$_SESSION['idcat']:$_POST['idcat'];

$conn=new Sql();

$result= $conn->sql( basename(__FILE__), "SELECT hostname, username, password, dbname, port, player
											FROM adm_databases
                                           WHERE iddb = :IDDB",
                                           array(":IDDB" => $iddb));
                                           

$localhost	= $result[0]['hostname'];
$user	    = $result[0]['username'];
$password	= encrypt_decrypt('decrypt', $result[0]['password']);
$dbname		= $result[0]['dbname'];
$port		= $result[0]['port'];
$player		= $result[0]['player'];


$conn=new Sql($player, $localhost, $user, $password, $dbname, $port);

if (isset($_SESSION['msg']) && strlen($_SESSION['msg'])>0 ):
    header("Location: \admloginslog/0/0");
	exit;	
endif;

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
$user_name = strtoupper($user_name);

$result= $conn->sql(basename(__FILE__), 
					"SELECT count(*) as QTD FROM adm_logins_to_kill WHERE username = :USERNAME", array(":USERNAME" => $user_name));

if ($result[0]['QTD']>0):
    $result= $conn->sql(basename(__FILE__), 
                        "DELETE FROM adm_logins_to_kill WHERE username = :USERNAME", array(":USERNAME" => $user_name));

    $result= $conn->sql(basename(__FILE__), 
                        "INSERT INTO ADM_LOGINS_AUD ( ACTION, ACTION_BY, ACTION_DATE, USERNAME )
                              VALUES ('KILL-NOT', '$loginname', $datetimestr, :USERNAME)", array(":USERNAME" => $user_name));

                        
else:
    $result= $conn->sql(basename(__FILE__), 
                        "INSERT INTO adm_logins_to_kill values (:USERNAME)", array(":USERNAME" => $user_name));

    $result= $conn->sql(basename(__FILE__), 
                        "INSERT INTO ADM_LOGINS_AUD ( ACTION, ACTION_BY, ACTION_DATE, USERNAME )
                              VALUES ('KILL-YES', '$loginname', $datetimestr, :USERNAME)", array(":USERNAME" => $user_name));

endif;


/*
$result= $conn->sql(basename(__FILE__),                    
                    "SELECT count(*) as QTD 
                       FROM adm_logins_to_kill 
                      WHERE username = (select username from adm_logins_log where id_log = :ID_LOG)", 
                     array(":ID_LOG" => $id_log));
                  


if ($result[0]['QTD']>0):
    $result= $conn->sql(basename(__FILE__),
                        "DELETE FROM adm_logins_to_kill 
                          WHERE username = (select username from adm_logins_log where id_log = :ID_LOG)",
                          array(":ID_LOG" => $id_log));

else:
    $result= $conn->sql(basename(__FILE__), 
                        "INSERT INTO adm_logins_to_kill 
                         SELECT username from adm_logins_log where id_log = :ID_LOG",
                          array(":ID_LOG" => $id_log));

endif;
*/

//header("Location: \admlogins");
header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
exit;	
              