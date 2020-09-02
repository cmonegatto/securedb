<?php 

include_once "class/Sql.php";
include "function/utils.php";

//$iddb  = $data['iddb'];
$idcat = $data['idcat'];
//$days  = $data['days'] * -1;

$conn1=new Sql();
$conn2=new Sql();

$pauta = "corsim2";


$result1 = $conn1->sql( basename(__FILE__), "SELECT hostname, username, password, dbname, port, player
											  FROM adm_databases
											 WHERE idcat = :IDCAT
											 ORDER BY dbname",
											 array(":IDCAT" => $idcat));



foreach ($result1 as $key1 => $value) {
	

	$localhost	= $result1[$key1]['hostname'];
	$username	= $result1[$key1]['username'];
	$password	= encrypt_decrypt('decrypt', $result1[$key1]['password']);
	$dbname		= strtoupper($result1[$key1]['dbname']);
	$port		= $result1[$key1]['port'];
	$player		= $result1[$key1]['player'];


	$_SESSION['msg'] = '';
	
	$conn2=new Sql($player, $localhost, $username, $password, $dbname, $port);


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
                                    , adm_logins_fun(ll.username, ll.osuser, '%' || substr(ll.machine, instr(ll.machine, '\')+1) || '%', ll.program, ll.module) as REGRA
--                                    , max(id_log) as ID_LOG
                                FROM adm_logins_log ll
                                LEFT JOIN adm_logins_to_kill tk
                                    ON ll.username = tk.username
--                                 WHERE adm_logins_fun(ll.username, ll.osuser, '%' || substr(ll.machine, instr(ll.machine, '\')+1) || '%', ll.program, ll.module) =1
                                   --AND decode(tk.username,'','N','S') ='N'
                                GROUP BY  ll.username, ll.osuser, ll.machine, ll.program, ll.module, decode(tk.username,'','N','S')
                                ORDER BY adm_logins_fun(ll.username, ll.osuser, '%' || substr(ll.machine, instr(ll.machine, '\')+1) || '%', ll.program, ll.module) DESC, decode(tk.username,'','N','S'), 1 DESC"
                                );



		elseif ($player == 'SQLSRV'):

            $result2 = $conn2->sql( basename(__FILE__), 
                                "SELECT count(*) as QTD
                                    , ll.USERNAME
                                    , ll.OSUSER
                                    , ll.MACHINE
                                    , ll.PROGRAM
                                    , ll.MODULE
                                    , iif(tk.USERNAME IS NULL,'N','S') as TO_KILL
                                    , iif(securedb.dbo.F_LOGON ('%', ll.username, ll.osuser, ll.program, ll.machine,0)<=0,1,0) as REGRA
--                                    , max(id_log) as ID_LOG
                                   FROM adm_logins_log ll
                                   LEFT JOIN adm_logins_to_kill tk
                                     ON ll.username = tk.username
--                                  WHERE iif(securedb.dbo.F_LOGON ('%', ll.username, ll.osuser, ll.program, ll.machine,0)<=0,1,0)=1
--                                    AND iif(tk.USERNAME IS NULL,'N','S') = 'N'
                                  GROUP BY  ll.username, ll.osuser, ll.machine, ll.program, ll.module, iif(tk.username IS NULL,'N','S')
                                  ORDER BY iif(securedb.dbo.F_LOGON ('%', ll.username, ll.osuser, ll.program, ll.machine,0)<=0,1,0) DESC, iif(tk.USERNAME IS NULL,'N','S'), 1 DESC"
                                );
            
                
		endif;



		if (strlen($_SESSION['msg']) > 0 ):
			echo "<tr class='$pauta none'>";
			echo "<td style='text-align:center'>".$dbname."</td>";
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

            if ($result2[$key2]['REGRA'] && $result2[$key2]['TO_KILL']=='S'):
                echo "<tr class='$pauta todie'>";
            elseif ($result2[$key2]['REGRA'] ):
                echo "<tr class='$pauta norule'>";
            else: 
                echo "<tr class='$pauta rule'>";
                $cadeado = 1;
            endif;

			//echo "<tr class='$pauta'>";
            //echo "<td style='text-align:center; font-weight:bolder'>".$dbname."</td>";
            

			echo "<td style='text-align:center'>".$dbname."</td>";
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
		echo "<td style='text-align:center'>".$dbname."</td>";
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