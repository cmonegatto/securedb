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
										   WHERE iddb = :IDDB", array(":IDDB" => $iddb));
										   
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


	if ($player == 'OCI'):

		$result= $conn->sql( basename(__FILE__), 
							"SELECT l.ID_LOGIN
								  , l.USERNAME
								  , l.OSUSER
								  , l.MACHINE
								  , to_char(l.begin_date, 'dd/mm/yy hh24:mi') as BEGIN_DATE
								  , to_char(l.end_date, 'dd/mm/yy hh24:mi')   as END_DATE
								  , l.FREETOOLS
								  , l.SESSIONS_PER_USER
								  , l.LOG_LOGON
								  , l.TRACE
								  , l.CURSOR_SHARING
								  , l.INIT_PLSQL
								  , l.COMMENTS
								  , decode(tk.USERNAME,'','N','S') as TO_KILL
							FROM adm_logins l
							LEFT JOIN adm_logins_to_kill tk
								ON l.username = tk.username
							ORDER BY id_login desc"
							);

	elseif ($player == 'SQLSRV'):


		$result= $conn->sql( basename(__FILE__), 
							"SELECT l.ID_LOGIN
								  , l.USERNAME
								  , l.OSUSER
								  , l.MACHINE
								  , format(l.BEGIN_DATE,'dd/MM/yyyy HH:mm:ss')  as BEGIN_DATE
								  , format(l.END_DATE,'dd/MM/yyyy HH:mm:ss')	as END_DATE
								  , l.FREETOOLS
								  , l.SESSIONS_PER_USER
								  , l.LOG_LOGON
								  , l.TRACE
								  , l.CURSOR_SHARING
								  , l.INIT_PLSQL
								  , l.COMMENTS
								,   CASE 
										WHEN tk.USERNAME is null THEN 'N'
										ELSE 'S'
									END as TO_KILL
							FROM adm_logins l
							LEFT JOIN adm_logins_to_kill tk
								ON l.username = tk.username
							ORDER BY id_login desc"
							);



	/*
	elseif ($player == 'SQLSRV'):
		$result= $conn->sql( basename(__FILE__), 
							"SELECT l.ID_LOGIN
								  , l.USERNAME
								  , l.OSUSER
								  , l.MACHINE
								  , format(l.BEGIN_DATE,'dd/MM/yyyy HH:mm:ss')  as BEGIN_DATE
								  , format(l.END_DATE,'dd/MM/yyyy HH:mm:ss')	as END_DATE
								  , l.FREETOOLS
								  , l.SESSIONS_PER_USER
								  , l.LOG_LOGON
								  , l.TRACE
								  , l.CURSOR_SHARING
								  , l.INIT_PLSQL
								  , l.COMMENTS
								  , iif(tk.USERNAME is null,'N','S') as TO_KILL
							FROM adm_logins l
							LEFT JOIN adm_logins_to_kill tk
								ON l.username = tk.username
							ORDER BY id_login desc"
							);
	*/
	endif;

			  



	foreach ($result as $key => $value) {
		
		$id 	  = $result[$key]['ID_LOGIN'];
		$username = $result[$key]['USERNAME'];

		
		if ($result[$key]['TO_KILL'] == "S" && $result[$key]['USERNAME']):
			//echo "<td><a href='\admlogins/lockuser/$username'><i class='fa fa-lock'></i></a></td>";
			echo "<td><i class='fa fa-lock'></i></a></td>";
		elseif ($result[$key]['TO_KILL'] == "N" && $result[$key]['USERNAME']):
			//echo "<td><a href='\admlogins/lockuser/$username'><i class='fa fa-unlock'></i></a></td>";
			echo "<td><i class='fa fa-unlock'></i></a></td>";
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