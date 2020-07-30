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


	$result= $conn->sql( basename(__FILE__), 
						"SELECT count(*) as qtd, ll.username, ll.osuser, ll.machine, ll.program, ll.module, decode(tk.username,'','N','S') as to_kill,
								adm_logins_fun(ll.username, ll.osuser, '%' || substr(ll.machine, instr(ll.machine, '\')+1) || '%', ll.program, ll.module) as rule
						   FROM adm_logins_log ll
						   LEFT JOIN adm_logins_to_kill tk
						     ON ll.username = tk.username
						  GROUP BY  ll.username, ll.osuser, ll.machine, ll.program, ll.module, decode(tk.username,'','N','S')
						  ORDER BY adm_logins_fun(ll.username, ll.osuser, '%' || substr(ll.machine, instr(ll.machine, '\')+1) || '%', ll.program, ll.module) DESC, decode(tk.username,'','N','S'), 1 DESC"
						);

/*
						"SELECT count(*) as qtd, ll.username, ll.osuser, ll.machine, ll.program, ll.module, decode(tk.username,'','N','S') as to_kill,
								adm_logins_fun(ll.username, ll.osuser, '%' || substr(ll.machine, instr(ll.machine, '\')+1) || '%', ll.program, ll.module) as rule
						   FROM adm_logins_log ll
						   LEFT JOIN adm_logins_to_kill tk
						     ON ll.username = tk.username
						  WHERE adm_logins_fun(ll.username, ll.osuser, '%' || substr(ll.machine, instr(ll.machine, '\')+1) || '%', ll.program, ll.module) = 1
						     OR decode(tk.username,'','N','S') = 'N'
						   --OR (adm_logins_fun(ll.username, ll.osuser, '%' || substr(ll.machine, instr(ll.machine, '\')+1) || '%', ll.program, ll.module) = 0 AND decode(tk.username,'','N','S') = 'N')
						  GROUP BY  ll.username, ll.osuser, ll.machine, ll.program, ll.module, decode(tk.username,'','N','S')
						  ORDER BY adm_logins_fun(ll.username, ll.osuser, '%' || substr(ll.machine, instr(ll.machine, '\')+1) || '%', ll.program, ll.module) DESC, decode(tk.username,'','N','S'), 1 DESC"
						);
*/

	foreach ($result as $key => $value) {

		

		if ($result[$key]['RULE'] && $result[$key]['TO_KILL']=='S'):
			echo "<tr class='todie'>";
		elseif ($result[$key]['RULE'] ):
			echo "<tr class='norule'>";
		else: 
			echo "<tr class='rule'>";
		endif;

		$username	= $result[$key]['USERNAME'];
		$osuser		= $result[$key]['OSUSER'];
		$machine	= $result[$key]['MACHINE'];
		$program	= $result[$key]['PROGRAM'];
		$module		= $result[$key]['MODULE'];
		//$killed		= $result[$key]['KILLED'];


		if ($result[$key]['TO_KILL'] == "S" && $result[$key]['USERNAME']):
			echo "<td style='text-align:center'><a href='\admlogins/lockuser/$username'><i class='fa fa-lock'></i></a></td>";
			//echo "<td style='text-align:center'><i class='fa fa-lock'></i></a></td>";
		elseif ($result[$key]['TO_KILL'] == "N" && $result[$key]['USERNAME']):
			echo "<td style='text-align:center'><a href='\admlogins/lockuser/$username'><i class='fa fa-unlock'></i></a></td>";
			//echo "<td style='text-align:center'><i class='fa fa-unlock'></i></a></td>";
		else:
			echo "<td></td>";			
		endif;


		$id = $username . '/' . str_replace('\\','*',$osuser) .'/'. str_replace('\\','*',$machine) .'/'. str_replace('\\','*',$program) .'/'. str_replace('\\','*',$module);



		//echo "<td><a href='\admloginslog/detail/$username/$osuser/$machine/$program/$module/$killed'><i class='fa fa-search'></i></a></td>";

		echo "<td style='text-align:center'><a href='\admloginslog/detail/$id'><i class='fa fa-search'></i></a></td>";

		if ($result[$key]['RULE'] ):
			echo "<td style='text-align:center'><a href='\admloginslog/$id'><i class='fa fa-thumbs-up'></i></a></td>";
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