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
   
<div class="container">

    <div class="row">

        <div class="col-sm-12" >

            <!-- <form method="post" action="\admlogins/insert"> -->
            <form method="post" action="../model/ins-admlogins.php">


                <h3>Gestão das regras de acesso</h3>  

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

                    <div class="form-group col-md-2">
                        <label for="username">Usuário (username)</label>
                        <input type="text" name="username" class="form-control upper" id="username" maxlength="30" autofocus>
                    </div>

                    <div class="form-group col-md-2">
                        <label  for="osuser">Usuário AD (osuser)</label>
                        <input type="text" name="osuser" class="form-control upper" id="osuser" maxlength="30" >
                    </div>          

                    <div class="form-group col-md-2">
                        <label  for="machine">Máquina (hostname)</label>
                        <input type="text" name="machine" class="form-control upper" id="machine" maxlength="64" >
                    </div>          

                    <div class="form-group col-md-3">
                        <label  for="begindate">Data Inicio</label>
                        <input type="datetime-local" value="<?php echo $datetime?>" name="begindate" class="form-control" id="begindate" required>
                    </div>          

                    <div class="form-group col-md-3">
                        <label  for="enddate">Data Fim</label>
                        <input type="datetime-local" name="enddate" class="form-control" id="enddate" >
                    </div>         

                </div>


                <div class="row">   

                    <div class="form-group col-md-10">
                        <label for="freetools">Ferramentas autorizadas</label>
                        <input type="text" name="freetools" class="form-control upper" value="*" id="freetools" maxlength="200"  >
                    </div>

                    <div class="form-group col-md-2">
                        <label  for="sessionsperuser">Sessões</label>
                        <input type="text" name="sessionsperuser" class="form-control" id="sessionsperuser" maxlength="2">
                    </div>             

                </div>


                <div class="row">   
                    <div class="form-group col-md-6">
                        <label for="initplsql">PL/SQL para inicialização</label>
                        <textarea class="form-control" name="initplsql" id="initplsql" rows="3" maxlength="4000"></textarea>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="comments">Comentários sobre essa regra...</label>
                        <textarea class="form-control" name="comments" id="comments" rows="3" maxlength="4000"></textarea>
                    </div>

                </div>



                <div class="row"> 
                    <div class="custom-control custom-checkbox col-md-2">
                        <input type="checkbox" class="custom-control-input" value="0" name="loglogon" id="loglogon" >
                        <label class="custom-control-label" for="loglogon">Logar</label>
                    </div>

                    <div class="custom-control custom-checkbox col-md-2">
                        <input type="checkbox" class="custom-control-input" name="trace" id="trace" >
                        <label class="custom-control-label" for="trace">Trace</label>
                    </div>

                    <div class="custom-control custom-checkbox col-md-2">
                        <input type="checkbox" class="custom-control-input" name="cursorsharing" id="cursorsharing" >
                        <label class="custom-control-label" for="cursorsharing">Cursor Sharing</label>
                    </div>
                </div>



                <hr />

                <div class="row"> 
                    <div class="input-field col-md-4">
                        <input type="submit" value="Salvar" class="btn btn-primary">
                        <input type="button" value="Voltar" id="btnvoltar" class="btn btn-secondary">
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

                <table class="table table-hover table-bordered display nowrap" id="myTable" style="width:100%"> 
                    <thead>
                        <tr>
                            <th scope="col"></th>
                            <th scope="col"></th>
                            <th scope="col"></th>
                            <th scope="col">#</th>
                            <th scope="col">Username</th>
                            <th scope="col">OsUser</th>
                            <th scope="col">Machine</th>
                            <th scope="col">Data inicio</th>
                            <th scope="col">Data Fim</th>
                            <th scope="col">Ferramentas</th>
                            <th scope="col">Nº Sessões</th>
                            <th scope="col">Logar</th>
                            <th scope="col">Trace</th>
                            <th scope="col">Cursor;Sharing</th>
                            <th scope="col">PL/SQL init</th>
                            <th scope="col">Comentários</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php 
                            //if ($_SESSION['iddb'] <> 0):
                                include_once 'model/list-admlogins.php';                            
                            //endif;
                        ?>

                    </tbody>

                </table>

                <hr />

        </div>

    </div>
</div>

<?php 
//$_SESSION['iddb']  = 0;
//$_SESSION['idcat'] = 0;
?>


<script>
    $(".upper").change(function(){

    $(this).val($(this).val().toUpperCase());
    });


    $("#btnvoltar").click(function() {
        var iddb    =   $("#iddb").val();
        var idcat   =   $("#idcat").val();
        
            window.location=`\../admloginslog/${iddb}/${idcat}`;
    });


    $('#idcat').after(function(){
        if( $(this).val() ) {

            //$('#iddb').hide();
            //$('.carregando').show();

            //alert ("iddb: " + $("#iddb").val()  + " - idcat: " + $(this).val());

            
            $.getJSON('../../model/tab-admloginslog-post.php?search=',{idcat: $(this).val(), ajax: 'true'}, function(j){
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
            
            
            $.getJSON('../../model/tab-admloginslog-post.php?search=',{idcat: $(this).val(), ajax: 'true'}, function(j){
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

</script>


<?php include_once 'include/footer_inc.php' ?>
