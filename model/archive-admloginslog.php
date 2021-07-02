<?php 

include_once "class/Sql.php";
include "function/utils.php";


//$_SESSION['id_log']   = $data['id_log'];
$id_log = $data['id_log'];
$iddb   = $data['iddb'];

$idcat	= $_SESSION['idcat'];



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


if ($player == 'OCI'):

	$result= $conn->sql( basename(__FILE__), 
						"UPDATE adm_logins_log ll1
                            SET archived = '*'
                          WHERE (USERNAME, OSUSER, MACHINE, PROGRAM, MODULE) 
                             in
                                (SELECT USERNAME, OSUSER, MACHINE, PROGRAM, MODULE 
                                   FROM adm_logins_log ll2 
                                  WHERE id_log=:ID_LOG
                                )",
						array( ":ID_LOG"=>$id_log)
						);

	

elseif ($player == 'SQLSRV'):

	$result= $conn->sql( basename(__FILE__), 
						"UPDATE adm_logins_log
                            SET archived = '*'
                           FROM adm_logins_log ll1
                     INNER JOIN
                       ( SELECT username, osuser, machine, program, module 
                           FROM adm_logins_log
                          WHERE ID_LOG = :ID_LOG
                        ) ll2
                             ON	ll1.USERNAME like ll2.USERNAME
                            AND CASE WHEN ll1.OSUSER  IS NULL THEN '%' else ll1.OSUSER   END LIKE CASE WHEN ll2.OSUSER  IS NULL THEN '%' else ll2.OSUSER  end
                            AND CASE WHEN ll1.MACHINE IS NULL THEN '%' else ll1.MACHINE  END LIKE CASE WHEN ll2.MACHINE IS NULL THEN '%' else ll2.MACHINE end
                            AND CASE WHEN ll1.PROGRAM IS NULL THEN '%' else ll1.PROGRAM  END LIKE CASE WHEN ll2.PROGRAM IS NULL THEN '%' else ll2.PROGRAM end
                            AND CASE WHEN ll1.MODULE  IS NULL THEN '%' else ll1.MODULE   END LIKE CASE WHEN ll2.MODULE  IS NULL THEN '%' else ll2.MODULE  end
                        ",
							array( ":ID_LOG"=>$id_log)
						);


elseif ($player == 'MYSQL'):

	$result= $conn->sql( basename(__FILE__), 
						"UPDATE adm_logins_log ll1
                            SET archived = '*'
                          WHERE (coalesce(USERNAME,''), coalesce(OSUSER,''), coalesce(MACHINE,''), coalesce(PROGRAM,''), coalesce(MODULE,'')) 
                             in
                                (SELECT coalesce(USERNAME,''), coalesce(OSUSER,''), coalesce(MACHINE,''), coalesce(PROGRAM,''), coalesce(MODULE,'') 
                                   FROM adm_logins_log ll2 
                                  WHERE id_log=:ID_LOG
                                )",
						array( ":ID_LOG"=>$id_log)
						);

endif;




header("Location: \admloginslog/$iddb/$idcat");

exit;	


?>