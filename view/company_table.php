<?php

include_once 'include/header_inc.php';
include_once 'include/menu_inc.php';

?>
   
<div class="container">

    <div class="row">

        <div class="col-sm-12" >

            <form method="post" action="\company/insert">


                <h3>Cadastro de Empresas</h3>  
                <table class="table table-hover" id="myTable">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Companhia</th>
                            <th scope="col">Responsável</th>
                            <th scope="col">e-mail</th>
                            <th scope="col">Telefone</th>
                            <th scope="col">Status</th>
                            <th scope="col">Data Registro</th>
                            <th scope="col">Editar</th>
                            <th scope="col">Excluir</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php 
                            include_once 'model/read.php';
                        ?>

                    </tbody>

                </table>
                <button type="submit" class="btn btn-primary">Novo registro</button>
            </form>
        </div>

    </div>
</div>

<?php include_once 'include/footer_inc.php' ?>
