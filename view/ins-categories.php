<?php

//session_start();

include_once 'include/header_inc.php';
include_once 'include/menu_inc.php';

?>


<div class="container">
    <div>
        <h3>Cadastro de Categorias (new)</h3>         
        <hr />
    </div>

    <form method="post" action="\model/ins-categories.php">

        <div class="row">        

            <div style="margin-left:2px" class="row col-md-8">               
                <label for="idcia">Empresa</label>

                <?php 
                    //$_SESSION['idcia'] = 0;
                    include_once 'model/list-users-ciabox.php';
                ?>

            </div>
        </div>


        <div class="row">   

            <div class="form-group col-md-4">
                <label for="category">Categoria</label>
                <input type="text" name="category" class="form-control" id="category" required>
            </div>

            <div class="form-group col-md-4">
                <label  for="descat">Descrição da Categoria</label>
                <input type="text" name="descat" class="form-control" id="descat" required>
            </div>          
            
        </div>

        <hr />

        <div class="row">       
            <div class="input-field col-md-4">
                <input type="submit" value="Salvar" class="btn btn-primary">                
                <a href="\categories" class="btn btn-secondary">Voltar</a>
            </div>
        </div>


        <?php

            if(isset($_SESSION['msg'])):
                echo "<span style='color:red'> {$_SESSION['msg']}</span>";
                $_SESSION['msg']="";
            endif;
        ?>


    </form>

</div>


<?php include_once 'include/footer_inc.php' ?>
