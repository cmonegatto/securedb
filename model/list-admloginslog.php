<?php 

include_once "class/Sql.php";
include "function/utils.php";

$iddb  = $data['iddb'];
$idcat = $data['idcat'];

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


if (isset($_SESSION['iddb']) && $_SESSION['iddb'] >0 ) :

	//$_SESSION['iddb'] = 0;

	if ($player == 'OCI'):

		$result= $conn->sql( basename(__FILE__), 
							"SELECT count(*) as qtd
								  , ll.username
								  , ll.osuser
								  , ll.machine
								  , ll.program
								  , ll.module
								  , decode(tk.username,'','N','S') as to_kill
								  , adm_logins_fun(ll.username, ll.osuser, '%' || substr(ll.machine, instr(ll.machine, '\')+1) || '%', ll.program, ll.module) as REGRA
								  , max(id_log) as ID_LOG
							FROM adm_logins_log ll
							LEFT JOIN adm_logins_to_kill tk
								ON ll.username = tk.username
							GROUP BY  ll.username, ll.osuser, ll.machine, ll.program, ll.module, decode(tk.username,'','N','S')
							ORDER BY adm_logins_fun(ll.username, ll.osuser, '%' || substr(ll.machine, instr(ll.machine, '\')+1) || '%', ll.program, ll.module) DESC, decode(tk.username,'','N','S'), 1 DESC"
							);

	elseif ($player == 'SQLSRV'):


		$result= $conn->sql( basename(__FILE__), 
							"SELECT count(*) as QTD
								  , ll.USERNAME
								  , ll.OSUSER
								  , ll.MACHINE
								  , ll.PROGRAM
								  , ll.MODULE
								  , iif(tk.USERNAME IS NULL,'N','S') as TO_KILL
								  , iif(securedb.dbo.F_LOGON ('%', ll.username, ll.osuser, ll.program, ll.machine,0)<=0,1,0) as REGRA
								  , max(id_log) as ID_LOG
							  FROM adm_logins_log ll
							  LEFT JOIN adm_logins_to_kill tk
							    ON ll.username = tk.username
							 GROUP BY  ll.username, ll.osuser, ll.machine, ll.program, ll.module, iif(tk.username IS NULL,'N','S')
							 ORDER BY iif(securedb.dbo.F_LOGON ('%', ll.username, ll.osuser, ll.program, ll.machine,0)<=0,1,0) DESC, iif(tk.USERNAME IS NULL,'N','S'), 1 DESC"
							);
	endif;
							
	foreach ($result as $key => $value) {

		

		if ($result[$key]['REGRA'] && $result[$key]['TO_KILL']=='S'):
			echo "<tr class='todie'>";
		elseif ($result[$key]['REGRA'] ):
			echo "<tr class='norule'>";
		else: 
			echo "<tr class='rule'>";
		endif;

		$id_log		= $result[$key]['ID_LOG'];
		$username	= $result[$key]['USERNAME'];
		$osuser		= $result[$key]['OSUSER'];
		$machine	= $result[$key]['MACHINE'];
		$program	= $result[$key]['PROGRAM'];
		$module		= $result[$key]['MODULE'];
		//$killed		= $result[$key]['KILLED'];

		$user_name = str_replace('\\', '*', $result[$key]['USERNAME']);


		if ($result[$key]['TO_KILL'] == "S" && $result[$key]['USERNAME']):
			echo "<td style='text-align:center'><a href='\admlogins/lockuser/$user_name'><i class='fa fa-lock'></i></a></td>";
			//echo "<td style='text-align:center'><i class='fa fa-lock'></i></a></td>";
		elseif ($result[$key]['TO_KILL'] == "N" && $result[$key]['USERNAME']):
			echo "<td style='text-align:center'><a href='\admlogins/lockuser/$user_name'><i class='fa fa-unlock'></i></a></td>";
			//echo "<td style='text-align:center'><i class='fa fa-unlock'></i></a></td>";
		else:
			echo "<td></td>";			
		endif;


		//$id = $username . '/' . str_replace('\\','*',$osuser) .'/'. str_replace('\\','*',$machine) .'/'. str_replace('\\','*',$program) .'/'. str_replace('\\','*',$module);


		//echo "<td><a href='\admloginslog/detail/$username/$osuser/$machine/$program/$module/$killed'><i class='fa fa-search'></i></a></td>";

		echo "<td style='text-align:center'><a href='\admloginslog/detail/$id_log'><i class='fa fa-search'></i></a></td>";

		if ($result[$key]['REGRA'] ):
			echo "<td style='text-align:center'><a href='\admloginslog/insclick/$id_log'><i class='fa fa-thumbs-up'></i></a></td>";
		else:
			echo "<td></td>";			
		endif;

		//echo "<td><a href='#'><i class='fa fa-trash'></i></a></td>";
  		echo "<td style='text-align:center'>".$result[$key]['QTD']."</td>";
		echo "<td>".$result[$key]['USERNAME']."</td>";
		echo "<td>".$result[$key]['OSUSER']."</td>";
		echo "<td>".$result[$key]['MACHINE']."</td>";
		echo "<td>".$result[$key]['PROGRAM']."</td>";
		echo "<td>".$result[$key]['MODULE']."</td>";
		
/*		
		if ($result[$key]['KILLED'] == '*'):
			echo "<td style='text-align:center'><i class='fa fa-user-times'></i></a></td>";
		elseif ($result[$key]['KILLED'] == '#'):
			echo "<td style='text-align:center'><i class='fa fa-clock-o'></i></a></td>";			
		else:
			echo "<td></td>";			
		endif;
*/

		echo "</tr>";

	};

endif;

?>