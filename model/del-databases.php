<?php 

include "class/Sql.php";

$iddb = $data['iddb'];

$conn=new Sql();

$result= $conn->sql(basename(__FILE__), 
					"DELETE FROM adm_databases WHERE iddb = :IDDB", array(":IDDB" => $iddb));

header("Location: \databases");
exit;
					
?>