<?php

include_once 'include/header_inc.php';
include_once 'include/menu_inc.php';

$iddb	= (!isset($_POST['iddb']))?$_SESSION['iddb']:$_POST['iddb'];
$idcat	= (!isset($_POST['idcat']))?$_SESSION['idcat']:$_POST['idcat'];

$_SESSION['iddb']   = $iddb;
$_SESSION['idcat']  = $idcat;

$_SESSION['username']   = $data['username'];
$_SESSION['osuser']     = str_replace('*', '%', $data['osuser']);
$_SESSION['machine']    = str_replace('*', '%', $data['machine']);
$_SESSION['program']    = str_replace('*', '%', $data['program']);
$_SESSION['module']     = str_replace('*', '%', $data['module']);


?>
   
<div class="container">

    <div class="row">

        <div class="col-sm-12" >

            <!-- <form method="post" action="\admlogins/insert"> -->
            <form method="post" action="../model/ins-admlogins.php">


                <h3>Detalhamento dos registros do log do banco de dados</h3>  

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


                <hr />


            </form>


            <?php
                if(isset($_SESSION['msg'])):
                    echo "<span style='color:red'> {$_SESSION['msg']}</span>";
                    $_SESSION['msg']="";
                endif;
            ?>


                <!-- *********************************************************************************************** -->

                <table class="table table-hover table-bordered display nowrap" id="myTable" style="width:100%">

                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Data</th>
                            <th scope="col">Username</th>
                            <th scope="col">OsUser</th>
                            <th scope="col">Machine</th>
                            <th scope="col">Terminal</th>
                            <th scope="col">Program</th>
                            <th scope="col">Module</th>
                            <th scope="col">Killed</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php 
                            //if ($_SESSION['iddb'] <> 0):
                                include_once 'model/list-det-admloginslog.php';                                        
                            //endif;
                        ?>

                    </tbody>

                </table>

                <hr />

                <div class="row"> 
                    <div class="input-field col-md-4">
                        <input type="button" value="Voltar" id="btnvoltar" class="btn btn-secondary">
                    </div>
                </div>


        </div>

    </div>
</div>

<?php 
//$_SESSION['iddb']  = 0;
//$_SESSION['idcat'] = 0;
?>


<script>
$(document).ready(function(){

    $(".upper").change(function(){

    $(this).val($(this).val().toUpperCase());
    });


    $("#btnvoltar").click(function() {
        var iddb    =   $("#iddb").val();
        var idcat   =   $("#idcat").val();
        
        window.location=`/admloginslog/${iddb}/${idcat}`;
    });


    $('#idcat').after(function(){      
        if( $(this).val() ) {

            //$('#iddb').hide();
            //$('.carregando').show();

            //alert ("iddb: " + $("#iddb").val()  + " - idcat: " + $(this).val());

            $.getJSON('../../../../../../model/tab-admloginslog-post.php?search=',{idcat: $(this).val(), ajax: 'true'}, function(j){
                var options = ''; //'<option value="">Escolha o banco de dados</option>';	
                for (var i = 0; i < j.length; i++) {
                    options += '<option value="' + j[i].iddb + '">' + j[i].dbname + '</option>';
                }	
                $('#iddb').html(options).show();
                //$('.carregando').hide();
            });
        } else {
            //$('#iddb').html('<option value="">– erro na leitura da categoria –</option>');
        }
    });

    $('#idcat').change(function(){
        if( $(this).val() ) {

            //$('#iddb').hide();
            //$('.carregando').show();
            
            
            $.getJSON('../../../../../../model/tab-admloginslog-post.php?search=',{idcat: $(this).val(), ajax: 'true'}, function(j){
                var options = '<option value="">Escolha o banco de dados</option>';	
                for (var i = 0; i < j.length; i++) {
                    options += '<option value="' + j[i].iddb + '">' + j[i].dbname + '</option>';
                }	
                $('#iddb').html(options).show();
                //$('.carregando').hide();
            });
        } else {
            //$('#iddb').html('<option value="">– erro na leitura da categoria –</option>');
        }
    });

});
</script>


<?php include_once 'include/footer_inc.php' ?>
