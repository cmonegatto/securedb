<?php

include_once 'include/header_inc.php';
include_once 'include/menu_inc.php';


$_SESSION['idcat'] = $data['idcat'];
$dayAccess = $data['dayAccess'];
$dayRules  = $data['dayRules'];


?>
   
<div class="container">

    <div class="row">

        <div class="col-md-12" >

            <form method="post" action="\admlogins">


                <!-- <h3>Lista de conexões bloqueadas no banco de dados</h3>   -->
                <h3>Indicadores de Gestão</h3>  

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
                        <label style="padding-left:50px; padding-right:50px" for="dayAccess" class="col-md-1 col-form-label">Acessos</label>
                        <div class="col-md-6">
                            <input class="form-control" type="number" maxlength="3" <?php echo "value=$dayAccess"?>  id="dayAccess" min="-90" max="-0" require>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label style="padding-left:50px; padding-right:50px" for="dayRules" class="col-md-1 col-form-label">Regras</label>
                        <div class="col-md-6">
                            <input class="form-control" type="number" maxlength="3" <?php echo "value=$dayRules"?>  id="dayRules" min="-365" max="-30" require>
                        </div>
                    </div>



                    <div>
                        <a href="#" type="button" class="btn btn-success" style="margin-left:20px" >
                            <span class="fa fa-refresh" style="color:white" id="pesquisar"> refresh</span>
                            </a>
                    </div>

                </div>


                <?php 
                    if ($_SESSION['idcat'] >0) :
                        include_once 'view/charts-kpis.php';
                    endif;
                ?>



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
        var idcat     =   $("#idcat").val();
        var dayAccess =   $("#dayAccess").val();
        var dayRules  =   $("#dayRules").val();
        
        // alert ('ops.. idcat: ' + idcat);

        if (idcat !== "") {
            // window.location=`\/kpi-charts/${idcat}/${dayAccess}/${dayRules}`;
            window.location=`\/kpi/${idcat}/${dayAccess}/${dayRules}`;               
               
        };    

    });

</script>


<?php include_once 'include/footer_inc.php' ?>

