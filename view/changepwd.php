<?php

//session_start();

include_once 'include/header_inc.php';
include_once 'include/menu_inc.php';

?>


<div class="container">
    <div>
        <h3>Alteração de Senha</h3>         
        <hr />
    </div>

    <form method="post" action="\model/upd-changepwd.php">    

        <div class="row">   

            <div class="form-group col-md-4">
                <label for="nome">Nome</label>
                <input type="text" name="nome" class="form-control" id="nome" value="<?php echo $_SESSION['s_nameuser']?>" disabled>
            </div>

            <div class="form-group col-md-4">
                <label  for="login">Login</label>
                <input type="text" name="login" class="form-control" id="login" value="<?php echo $_SESSION['s_login']?>" disabled>
            </div>          
            
        </div>


        <div class="row">       
            <div class="form-group col-md-4">
                <label  for="email">E-mail</label>
                <input type="email" name="email" class="form-control" id="email" value="<?php echo $_SESSION['s_emailuser']?>" disabled>
            </div>

            <div class="form-group col-md-4">
                <label  for="telefone">Telefone</label>
                <input type="tel" name="telefone" class="form-control" id="telefone" placeholder="(99) 9999-9999" data-mask="(00) 00000-0000" value="<?php echo $_SESSION['s_celphone']?>" disabled>
            </div>
        </div>     


        <div class="row">     

            <div class="form-group col-md-4">
                <label  for="senha">Senha</label>
                <input type="password" name="senha" class="form-control" id="senha" minlength="6" placeholder="digite sua nova senha" autofocus required>
            </div>

            <div class="form-group col-md-4">
                <label  for="senha2">Confirme a Senha</label>
                <input type="password" name="senha2" class="form-control" id="senha2" minlength="6" placeholder="confirme sua nova senha" required >
            </div>

        </div>

      

        <hr />

        <div class="row">       
            <div class="input-field col-md-4">
                <input type="submit" value="Salvar" class="btn btn-primary">                
                <a href="\" class="btn btn-secondary">Sair</a> <br><br>
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
