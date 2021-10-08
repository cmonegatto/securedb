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
								  , to_char(l.begin_date, 'dd/mm/yy hh24:mi') 	as BEGIN_DATE
								  , to_char(l.end_date, 'dd/mm/yy hh24:mi')   	as END_DATE
								  , l.FREETOOLS
								  , l.SESSIONS_PER_USER
								  , l.LOG_LOGON
								  , l.TRACE
								  , l.CURSOR_SHARING
								  , l.INIT_PLSQL
								  , l.COMMENTS
								  , decode(tk.USERNAME,'','N','S') 				as TO_KILL
								  , l.CREATED_BY 								as CREATED_BY
								  , to_char(l.created_date, 'dd/mm/yy hh24:mi') as CREATED_DATE
								  , l.LAST_UPDATED_BY
								  , to_char(l.last_updated_date, 'dd/mm/yy hh24:mi') as LAST_UPDATED_DATE
								  , to_char(l.last_used_date, 'dd/mm/yy hh24:mi') 	 as LAST_USED_DATE
								  , l.FLAG
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
								  -- , format(l.BEGIN_DATE,'dd/MM/yyyy HH:mm:ss')  as BEGIN_DATE
								  , CONVERT(VARCHAR(10), l.BEGIN_DATE, 103) + ' '  + convert(VARCHAR(8), l.BEGIN_DATE, 14) as BEGIN_DATE
								  -- , format(l.END_DATE,'dd/MM/yyyy HH:mm:ss')	as END_DATE
								  , CONVERT(VARCHAR(10), l.END_DATE, 103) + ' '  + convert(VARCHAR(8), l.END_DATE, 14) as END_DATE
								  , l.FREETOOLS
								  , l.SESSIONS_PER_USER
								  , l.LOG_LOGON
								  , l.TRACE
								  , l.CURSOR_SHARING
								  , l.INIT_PLSQL
								  , l.COMMENTS								  
								  , CASE 
										WHEN tk.USERNAME is null THEN 'N'
										ELSE 'S'
									END as TO_KILL
								  , l.CREATED_BY 									as CREATED_BY
								  -- , format(l.CREATED_DATE,'dd/MM/yyyy HH:mm:ss')  	as CREATED_DATE
								  , CONVERT(VARCHAR(10), l.CREATED_DATE, 103) + ' '  + convert(VARCHAR(8), l.CREATED_DATE, 14) as CREATED_DATE
								  , l.LAST_UPDATED_BY
								  -- , format(l.LAST_UPDATED_DATE,'dd/MM/yyyy HH:mm:ss')  	as LAST_UPDATED_DATE
								  , CONVERT(VARCHAR(10), l.LAST_UPDATED_DATE, 103) + ' '  + convert(VARCHAR(8), l.LAST_UPDATED_DATE, 14) as LAST_UPDATED_DATE
								  -- , format(l.LAST_USED_DATE,'dd/MM/yyyy HH:mm:ss')  	as LAST_USED_DATE
								  , CONVERT(VARCHAR(10), l.LAST_USED_DATE, 103) + ' '  + convert(VARCHAR(8), l.LAST_USED_DATE, 14) as LAST_USED_DATE								  
								  , l.FLAG
							FROM adm_logins l
							LEFT JOIN adm_logins_to_kill tk
								ON l.username = tk.username
							ORDER BY id_login desc"
							);

	
	
	elseif ($player == 'MYSQL'):


		$result= $conn->sql( basename(__FILE__), 
							"SELECT l.ID_LOGIN
								  , l.USERNAME
								  , l.OSUSER
								  , l.MACHINE
								  , DATE_FORMAT(l.BEGIN_DATE, '%d/%m/%Y %H:%i:%s')	as BEGIN_DATE
								  , DATE_FORMAT(l.END_DATE, '%d/%m/%Y %H:%i:%s') 	as END_DATE
								  , l.FREETOOLS
								  , l.SESSIONS_PER_USER
								  , l.LOG_LOGON
								  , l.TRACE
								  , l.CURSOR_SHARING
								  , l.INIT_PLSQL
								  , l.COMMENTS								  
								  , CASE 
										WHEN tk.USERNAME is null THEN 'N'
										ELSE 'S'
									END as TO_KILL
								  , l.CREATED_BY 									 	as CREATED_BY
								  , DATE_FORMAT(l.CREATED_DATE, '%d/%m/%Y %H:%i:%s')	as CREATED_DATE
								  , l.LAST_UPDATED_BY
								  , DATE_FORMAT(l.LAST_UPDATED_DATE, '%d/%m/%Y %H:%i:%s') as LAST_UPDATED_DATE
								  , DATE_FORMAT(l.LAST_USED_DATE, '%d/%m/%Y %H:%i:%s') as LAST_USED_DATE
								  , l.FLAG
							FROM adm_logins l
							LEFT JOIN adm_logins_to_kill tk
								ON l.username = tk.username
							ORDER BY id_login desc"
							);


							
	endif;

	 
	foreach ($result as $key => $value) {
		
		$id 	  = $result[$key]['ID_LOGIN'];
		$username = $result[$key]['USERNAME'];

		if ($result[$key]['FLAG']):
			echo "<tr class='to-conf'>";
			echo "<td><a href='\admlogins/update-toconf/$id'><i class='fa fa-question-circle fa-2x'></i></a></td>";
		else: 
			echo "<tr class=''>";
			echo "<td><a href='\admlogins/update-toconf/$id'><i class='fa fa-question-circle fa-1x'></i></a></td>";
		endif;

		

		if ($result[$key]['TO_KILL'] == "S" && $result[$key]['USERNAME']):
			echo "<td><a href='\admlogins/lockuser/$username'><i class='fa fa-lock'></i></a></td>"; // habilita o click no cadeado para LOCK
			//echo "<td><i class='fa fa-lock'></i></a></td>";										// desabilita o click no cadeado para LOCK
		elseif ($result[$key]['TO_KILL'] == "N" && $result[$key]['USERNAME']):
			echo "<td><a href='\admlogins/lockuser/$username'><i class='fa fa-unlock'></i></a></td>";	// habilita o click no cadeado para LOCK
			//echo "<td><i class='fa fa-unlock'></i></a></td>";											// desabilita o click no cadeado para LOCK 
		else:
			echo "<td></td>";			
		endif;


		echo "<td><a href='\admlogins/update/$id'><i class='fa fa-pencil'></i></a></td>";
		//echo "<td><a href='\admlogins/delete/$id'><i class='fa fa-trash'></i></a></td>"; // essa linha deleta sem pedir confirmação. alterada pela linha abaixo!
		echo "<td><a href='\admlogins/delete/$id' onclick=\"return confirm('Tem certeza que deseja deletar esse registro?');\"><i class='fa fa-trash'></i></a></td>";

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
		echo "<td>".$result[$key]['CREATED_BY']."</td>";
		echo "<td>".$result[$key]['CREATED_DATE']."</td>";
		echo "<td>".$result[$key]['LAST_UPDATED_BY']."</td>";
		echo "<td>".$result[$key]['LAST_UPDATED_DATE']."</td>";
		echo "<td>".$result[$key]['LAST_USED_DATE']."</td>";		

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