<?php 

include "class/Sql.php";

$conn=new Sql();

$result= $conn->select("SELECT * FROM adm_cias ORDER BY cianame");

foreach ($result as $key => $value) {

	$status = $result[$key]['status']==1?"Ativo":"Inativo";

	echo 
	"<tr>";
		echo "<td>".$result[$key]['idcia']."</td>";		
		echo "<td>".$result[$key]['cianame']."</td>";		
		echo "<td>".$result[$key]['respname']."</td>";		
		echo "<td>".$result[$key]['email']."</td>";		
		echo "<td>".$result[$key]['celphone']."</td>";		
		echo "<td>".$status."</td>";		
		echo "<td>".$result[$key]['dtregister']."</td>";		

		$id = $result[$key]["idcia"];

		echo "<td><a href='\company/update/$id'><i class='fa fa-pencil'></i></a></td>
			<td><a href='\company/delete/$id'><i class='fa fa-trash'></i></a></td>
	</tr>";

}


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