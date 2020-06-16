<?php 

include "class/Sql.php";

$conn=new Sql();

$result= $conn->sql(basename(__FILE__), 
					"SELECT u.*, c.cianame 
					   FROM adm_users u, adm_cias c
					  WHERE u.idcia = c.idcia");
			  

foreach ($result as $key => $value) {
	

	$status = $result[$key]['status']==1?"Ativo":"Inativo";
	$admin = $result[$key]['admin']==1?"Sim":"Não";
	$superuser = $result[$key]['superuser']==1?"Sim":"Não";
	$dtregister = date("m-Y", strtotime($result[$key]['dtregister']));

	echo 
	"<tr>";
//		echo "<td>".$result[$key]['iduser']."</td>";		
//		echo "<td>".$result[$key]['name']."</td>";

		$id = $result[$key]["iduser"];

		echo "<td><a href='\users/update/$id'><i class='fa fa-pencil'></i></a></td>
			  <td><a href='\users/delete/$id'><i class='fa fa-trash'></i></a></td>";

		echo "<td>".$result[$key]['name']."</td>";		
		echo "<td>".$result[$key]['cianame']."</td>";
		echo "<td>".$result[$key]['login']."</td>";		
		echo "<td>".$result[$key]['email']."</td>";		
		echo "<td>".$result[$key]['celphone']."</td>";		
		echo "<td>".$status."</td>";		
		echo "<td>".$admin."</td>";		
		echo "<td>".$superuser."</td>";		
		echo "<td>".$dtregister ."</td>

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