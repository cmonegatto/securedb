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

if (!($username.$osuser.$machine)):
     $_SESSION['msg'] = 'Preencha ao menos um dos três primeiros campos!';
    header("Location: \admlogins");
    exit;
endif;

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


$result= $conn->sql( basename(__FILE__), 
					 "INSERT INTO adm_logins (username, osuser, machine, begin_date, end_date, freetools, sessions_per_user, log_logon, trace, cursor_sharing, init_plsql, comments)
					  VALUES (:USERNAME, :OSUSER, :MACHINE, to_date(:BEGIN_DATE, 'yyyy-mm-dd hh24:mi'), to_date(:END_DATE, 'yyyy-mm-dd hh24:mi'), :FREETOOLS, :SESSIONS_PER_USER, :LOG_LOGON, :TRACE, :CURSOR_SHARING, :INIT_PLSQL, :COMMENTS)",
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


    header("Location: \admlogins");
	exit;	


?>