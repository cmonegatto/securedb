<?php

//session_start();

include_once 'include/header_inc.php';
include_once 'include/menu_inc.php';

?>
   
<div class="container">

    <div class="row">

        <div class="col-sm-8">
            
            <h3>Cadastro de Empresas (new)</h3>
            <hr />         

            <form method="post" action="\model/ins-company.php">
                <div class="form-group">
                    <label for="nome">Nome da Companhia</label>
                    <input type="text" class="form-control" name="cianame" id="cianame" maxlength="50" autofocus required >
                </div>

                <div class="form-group">
                    <label for="respname">Nome do Responsável</label>
                    <input type="text" class="form-control" name="respname" id="respname" maxlength="50" required>
                </div>          

                <div class="form-group">
                    <label for="email">E-mail</label>
                    <input type="email" class="form-control" name="email" id="email" maxlength="50" required>
                </div>          

                <div class="form-group">
                    <label for="celphone">Número telefone celular</label>
                    <input type="tel" placeholder="(99) 9999-9999" class="form-control" name="celphone" id="celphone" data-mask="(00) 00000-0000" maxlength="20" required>
                </div>          


                <div class="custom-control custom-checkbox col-md-2">
                    <input type="checkbox" class="custom-control-input" name="status" id="status" checked>
                    <label class="custom-control-label" for="status">Ativo</label>
                </div>


                <hr />

                <div class="input-field">
                    <input type="submit" value="Salvar" class="btn btn-primary">                
                    <a href="\company" class="btn btn-secondary">Voltar</a>
                </div>

                <?php

                    if(isset($_SESSION['msg'])):
                        echo "<span style='color:red'> {$_SESSION['msg']}</span>";
                        $_SESSION['msg']="";
                    endif;
                ?>



            </form>
        </div>

    </div>
</div>

<?php include_once 'include/footer_inc.php' ?>
