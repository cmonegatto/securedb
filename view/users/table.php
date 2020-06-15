<?php

include_once 'include/header_inc.php';
include_once 'include/menu_inc.php';

?>
   
<div class="container">

    <div class="row">

        <div class="col-sm-12" >

            <form method="post" action="\users/insert">


                <h3>Cadastro de Usuários</h3>  
                <hr />
                <table class="table table-hover display nowrap" id="myTable" style="width:100%"> 
                    <thead>
                        <tr>
<!--                        <th scope="col">#</th> -->
                            <th scope="col"></th>
                            <th scope="col"></th>
                            <th scope="col">Nome</th>
                            <th scope="col">Empresa</th>
                            <th scope="col">Login</th>
                            <th scope="col">e-mail</th>
                            <th scope="col">Telefone</th>
                            <th scope="col">Status</th>
                            <th scope="col">ADM</th>
                            <th scope="col">Super</th>
                            <th scope="col">Data</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php 
                            include_once 'model/users/read.php';
                        ?>

                    </tbody>

                </table>
                <button type="submit" class="btn btn-primary">Novo registro</button>
            </form>
        </div>

    </div>
</div>

<?php include_once 'include/footer_inc.php' ?>
