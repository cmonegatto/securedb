<?php 

session_start();

include "../class/Sql.php";

$idcia		= $_POST["idcia"];
$category 	= $_POST["category"];
$descat		= $_POST["descat"];

$idcat = $_SESSION['idcat'];

$conn=new Sql();
/*
$result= $conn->sql( basename(__FILE__), 
					 "SELECT count(*) as qtd
					    FROM adm_categories
					   WHERE idcat <> :IDCAT
						 AND idcia = :IDCIA
						 AND category = :CATEGORY",
					  array(":IDCIA"=> $idcia,
					    	":IDCAT"=> $idcat,
					  		":CATEGORY"=> $category
					  )
				   );

if ($result[0]["qtd"] > 0):
	$_SESSION['msg']="Registro alterado duplicou com um já existente!";
endif;

if (isset($_SESSION['msg']) && strlen($_SESSION['msg'])>0 ):
	header("Location: \categories");
	exit;	
endif;
*/
$result= $conn->sql( basename(__FILE__), 
					 "UPDATE adm_categories
						 SET idcia = :IDCIA, category = :CATEGORY, descat = :DESCAT
					   WHERE idcat = :IDCAT",
					  array(":IDCIA"=> $idcia,
					    	":IDCAT"=> $idcat,
					  		":CATEGORY"=> $category,
							":DESCAT"=> $descat
					  )
				   );

				   
    header("Location: \categories");
	exit;	


?>