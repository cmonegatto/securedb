<?php 

include_once 'model/conexao.php';

$iduser = $data['iduser'];


$sql = "delete from adm_users where iduser=$iduser";

$queryUpdate = $link->query($sql);


if (!$queryUpdate):
	echo "erro no DELETE! <br>";
	echo $sql;
endif;

if( mysqli_affected_rows($link) >0):
	header("Location: \user");
	exit;
	
endif;

?>