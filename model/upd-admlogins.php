<?php 

session_start();

include "../class/Sql.php";
include "../function/utils.php";

$iddb	= $_SESSION['iddb'];
$idcat	= $_SESSION['idcat'];

$id_login           = $_SESSION['id_login'];

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

$loginname  = strtoupper($_SESSION['s_login']);


if (!($username.$osuser.$machine)):
     $_SESSION['msg'] = 'Preencha ao menos um dos três primeiros campos!';
    header("Location: \admlogins");
    exit;
endif;

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

if ($player == 'OCI'):

	$result= $conn->sql( basename(__FILE__), 
						"UPDATE adm_logins 
							SET username           = :USERNAME, 
								osuser             = :OSUSER, 
								machine            = :MACHINE, 
								begin_date         = to_date(:BEGIN_DATE, 'yyyy-mm-dd hh24:mi'),
								end_date           = to_date(:END_DATE, 'yyyy-mm-dd hh24:mi'),
								freetools          = :FREETOOLS, 
								sessions_per_user  = :SESSIONS_PER_USER, 
								log_logon          = :LOG_LOGON, 
								trace              = :TRACE, 
								cursor_sharing     = :CURSOR_SHARING, 
								init_plsql         = :INIT_PLSQL, 
								comments           = :COMMENTS,
								last_updated_by	   = :LAST_UPDATED_BY,
								last_updated_date  = sysdate

						WHERE id_login = $id_login",
						array(":USERNAME"         => $username,
								":OSUSER"           => $osuser,
								":MACHINE"          => $machine,
								":BEGIN_DATE"       => $begindate,
								":END_DATE"         => $enddate,
								":FREETOOLS"        => $freetools,
								":SESSIONS_PER_USER"=> $sessionsperuser,
								":LOG_LOGON"        => $loglogon,
								":TRACE"            => $trace,
								":CURSOR_SHARING"   => $cursorsharing,
								":INIT_PLSQL"       => $initplsql,
								":COMMENTS"         => $comments,
								":LAST_UPDATED_BY"  => $loginname
								)
					);

elseif ($player == 'SQLSRV'):


	$username		= (empty($username)) 		?NULL:$username;
	$osuser			= (empty($osuser)) 			?NULL:$osuser;
	$machine   		= (empty($machine))			?NULL:$machine;
	$freetools 		= (empty($freetools))		?NULL:$freetools;
	$sessionsperuser= (empty($sessionsperuser))	?NULL:$sessionsperuser;
	$initplsql		= (empty($initplsql))		?NULL:$initplsql;
	$comments		= (empty($comments))		?NULL:$comments;


	$xenddate = empty($enddate)? 1 : 0;


	$result= $conn->sql( basename(__FILE__), 
						"UPDATE adm_logins 
							SET username           = :USERNAME, 
								osuser             = :OSUSER, 
								machine            = :MACHINE, 
								begin_date		   = CONVERT(DATETIME, :BEGIN_DATE, 20),
							    end_date		   = CASE WHEN $xenddate=0 THEN CONVERT(DATETIME, :END_DATE, 20) ELSE NULL END,
								freetools          = :FREETOOLS, 
								sessions_per_user  = :SESSIONS_PER_USER, 
								log_logon          = :LOG_LOGON, 
								trace              = :TRACE, 
								cursor_sharing     = :CURSOR_SHARING, 
								init_plsql         = :INIT_PLSQL, 
								comments           = :COMMENTS,
								last_updated_by	   = :LAST_UPDATED_BY,
								last_updated_date  = GETDATE()

						WHERE id_login = $id_login",
						array(":USERNAME"         => $username,
								":OSUSER"           => $osuser,
								":MACHINE"          => $machine,
								":BEGIN_DATE"       => $begindate,
								":END_DATE"         => $enddate,
								":FREETOOLS"        => $freetools,
								":SESSIONS_PER_USER"=> $sessionsperuser,
								":LOG_LOGON"        => $loglogon,
								":TRACE"            => $trace,
								":CURSOR_SHARING"   => $cursorsharing,
								":INIT_PLSQL"       => $initplsql,
								":COMMENTS"         => $comments,
								":LAST_UPDATED_BY"  => $loginname								
								)
					);



/*
	$result= $conn->sql( basename(__FILE__), 
						"UPDATE adm_logins 
							SET username           = :USERNAME, 
								osuser             = :OSUSER, 
								machine            = :MACHINE, 
								begin_date		   = CONVERT(DATETIME, :BEGIN_DATE, 20),
							    end_date		   = iif( $xenddate=0, CONVERT(DATETIME, :END_DATE, 20),NULL),
								freetools          = :FREETOOLS, 
								sessions_per_user  = :SESSIONS_PER_USER, 
								log_logon          = :LOG_LOGON, 
								trace              = :TRACE, 
								cursor_sharing     = :CURSOR_SHARING, 
								init_plsql         = :INIT_PLSQL, 
								comments           = :COMMENTS
						WHERE id_login = $id_login",
						array(":USERNAME"         => $username,
								":OSUSER"           => $osuser,
								":MACHINE"          => $machine,
								":BEGIN_DATE"       => $begindate,
								":END_DATE"         => $enddate,
								":FREETOOLS"        => $freetools,
								":SESSIONS_PER_USER"=> $sessionsperuser,
								":LOG_LOGON"        => $loglogon,
								":TRACE"            => $trace,
								":CURSOR_SHARING"   => $cursorsharing,
								":INIT_PLSQL"       => $initplsql,
								":COMMENTS"         => $comments
								)
					);
*/
endif;



    header("Location: \admlogins");
	exit;	


?>