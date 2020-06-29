<?php 

include_once "class/Sql.php";
include "function/utils.php";

$iddb  = $data['iddb'];
$idcat = $data['idcat'];

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


if (isset($_SESSION['iddb']) && $_SESSION['iddb'] >0 ) :

	//$_SESSION['iddb'] = 0;

	$result= $conn->sql( basename(__FILE__), 
						"SELECT count(*) as qtd, ll.username, ll.osuser, ll.machine, ll.program, ll.module, ll.killed, decode(tk.username,'','N','S') as to_kill,
								adm_logins_fun(ll.username, ll.osuser, ll.machine, ll.program, ll.module) as kill
						   FROM adm_logins_log ll
						   LEFT JOIN adm_logins_to_kill tk
						     ON ll.username = tk.username
						  GROUP BY  ll.username, ll.osuser, ll.machine, ll.program, ll.module, ll.killed, decode(tk.username,'','N','S')
						  ORDER BY 1 desc"						
						);
			  

	foreach ($result as $key => $value) {
		
		if ($result[$key]['KILL']):
			echo "<tr class='nok'>";
		else:
			echo "<tr class='ok'>";
		endif;


		if ($result[$key]['TO_KILL'] == "S"):
			echo "<td><a href='#'><i class='fa fa-lock'></i></a></td>";
		else:
			echo "<td><a href='#'><i class='fa fa-unlock'></i></a></td>";
		endif;


		echo "<td><a href='#'><i class='fa fa-trash'></i></a></td>";
  		echo "<td>".$result[$key]['QTD']."</td>";
		echo "<td>".$result[$key]['USERNAME']."</td>";
		echo "<td>".$result[$key]['OSUSER']."</td>";
		echo "<td>".$result[$key]['MACHINE']."</td>";
		echo "<td>".$result[$key]['PROGRAM']."</td>";
		echo "<td>".$result[$key]['MODULE']."</td>";
		echo "<td>".$result[$key]['KILLED']."</td>";



		echo "</tr>";

	};

endif;

?>