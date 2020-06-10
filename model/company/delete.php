<?php 

include_once 'model/conexao.php';

$idcia = $data['idcia'];

$sql = "delete from adm_cias where idcia=$idcia";

$queryUpdate = $link->query($sql);


if (!$queryUpdate):
	echo "erro no DELETE! <br>";
	echo $sql;
endif;

if( mysqli_affected_rows($link) >0):
	header("Location: \company");
	exit;
	
endif;

?>