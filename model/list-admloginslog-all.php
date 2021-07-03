<?php 

include_once "class/Sql.php";
include "function/utils.php";

//$iddb  = $data['iddb'];
$idcat = $data['idcat'];
$days  = $data['days'] * -1;

$conn1=new Sql();
$conn2=new Sql();

$pauta = "corsim";


$result1 = $conn1->sql( basename(__FILE__), "SELECT hostname, username, password, aliasdb, dbname, port, player, iddb
											  FROM adm_databases
											 WHERE idcat = :IDCAT
											 ORDER BY dbname",
											 array(":IDCAT" => $idcat));



foreach ($result1 as $key1 => $value) {
	

	$localhost	= $result1[$key1]['hostname'];
	$username	= $result1[$key1]['username'];
	$password	= encrypt_decrypt('decrypt', $result1[$key1]['password']);
	$aliasdb	= $result1[$key1]['aliasdb'];
	$dbname		= $result1[$key1]['dbname'];
	$port		= $result1[$key1]['port'];
	$player		= $result1[$key1]['player'];
	$iddb		= $result1[$key1]['iddb'];


	$_SESSION['msg'] = '';
	
	$conn2=new Sql($player, $localhost, $username, $password, $dbname, $port);


	$aliasdb	= strtoupper($result1[$key1]['aliasdb']);
	$dbname		= strtoupper($result1[$key1]['dbname']);

	$pauta = ($pauta=="corsim")?"cornao":"corsim";

	
	if (strlen($_SESSION['msg']) == 0 ):


		if ($player == 'OCI'):

			$result2= $conn2->sql( basename(__FILE__), 
								"SELECT max(id_log) ID_LOG, count(*) as qtd, ll.username, ll.osuser, ll.machine, ll.program, ll.module, ll.killed
									FROM adm_logins_log ll
									WHERE /* killed is not null
									AND */ datetime >=trunc(sysdate-$days)
									GROUP BY ll.username, ll.osuser, ll.machine, ll.program, ll.module, ll.killed
									ORDER BY 2 DESC"
								);

		elseif ($player == 'SQLSRV'):

			$result2= $conn2->sql( basename(__FILE__), 
								"SELECT max(id_log) ID_LOG, count(*) as QTD, ll.USERNAME, ll.OSUSER, ll.MACHINE, ll.PROGRAM, ll.MODULE, ll.KILLED
									FROM adm_logins_log ll
									WHERE /* killed is not null
									AND */ datetime >= cast(GETDATE()-$days as date)
									GROUP BY ll.username, ll.osuser, ll.machine, ll.program, ll.module, ll.killed
									ORDER BY 2 DESC"
								);

		elseif ($player == 'MYSQL'):

			$result2= $conn2->sql( basename(__FILE__), 
								"SELECT max(id_log) ID_LOG, count(*) as QTD, ll.USERNAME, ll.OSUSER, ll.MACHINE, ll.PROGRAM, ll.MODULE, ll.KILLED
									FROM adm_logins_log ll
									WHERE datetime >= date_sub(now(), interval $days day)
									GROUP BY ll.username, ll.osuser, ll.machine, ll.program, ll.module, ll.killed
									ORDER BY 2 DESC"
								);
		endif;










		if (strlen($_SESSION['msg']) > 0 ):
			echo "<tr class='$pauta'>";
			echo "<td></td>";
			echo "<td style='text-align:center'>".$aliasdb."</td>";
			echo "<td></td>";
			echo "<td></td>";
			echo "<td></td>";
			echo "<td>*** FALHA DE DADOS/TABELAS *** </td>";
			echo "<td></td>";
			echo "<td></td>";
			echo "</tr>";
			$_SESSION['msg']='';
		endif;					


		if (count($result2)==0):
			$pauta = ($pauta=="corsim")?"cornao":"corsim";
		endif;


		foreach ($result2 as $key2 => $value) {

			$id_log		= $result2[$key2]['ID_LOG'];
			$username	= $result2[$key2]['USERNAME'];
			$osuser		= $result2[$key2]['OSUSER'];
			$machine	= $result2[$key2]['MACHINE'];
			$program	= $result2[$key2]['PROGRAM'];
			$module		= $result2[$key2]['MODULE'];
			$killed		= $result2[$key2]['KILLED'];
			$rule		= (is_null($killed))?"norule":"";


			echo "<tr class='$pauta $rule'>";
			echo "<td style='text-align:center'><a href='\admloginslog/detail/$id_log/$iddb/1/$days'><i class='fa fa-search'></i></a></td>";			
			echo "<td style='text-align:center; font-weight:bolder'>".$aliasdb."</td>";

			if ($result2[$key2]['KILLED'] == '*'):
				echo "<td style='text-align:center'><i class='fa fa-user-times'></i></a></td>";
			elseif ($result2[$key2]['KILLED'] == '#'):
				echo "<td style='text-align:center'><i class='fa fa-clock-o'></i></a></td>";			
			else:
				//echo "<td></td>";	
				echo "<td style='text-align:center'><i class='fa fa-exclamation-triangle'></i></a></td>";
			endif;

			echo "<td style='text-align:center'>".$result2[$key2]['QTD']."</td>";
			echo "<td>".$result2[$key2]['USERNAME']."</td>";
			echo "<td>".$result2[$key2]['OSUSER']."</td>";
			echo "<td>".$result2[$key2]['MACHINE']."</td>";
			echo "<td>".$result2[$key2]['PROGRAM']."</td>";
			// echo "<td>".$result2[$key2]['MODULE']."</td>";

/*			
			if ($result2[$key2]['KILLED'] == '*'):
				echo "<td style='text-align:center'><i class='fa fa-user-times'></i></a></td>";
			elseif ($result2[$key2]['KILLED'] == '#'):
				echo "<td style='text-align:center'><i class='fa fa-clock-o'></i></a></td>";			
			else:
				//echo "<td></td>";	
				echo "<td style='text-align:center'><i class='fa fa-exclamation-triangle'></i></a></td>";
			endif;
*/			

			echo "</tr>";

		};
	else:
		echo "<tr class='$pauta'>";
		echo "<td></td>";		
		echo "<td style='text-align:center'>".$aliasdb."</td>";
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