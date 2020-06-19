<?php 

include "class/Sql.php";

$iddb = $data['iddb'];

$conn=new Sql();

$result= $conn->sql(basename(__FILE__), 
					"delete from adm_databases where iddb=$iddb");

header("Location: \databases");
exit;
					
?>