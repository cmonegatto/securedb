<?php

session_start();

include_once 'include/header_inc.php';
include_once 'include/menu_inc.php';
include_once 'class/Sql.php';

$idcia = $data['idcia'];

$_SESSION['idcia'] = $idcia;

$conn=new Sql();

$result = $conn->select("SELECT * FROM adm_cias WHERE idcia={$idcia}");

$cianame = $result[0]['cianame'];
$respname = $result[0]['respname'];
$email = $result[0]['email'];
$celphone = $result[0]['celphone'];
$status = $result[0]['status'];


?>
   
<div class="container">

    <div class="row">

        <div class="col-sm-8">
            
            <h3>Cadastro de Empresas (edit)</h3>  
            <hr />

            <form method="post" action="\model/upd-company.php">
                <div class="form-group">
                    <label for="nome">Nome da Companhia</label>
                    <input type="text" class="form-control" name="cianame" id="cianame" value="<?php echo $cianame?>" maxlength="50" required >
                </div>

                <div class="form-group">
                    <label for="respname">Nome do Responsável</label>
                    <input type="text" class="form-control" name="respname" id="respname" value="<?php echo $respname?>"maxlength="50" required>
                </div>          

                <div class="form-group">
                    <label for="email">E-mail</label>
                    <input type="email" class="form-control" name="email" id="email" maxlength="50" value="<?php echo $email?>" required>
                </div>          

                <div class="form-group">
                    <label for="celphone">Número telefone celular</label>
                    <input type="tel" class="form-control" name="celphone" id="celphone" maxlength="20" value="<?php echo $celphone?>" required>

                </div>          

<!--
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios1" value="ativo" <?php echo ($status==1)? "checked":""?> >
                    <label class="form-check-label" for="exampleRadios1">Ativo</label>
                </div>


                <div class="form-check">
                    <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios2" value="inativo" <?php echo ($status==0)? "checked":""?> >
                    <label class="form-check-label" for="exampleRadios2">Invativo</label>
                </div>
-->

                <div class="custom-control custom-checkbox col-md-2">
                    <input type="checkbox" class="custom-control-input" name="status" id="status" <?php echo ($status==1)? "checked":""?>>
                    <label class="custom-control-label" for="status">Ativo</label>
                </div>

                <hr />

                <div class="input-field">
                    <input type="submit" value="Salvar" class="btn btn-primary">                
                    <a href="\company" class="btn btn-secondary">Voltar</a>
                </div>


            </form>
        </div>

    </div>
</div>

<?php include_once 'include/footer_inc.php' ?>
