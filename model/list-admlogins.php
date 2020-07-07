<?php 

include_once "class/Sql.php";
include "function/utils.php";

//$iddb  = $data['iddb'];
//$idcat = $data['idcat'];

//$iddb  = $_SESSION['iddb'];
//$idcat = $_SESSION['idcat'];


$iddb	= (!isset($_POST['iddb']))?$_SESSION['iddb']:$_POST['iddb'];
$idcat	= (!isset($_POST['idcat']))?$_SESSION['idcat']:$_POST['idcat'];


$conn=new Sql();


$result= $conn->sql( basename(__FILE__), "SELECT hostname, username, password, dbname, port, player
											FROM adm_databases
										   WHERE iddb = $iddb");

$localhost	= $result[0]['hostname'];
$username	= $result[0]['username'];
$password	= encrypt_decrypt('decrypt', $result[0]['password']);
$dbname		= $result[0]['dbname'];
$port		= $result[0]['port'];
$player		= $result[0]['player'];


$conn=new Sql($player, $localhost, $username, $password, $dbname, $port);

if (isset($_SESSION['msg']) && strlen($_SESSION['msg'])>0 ):
    header("Location: \admloginslog/0/0");
	exit;	
endif;

//if (isset($_SESSION['iddb']) && $_SESSION['iddb'] >0 ) :

	$result= $conn->sql( basename(__FILE__), 
						"SELECT l.id_login, l.username, l.osuser, l.machine, to_char(l.begin_date, 'dd/mm/yy hh24:mi') as begin_date, to_char(l.end_date, 'dd/mm/yy hh24:mi') as end_date, l.freetools, l.sessions_per_user, 
								l.log_logon, l.trace, l.cursor_sharing, l.init_plsql, l.comments, decode(tk.username,'','N','S') as to_kill
						   FROM adm_logins l
						   LEFT JOIN adm_logins_to_kill tk
							 ON l.username = tk.username
						  ORDER BY id_login desc"
						);
			  



	foreach ($result as $key => $value) {
		
		$id 	  = $result[$key]['ID_LOGIN'];
		$username = $result[$key]['USERNAME'];

		
		if ($result[$key]['TO_KILL'] == "S" && $result[$key]['USERNAME']):
			echo "<td><a href='\admlogins/lockuser/$username'><i class='fa fa-lock'></i></a></td>";
		elseif ($result[$key]['TO_KILL'] == "N" && $result[$key]['USERNAME']):
			echo "<td><a href='\admlogins/lockuser/$username'><i class='fa fa-unlock'></i></a></td>";
		else:
			echo "<td></td>";			
		endif;


		echo "<td><a href='\admlogins/update/$id'><i class='fa fa-pencil'></i></a></td>";
		echo "<td><a href='\admlogins/delete/$id'><i class='fa fa-trash'></i></a></td>";
  		echo "<td>".$result[$key]['ID_LOGIN']."</td>";
  		echo "<td>".$result[$key]['USERNAME']."</td>";
  		echo "<td>".$result[$key]['OSUSER']."</td>";
  		echo "<td>".$result[$key]['MACHINE']."</td>";
		echo "<td>".$result[$key]['BEGIN_DATE']."</td>";
		echo "<td>".$result[$key]['END_DATE']."</td>";
		echo "<td>".$result[$key]['FREETOOLS']."</td>";
		echo "<td>".$result[$key]['SESSIONS_PER_USER']."</td>";
		echo "<td>".$result[$key]['LOG_LOGON']."</td>";
		echo "<td>".$result[$key]['TRACE']."</td>";
		echo "<td>".$result[$key]['CURSOR_SHARING']."</td>";
		echo "<td>".$result[$key]['INIT_PLSQL']."</td>";
		echo "<td>".$result[$key]['COMMENTS']."</td>";

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