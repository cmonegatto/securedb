<?php 

session_start();
include "../class/Sql.php";

/*
if (!isset($data)) {
    $operation = "INSERT";
} else {
    $operation = "UPDATE";
    $_SESSION["idcia"] = $data['idcia'];
};
*/

$idcia = $_SESSION["idcia"];


$conn=new Sql();

$result= $conn->select("SELECT * FROM adm_cias WHERE idcia={$idcia}");

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
if (!$queryUpdate):
	echo "erro no INSERT! <br>";
	echo $sql;
endif;


if( mysqli_affected_rows($link) >0):
    header("Location: \company/insert");
	exit;	
endif;

$cianame = $_GET["cianame"];
$respname = $_GET["respname"];
$email = $_GET["email"];
$celphone = $_GET["celphone"];
$exampleRadios = $_GET["exampleRadios"];

$status = ($exampleRadios == "ativo")? 1: 0;

*/

?>