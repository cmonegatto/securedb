<?php 

include_once "class/Sql.php";
include "function/utils.php";

//$iddb  = $data['iddb'];
$idcat = $data['idcat'];
//$days  = $data['days'] * -1;

$conn1=new Sql();
$conn2=new Sql();


$result1 = $conn1->sql( basename(__FILE__), "SELECT hostname, username, password, aliasdb, dbname, port, player
											  FROM adm_databases
											 WHERE idcat = :IDCAT
											 ORDER BY aliasdb",
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


	if (strlen($_SESSION['msg']) == 0 ):


		if ($player == 'OCI'):

			$result2= $conn2->sql( basename(__FILE__), 
									"SELECT ID_LOGIN
											,USERNAME
											,OSUSER
											,MACHINE
											, to_char(l.begin_date, 'dd/mm/yyyy hh24:mi:ss') 	as BEGIN_DATE
								  			, to_char(l.end_date, 'dd/mm/yyyy hh24:mi:ss')   	as END_DATE
											,FREETOOLS
											,SESSIONS_PER_USER
											,LOG_LOGON
											,TRACE
											,CURSOR_SHARING
											,COMMENTS
											,CREATED_BY
											,CREATED_DATE
											,LAST_UPDATED_BY
											,LAST_UPDATED_DATE
											, to_char(LAST_USED_DATE, 'dd/mm/yyyy') 	as LAST_USED_DATE
											,FLAG
									   FROM ADM_LOGINS l
									  ORDER BY USERNAME, OSUSER, MACHINE									
									"
			);


		elseif ($player == 'SQLSRV'):

			$result2= $conn2->sql( basename(__FILE__), 
									"SELECT ID_LOGIN
											,USERNAME
											,OSUSER
											,MACHINE
											, CONVERT(VARCHAR(10), l.BEGIN_DATE, 103) + ' '  + convert(VARCHAR(8), l.BEGIN_DATE, 14) as BEGIN_DATE
								  			, CONVERT(VARCHAR(10), l.END_DATE, 103) + ' '  + convert(VARCHAR(8), l.END_DATE, 14) as END_DATE
											,FREETOOLS
											,SESSIONS_PER_USER
											,LOG_LOGON
											,TRACE
											,CURSOR_SHARING
											,COMMENTS
											,CREATED_BY
											,CREATED_DATE
											,LAST_UPDATED_BY
											,LAST_UPDATED_DATE
											--,CONVERT(VARCHAR(10), LAST_USED_DATE, 103) + ' '  + convert(VARCHAR(8), LAST_USED_DATE, 14) as LAST_USED_DATE
											,CONVERT(VARCHAR(10), LAST_USED_DATE, 103)  as LAST_USED_DATE
											,FLAG
									   FROM ADM_LOGINS l
									  ORDER BY USERNAME, OSUSER, MACHINE									
									"
			);

            
                
		elseif ($player == 'MYSQL'):

                                            
			$result2= $conn2->sql( basename(__FILE__), 
									"SELECT ID_LOGIN
											,USERNAME
											,OSUSER
											,MACHINE
											, DATE_FORMAT(l.BEGIN_DATE, '%d/%m/%Y %H:%i:%s')	as BEGIN_DATE
								  			, DATE_FORMAT(l.END_DATE, '%d/%m/%Y %H:%i:%s') 	as END_DATE
											,FREETOOLS
											,SESSIONS_PER_USER
											,LOG_LOGON
											,TRACE
											,CURSOR_SHARING
											,COMMENTS
											,CREATED_BY
											,CREATED_DATE
											,LAST_UPDATED_BY
											,LAST_UPDATED_DATE
											,DATE_FORMAT(LAST_USED_DATE, '%d/%m/%Y') 	as LAST_USED_DATE
											,FLAG 
									   FROM ADM_LOGINS l
									  ORDER BY USERNAME, OSUSER, MACHINE									
									"
			);

	
		endif;

		
		if (strlen($_SESSION['msg']) > 0 ):
			echo "<td></td>";
			echo "<td style='text-align:center'>".$aliasdb."</td>";
			echo "<td></td>";
			echo "<td>*** FALHA DE DADOS/TABELAS *** </td>";
			echo "<td></td>";
			echo "<td></td>";
			echo "<td></td>";
			echo "<td></td>";
			echo "<td></td>";
			echo "<td></td>";
			echo "</tr>";
            $_SESSION['msg']='';

		endif;					



		foreach ($result2 as $key2 => $value) {


			if ($result2[$key2]['FLAG']):
				echo "<tr class='to-conf'>";
				echo "<td><i class='fa fa-question-circle fa-2x'></i></a></td>";
			else: 
				echo "<tr class=''>";
				echo "<td></td>";
			endif;
	


			echo "<td style='text-align:center'>".$aliasdb."</td>";

			echo "<td>".$result2[$key2]['ID_LOGIN']."</td>";
			echo "<td>".$result2[$key2]['USERNAME']."</td>";
			echo "<td>".$result2[$key2]['OSUSER']."</td>";
			echo "<td>".$result2[$key2]['MACHINE']."</td>";

			echo "<td>".$result2[$key2]['BEGIN_DATE']."</td>";
			echo "<td>".$result2[$key2]['LOG_LOGON']."</td>";

			echo "<td>".$result2[$key2]['FREETOOLS']."</td>";
			echo "<td>".$result2[$key2]['LAST_USED_DATE']."</td>";

			/*
			echo "<td>".$result2[$key2]['BEGIN_DATE']."</td>";
			echo "<td>".$result2[$key2]['END_DATE']."</td>";
			echo "<td>".$result2[$key2]['SESSIONS_PER_USER']."</td>";
			echo "<td>".$result2[$key2]['LOG_LOGON']."</td>";
			echo "<td>".$result2[$key2]['TRACE']."</td>";
			echo "<td>".$result2[$key2]['COMMENTS']."</td>";
			echo "<td>".$result2[$key2]['CREATED_BY']."</td>";
			echo "<td>".$result2[$key2]['CREATED_DATE']."</td>";
			echo "<td>".$result2[$key2]['LAST_UPDATED_BY']."</td>";
			echo "<td>".$result2[$key2]['LAST_UPDATED_DATE']."</td>";
			echo "<td>".$result2[$key2]['LAST_USED_DATE']."</td>";
            */

			echo "</tr>";

		};
	else:
		echo "<td></td>";
		echo "<td style='text-align:center'>".$aliasdb."</td>";
		echo "<td></td>";
		echo "<td>*** FALHA DE CONEXÃO *** </td>";
		echo "<td></td>";
		echo "<td></td>";
		echo "<td></td>";
		echo "<td></td>";
		echo "<td></td>";
		echo "<td></td>";
		echo "</tr>";
        $_SESSION['msg']='';
	endif;


};

?>