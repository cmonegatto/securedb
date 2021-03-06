<?php 

/*
* Carrega o combo de empresas: para INSERT carrega todos, para UPDATE coloca a empresa selecionada em primeiro
*/

include_once "class/Sql.php";


$conn=new Sql();

$idcia=0;

if (isset($_SESSION['idcia'])) :
    $idcia = $_SESSION['idcia'];
else:
    $idcia = $_SESSION['s_idcia'];
endif;


if ($_SESSION['s_superuser']):
    $ciacombo = $conn->sql(basename(__FILE__), "SELECT idcia, cianame FROM adm_cias WHERE idcia =  :IDCIA ORDER BY cianame", array(":IDCIA" => $idcia));
    $result   = $conn->sql(basename(__FILE__), "SELECT idcia, cianame FROM adm_cias WHERE idcia <> :IDCIA ORDER BY cianame", array(":IDCIA" => $idcia));

else:
    $ciacombo = $conn->sql(basename(__FILE__), "SELECT idcia, cianame FROM adm_cias WHERE idcia =  :IDCIA ORDER BY cianame", array(":IDCIA" => $idcia));
    $result   = $conn->sql(basename(__FILE__), "SELECT idcia, cianame FROM adm_cias WHERE 1 = 2           ORDER BY cianame");
endif;    


echo '<select class="input-large form-control" id="idcia" name="idcia" style="margin-bottom: 20px" autofocus required>';


//monta o primeiro registro conforme associado ao usuario
if ($idcia > 0 ) {
    echo "
        <option value=".$ciacombo[0]['idcia'].">".$ciacombo[0]['cianame']."</option>";  
} else {
    echo "
        <option value=''</option>";      
}


foreach ($result as $key => $value) {
    
    echo "
    <option value=".$result[$key]['idcia'].">".$result[$key]['cianame']."</option>";    

};

echo "</select>";

?>

