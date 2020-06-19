<?php

//session_start();

include_once 'include/header_inc.php';
include_once 'include/menu_inc.php';

?>


<div class="container">

    <div>
        <h3>Cadastro de Banco de Dados (new)</h3>         
        <hr />
    </div>

    <form method="post" action="\model/ins-databases.php">

    <div class="row">
            <div style="margin-left:2px" class="row col-md-12">   
                <label for="idcat">Categoria DB</label>
            </div>

            <?php 
                //$_SESSION['idcia'] = 0;
                include_once 'model/list-databases-catbox.php';
            ?>

        </div>


        <div class="row">   

            <div class="form-group col-md-4">
                <label for="dbname">Nome DB</label>
                <input type="text" name="dbname" class="form-control" id="dbname" required>
            </div>

            <div class="form-group col-md-4">
                <label  for="hostname">HOSTNAME</label>
                <input type="text" name="hostname" class="form-control" id="hostname" required>
            </div>          

        </div>


        <div class="row">

            <div class="form-group col-md-4">
                <label  for="username">Username</label>
                <input type="text" name="username" class="form-control" id="username" required>
            </div>          

            <div class="form-group col-md-4">
                <label  for="password">Senha</label>
                <input type="text" name="password" class="form-control" id="password" required>
            </div>          

        </div>


        <hr />

        <div class="row">       
            <div class="input-field col-md-4">
                <input type="submit" value="Salvar" class="btn btn-primary">                
                <a href="\databases" class="btn btn-secondary">Voltar</a>
            </div>
        <div>


        <?php

            if(isset($_SESSION['msg'])):
                echo "<span style='color:red'> {$_SESSION['msg']}</span>";
                $_SESSION['msg']="";
            endif;
        ?>


    </form>

</div>


<?php include_once 'include/footer_inc.php' ?>
