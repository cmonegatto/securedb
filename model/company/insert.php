<?php 

include_once '../conexao.php';

$cianame = $_POST["cianame"];
$respname = $_POST["respname"];
$email = $_POST["email"];
$celphone = $_POST["celphone"];
$status = $_POST["status"];

$status = ($status == "on")? 1: 0;


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