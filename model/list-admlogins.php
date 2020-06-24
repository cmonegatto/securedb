<?php 

include_once "class/Sql.php";

$conn=new Sql("oci", "localhost", "administrador", "adm", "XE");


if (isset($_SESSION['iddb']) && $_SESSION['iddb'] >0 ) :

	//$_SESSION['iddb'] = 0;

	$result= $conn->sql( basename(__FILE__), 
						"SELECT count(*) as qtd, username, osuser, machine, program, killed
							FROM adm_logins_log
						GROUP BY  username, osuser, machine, program, killed
						ORDER BY 1 desc"
						
						);
			  

	foreach ($result as $key => $value) {
		
		if ($result[$key]['USERNAME'] == 'ADMINISTRADOR'):
			echo 
			"<tr class='dif'>";
		else:
			"<tr>";
		endif;


		echo "<td><a href='\databases/update/1/1'><i class='fa fa-pencil'></i></a></td>";
		echo "<td><a href='\databases/delete/1'><i class='fa fa-trash'></i></a></td>";
  		echo "<td>".$result[$key]['QTD']."</td>";
		echo "<td>".$result[$key]['USERNAME']."</td>";
		echo "<td>".$result[$key]['OSUSER']."</td>";
		echo "<td>".$result[$key]['MACHINE']."</td>";
		echo "<td>".$result[$key]['PROGRAM']."</td>";
		echo "<td>".$result[$key]['KILLED']."</td>";
		echo "<td><a href='\databases/delete/1'><i class='fa fa-unlock'></i></a></td>


		</tr>";

	};

endif;

?>