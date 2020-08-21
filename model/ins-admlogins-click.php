<?php 

//session_start();

include "class/Sql.php";
include "function/utils.php";


$iddb	= $_SESSION['iddb'];
$idcat	= $_SESSION['idcat'];

$id_log = $data["id_log"];

/*
$username		    = str_replace('*', '\\', $data["username"]);
$osuser		        = str_replace('*', '\\', $data["osuser"]);
$machine		    = str_replace('*', '\\', $data["machine"]);
$freetools		    = str_replace('*', '\\', $data["program"]);
*/

$begindate		    = date("Y-m-d", strtotime("now")) . ' ' . date("H:i", strtotime("now"));


$conn=new Sql();


$result= $conn->sql( basename(__FILE__), "SELECT hostname, username, password, dbname, port, player
											FROM adm_databases
										   WHERE iddb = $iddb", array(":IDDB" => $iddb));

$localhost	= $result[0]['hostname'];
$user	    = $result[0]['username'];
$password	= encrypt_decrypt('decrypt', $result[0]['password']);
$dbname		= $result[0]['dbname'];
$port		= $result[0]['port'];
$player		= $result[0]['player'];


$conn=new Sql($player, $localhost, $user, $password, $dbname, $port);

if ($player == 'OCI'):

    $result= $conn->sql( basename(__FILE__), 
                        "SELECT COUNT(*) as QTD
                            FROM adm_logins l,
                        (select USERNAME, OSUSER, MACHINE, PROGRAM, MODULE from adm_logins_log where id_log=:ID_LOG) ll
                            WHERE l.username = ll.username
                            and l.osuser     = ll.osuser
                            and l.machine    = ll.machine
--                          and l.freetools  = ll.program",
                        array(":ID_LOG"=> $id_log)
                        );

elseif ($player == 'SQLSRV'):

    $result= $conn->sql( basename(__FILE__), 
                        "SELECT COUNT(*) as QTD
                        FROM adm_logins l,
                        (select USERNAME, OSUSER, MACHINE, PROGRAM, MODULE from adm_logins_log where id_log=:ID_LOG) ll
                        WHERE l.username = ll.username
                        and isnull(l.osuser,'')     = isnull(ll.osuser,'')
                        and isnull(l.machine,'')    = isnull(ll.machine,'')
--                      and isnull(l.freetools,'')  = isnull(ll.program,'')",
                        array(":ID_LOG"=> $id_log)
                        );

endif;


if ($result[0]['QTD'] >0) :

    if ($player == 'OCI'):

        $result= $conn->sql( basename(__FILE__), 
                            "UPDATE adm_logins
                                SET freetools = freetools || '; ' || (select program from adm_logins_log where  id_log=:ID_LOG)
                              WHERE (username, osuser, machine) in (select username, osuser, machine from adm_logins_log where  id_log=:ID_LOG)",
                            array(":ID_LOG"=> $id_log)
                        );
/*
                            "UPDATE adm_logins
                                SET freetools = freetools || '; ' || (select username, osuser, machine from adm_logins_log where  id_log=:ID_LOG)
                              WHERE (username, osuser, machine) in (select username, osuser, machine from adm_logins_log where  id_log=:ID_LOG)",
                            array(":ID_LOG"=> $id_log)
                            );
*/                            

    elseif ($player == 'SQLSRV'):
        $result= $conn->sql( basename(__FILE__), 
                            "UPDATE adm_logins 
                            SET freetools = freetools + '; ' + ll.program
                            FROM (
                                SELECT username, osuser, machine, program
                                FROM adm_logins_log
                                where id_log=:ID_LOG) as ll
                            WHERE isnull(adm_logins.username,'') = isnull(ll.username,'')
                            and isnull(adm_logins.osuser,'')   = isnull(ll.osuser,'')
                            and isnull(adm_logins.machine,'')  = isnull(ll.machine,'')",
                            array(":ID_LOG"=> $id_log)
                            );
        
    
    endif;


else:

    if ($player == 'OCI'):

        $result= $conn->sql( basename(__FILE__), 
                            "INSERT INTO adm_logins (username, osuser, machine, begin_date, freetools)
                             SELECT username, osuser, machine, sysdate, program
                               FROM adm_logins_log
                              WHERE id_log = :ID_LOG",
                            array(":ID_LOG"=> $id_log)
                        );
    


    elseif ($player == 'SQLSRV'):

        $result= $conn->sql( basename(__FILE__), 
                            "INSERT INTO adm_logins (username, osuser, machine, begin_date, freetools)
                             SELECT username, osuser, machine, getdate(), program
                               FROM adm_logins_log
                              WHERE id_log = :ID_LOG",
                            array(":ID_LOG"=> $id_log)
                        );
       

    endif;



endif;


    header("Location: \admloginslog/$iddb/$idcat");
	exit;	


?>