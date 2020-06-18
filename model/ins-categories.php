<?php 

session_start();

include "../class/Sql.php";

$idcia		= $_POST["idcia"];
$category 	= $_POST["category"];
$descat		= $_POST["descat"];

$conn=new Sql();


$result= $conn->sql( basename(__FILE__), 
					 "SELECT count(*) as qtd
					    FROM adm_categories
					   WHERE idcia = :IDCIA
						 AND category = :CATEGORY",
					  array(":IDCIA"=> $idcia,
					  		":CATEGORY"=> $category
					  )
				   );

if ($result[0]["qtd"] > 0):
	$_SESSION['msg']="Registro já existe!";
endif;

if (isset($_SESSION['msg']) && strlen($_SESSION['msg'])>0 ):
    header("Location: \categories/insert");
	exit;	
endif;


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