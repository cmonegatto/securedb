<?php 

include_once '../conexao.php';

$cianame = $_GET["cianame"];
$respname = $_GET["respname"];
$email = $_GET["email"];
$celphone = $_GET["celphone"];
$exampleRadios = $_GET["exampleRadios"];

$status = ($exampleRadios == "ativo")? 1: 0;


$sql = "INSERT INTO adm_cias (cianame, respname, email, celphone, status)
             VALUES ('$cianame', '$respname', '$email', $celphone, $status)";

$queryUpdate = $link->query($sql);


if (!$queryUpdate):
	echo "erro no INSERT! <br>";
	echo $sql;
endif;


if( mysqli_affected_rows($link) >0):
    header("Location: \company/insert");
	exit;	
endif;


?>