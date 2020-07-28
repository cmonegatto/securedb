<?php 

session_start();

include "../class/Sql.php";

$idcia		= $_POST["idcia"];
$category 	= $_POST["category"];
$descat		= $_POST["descat"];

$conn=new Sql();

$result= $conn->sql( basename(__FILE__), 
					 "INSERT INTO adm_categories (idcia, category, descat)
					  VALUES (:IDCIA, :CATEGORY, :DESCAT)",
					  array(":IDCIA"=> $idcia,
					  		":CATEGORY"=> $category,
							":DESCAT"=> $descat
					  		)
				   );


    header("Location: \categories/insert");
	exit;	


?>