<?php 

//session_start();

include "class/Sql.php";
include "function/utils.php";


$iddb	= $_SESSION['iddb'];
$idcat	= $_SESSION['idcat'];


$username		    = str_replace('*', '\\', $data["username"]);
$osuser		        = str_replace('*', '\\', $data["osuser"]);
$machine		    = str_replace('*', '\\', $data["machine"]);
$freetools		    = str_replace('*', '\\', $data["program"]);


$begindate		    = date("Y-m-d", strtotime("now")) . ' ' . date("H:i", strtotime("now"));

//echo "username: $username - osuser: $osuser - machine: $machine - freetools: $freetools - beginDate: $begindate";
//die();

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

$result= $conn->sql( basename(__FILE__), 
					 "SELECT COUNT(*) as qtd 
                        FROM adm_logins
                       WHERE username = :USERNAME 
                         AND osuser = :OSUSER
                         AND machine = :MACHINE",
					  array(":USERNAME"=> $username,
					  		":OSUSER"=> $osuser,
					  		":MACHINE"=> $machine,
                      ));


if ($result[0]['QTD'] >0) :

    $result= $conn->sql( basename(__FILE__), 
        "UPDATE adm_logins
            SET freetools = freetools || '; ' || :FREETOOLS
        WHERE username = :USERNAME 
        AND osuser = :OSUSER
        AND machine = :MACHINE",
        array(":USERNAME"=> $username,
                ":OSUSER"=> $osuser,
                ":MACHINE"=> $machine,
                ":FREETOOLS"=> $freetools
                )
    );

else:

    $result= $conn->sql( basename(__FILE__), 
                        "INSERT INTO adm_logins (username, osuser, machine, begin_date, freetools)
                        VALUES (:USERNAME, :OSUSER, :MACHINE, to_date(:BEGIN_DATE, 'yyyy-mm-dd hh24:mi'), :FREETOOLS)",
                        array(":USERNAME"=> $username,
                                ":OSUSER"=> $osuser,
                                ":MACHINE"=> $machine,
                                ":BEGIN_DATE"=> $begindate,
                                ":FREETOOLS"=> $freetools
                                )
                    );
endif;


    header("Location: \admloginslog/$iddb/$idcat");
	exit;	


?>