<?php 

include "class/Sql.php";

$conn = new Sql();

$result = $conn->sql(basename(__FILE__), "SELECT * FROM adm_cias ORDER BY cianame");

foreach ($result as $key => $value) {

	$status = $result[$key]['status']==1?"Ativo":"Inativo";
	$dtregister = date("m-Y", strtotime($result[$key]['dtregister']));

	echo 
	"<tr>";
//		echo "<td>".$result[$key]['idcia']."</td>";		
//		echo "<td>".$result[$key]['cianame']."</td>";		

		$id = $result[$key]["idcia"];

		echo "<td><a href='\company/update/$id'><i class='fa fa-pencil'></i></a></td>";

		if ($_SESSION['s_superuser']):
			echo "<td><a href='\company/delete/$id' onclick=\"return confirm('Tem certeza que deseja deletar esse registro?');\"><i class='fa fa-trash'></i></a></td>";
		else:
			echo "<td><a href='#'><i class='fa fa-trash'></i></a></td>";
		endif;

		if ($_SESSION['s_superuser']) echo "<td>".$result[$key]['cianame']."</td>";
		echo "<td>".$result[$key]['respname']."</td>";		
		echo "<td>".$result[$key]['email']."</td>";		
		echo "<td>".$result[$key]['celphone']."</td>";		
		echo "<td>".$status."</td>";		
		echo "<td>".$dtregister."</td>


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