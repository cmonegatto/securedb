<?php 

include "class/Sql.php";

$iduser = $data['iduser'];

$conn=new Sql();

$result= $conn->sql(basename(__FILE__), 
					"DELETE FROM adm_users where iduser=$iduser", array(":IDUSER" => $iduser));

header("Location: \users");
exit;
					
?>