<?php 

include "class/Sql.php";

$idcat = $data['idcat'];

$conn=new Sql();


$result= $conn->sql(basename(__FILE__), 
					"DELETE FROM adm_categories WHERE idcat = :IDCAT", array(":IDCAT" => $idcat));

					header("Location: \categories");
exit;
					
?>