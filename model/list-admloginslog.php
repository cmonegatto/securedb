<?php 

include_once "class/Sql.php";
include "function/utils.php";

$iddb  = $data['iddb'];
$idcat = $data['idcat'];

$conn=new Sql();

$result= $conn->sql( basename(__FILE__), "SELECT hostname, username, password, dbname, port, player, iddb
											FROM adm_databases
										   WHERE iddb = :IDDB", array(":IDDB" => $iddb));

$localhost	= $result[0]['hostname'];
$username	= $result[0]['username'];
$password	= encrypt_decrypt('decrypt', $result[0]['password']);
$dbname		= $result[0]['dbname'];
$port		= $result[0]['port'];
$player		= $result[0]['player'];
$iddb		= $result[0]['iddb'];


//INSERIDO PARA PEGAR QUAL BANCO DE DADOS FOI ESCOLHIDO NO COMBO, E DESABILITAR ALGUNS INPUTS QUE SÃO SOMENTE PARA ORACLE
$_SESSION['player'] = $player;


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
										, decode(tk.username,'','N','S') as TO_KILL
										, SecDB_F(ll.username, ll.osuser, ll.machine, ll.program, ll.module) as REGRA										
										, max(id_log) as ID_LOG
									FROM adm_logins_log ll 
									LEFT JOIN adm_logins_to_kill tk ON ll.username = tk.username                              
									WHERE  ll.archived is null
									GROUP BY decode(tk.username,'','N','S'), ll.username, ll.osuser, ll.machine, ll.program, ll.module
								ORDER BY decode(REGRA,-1,1,decode(REGRA,0,0,decode(REGRA,9,2,3))) , 1 DESC"

							);
								/*
								Racional do ORDER BY 
									-1 		(kill)			=> 1
									 0 		(sem regra)		=> 0
									 9 		(com regra ?)	=> 2
									 1>0	(com regra)		=> 3
								*/


	elseif ($player == 'SQLSRV'):

		$result= $conn->sql( basename(__FILE__), 

					   "SELECT count(*) as QTD
						   , ll.USERNAME
						   , ll.OSUSER
						   , ll.MACHINE
						   , ll.PROGRAM
						   , ll.MODULE
						   , CASE WHEN tk.username IS NULL THEN 'N' ELSE 'S' END as TO_KILL
						   , securedb.dbo.F_LOGON ('%', ll.username, ll.osuser, ll.program, ll.machine, 0) as REGRA						   
						   , max(id_log) as ID_LOG
						FROM adm_logins_log ll
						LEFT JOIN adm_logins_to_kill tk ON ll.username = tk.username
					   WHERE  ll.archived is null
					   GROUP BY  ll.username, ll.osuser, ll.machine, ll.program, ll.module, CASE WHEN tk.username IS NULL THEN 'N' ELSE 'S' END
				   	   ORDER by CASE WHEN securedb.dbo.F_LOGON ('%', ll.username, ll.osuser, ll.program, ll.machine, 0)=-1 THEN 1
						   ELSE CASE WHEN securedb.dbo.F_LOGON ('%', ll.username, ll.osuser, ll.program, ll.machine, 0)=0 THEN 0 
						   ELSE CASE WHEN securedb.dbo.F_LOGON ('%', ll.username, ll.osuser, ll.program, ll.machine, 0)=9 THEN 2 
						   ELSE 3 END END END, QTD DESC"
							);						 


	elseif ($player == 'MYSQL'):

			$result= $conn->sql( basename(__FILE__),
						 "SELECT count(*) as QTD
							, ll.USERNAME
							, ll.OSUSER
							, ll.MACHINE
							, ll.PROGRAM
							, ll.MODULE
							, if ( if(ISNULL(tk.username), 'N', 'S')='S', 'S', if(length(concat( coalesce(lk.username,''), coalesce(lk.machine,''), coalesce(lk.osuser,'')))>0, 'S','N')) as TO_KILL
							-- , if( F_LOGON ('%', ll.username, ll.osuser, ll.program, ll.machine,0)<=0, 1, 0) as REGRA
							, F_LOGON ('%', ll.username, ll.osuser, ll.program, ll.machine,0) as REGRA
							, max(id_log) as ID_LOG
							FROM adm_logins_log ll
						LEFT JOIN adm_logins_to_kill tk
							ON ll.username = tk.username
					--
						LEFT JOIN adm_logins_locked lk
							ON if( isnull(ll.username), '%', ll.username) LIKE if( isnull( lk.username), '%', lk.username)
							AND ( if( isnull(ll.machine), '%', ll.machine) LIKE if( isnull(lk.machine), '%', '%' + lk.machine)
							OR   if( isnull(lk.machine), '%', lk.machine) LIKE if( isnull(ll.machine) , '%', ll.machine)
								)
							AND   if( isnull(ll.osuser), '%', ll.osuser) LIKE if( isnull( lk.osuser), '%', lk.osuser)
							WHERE  ll.archived is null

					--
						GROUP BY  ll.username, ll.osuser, ll.machine, ll.program, ll.module
									, if (isnull(tk.username), 'N', 'S') ='S', 'S', if(length(concat( coalesce(lk.username,''), coalesce(lk.machine,''), coalesce(lk.osuser,'')))>0, 'S','N')
					--
							-- ,if( isnull(tk.username), 'N', 'S')
							-- ,if( isnull(concat(lk.username,lk.machine,lk.osuser)), 'N', 'S')
						ORDER BY if(F_LOGON ('%', ll.username, ll.osuser, ll.program, ll.machine,0)<=0, 1, 0) DESC
									, if (isnull(tk.username), 'N', 'S')='S', 'S', if(length(concat( coalesce(lk.username,''), coalesce(lk.machine,''), coalesce(lk.osuser,'')))>0, 'S','N')
									, 1 DESC, 2"
								);		

	endif;
							
	foreach ($result as $key => $value) {

		$id_log		= $result[$key]['ID_LOG'];
		$username	= $result[$key]['USERNAME'];
		$osuser		= $result[$key]['OSUSER'];
		$machine	= $result[$key]['MACHINE'];
		$program	= $result[$key]['PROGRAM'];
		$module		= $result[$key]['MODULE'];

		$cadeado = 0;

		if ($result[$key]['REGRA'] < 0): // && $result[$key]['TO_KILL']=='S'):
			echo "<tr class='todie'>";
		elseif ($result[$key]['REGRA'] ==0 ):
			echo "<tr class='norule'>";
		else: 
			echo "<tr class='rule'>";
			$cadeado = 1;
		endif;

		$user_name = str_replace('\\', '*', $result[$key]['USERNAME']);

		
		if ($cadeado && $result[$key]['REGRA'] && $result[$key]['TO_KILL'] =='N' ):
			// abaixo configura opção do cadeado: a primeira só mostra o cadeado, a segunda permite clicar e aitvar KILL
			echo "<td style='text-align:center'><i class='fa fa-unlock x'></i></a></td>";
			//echo "<td style='text-align:center'><a href='\admlogins/lockuser/$user_name'><i class='fa fa-unlock x'></i></a></td>";

		else:
			echo "<td></td>";			                
		endif;		


		echo "<td style='text-align:center'><a href='\admloginslog/detail/$id_log/$iddb/0/0'><i class='fa fa-search'></i></a></td>";
		

		if ($result[$key]['REGRA'] <=0):
			echo "<td style='text-align:center'><a href='\admloginslog/insclick/$id_log'><i class='fa fa-thumbs-up'></i></a></td>";
		elseif ($result[$key]['REGRA'] ==9 ):
			  echo "<td style='text-align:center'><i  style='color:red'; class='fa fa-question-circle fa-2x'></i></td>";
		else:
			echo "<td></td>";			
		endif;

  		echo "<td style='text-align:center'>".$result[$key]['QTD']."</td>";
		echo "<td>".$result[$key]['USERNAME']."</td>";
		echo "<td>".$result[$key]['OSUSER']."</td>";
		echo "<td>".$result[$key]['MACHINE']."</td>";
		echo "<td>".$result[$key]['PROGRAM']."</td>";
		echo "<td>".$result[$key]['MODULE']."</td>";

		if ($result[$key]['REGRA'] ==0): // && $result[$key]['TO_KILL']!='S'):			
			echo "<td style='text-align:center'><a href='\admloginslog/archive/$id_log/$iddb'><i class='fa fa-download'></i></a></td>";
		else:
			echo "<td></td>";			
		endif;
		
		echo "</tr>";

	};

endif;

?>