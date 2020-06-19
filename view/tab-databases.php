<?php

include_once 'include/header_inc.php';
include_once 'include/menu_inc.php';

?>
   
<div class="container">

    <div class="row">

        <div class="col-sm-12" >

            <form method="post" action="\databases/insert">


                <h3>Cadastro de Banco de dados</h3>  
                <hr />
                <table class="table table-hover display nowrap" id="myTable" style="width:100%"> 
                    <thead>
                        <tr>
<!--                        <th scope="col">#</th> -->
                            <th scope="col"></th>
                            <th scope="col"></th>

                            <?php 
                                if ($_SESSION['s_superuser']):
                                    echo '<th scope="col">Empresa</th>';
                                endif;
                            ?>

                            <th scope="col">Nome DB</th>
                            <th scope="col">Categoria</th>
                            <th scope="col">Hostname</th>
                            <th scope="col">Username</th>
                            <th scope="col">Password</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php 
                            include_once 'model/list-databases.php';
                        ?>

                    </tbody>

                </table>
                <button type="submit" class="btn btn-primary">Novo registro</button>

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
