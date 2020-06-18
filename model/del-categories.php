<?php 

include "class/Sql.php";

$idcat = $data['idcat'];

$conn=new Sql();

$result= $conn->sql(basename(__FILE__), 
					"delete from adm_categories where idcat=$idcat");

header("Location: \categories");
exit;
					
?>