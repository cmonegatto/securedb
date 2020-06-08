<?php

include_once 'include/header_inc.php';
include_once 'include/menu_inc.php';

?>
   
<div class="container">

<!--
    <div class="row">
        <div class="col-sm-5">
            
            <h3>Cadastro de Empresas</h3>  


            <form>
            
                <div class="form-group">
                    <label for="nome">Nome da Companhia</label>
                    <input type="text" class="form-control" id="nome">
                </div>

                <div class="form-group">
                    <label for="respname">Nome do Responsável</label>
                    <input type="text" class="form-control" id="respname">
                </div>          

                <div class="form-group">
                    <label for="email">E-mail</label>
                    <input type="email" class="form-control" id="email">
                </div>          

                <div class="form-group">
                    <label for="celphone">Número telefone celular</label>
                    <input type="tel" class="form-control" id="celphone">
                </div>          

                <div class="form-check">
                    <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios1" value="option1" checked>
                    <label class="form-check-label" for="exampleRadios1">Ativo</label>
                </div>

                <div class="form-check">
                    <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios2" value="option2">
                    <label class="form-check-label" for="exampleRadios2">Invativo</label>
                </div>

                <button type="submit" class="btn btn-primary">Salvar</button>
                <button type="reset" class="btn btn-secondary">Limpar</button>

            </form>
        </div>

-->


        <div class="col-sm-12" >

        <h3>Cadastro de Empresas</h3>  
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Companhia</th>
                                <th scope="col">Responsável</th>
                                <th scope="col">e-mail</th>
                                <th scope="col">Telefone</th>
                                <th scope="col">Status</th>
                                <th scope="col">Data Registro</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php 
                                include_once 'model/read.php';
                            ?>

                        </tbody>

                    </table>
                </div>





    </div>
</div>

<?php include_once 'include/footer_inc.php' ?>
