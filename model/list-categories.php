<?php 

include "class/Sql.php";

if (!$_SESSION['s_superuser']):
	$idcia = $_SESSION['s_idcia'];
else:
	$idcia = '%';
endif;	

$conn=new Sql();

$result= $conn->sql( basename(__FILE__), 
					 "SELECT cat.*, cia.cianame 
					    FROM adm_categories cat, adm_cias cia
					   WHERE cat.idcia = cia.idcia
						 AND cat.idcia like '$idcia'
					   ORDER BY cianame, category"
					);
			  

foreach ($result as $key => $value) {
	
	echo 
	"<tr>";

		$id = $result[$key]["idcat"];

		echo "<td><a href='\categories/update/$id'><i class='fa fa-pencil'></i></a></td>";

		if ($_SESSION['s_superuser']):		
			echo "<td><a href='\categories/delete/$id'><i class='fa fa-trash'></i></a></td>";
		else:
			echo "<td><a href='#'><i class='fa fa-trash'></i></a></td>";
		endif;

		if ($_SESSION['s_superuser']) echo "<td>".$result[$key]['cianame']."</td>";		
  		echo "<td>".$result[$key]['category']."</td>";
		echo "<td>".$result[$key]['descat']."</td>

	</tr>";

};


/*
foreach ($result as $key => $value) {

	echo "<tr>";

	foreach ($result[$key] as $data){
		echo "<td>$data</td>";		
	}
	
	$id = $result[$key]["idcia"];
	echo "
			<td>
				<a href='\company/insert/$id'>
					<i class='fa fa-pencil'></i>
				</a>
			</td>

			<td>
				<a href='\company/delete/$id'>
					<i class='fa fa-trash'></i>
				</a>
			</td>";


	echo "</tr>";

}
*/

?>