<?php

include_once 'include/header_inc.php';
include_once 'include/menu_inc.php';

//$iddb = $_SESSION['iddb'];
//$idcat = $_SESSION['idcat'];


$iddb	= (!isset($_POST['iddb']))?$_SESSION['iddb']:$_POST['iddb'];
$idcat	= (!isset($_POST['idcat']))?$_SESSION['idcat']:$_POST['idcat'];

$_SESSION['iddb']   = $iddb;
$_SESSION['idcat']  = $idcat;



//$datetime = date("Y-m-d", strtotime("now")) . "T08:00";

$datetime = date("Y-m-d", strtotime("now")) . 'T' . date("H:i", strtotime("now"));

//$datetime = date("Y/m/d H:i", strtotime("now"));


?>
   
<div class="container" >

    <div class="row">

        <div class="col-md-12" >

            <!-- <form method="post" action="\admlogins/insert"> -->
            <form method="post" action="../model/ins-blacklist.php">


                <h3>Lista de usuários Bloqueados (new)</h3>  

                <hr />

                <!-- ****************************************************************************************** -->


                <div class="row" style="padding-bottom:20px">

                    <div style="margin-left:2px" class="row col-md-1">   
                        <label for="idcat">Categoria</label>
                    </div>


                    <select class="col-md-4 input-large form-control" id="idcat" name="idcat" style="margin-bottom: 15px; margin-left:15px" disabled>'
                        <?php 
                            include_once 'model/list-cat-combo.php';
                        ?>
                    </select>


                    <div style="margin-left:2px" class="row col-md-1">   
                        <label for="iddb">Database</label>
                    </div>


                    <select class="col-md-4 input-large form-control" id="iddb" name="iddb" style="margin-bottom: 15px; margin-left:15px" disabled>';
                        <!--<option value="">Escolha a CATEGORIA</option>-->
                    </select>

                </div>



                <div class="row">

                    <div class="form-group col-md-3">
                        <label for="username">Usuário (username)</label>
                        <input type="text" name="username" class="form-control upper" id="username" maxlength="30" autofocus>
                    </div>

                    <div class="form-group col-md-3">
                        <label for="osuser">Usuário AD (osuser)</label>
                        <input type="text" name="osuser" class="form-control upper" id="osuser" maxlength="30">
                    </div>

                    <div class="form-group col-md-4">
                        <label for="machine">Máquina (host/machine)</label>
                        <input type="text" name="machine" class="form-control upper" id="machine" maxlength="64">
                    </div>



                </div>


                <hr />

                <div class="row"> 
                    <div class="input-field col-md-10">
                        <input type="submit" value="Salvar"                         class="btn btn-primary">
                        <input type="button" value="Voltar"                         class="btn btn-secondary"   id="btnvoltar" >
                    </div>
                </div>


            </form>


            <?php
                if(isset($_SESSION['msg'])):
                    echo "<span style='color:red'> {$_SESSION['msg']}</span>";
                    $_SESSION['msg']="";
                endif;
            ?>


                <hr />

              

                <!-- *********************************************************************************************** -->

                <table class="table table-hover tab-admlogins table-bordered display nowrap" id="myTable" style="width:100%"> 
                    <thead>
                        <tr>
                            <th style='text-align:center width:50px' scope="col">Excluir</th>
                            <th scope="col">Data inclusão</th>
                            <th scope="col">Username</th>
                            <th scope="col">Osuser</th>
                            <th scope="col">Machine</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php 
                            include_once 'model/list-blacklist.php';
                        ?>

                    </tbody>

                </table>

                <hr />

        </div>

    </div>
</div>


<script>
    $(".upper").change(function(){

    $(this).val($(this).val().toUpperCase());
    });


    $("#btnvoltar").click(function() {
        var iddb    =   $("#iddb").val();
        var idcat   =   $("#idcat").val();
        
            window.location=`\../admlogins`;
    });


    $('#idcat').after(function(){
        if( $(this).val() ) {

            //$('#iddb').hide();
            //$('.carregando').show();

            //alert ("iddb: " + $("#iddb").val()  + " - idcat: " + $(this).val());

            
            $.getJSON('../../model/tab-admloginslog-post.php?search=',{idcat: $(this).val(), ajax: 'true'}, function(j){
                var options = ''; //'<option value="">Escolha o banco de dados</option>';	
                for (var i = 0; i < j.length; i++) {
                    options += '<option value="' + j[i].iddb + '">' + j[i].aliasdb + '</option>';
                }	
                $('#iddb').html(options).show();
                //$('.carregando').hide();
            });
        } else {
            //$('#iddb').html('<option value="">– erro na leitura da categoria –</option>');
        }
    });
    


    /*
    $('#idcat').change(function(){
        if( $(this).val() ) {

            //$('#iddb').hide();
            //$('.carregando').show();
            
            
            $.getJSON('../../model/tab-admloginslog-post.php?search=',{idcat: $(this).val(), ajax: 'true'}, function(j){
                var options = '<option value="">Escolha o banco de dados</option>';	
                for (var i = 0; i < j.length; i++) {
                    options += '<option value="' + j[i].iddb + '">' + j[i].aliasdb + '</option>';
                }	
                $('#iddb').html(options).show();
                //$('.carregando').hide();
            });
        } else {
            //$('#iddb').html('<option value="">– erro na leitura da categoria –</option>');
        }
    });
    */

</script>


<?php include_once 'include/footer_inc.php' ?>
