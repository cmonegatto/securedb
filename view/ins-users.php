<?php

//session_start();

include_once 'include/header_inc.php';
include_once 'include/menu_inc.php';

?>


<div class="container">
    <div>
        <h3>Cadastro de Usuários (new)</h3>         
        <hr />
    </div>

    <form method="post" action="\model/ins-users.php">

        <div style="padding-bottom:5px; padding-right:0px" class="row col-md-8">   
            <label for="idcia">Empresa</label>

            <?php 
                //$_SESSION['idcia'] = 0;
                include_once 'model/list-cia-combo.php';
            ?>

        </div>


        <div class="row">   

            <div class="form-group col-md-4">
                <label for="nome">Nome</label>
                <input type="text" name="nome" class="form-control" id="nome" required>
            </div>

            <div class="form-group col-md-4">
                <label  for="login">Login</label>
                <input type="text" name="login" class="form-control" id="login" required>
            </div>          
            
        </div>


        <div class="row">       
            <div class="form-group col-md-4">
                <label  for="email">E-mail</label>
                <input type="email" name="email" class="form-control" id="email" required>
            </div>

            <div class="form-group col-md-4">
                <label  for="telefone">Telefone</label>
                <input type="tel" name="telefone" class="form-control" id="telefone" placeholder="(99) 9999-9999" data-mask="(00) 00000-0000" required>
            </div>
        </div>     


        <div class="row">     

            <div class="form-group col-md-4">
                <label  for="senha">Senha</label>
                <input type="password" name="senha" class="form-control" id="senha" required>
            </div>

<!--
            <div class="form-group col-md-4">
                <label  for="confsenha">Confirme a senha</label>
                <input type="password" name="confsenha" class="form-control" id="confsenha"  required>
            </div>
-->

        </div>


        <div class="row col-md-8">       

            <div class="custom-control custom-checkbox col-md-2">
                <input type="checkbox" class="custom-control-input" name="status" id="status" checked>
                <label class="custom-control-label" for="status">Ativo</label>
            </div>

            <div class="custom-control custom-checkbox col-md-2">
                <input type="checkbox" class="custom-control-input" name="admin" id="admin">
                <label class="custom-control-label" for="admin">Admin</label>
            </div>

            <div class="custom-control custom-checkbox col-md-2">
                <input type="checkbox" class="custom-control-input" name="superuser" id="superuser" <?= ($_SESSION['s_superuser'])?"":"disabled"?> >
                <label class="custom-control-label" for="superuser">Super-User</label>
            </div>

        </div>

        <hr />

        <div class="row">       
            <div class="input-field col-md-4">
                <input type="submit" value="Salvar" class="btn btn-primary">                
                <a href="\users" class="btn btn-secondary">Voltar</a>
                <!-- <input type="button" value="Voltar" class="btn btn-secondary" onClick="JavaScript: window.history.back();"> -->
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
