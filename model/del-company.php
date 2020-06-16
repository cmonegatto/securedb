<?php 

include "class/Sql.php";

$idcia = $data['idcia'];

$conn=new Sql();

$result= $conn->sql(basename(__FILE__), 
					"delete from adm_cias where idcia=$idcia");

header("Location: \company");
exit;
	



?>