<?php 

session_start();

include "../class/Sql.php";

$idcia = $_SESSION['idcia'];
$cianame = $_POST["cianame"];
$respname = $_POST["respname"];
$email = $_POST["email"];
$celphone = $_POST["celphone"];
$status = $_POST["status"];

$status = ($status == "on")? 1: 0;


$conn=new Sql();
/*
$result= $conn->select("UPDATE adm_cias 
						   SET cianame = '$cianame', respname = '$respname', email = '$email', celphone = $celphone, status = $status
						 WHERE idcia={$idcia}");
*/

$result= $conn->sql( basename(__FILE__),
					 "UPDATE adm_cias 
						 SET cianame = '$cianame', respname = '$respname', email = '$email', celphone = $celphone, status = $status
					   WHERE idcia=:IDCIA", array(":IDCIA"=>$idcia));


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

$cianame = $_POST["cianame"];
$respname = $_POST["respname"];
$email = $_POST["email"];
$celphone = $_POST["celphone"];
$exampleRadios = $_POST["exampleRadios"];

$status = ($exampleRadios == "ativo")? 1: 0;

*/

?>