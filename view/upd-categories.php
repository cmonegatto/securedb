<?php

//session_start();

include_once 'include/header_inc.php';
include_once 'include/menu_inc.php';
include_once 'class/Sql.php';

$idcat = $data['idcat'];

$_SESSION['idcat'] = $idcat;


$conn=new Sql();

$result = $conn->select("SELECT * FROM adm_categories WHERE idcat={$idcat}");

$idcat		= $result[0]["idcat"];
$idcia		= $result[0]["idcia"];
$category	= $result[0]["category"];
$descat		= $result[0]["descat"];

$_SESSION['idcia'] = $idcia;

?>


<div class="container">
    <div>
        <h3>Cadastro de Categorias (edit)</h3>         
        <hr />
    </div>

    <form method="post" action="\model/upd-categories.php">

        <div style="padding-bottom:5px; padding-right:0px" class="row col-md-8">   
            <label for="idcia">Empresa</label>

            <?php 
                //$_SESSION['idcia'] = 0;
                include_once 'model/list-cia-combo.php';
            ?>

        </div>


        <div class="row">   

            <div class="form-group col-md-4">
                <label for="category">Categoria</label>
                <input type="text" name="category" class="form-control" id="category" value="<?php echo $category?>" required>
            </div>

            <div class="form-group col-md-4">
                <label  for="descat">Descrição da Categoria</label>
                <input type="text" name="descat" class="form-control" id="descat" value="<?php echo $descat?>" required>
            </div>          
            
        </div>

        <hr />

        <div class="row">       
            <div class="input-field col-md-4">
                <input type="submit" value="Salvar" class="btn btn-primary">                
                <a href="\categories" class="btn btn-secondary">Voltar</a>
            </div>
        </div>

    </form>

</div>




<?php include_once 'include/footer_inc.php' ?>
