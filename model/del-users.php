<?php 

include "class/Sql.php";

$iduser = $data['iduser'];

$conn=new Sql();

$result= $conn->sql(basename(__FILE__), 
					"delete from adm_users where iduser=$iduser");

header("Location: \users");
exit;
					
?>