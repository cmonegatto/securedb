<?php

include_once 'include/header_inc.php';
include_once 'include/menu_inc.php';


$_SESSION['idcat'] = $data['idcat'];
$days = $data['days'];

?>
   
<div class="container">

    <div class="row">

        <div class="col-md-12" >

            <form method="post" action="\admlogins">


                <!-- <h3>Lista de conexões bloqueadas no banco de dados</h3>   -->
                <h3>BLOQUEIOS em todos bancos de dados da categoria</h3>  

                <hr />


                <div class="row" style="padding-bottom:20px">

                    <div style="margin-left:2px" class="row col-md-1">   
                        <label for="idcat">Categoria</label>
                    </div>


                    <select class="col-md-4 input-large form-control" id="idcat" name="idcat" style="margin-bottom: 15px; margin-left:15px; margin-right:15px"  autofocus required>'
                        <?php 
                            include_once 'model/list-cat-combo-all.php';
                        ?>
                    </select>


                    <div class="form-group row">
                        <label style="padding-left:50px; padding-right:30px" for="days" class="col-md-1 col-form-label">Dias</label>
                        <div class="col-md-6">
                            <input class="form-control" type="number" maxlength="3" <?php echo "value=$days"?>  id="days" min="-90" max="-1" require>
                        </div>
                    </div>




                    <div>
                        <a href="#" type="button" class="btn btn-success" style="margin-left:20px" >
                            <span class="fa fa-refresh" style="color:white" id="pesquisar"> refresh</span>
                            </a>
                    </div>

                </div>


                <!-- <table class="table tab-admloginslog-all table-bordered" id="myTable" style="width:100%"> -->
                <table class="table table-dark  tab-admloginslog" id="myTable" style="width:100%"> 

                    <thead>
                        <tr>
                            <th scope="col"></th>
                            <th scope="col">Instância</th>
                            <th scope="col">Qtd</th>
                            <th scope="col">Username</th>
                            <th scope="col">OsUser</th>
                            <th scope="col">Machine</th>
                            <th scope="col">Program</th>
                            <th scope="col">Module</th>
                            <th scope="col">Status</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php 
                            if ($_SESSION['idcat'] <> 0):
                                include_once 'model/list-admloginslog-all.php';                            
                            endif;
                        ?>

                    </tbody>

                </table>

                <!-- <button type="submit" class="btn btn-primary">Gestão das Regras</button> -->

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
        var idcat   =   $("#idcat").val();
        var days    =   $("#days").val();
        
        //alert ("dias: " + days);

        if (idcat !== "") {
            window.location=`\../../admloginslogall/${idcat}/${days}`;

        };    

    });

</script>


<?php include_once 'include/footer_inc.php' ?>

