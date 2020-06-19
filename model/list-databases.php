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
						 AND cat.idcia like '$idcia'
					   ORDER BY cianame, category, dbname"
					);
			  

foreach ($result as $key => $value) {
	
	echo 
	"<tr>";

		$iddb = $result[$key]["iddb"];
		$idcia = $result[$key]["idcia"];

		echo "<td><a href='\databases/update/$iddb/$idcia'><i class='fa fa-pencil'></i></a></td>";

		if ($_SESSION['s_superuser']):		
			echo "<td><a href='\databases/delete/$iddb'><i class='fa fa-trash'></i></a></td>";
		else:
			echo "<td><a href='#'><i class='fa fa-trash'></i></a></td>";
		endif;

		if ($_SESSION['s_superuser']) echo "<td>".$result[$key]['cianame']."</td>";		
  		echo "<td>".$result[$key]['dbname']."</td>";
		echo "<td>".$result[$key]['category']."</td>";
		echo "<td>".$result[$key]['hostname']."</td>";
		echo "<td>".$result[$key]['username']."</td>";
		echo "<td>".$result[$key]['password']."</td>

	</tr>";

};



?>