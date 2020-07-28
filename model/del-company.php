<?php 

include "class/Sql.php";

$idcia = $data['idcia'];

$conn=new Sql();


$result= $conn->sql(basename(__FILE__), 
					"DELETE FROM adm_cias WHERE idcia = :IDCIA", array(":IDCIA" => $idcia));
					
header("Location: \company");
exit;
	



?>