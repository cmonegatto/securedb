<?php 

/*
Ver aula 64 SELECT DAO tem td lá..

*/

include "Usuario.php";
include "Sql.php";


$x=new Sql();

$result= $x->select("select * from adm_cias where idcia=:IDCIA", array(
    ":IDCIA"=>65599)
);

//var_dump($result);

echo json_encode($result);
echo "<br>";


$user = new Usuario();
$user->loadbyId(3);
echo $user;


?>

