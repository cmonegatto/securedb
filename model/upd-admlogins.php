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


    header("Location: \admlogins");
	exit;	


?>