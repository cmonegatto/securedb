<?php 

include "class/Sql.php";


/* Se for SUPERUSER pega todas CIAS senão somente a do usuario 
--------------------------------------------------------------*/
if (!$_SESSION['s_superuser']):
	$idcia = $_SESSION['s_idcia'];
else:
	$idcia = '%';
endif;	
/*--------------------------------------------------------------*/


$conn=new Sql();

$result= $conn->sql( basename(__FILE__), 
					 "SELECT db.*, cat.category, cia.idcia, cia.cianame 
					    FROM adm_databases db, adm_categories cat, adm_cias cia
					   WHERE db.idcat = cat.idcat
					     AND cat.idcia = cia.idcia
						 AND cat.idcia like :IDCIA
					   ORDER BY cianame, category, dbname",
					   array(":IDCIA" => $idcia));

			  

foreach ($result as $key => $value) {
	
	echo 
	"<tr>";

		$iddb = $result[$key]["iddb"];
		$idcia = $result[$key]["idcia"];

		echo "<td><a href='\databases/update/$iddb/$idcia'><i class='fa fa-pencil'></i></a></td>";
		// echo "<td><a href='\databases/delete/$iddb'><i class='fa fa-trash'></i></a></td>";

		echo "<td><a href='\databases/delete/$iddb' onclick=\"return confirm('Tem certeza que deseja deletar esse registro?');\"><i class='fa fa-trash'></i></a></td>";


		if ($_SESSION['s_superuser']) echo "<td>".$result[$key]['cianame']."</td>";		
		echo "<td>".$result[$key]['category']."</td>";
		echo "<td>".$result[$key]['aliasdb']."</td>";
  		echo "<td>".$result[$key]['dbname']."</td>";
		echo "<td>".$result[$key]['hostname']."</td>";
		echo "<td>".$result[$key]['port']."</td>";
		echo "<td>".$result[$key]['player']."</td>";
		echo "<td>".$result[$key]['username']."</td>


	</tr>";

};



?>