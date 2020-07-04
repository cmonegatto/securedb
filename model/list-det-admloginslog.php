<?php 

include_once "class/Sql.php";
include "function/utils.php";

//$iddb  = $data['iddb'];
//$idcat = $data['idcat'];

//$iddb  = $_SESSION['iddb'];
//$idcat = $_SESSION['idcat'];


$iddb	= (!isset($_POST['iddb']))?$_SESSION['iddb']:$_POST['iddb'];
$idcat	= (!isset($_POST['idcat']))?$_SESSION['idcat']:$_POST['idcat'];


$username   = $_SESSION['username'];
$osuser     = $_SESSION['osuser'];
$machine    = $_SESSION['machine'];
$program    = $_SESSION['program'];
$module     = $_SESSION['module'];


$conn=new Sql();


$result= $conn->sql( basename(__FILE__), "SELECT hostname, username, password, dbname, port, player
											FROM adm_databases
										   WHERE iddb = $iddb");

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




	$result= $conn->sql( basename(__FILE__), 
						"SELECT id, to_char(datetime, 'dd-mm-yy hh24:mi:ss') datetime, username, osuser, machine, terminal, program, module, killed								
						   FROM adm_logins_log
                          WHERE username = :USERNAME
                            AND osuser  like :OSUSER
                            AND machine like :MACHINE
                            AND program like :PROGRAM
                            AND module  like :MODULE
                          ORDER BY datetime desc",
                          array( ":USERNAME"=>$username,
                                 ":OSUSER"  =>$osuser,
                                 ":MACHINE" =>$machine,
                                 ":PROGRAM" =>$program,
                                 ":MODULE"  =>$module
                               )                          
						);
			  

	foreach ($result as $key => $value) {
		
  		echo "<td>".$result[$key]['ID']."</td>";
  		echo "<td>".$result[$key]['DATETIME']."</td>";
  		echo "<td>".$result[$key]['USERNAME']."</td>";
  		echo "<td>".$result[$key]['OSUSER']."</td>";
  		echo "<td>".$result[$key]['MACHINE']."</td>";
  		echo "<td>".$result[$key]['TERMINAL']."</td>";
  		echo "<td>".$result[$key]['PROGRAM']."</td>";
  		echo "<td>".$result[$key]['MODULE']."</td>";
  		echo "<td>".$result[$key]['KILLED']."</td>";
		

		/*
		if ($result[$key]['TO_KILL'] == "S"):
			echo "<td><a href='\databases/delete/1'><i class='fa fa-lock'></i></a></td>";
		else:
			echo "<td><a href='\databases/delete/1'><i class='fa fa-unlock'></i></a></td>";
		endif; */


		echo "</tr>";

	};

//endif;

?>