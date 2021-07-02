<?php 

session_start();

include "../class/Sql.php";
include "../function/utils.php";


$iddb	= $_SESSION['iddb'];
$idcat	= $_SESSION['idcat'];


$username		    = $_POST["username"];
$osuser		        = $_POST["osuser"];
$machine		    = $_POST["machine"];
$begindate		    = $_POST["begindate"];
$enddate		    = $_POST["enddate"];
$freetools		    = $_POST["freetools"];
$sessionsperuser	= $_POST["sessionsperuser"];
$initplsql		    = $_POST["initplsql"];
$comments		    = $_POST["comments"];
$loglogon		    = (isset($_POST["loglogon"])?'S':'N');
$trace		        = (isset($_POST["trace"])?'S':'N');
$cursorsharing		= (isset($_POST["cursorsharing"])?'S':'N');

$begindate  = str_replace("T", " ", $begindate);
$enddate    = str_replace("T", " ", $enddate);

$loginname = strtoupper($_SESSION['s_login']);

if (!($username.$osuser.$machine)):
     $_SESSION['msg'] = 'Preencha ao menos um dos três primeiros campos!';
    header("Location: \admlogins");
    exit;
endif;

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
						"INSERT INTO adm_logins (username, osuser, machine, begin_date, end_date, freetools, sessions_per_user, log_logon, trace, cursor_sharing, init_plsql, comments, created_by, created_date)
						VALUES (:USERNAME, :OSUSER, :MACHINE, to_date(:BEGIN_DATE, 'yyyy-mm-dd hh24:mi'), to_date(:END_DATE, 'yyyy-mm-dd hh24:mi'), :FREETOOLS, :SESSIONS_PER_USER, :LOG_LOGON, :TRACE, :CURSOR_SHARING, :INIT_PLSQL, :COMMENTS, :CREATED_BY, SYSDATE)",
						array(":USERNAME"=> $username,
								":OSUSER"=> $osuser,
								":MACHINE"=> $machine,
								":BEGIN_DATE"=> $begindate,
								":END_DATE"=> $enddate,
								":FREETOOLS"=> $freetools,
								":SESSIONS_PER_USER"=> $sessionsperuser,
								":LOG_LOGON"=> $loglogon,
								":TRACE"=> $trace,
								":CURSOR_SHARING"=> $cursorsharing,
								":INIT_PLSQL"=> $initplsql,
								":COMMENTS"=> $comments,
								":CREATED_BY"=> $loginname
								)
					);

