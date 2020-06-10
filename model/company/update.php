<?php 

session_start();

include "../../class/Sql.php";

$idcia = $_SESSION['idcia'];
$cianame = $_GET["cianame"];
$respname = $_GET["respname"];
$email = $_GET["email"];
$celphone = $_GET["celphone"];
$exampleRadios = $_GET["exampleRadios"];
$status = ($exampleRadios == "ativo")? 1: 0;


$conn=new Sql();
/*
$result= $conn->select("UPDATE adm_cias 
						   SET cianame = '$cianame', respname = '$respname', email = '$email', celphone = $celphone, status = $status
						 WHERE idcia={$idcia}");
*/

$result= $conn->select("UPDATE adm_cias 
						   SET cianame = '$cianame', respname = '$respname', email = '$email', celphone = $celphone, status = $status
						 WHERE idcia=:IDCIA", array(":IDCIA"=>$idcia));



var_dump($result);
die();

if (!$result) {
	echo "ERRO NA ATUALIZAÇÃO";
	die();
} else {

}


header("Location: \company");



/*
if (!$queryUpdate):
	echo "erro no INSERT! <br>";
	echo $sql;
endif;


if( mysqli_affected_rows($link) >0):
    header("Location: \company/insert");
	exit;	
endif;

$cianame = $_GET["cianame"];
$respname = $_GET["respname"];
$email = $_GET["email"];
$celphone = $_GET["celphone"];
$exampleRadios = $_GET["exampleRadios"];

$status = ($exampleRadios == "ativo")? 1: 0;

*/

?>