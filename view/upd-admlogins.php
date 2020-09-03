<?php

include_once 'include/header_inc.php';
include_once 'include/menu_inc.php';
include "class/Sql.php";
include "function/utils.php";


$iddb	= (!isset($_POST['iddb']))?$_SESSION['iddb']:$_POST['iddb'];
$idcat	= (!isset($_POST['idcat']))?$_SESSION['idcat']:$_POST['idcat'];

$_SESSION['iddb']   = $iddb;
$_SESSION['idcat']  = $idcat;

///////////////////

$id_login = $data['id'];
$_SESSION['id_login'] = $id_login;

$iddb	= (!isset($_POST['iddb']))?$_SESSION['iddb']:$_POST['iddb'];
$idcat	= (!isset($_POST['idcat']))?$_SESSION['idcat']:$_POST['idcat'];

$conn=new Sql();

$result= $conn->sql( basename(__FILE__), "SELECT hostname, username, password, dbname, port, player
											FROM adm_databases
										   WHERE iddb = $iddb");

$localhost	= $result[0]['hostname'];
$user	    = $result[0]['username'];
$password	= encrypt_decrypt('decrypt', $result[0]['password']);
$dbname		= $result[0]['dbname'];
$port		= $result[0]['port'];
$player		= $result[0]['player'];


$conn=new Sql($player, $localhost, $user, $password, $dbname, $port);

if (isset($_SESSION['msg']) && strlen($_SESSION['msg'])>0 ):
    header("Location: \admloginslog/0/0");
	exit;	
endif;


if ($player == 'OCI'):

    $result= $conn->sql(basename(__FILE__), 
                        "SELECT l.ID_LOGIN
                              , l.USERNAME    AS USERNAME
                              , l.OSUSER
                              , l.MACHINE
                              , to_char(l.BEGIN_DATE, 'yyyy-mm-dd hh24:mi') as BEGIN_DATE
                              , to_char(l.END_DATE, 'yyyy-mm-dd hh24:mi')   as END_DATE
                              , l.FREETOOLS
                              , l.SESSIONS_PER_USER
                              , l.INIT_PLSQL
                              , l.COMMENTS
                              , l.LOG_LOGON
                              , l.TRACE
                              , l.CURSOR_SHARING
                        FROM adm_logins l
                        WHERE id_login=$id_login");

elseif ($player == 'SQLSRV'):

    $result= $conn->sql(basename(__FILE__), 
                        "SELECT l.ID_LOGIN
                              , l.USERNAME    AS USERNAME
                              , l.OSUSER
                              , l.MACHINE
                              , format(l.BEGIN_DATE,'yyyy/MM/dd HH:mm:ss')  as BEGIN_DATE
							  , format(l.END_DATE,'yyyy/MM/dd HH:mm:ss')	as END_DATE
                              , l.FREETOOLS
                              , l.SESSIONS_PER_USER
                              , l.INIT_PLSQL
                              , l.COMMENTS
                              , l.LOG_LOGON
                              , l.TRACE
                              , l.CURSOR_SHARING
                        FROM adm_logins l
                        WHERE id_login=$id_login");
    
endif;


$username		    = $result[0]["USERNAME"];
$osuser		        = $result[0]["OSUSER"];
$machine		    = $result[0]["MACHINE"];
$begindate		    = $result[0]["BEGIN_DATE"];
$enddate		    = $result[0]["END_DATE"];
$freetools		    = $result[0]["FREETOOLS"];
$sessionsperuser	= $result[0]["SESSIONS_PER_USER"];
$initplsql		    = $result[0]["INIT_PLSQL"];
$comments		    = $result[0]["COMMENTS"];
$loglogon		    = ($result[0]["LOG_LOGON"]=="S") ? "checked" : "";
$trace		        = ($result[0]["TRACE"]=="S") ? "checked" : "";
$cursorsharing		= ($result[0]["CURSOR_SHARING"]=="S") ? "checked" : "";

$begindate = date("Y-m-d", strtotime($result[0]['BEGIN_DATE'])) . 'T' . date("H:i", strtotime($result[0]['BEGIN_DATE']));

if ($enddate):
    $enddate   = date("Y-m-d", strtotime($result[0]['END_DATE']))   . 'T' . date("H:i", strtotime($result[0]['END_DATE']));
endif;



/////////////////////


?>
   
<div class="container">

    <div class="row">

        <div class="col-sm-12" >

            <!-- <form method="post" action="\admlogins/insert"> -->
            <form method="post" action="../../model/upd-admlogins.php">


                <h3>Gestão das regras de acesso (edit)</h3>  

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
                        <input type="text" value="<?php echo $username ?>" name="username" class="form-control upper" id="username" maxlength="30" autofocus>
                    </div>

                    <div class="form-group col-md-2">
                        <label  for="osuser">Usuário AD (osuser)</label>
                        <input type="text" value="<?php echo $osuser ?>" name="osuser" class="form-control upper" id="osuser" maxlength="30" >
                    </div>          

                    <div class="form-group col-md-2">
                        <label  for="machine">Máquina (host)</label>
                        <input type="text" value="<?php echo $machine ?>" name="machine" class="form-control upper" id="machine" maxlength="64" >
                    </div>          

                    <div class="form-group col-md-3">
                        <label  for="begindate">Data Inicio</label>
                        <input type="datetime-local" value="<?php echo $begindate ?>" name="begindate" class="form-control" id="begindate" required>
                    </div>          

                    <div class="form-group col-md-3">
                        <label  for="enddate">Data Fim</label>
                        <input type="datetime-local" value="<?php echo $enddate ?>" name="enddate" class="form-control" id="enddate" >
                    </div>         

                </div>


                <div class="row">   

                    <div class="form-group col-md-10">
                        <label for="freetools">Ferramentas autorizadas</label>
                        <input type="text" value="<?php echo $freetools ?>" name="freetools" class="form-control upper" value="*" id="freetools" maxlength="500"  >
                    </div>

                    <div class="form-group col-md-2">
                        <label  for="sessionsperuser">Sessões</label>
                        <input type="text" value="<?php echo $sessionsperuser ?>" name="sessionsperuser" class="form-control" id="sessionsperuser" data-mask="99" maxlength="2">
                    </div>             

                </div>


                <div class="row">   
                    <div class="form-group col-md-6">
                        <label for="initplsql">PL/SQL para inicialização</label>
                        <textarea class="form-control" name="initplsql" id="initplsql" rows="3" maxlength="4000"><?php echo $initplsql ?></textarea>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="comments">Comentários sobre essa regra...</label>
                        <textarea class="form-control" name="comments" id="comments" rows="3" maxlength="4000"><?php echo $comments ?></textarea>
                    </div>

                </div>


                <div class="row col-md-10"> 
                    <div class="custom-control custom-checkbox col-md-4">
                        <input type="checkbox" class="custom-control-input" name="loglogon" id="loglogon" <?php echo $loglogon ?> >
                        <label class="custom-control-label" for="loglogon">Gerar histórico de Acessos</label>                    </div>

                    <div class="custom-control custom-checkbox col-md-4">
                        <input type="checkbox" class="custom-control-input" name="trace" id="trace" <?php echo $trace ?> >
                        <label class="custom-control-label" for="trace">Gerar trace</label>
                    </div>

                    <div class="custom-control custom-checkbox col-md-4">
                        <input type="checkbox" class="custom-control-input" name="cursorsharing" id="cursorsharing" <?php echo $cursorsharing ?> >
                        <label class="custom-control-label" for="cursorsharing">Ativar Cursor Sharing</label>
                    </div>
                </div>



                <hr />

                <div class="row"> 
                    <div class="input-field col-md-8">
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
        
            window.location=`/admlogins`;
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

</script>


<?php include_once 'include/footer_inc.php' ?>
