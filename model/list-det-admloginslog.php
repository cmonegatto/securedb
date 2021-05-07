<?php 

include_once "class/Sql.php";
include "function/utils.php";

//$iddb  = $data['iddb'];
//$idcat = $data['idcat'];

//$iddb  = $_SESSION['iddb'];
//$idcat = $_SESSION['idcat'];


//$iddb	= (!isset($_POST['iddb']))?$_SESSION['iddb']:$_POST['iddb'];
$idcat	= (!isset($_POST['idcat']))?$_SESSION['idcat']:$_POST['idcat'];


//$_SESSION['id_log']   = $data['id_log'];
$id_log = $data['id_log'];
$iddb   = $data['iddb'];
$killed = $data['killed'];
$days   = $data['days'];

//se $days for 0 então pega tudo senão considera a quantidade de dias...
$days = ($days==0)?$days=2000:$days;


/*
$username   = $_SESSION['username'];
$osuser     = $_SESSION['osuser'];
$machine    = $_SESSION['machine'];
$program    = $_SESSION['program'];
$module     = $_SESSION['module'];
*/

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

if ($killed):
	$whereclause = " and killed is not null ";
else:
	$whereclause = " and 1=1 "	; // mostra todos registros 
endif;

if ($player == 'OCI'):


	$result= $conn->sql( basename(__FILE__), 
						"SELECT ID_LOG
							, to_char(datetime, 'dd-mm-yy hh24:mi:ss') datetime
							, datetime datetime2
							, ll1.USERNAME
							, ll1.OSUSER
							, ll1.MACHINE
							, ll1.TERMINAL
							, ll1.PROGRAM
							, ll1.MODULE
							, ll1.KILLED
						FROM adm_logins_log ll1,
						(select USERNAME, OSUSER, MACHINE, PROGRAM, MODULE from adm_logins_log ll2 where id_log=:ID_LOG) ll2
						WHERE ll1.username = ll2.username
						and ll1.osuser   = ll2.osuser
						and ll1.machine  = ll2.machine
						and ll1.program  = ll2.program
						and ll1.module   = ll2.module
						and datetime >=trunc(sysdate-$days) 
						/* . $whereclause . */
						ORDER BY datetime2 desc",
						array( ":ID_LOG"=>$id_log)
						);

	

elseif ($player == 'SQLSRV'):

	$result= $conn->sql( basename(__FILE__), 
						"SELECT ID_LOG
							  , format(ll1.DATETIME,'dd/MM/yyyy HH:mm:ss')  as DATETIME
							  , DATETIME as DATETIME2
							  , ll1.USERNAME
							  , ll1.OSUSER
							  , ll1.MACHINE
							  , ll1.TERMINAL
							  , ll1.PROGRAM
							  , ll1.MODULE
							  , ll1.KILLED
						   FROM adm_logins_log ll1,
						(select USERNAME, OSUSER, MACHINE, PROGRAM, MODULE from adm_logins_log ll2 where id_log=:ID_LOG) ll2
						  WHERE ll1.username = ll2.username
							and isnull(ll1.osuser,'')   = isnull(ll2.osuser,'')
							and isnull(ll1.machine,'')  = isnull(ll2.machine,'')
							and isnull(ll1.program,'')  = isnull(ll2.program,'')
							and isnull(ll1.module,'')   = isnull(ll2.module,'')
							and datetime >= cast(GETDATE()-$days as date)
							/* . $whereclause .*/
							ORDER BY datetime2 desc",
							array( ":ID_LOG"=>$id_log)
						);



elseif ($player == 'MYSQL'):

	$result= $conn->sql( basename(__FILE__), 
						"SELECT ID_LOG
								, DATETIME as DATETIME
								, DATETIME as DATETIME2
								, ll1.USERNAME
								, ll1.OSUSER
								, ll1.MACHINE
								, ll1.TERMINAL
								, ll1.PROGRAM
								, ll1.MODULE
								, ll1.KILLED
							FROM adm_logins_log ll1,
						(select USERNAME, OSUSER, MACHINE, PROGRAM, MODULE from adm_logins_log ll2 where id_log=:ID_LOG) ll2
							WHERE ll1.username = ll2.username
							and if(isnull(ll1.osuser),'', ll1.osuser)   = if(isnull(ll2.osuser),'', ll2.osuser)
							and if(isnull(ll1.machine),'', ll1.machine)  = if(isnull(ll2.machine),'', ll2.machine)
							and if(isnull(ll1.program),'', ll1.program)  = if(isnull(ll2.program),'', ll2.program)
							and if(isnull(ll1.module),'', ll1.module)    = if(isnull(ll2.module),'', ll2.module)
							and datetime >= date_sub(now(), interval $days day)
							/* . $whereclause .*/
							ORDER BY datetime2 desc",
							array( ":ID_LOG"=>$id_log)
						);						


endif;


foreach ($result as $key => $value) {
	
	echo "<td>".$result[$key]['ID_LOG']."</td>";
	echo "<td>".$result[$key]['DATETIME']."</td>";
	echo "<td>".$result[$key]['USERNAME']."</td>";
	echo "<td>".$result[$key]['OSUSER']."</td>";
	echo "<td>".$result[$key]['MACHINE']."</td>";
	echo "<td>".$result[$key]['TERMINAL']."</td>";
	echo "<td>".$result[$key]['PROGRAM']."</td>";
	echo "<td>".$result[$key]['MODULE']."</td>";

	if ($result[$key]['KILLED'] == '*'):
		echo "<td style='text-align:center'><i class='fa fa-user-times'></i></a></td>";
	elseif ($result[$key]['KILLED'] == '#'):
		echo "<td style='text-align:center'><i class='fa fa-clock-o'></i></a></td>";			
	else:
		echo "<td></td>";			
	endif;
		

	echo "</tr>";

};


?>