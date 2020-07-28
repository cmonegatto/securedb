<?php 

session_start();

include "../class/Sql.php";

$cianame = $_POST["cianame"];
$respname = $_POST["respname"];
$email = $_POST["email"];
$celphone = $_POST["celphone"];
$status = $_POST["status"];

$status = ($status == "on")? 1: 0;


$conn=new Sql();

$result= $conn->sql(basename(__FILE__), 
					"INSERT INTO adm_cias (cianame, respname, email, celphone, status)
					 VALUES (:CIANAME, :RESPNAME, :EMAIL, :CELPHONE, :STATUS)",
					 array( ":CIANAME"=> $cianame,
					 		":RESPNAME"=> $respname,
					 		":EMAIL"=> $email,
					 		":CELPHONE"=> $celphone,
					 		":STATUS"=> $status
					 	  ) 

				);

header("Location: \company/insert");
exit;	

?>