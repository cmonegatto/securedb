<?php 

include_once "class/Sql.php";
include "function/utils.php";

//$iddb  = $data['iddb'];
$idcat = $data['idcat'];
//$days  = $data['days'] * -1;

$conn1=new Sql();
$conn2=new Sql();

$pauta = "corsim2";


$result1 = $conn1->sql( basename(__FILE__), "SELECT hostname, username, password, aliasdb, dbname, port, player
											  FROM adm_databases
											 WHERE idcat = :IDCAT
											 ORDER BY dbname",
											 array(":IDCAT" => $idcat));



foreach ($result1 as $key1 => $value) {
	

	$localhost	= $result1[$key1]['hostname'];
	$username	= $result1[$key1]['username'];
	$password	= encrypt_decrypt('decrypt', $result1[$key1]['password']);
	$dbname		= $result1[$key1]['dbname'];
	$aliasdb	= $result1[$key1]['aliasdb'];
	$port		= $result1[$key1]['port'];
	$player		= $result1[$key1]['player'];


	$_SESSION['msg'] = '';
	
	$conn2=new Sql($player, $localhost, $username, $password, $dbname, $port);

	$dbname		= strtoupper($result1[$key1]['dbname']);
	$aliasdb	= strtoupper($result1[$key1]['aliasdb']);

	$pauta = ($pauta=="corsim2")?"cornao2":"corsim2";

	
	if (strlen($_SESSION['msg']) == 0 ):


		if ($player == 'OCI'):

			$result2= $conn2->sql( basename(__FILE__), 
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

			$result2 = $conn2->sql( basename(__FILE__), 	

			
							"SELECT * FROM  
								(
								SELECT count(*) as QTD
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
								) ll
								ORDER by CASE WHEN securedb.dbo.F_LOGON ('%', ll.username, ll.osuser, ll.program, ll.machine, 0)=-1 THEN 1
						    		ELSE CASE WHEN securedb.dbo.F_LOGON ('%', ll.username, ll.osuser, ll.program, ll.machine, 0)=0 THEN 0 
						   			ELSE CASE WHEN securedb.dbo.F_LOGON ('%', ll.username, ll.osuser, ll.program, ll.machine, 0)=9 THEN 2 
						   			ELSE 3 END END END, QTD DESC"
								);


                
		elseif ($player == 'MYSQL'):

			$result2= $conn2->sql( basename(__FILE__), 
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
							
							-- essa parte é para não apresentar os vermelhos --							
							and F_LOGON ('%', ll.username, ll.osuser, ll.program, ll.machine,0)>=0							

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

		
		if (strlen($_SESSION['msg']) > 0 ):
			echo "<tr class='$pauta none'>";
			echo "<td></td>";
			echo "<td></td>";
			echo "<td style='text-align:center'>".$aliasdb."</td>";
			echo "<td></td>";
			echo "<td></td>";
			echo "<td></td>";
			echo "<td></td>";
			echo "<td>*** FALHA DE DADOS/TABELAS *** </td>";
			echo "<td></td>";
			echo "<td></td>";
			echo "</tr>";
            $_SESSION['msg']='';
            $pauta = ($pauta=="corsim2")?"cornao2":"corsim2";

		endif;					


		if (count($result2)==0):
			$pauta = ($pauta=="corsim2")?"cornao2":"corsim2";
		endif;


		foreach ($result2 as $key2 => $value) {

			$username	= $result2[$key2]['USERNAME'];
			$osuser		= $result2[$key2]['OSUSER'];
			$machine	= $result2[$key2]['MACHINE'];
			$program	= $result2[$key2]['PROGRAM'];
			$module		= $result2[$key2]['MODULE'];


            $cadeado = 0;

            if ($result2[$key2]['REGRA'] <0 && $result2[$key2]['TO_KILL']=='S'):
                echo "<tr class='$pauta todie'>";
				echo "<td style='text-align:center'>---</td>";
            elseif ($result2[$key2]['REGRA'] ==0):
                echo "<tr class='$pauta norule'>";
				echo "<td style='text-align:center'>***</td>";
			elseif ($result2[$key2]['REGRA'] > 0):
				echo "<tr class='$pauta rule'>";
				echo "<td style='text-align:center'>...</td>";
                $cadeado = 1;
            endif;


			// ------------------------------------------------------------------------------------------------------------------------
			if ($result2[$key2]['REGRA'] == 9 ):
				echo "<td style='text-align:center'><i  style='color:red'; class='fa fa-question-circle fa-2x'></i></td>";
			else:
				echo "<td></td>";			
			endif;            
			// ------------------------------------------------------------------------------------------------------------------------

			
			echo "<td style='text-align:center'>".$aliasdb."</td>";
            echo "<td style='text-align:center'>".$result2[$key2]['QTD']."</td>";

/*            
            if ($result2[$key2]['TO_KILL'] == "S" && $result2[$key2]['USERNAME']):
                echo "<td </td>";
            elseif ($result2[$key2]['TO_KILL'] == "N" && $result2[$key2]['USERNAME']):
                echo "<td style='text-align:center'><i class='fa fa-unlock'></i></a></td>";
            else:
                echo "<td></td>";			
            endif;
*/
            if ($cadeado && $result2[$key2]['TO_KILL'] == "N"):
                echo "<td style='text-align:center'><i class='fa fa-unlock x'></i></a></td>";
            else:
                echo "<td></td>";			                
            endif;


			echo "<td>".$result2[$key2]['USERNAME']."</td>";
			echo "<td>".$result2[$key2]['OSUSER']."</td>";
			echo "<td>".$result2[$key2]['MACHINE']."</td>";
			echo "<td>".$result2[$key2]['PROGRAM']."</td>";
			echo "<td>".$result2[$key2]['MODULE']."</td>";

            
/*            
			if ($result2[$key2]['KILLED'] == '*'):
				echo "<td style='text-align:center'><i class='fa fa-user-times'></i></a></td>";
			elseif ($result2[$key2]['KILLED'] == '#'):
				echo "<td style='text-align:center'><i class='fa fa-clock-o'></i></a></td>";			
			else:
				echo "<td></td>";			
			endif;
*/
			echo "</tr>";

		};
	else:
		echo "<tr class='$pauta none'>";
		echo "<td></td>";
		echo "<td></td>";
		echo "<td style='text-align:center'>".$aliasdb."</td>";
		echo "<td></td>";
		echo "<td></td>";
		echo "<td></td>";
		echo "<td></td>";
		echo "<td>*** FALHA DE CONEXÃO *** </td>";
		echo "<td></td>";
		echo "<td></td>";
		echo "</tr>";
        $_SESSION['msg']='';
	endif;


};

?>