elseif ($player == 'SQLSRV'):

	$xenddate = empty($enddate)? 1 : 0;


	$username		= (empty($username)) 		?NULL:$username;
	$osuser			= (empty($osuser)) 			?NULL:$osuser;
	$machine   		= (empty($machine))			?NULL:$machine;
	$freetools 		= (empty($freetools))		?NULL:$freetools;
	$sessionsperuser= (empty($sessionsperuser))	?NULL:$sessionsperuser;
	$initplsql		= (empty($initplsql))		?NULL:$initplsql;
	$comments		= (empty($comments))		?NULL:$comments;



	$result= $conn->sql( basename(__FILE__), 
						"INSERT INTO adm_logins (username, osuser, machine, begin_date, end_date, freetools, sessions_per_user, log_logon, trace, cursor_sharing, init_plsql, comments, created_by, created_date)
						VALUES (:USERNAME, :OSUSER, :MACHINE, CONVERT(DATETIME, :BEGIN_DATE, 20), 
						CASE 
							WHEN $xenddate=0 THEN CONVERT(DATETIME, :END_DATE, 20)
							ELSE NULL
						END
						, :FREETOOLS, :SESSIONS_PER_USER, :LOG_LOGON, :TRACE, :CURSOR_SHARING, :INIT_PLSQL, :COMMENTS, :CREATED_BY, GETDATE() )",
						array(":USERNAME"=> $username,
								":OSUSER"=> $osuser,
								":MACHINE"=> $machine,
								":BEGIN_DATE"=> $begindate,
								":END_DATE"=> $enddate,
								":FREETOOLS"=> $freetools,
								":SESSIONS_PER_USER"=> $sessionsperuser,
								":LOG_LOGON"=> $loglogon,
								":TRACE"=> $trace,
								":CURSOR_SHARING"=> $cursorsharing,
								":INIT_PLSQL"=> $initplsql,
								":COMMENTS"=> $comments,
								":CREATED_BY"=> $loginname
								)
					);


	/*
	$result= $conn->sql( basename(__FILE__), 
						"INSERT INTO adm_logins (username, osuser, machine, begin_date, end_date, freetools, sessions_per_user, log_logon, trace, cursor_sharing, init_plsql, comments)
						VALUES (:USERNAME, :OSUSER, :MACHINE, CONVERT(DATETIME, :BEGIN_DATE, 20), iif( $xenddate=0, CONVERT(DATETIME, :END_DATE, 20),NULL)
						, :FREETOOLS, :SESSIONS_PER_USER, :LOG_LOGON, :TRACE, :CURSOR_SHARING, :INIT_PLSQL, :COMMENTS)",
						array(":USERNAME"=> $username,
								":OSUSER"=> $osuser,
								":MACHINE"=> $machine,
								":BEGIN_DATE"=> $begindate,
								":END_DATE"=> $enddate,
								":FREETOOLS"=> $freetools,
								":SESSIONS_PER_USER"=> $sessionsperuser,
								":LOG_LOGON"=> $loglogon,
								":TRACE"=> $trace,
								":CURSOR_SHARING"=> $cursorsharing,
								":INIT_PLSQL"=> $initplsql,
								":COMMENTS"=> $comments
								)
					);
	*/



elseif ($player == 'MYSQL'):

	$xenddate = empty($enddate)? 1 : 0;


	$username		= (empty($username)) 		?NULL:$username;
	$osuser			= (empty($osuser)) 			?NULL:$osuser;
	$machine   		= (empty($machine))			?NULL:$machine;
	$freetools 		= (empty($freetools))		?NULL:$freetools;
	$sessionsperuser= (empty($sessionsperuser))	?NULL:$sessionsperuser;
	$initplsql		= (empty($initplsql))		?NULL:$initplsql;
	$comments		= (empty($comments))		?NULL:$comments;



	$result= $conn->sql( basename(__FILE__), 
						"INSERT INTO adm_logins (username, osuser, machine, begin_date, end_date, freetools, sessions_per_user, log_logon, trace, cursor_sharing, init_plsql, comments, created_by, created_date)
						VALUES (:USERNAME, :OSUSER, :MACHINE, STR_TO_DATE(:BEGIN_DATE, '%Y-%m-%d %H:%i:%s'), 
						CASE 
							WHEN $xenddate=0 THEN STR_TO_DATE(:END_DATE, '%Y-%m-%d %H:%i:%s')
							ELSE NULL
						END
						, :FREETOOLS, :SESSIONS_PER_USER, :LOG_LOGON, :TRACE, :CURSOR_SHARING, :INIT_PLSQL, :COMMENTS, :CREATED_BY, NOW() )",
						array(":USERNAME"=> $username,
								":OSUSER"=> $osuser,
								":MACHINE"=> $machine,
								":BEGIN_DATE"=> $begindate,
								":END_DATE"=> $enddate,
								":FREETOOLS"=> $freetools,
								":SESSIONS_PER_USER"=> $sessionsperuser,
								":LOG_LOGON"=> $loglogon,
								":TRACE"=> $trace,
								":CURSOR_SHARING"=> $cursorsharing,
								":INIT_PLSQL"=> $initplsql,
								":COMMENTS"=> $comments,
								":CREATED_BY"=> $loginname
								)
					);


endif;







    header("Location: \admlogins");
	exit;	


?>