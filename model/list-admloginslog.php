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
								, decode(decode(tk.username,'','N','S'),'S','S', decode(lk.username||lk.machine||lk.osuser,'','N','S')) as TO_KILL
								-- , SecDB_F(ll.username, ll.osuser, '%' || substr(ll.machine, instr(ll.machine, '\')+1) || '%', ll.program, ll.module) as REGRA
								, SecDB_F(ll.username, ll.osuser, ll.machine, ll.program, ll.module) as REGRA
								, max(id_log) as ID_LOG
							   FROM adm_logins_log ll
						  LEFT JOIN adm_logins_to_kill tk
							     ON ll.username = tk.username                              
							--
						  LEFT JOIN adm_logins_locked lk
							     ON ll.username LIKE decode(lk.username, NULL, '%', lk.username)
							    AND ( ll.MACHINE  LIKE decode(lk.machine , NULL, '%', '%' || lk.machine)
							     OR   decode(lk.machine , NULL, '%', lk.machine) LIKE ll.MACHINE
							        )
							    AND   ll.OSUSER   LIKE decode(lk.osuser  , NULL, '%', lk.osuser) 
							  WHERE  ll.archived is null
						   GROUP BY  ll.username, ll.osuser, ll.machine, ll.program, ll.module
									, decode(decode(tk.username,'','N','S'),'S','S', decode(lk.username||lk.machine||lk.osuser,'','N','S'))
						   -- ORDER BY SecDB_F(ll.username, ll.osuser, '%' || substr(ll.machine, instr(ll.machine, '\')+1) || '%', ll.program, ll.module) DESC
						   ORDER BY SecDB_F(ll.username, ll.osuser, ll.machine, ll.program, ll.module) DESC
									, decode(decode(tk.username,'','N','S'),'S','S', decode(lk.username||lk.machine||lk.osuser,'','N','S'))
									, 1 DESC"


/*		
							"SELECT count(*) as qtd
								  , ll.username
								  , ll.osuser
								  , ll.machine
								  , ll.program
								  , ll.module
								  , decode(tk.username,'','N','S') as TO_KILL
								  , SecDB_F(ll.username, ll.osuser, '%' || substr(ll.machine, instr(ll.machine, '\')+1) || '%', ll.program, ll.module) as REGRA
								  , max(id_log) as ID_LOG
							FROM adm_logins_log ll
							LEFT JOIN adm_logins_to_kill tk
								ON ll.username = tk.username
							GROUP BY  ll.username, ll.osuser, ll.machine, ll.program, ll.module, decode(tk.username,'','N','S')
							ORDER BY SecDB_F(ll.username, ll.osuser, '%' || substr(ll.machine, instr(ll.machine, '\')+1) || '%', ll.program, ll.module) DESC, decode(tk.username,'','N','S'), 1 DESC"
*/
							);

	elseif ($player == 'SQLSRV'):

		$result= $conn->sql( basename(__FILE__), 

					   "SELECT count(*) as QTD
							, ll.USERNAME
							, ll.OSUSER
							, ll.MACHINE
							, ll.PROGRAM
							, ll.MODULE
							-- , CASE WHEN (CASE WHEN tk.username IS NULL THEN 'N' ELSE 'S' END)='S' THEN 'S' ELSE case when concat(lk.username,lk.machine,lk.osuser) ='' THEN 'N' ELSE 'S' END END as TO_KILL
							, CASE WHEN (CASE WHEN tk.username IS NULL THEN 'N' ELSE 'S' END)='S' THEN 'S' ELSE case when isnull(lk.username,'' ) + isnull(lk.machine,'' ) + isnull(lk.osuser,'' ) ='' THEN 'N' ELSE 'S' END END as TO_KILL
							, CASE WHEN securedb.dbo.F_LOGON ('%', ll.username, ll.osuser, ll.program, ll.machine,0)<=0 THEN 1 ELSE 0 END as REGRA
							, max(id_log) as ID_LOG
						  FROM adm_logins_log ll
					 LEFT JOIN adm_logins_to_kill tk
							ON ll.username = tk.username
					--
					 LEFT JOIN adm_logins_locked lk
							ON CASE WHEN ll.username IS NULL THEN '%' ELSE ll.username END LIKE CASE WHEN lk.username IS NULL THEN '%' ELSE lk.username END
						   AND ( CASE WHEN ll.machine IS NULL THEN '%' ELSE ll.machine END  LIKE CASE WHEN lk.machine IS NULL THEN '%' ELSE '%' + lk.machine END
							OR   CASE WHEN lk.machine IS NULL THEN '%' ELSE lk.machine END LIKE CASE WHEN ll.machine IS NULL THEN '%' ELSE ll.machine END
							   )
						   AND   CASE WHEN ll.osuser IS NULL THEN '%' ELSE ll.osuser END LIKE CASE WHEN lk.osuser IS NULL THEN '%' ELSE lk.osuser END
						 WHERE  ll.archived is null

					--
					  GROUP BY  ll.username, ll.osuser, ll.machine, ll.program, ll.module
								 --, CASE WHEN (CASE WHEN tk.username IS NULL THEN 'N' ELSE 'S' END)='S' THEN 'S' ELSE case when concat(lk.username,lk.machine,lk.osuser) ='' THEN 'N' ELSE 'S' END END
								 , CASE WHEN (CASE WHEN tk.username IS NULL THEN 'N' ELSE 'S' END)='S' THEN 'S' ELSE case when isnull(lk.username,'' ) + isnull(lk.machine,'' ) + isnull(lk.osuser,'' ) ='' THEN 'N' ELSE 'S' END END
					--
							,CASE WHEN tk.username IS NULL THEN 'N' ELSE 'S' END
							--,case when concat(lk.username,lk.machine,lk.osuser) IS NULL THEN 'N' ELSE 'S' END 
							,case when isnull(lk.username,'' ) + isnull(lk.machine,'' ) + isnull(lk.osuser,'' ) = '' THEN 'N' ELSE 'S' END 
						ORDER BY CASE WHEN securedb.dbo.F_LOGON ('%', ll.username, ll.osuser, ll.program, ll.machine,0)<=0 THEN 1 ELSE 0 END DESC
								 --, CASE WHEN (CASE WHEN tk.username IS NULL THEN 'N' ELSE 'S' END)='S' THEN 'S' ELSE case when concat(lk.username,lk.machine,lk.osuser) ='' THEN 'N' ELSE 'S' END END
								 , CASE WHEN (CASE WHEN tk.username IS NULL THEN 'N' ELSE 'S' END)='S' THEN 'S' ELSE case when isnull(lk.username,'' ) + isnull(lk.machine,'' ) + isnull(lk.osuser,'' ) ='' THEN 'N' ELSE 'S' END END
								 , 1 DESC"

/*
							"SELECT count(*) as QTD
								  , ll.USERNAME
								  , ll.OSUSER
								  , ll.MACHINE
								  , ll.PROGRAM
								  , ll.MODULE
								  , CASE WHEN tk.USERNAME IS NULL THEN 'N' ELSE 'S' END as TO_KILL
								  , CASE WHEN securedb.dbo.F_LOGON ('%', ll.username, ll.osuser, ll.program, ll.machine,0)<=0 THEN 1 ELSE 0 END as REGRA
								  , max(id_log) as ID_LOG
							  FROM adm_logins_log ll
							  LEFT JOIN adm_logins_to_kill tk
							    ON ll.username = tk.username
							 GROUP BY  ll.username, ll.osuser, ll.machine, ll.program, ll.module, CASE WHEN tk.username IS NULL THEN 'N' ELSE 'S' END
							 ORDER BY CASE WHEN securedb.dbo.F_LOGON ('%', ll.username, ll.osuser, ll.program, ll.machine,0)<=0 THEN 1 ELSE 0 END DESC, CASE WHEN tk.USERNAME IS NULL THEN 'N' ELSE 'S' END, 1 DESC"
*/							 
							);



		/*
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
		*/

	elseif ($player == 'MYSQL'):

			$result= $conn->sql( basename(__FILE__),
						 "SELECT count(*) as QTD
							, ll.USERNAME
							, ll.OSUSER
							, ll.MACHINE
							, ll.PROGRAM
							, ll.MODULE
							, if ( if(ISNULL(tk.username), 'N', 'S')='S', 'S', if(length(concat( coalesce(lk.username,''), coalesce(lk.machine,''), coalesce(lk.osuser,'')))>0, 'S','N')) as TO_KILL
							, if( F_LOGON ('%', ll.username, ll.osuser, ll.program, ll.machine,0)<=0, 1, 0) as REGRA
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

		if ($result[$key]['REGRA'] && $result[$key]['TO_KILL']=='S'):
			echo "<tr class='todie'>";
		elseif ($result[$key]['REGRA'] ):
			echo "<tr class='norule'>";
		else: 
			echo "<tr class='rule'>";
			$cadeado = 1;
		endif;

		$user_name = str_replace('\\', '*', $result[$key]['USERNAME']);

		
		if ($cadeado && $result[$key]['TO_KILL'] == "N"):
			//echo "<td style='text-align:center'><i class='fa fa-unlock x'></i></a></td>";
			echo "<td style='text-align:center'><a href='\admlogins/lockuser/$user_name'><i class='fa fa-unlock x'></i></a></td>";

		else:
			echo "<td></td>";			                
		endif;		

		//$user_name = str_replace('\\', '*', $result[$key]['USERNAME']);

/*
		if ($result[$key]['TO_KILL'] == "S" && $result[$key]['USERNAME']):
			echo "<td style='text-align:center'><a href='\admlogins/lockuser/$user_name'><i class='fa fa-lock'></i></a></td>";
			//echo "<td style='text-align:center'><i class='fa fa-lock'></i></a></td>";
		elseif ($result[$key]['TO_KILL'] == "N" && $result[$key]['USERNAME']):
			echo "<td style='text-align:center'><a href='\admlogins/lockuser/$user_name'><i class='fa fa-unlock'></i></a></td>";
			//echo "<td style='text-align:center'><i class='fa fa-unlock'></i></a></td>";
		else:
			echo "<td></td>";			
		endif;
*/

		//$id = $username . '/' . str_replace('\\','*',$osuser) .'/'. str_replace('\\','*',$machine) .'/'. str_replace('\\','*',$program) .'/'. str_replace('\\','*',$module);


		echo "<td style='text-align:center'><a href='\admloginslog/detail/$id_log/$iddb/0/0'><i class='fa fa-search'></i></a></td>";
		

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

		//if ($result[$key]['REGRA'] ):
		if ($result[$key]['REGRA'] && $result[$key]['TO_KILL']!='S'):			
			echo "<td style='text-align:center'><a href='\admloginslog/archive/$id_log/$iddb'><i class='fa fa-download'></i></a></td>";
		else:
			echo "<td></td>";			
		endif;
		

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