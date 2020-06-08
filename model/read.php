<?php 

include_once 'conexao.php';

$querySelect = $link->query("select * from adm_cias");

while ($registros = $querySelect->fetch_assoc()):
	$idcia = $registros['idcia'];
	$cianame = $registros['cianame'];
	$respname = $registros['respname'];
    $email = $registros['email'];
    $celphone = $registros['celphone'];
    $status = $registros['status'];
	$dtregister = date("d/m/Y", strtotime($registros['dtregister']));
    

	echo 
		"
		<tr>

			<td>$idcia</td>
			<td>$cianame </td>
			<td>$respname</td>
			<td>$email</td>
			<td>$celphone</td>
			<td>$status</td>
			<td>$dtregister</td>              

			<td>
				<a href='\company/insert/$idcia'>
					<i class='fa fa-pencil'></i>
				</a>
			</td>

			<td>
				<a href='\company/delete/$idcia'>
					<i class='fa fa-trash'></i>
				</a>
			</td>


		</tr>
		";
		  

endwhile;

 ?>