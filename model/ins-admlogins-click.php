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

$begindate	= date("Y-m-d", strtotime("now")) . ' ' . date("H:i", strtotime("now"));
$loginname  = strtoupper($_SESSION['s_login']);


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
                            --and l.freetools  = ll.program
                            --and instr(l.freetools, '%') =0",
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
                        --and isnull(l.freetools,'')  = isnull(ll.program,'')
                        --and charindex('%', l.freetools)=0",
                        array(":ID_LOG"=> $id_log)
                        );


elseif ($player == 'MYSQL'):

    $result= $conn->sql( basename(__FILE__), 
                        "SELECT COUNT(*) as QTD
                        FROM adm_logins l,
                        (select USERNAME, OSUSER, MACHINE, PROGRAM, MODULE from adm_logins_log where id_log=:ID_LOG) ll
                        WHERE l.username = ll.username
                        and if(isnull(l.osuser),'', l.osuser)     = if(isnull(ll.osuser),'', ll.osuser)
                        and if(isnull(l.machine),'', l.machine)    = if(isnull(ll.machine),'', ll.machine)
                        -- and isnull(l.freetools,'')  = isnull(ll.program,'')
                        and instr('%', l.freetools)=0",
                        array(":ID_LOG"=> $id_log)
                        );

endif;



if ($result[0]['QTD'] >0) :

    if ($player == 'OCI'):

        $result= $conn->sql( basename(__FILE__), 
                            "UPDATE adm_logins
                                SET freetools = freetools || '; ' || (select program from adm_logins_log where  id_log=:ID_LOG),
                                last_updated_by = :LAST_UPDATED_BY, last_updated_date = sysdate
                              WHERE (username, osuser, machine) in (select username, osuser, machine from adm_logins_log where  id_log=:ID_LOG)",
                            array(":ID_LOG"=> $id_log, ":LAST_UPDATED_BY" => $loginname)
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
                            SET freetools = freetools + '; ' + ll.program,
                            last_updated_by = :LAST_UPDATED_BY, last_updated_date = GETDATE()    
                            FROM (
                                SELECT username, osuser, machine, program
                                FROM adm_logins_log
                                where id_log=:ID_LOG) as ll
                            WHERE isnull(adm_logins.username,'') = isnull(ll.username,'')
                            and isnull(adm_logins.osuser,'')   = isnull(ll.osuser,'')
                            and isnull(adm_logins.machine,'')  = isnull(ll.machine,'')",
                            array(":ID_LOG"=> $id_log, ":LAST_UPDATED_BY" => $loginname)
                            );


    elseif ($player == 'MYSQL'):
        $result= $conn->sql( basename(__FILE__), 
                            /*
                            "UPDATE adm_logins 
                            SET freetools = freetools + '; ' + ll.program,
                            last_updated_by = :LAST_UPDATED_BY, last_updated_date = NOW()    
                            FROM (
                                SELECT username, osuser, machine, program
                                FROM adm_logins_log
                                where id_log=:ID_LOG) as ll
                            WHERE if(isnull(adm_logins.username),'', adm_logins.username)    = if(isnull(ll.username),'', ll.username)
                            and if(isnull(adm_logins.osuser),'', adm_logins.osuser)          = if(isnull(ll.osuser),'', ll.osuser)
                            and if(isnull(adm_logins.machine),'', adm_logins.machine)        = if(isnull(ll.machine),'', ll.machine)",
                            array(":ID_LOG"=> $id_log, ":LAST_UPDATED_BY" => $loginname)
                            );
                            */
                            
                            "UPDATE adm_logins l
                            JOIN adm_logins_log ll
                            on if(isnull(l.username),'', l.username)    = if(isnull(ll.username),'', ll.username)
                                and if(isnull(l.osuser),'', l.osuser)   = if(isnull(ll.osuser),'', ll.osuser)
                                and if(isnull(l.machine),'', l.machine) = if(isnull(ll.machine),'', ll.machine)    
                            SET freetools = concat(freetools, '; ', (select program from adm_logins_log where  id_log=:ID_LOG)),
                            last_updated_by = :LAST_UPDATED_BY, last_updated_date = NOW()
                            where ll.id_log=:ID_LOG",
                            array(":ID_LOG"=> $id_log, ":LAST_UPDATED_BY" => $loginname)
                        );

                        /* Trecho para medir o tamano do campo se estoura ou não... usar futuramente
                        select length( concat( if(isnull(ll.program),'',ll.program), if(isnull(l.freetools),'',l.freetools))  )
                        from adm_logins_log ll 
                        join adm_logins l
                          on if(isnull(l.username),'', l.username)    = if(isnull(ll.username),'', ll.username)
                              and if(isnull(l.osuser),'', l.osuser)   = if(isnull(ll.osuser),'', ll.osuser)
                              and if(isnull(l.machine),'', l.machine) = if(isnull(ll.machine),'', ll.machine)
                       where ll.id_log=846;
                       */                        
    endif;


else:

    if ($player == 'OCI'):

        $result= $conn->sql( basename(__FILE__), 
                            "INSERT INTO adm_logins (username, osuser, machine, begin_date, freetools, created_by, created_date)
                             SELECT username, osuser, machine, sysdate, program, :CREATED_BY, sysdate
                               FROM adm_logins_log
                              WHERE id_log = :ID_LOG",
                            array(":ID_LOG"=> $id_log, ":CREATED_BY" => $loginname)
                        );
    


    elseif ($player == 'SQLSRV'):

        $result= $conn->sql( basename(__FILE__), 
                            "INSERT INTO adm_logins (username, osuser, machine, begin_date, freetools, created_by, created_date)
                             SELECT username, osuser, machine, getdate(), program, :CREATED_BY, GETDATE()
                               FROM adm_logins_log
                              WHERE id_log = :ID_LOG",
                            array(":ID_LOG"=> $id_log, ":CREATED_BY" => $loginname)
                        );
       


    elseif ($player == 'MYSQL'):

        $result= $conn->sql( basename(__FILE__), 
                            "INSERT INTO adm_logins (username, osuser, machine, begin_date, freetools, created_by, created_date)
                                SELECT username, osuser, machine, NOW(), program, :CREATED_BY, NOW()
                                FROM adm_logins_log
                                WHERE id_log = :ID_LOG",
                            array(":ID_LOG"=> $id_log, ":CREATED_BY" => $loginname)
                        );


    endif;



endif;


    header("Location: \admloginslog/$iddb/$idcat");
	exit;	


?>