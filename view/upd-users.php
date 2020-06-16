<?php

//session_start();

include_once 'include/header_inc.php';
include_once 'include/menu_inc.php';
include_once 'class/Sql.php';

$iduser = $data['iduser'];

$_SESSION['iduser'] = $iduser;


$conn=new Sql();

$result = $conn->select("SELECT * FROM adm_users WHERE iduser={$iduser}");

$idcia		= $result[0]["idcia"];
$nome 		= $result[0]["name"];
$login		= $result[0]["login"];
$email		= $result[0]["email"];
$telefone 	= $result[0]["celphone"];
$senha		= $result[0]["password"];

$status		= ($result[0]["status"])     ? "checked" : "";
$admin		= ($result[0]["admin"])      ? "checked" : "";
$superuser	= ($result[0]["superuser"])  ? "checked" : "";


$_SESSION['idcia'] = $idcia;



?>


<div class="container">
    <div>
        <h3>Cadastro de Usuários (edit)</h3>         
        <hr />
    </div>

    <form method="post" action="\model/upd-users.php">


        <div style="padding-bottom:5px; padding-right:0px" class="row col-md-8">   
            <label for="idcia">Empresa</label>

            <?php 
                include_once 'model/list-users-ciabox.php';
            ?>

        </div>


        <div class="row">   

            <div class="form-group col-md-4">
                <label for="nome">Nome</label>
                <input type="text" name="nome" class="form-control" id="nome" value="<?php echo $nome?>" required>
            </div>

            <div class="form-group col-md-4">
                <label  for="login">Login</label>
                <input type="text" name="login" class="form-control" id="login" value="<?php echo $login?>" required>
            </div>          
            
        </div>


        <div class="row">       
            <div class="form-group col-md-4">
                <label  for="email">E-mail</label>
                <input type="email" name="email" class="form-control" id="email" value="<?php echo $email?>" required>
            </div>

            <div class="form-group col-md-4">
                <label  for="telefone">Telefone</label>
                <input type="tel" name="telefone" class="form-control" id="telefone" value="<?php echo $telefone?>" required>
            </div>
        </div>     


        <!--
        <div class="row">     

            <div class="form-group col-md-4">
                <label  for="senha">Senha</label>
                <input type="password" name="senha" class="form-control" id="senha" value="<?php echo $senha?>" required>
            </div>

            <div class="form-group col-md-4">
                <label  for="confsenha">Confirme a senha</label>
                <input type="password" name="confsenha" class="form-control" id="confsenha"  required>
            </div>

        </div>
-->

        <div class="row col-md-8">       

            <div class="custom-control custom-checkbox col-md-2">
                <input type="checkbox" class="custom-control-input" name="status" id="status" <?php echo $status ?> >
                <label class="custom-control-label" for="status">Ativo</label>
            </div>

            <div class="custom-control custom-checkbox col-md-2">
                <input type="checkbox" class="custom-control-input" name="admin" id="admin" <?php echo $admin ?> >
                <label class="custom-control-label" for="admin">Admin</label>
            </div>

            <div class="custom-control custom-checkbox col-md-2">
                <input type="checkbox" class="custom-control-input" name="superuser" id="superuser" <?php echo $superuser ?> >
                <label class="custom-control-label" for="superuser">Super-User</label>
            </div>

        </div>

        <hr />

        <div class="row">       
            <div class="input-field col-md-4">
                <input type="submit" value="Salvar" class="btn btn-primary">                
                <a href="\users" class="btn btn-secondary">Voltar</a>
            </div>
        </div>

    </form>

</div>


<?php include_once 'include/footer_inc.php' ?>
