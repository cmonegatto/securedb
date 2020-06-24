<?php

include_once 'include/header_inc.php';
include_once 'include/menu_inc.php';

?>
   
<div class="container">

    <div class="row">

        <div class="col-sm-12" >

            <form method="post" action="\databases/insert">


                <h3>LOG de usuarios suspeitos</h3>  
                <hr />


                <div class="row" style="padding-bottom:20px">
                    <div style="margin-left:2px" class="row col-md-1">   
                        <label for="idcat">Categoria</label>
                    </div>

                    <?php 
                        //$_SESSION['idcia'] = 0;
                        include_once 'model/list-cat-combo.php';
                    ?>


                    <div style="margin-left:2px" class="row col-md-1">   
                        <label for="iddb">Database</label>
                    </div>

                    <?php 
                        //$_SESSION['idcia'] = 0;
                        include_once 'model/list-db-combo.php';
                    ?>

<!--
                    <div>
                    <button style="margin-left:20px" type="button" class="btn btn-success" id="pxesquisar">xPesquisar</button>
                    </div>
-->
                    <div>
                        <a href="#" class="btn btn-success" style="margin-left:20px" >
                            <span class="fa fa-refresh" style="color:white" id="pesquisar"> refresh</span>
                            </a>
                    </div>

                </div>




                <table class="table table-bordered table-hover display nowrap" id="myTable" style="width:100%"> 
                    <thead>
                        <tr>
<!--                        <th scope="col">#</th> -->
                            <th scope="col"></th>
                            <th scope="col"></th>
                            <th scope="col">Qtd</th>
                            <th scope="col">Username</th>
                            <th scope="col">OsUser</th>
                            <th scope="col">Machine</th>
                            <th scope="col">Program</th>
                            <th scope="col">Status</th>
                            <th scope="col">?</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php 
                            include_once 'model/list-admlogins.php';
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


<script>
    $("#pesquisar").click(function() {
        //alert ("ops.. mudou algo ai...")
            window.location="\admlogins";
         

    });
</script>



<?php include_once 'include/footer_inc.php' ?>



