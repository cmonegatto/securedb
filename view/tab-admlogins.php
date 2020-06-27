<?php

include_once 'include/header_inc.php';
include_once 'include/menu_inc.php';

//$iddb = $_SESSION['iddb'];
//$idcat = $_SESSION['idcat'];


$iddb	= $_POST['iddb'];
$idcat	= $_POST['idcat'];

$datetime = date("Y-m-d", strtotime("now")) . "T08:00";

//$datetime = date("Y/m/d H:i", strtotime("now"));


?>
   
<div class="container">

    <div class="row">

        <div class="col-sm-12" >

            <form method="post" action="\admlogins/insert">


                <h3>Gestão das regras de acesso</h3>  

                <hr />

                <!-- ****************************************************************************************** -->


                <div class="row">   

                    <div class="form-group col-md-2">
                        <label for="username">Usuário (username)</label>
                        <input type="text" name="username" class="form-control upper" id="username" >
                    </div>

                    <div class="form-group col-md-2">
                        <label  for="OsUser">Usuário AD (osuser)</label>
                        <input type="text" name="OsUser" class="form-control upper" id="OsUser" >
                    </div>          

                    <div class="form-group col-md-2">
                        <label  for="machine">Máquina (hostname)</label>
                        <input type="text" name="machine" class="form-control upper" id="machine" >
                    </div>          

                    <div class="form-group col-md-3">
                        <label  for="begin-date">Data Inicio</label>
                        <input type="datetime-local" value="<?php echo $datetime?>" name="begin-date" class="form-control" id="begin-date" required>
                    </div>          

                    <div class="form-group col-md-3">
                        <label  for="end-date">Data Fim</label>
                        <input type="datetime-local" name="end-date" class="form-control" id="end-date" >
                    </div>         

                </div>


                <div class="row">   

                    <div class="form-group col-md-10">
                        <label for="freetools">Ferramentas autorizadas</label>
                        <input type="text" name="freetools" class="form-control upper" value="*" id="freetools" maxlength="200"  >
                    </div>

                    <div class="form-group col-md-2">
                        <label  for="sessions-per-user">Sessões</label>
                        <input type="text" name="sessions-per-user" class="form-control" id="sessions-per-user" >
                    </div>             

                </div>


                <div class="row">   
                    <div class="form-group col-md-6">
                        <label for="exampleFormControlTextarea1">PL/SQL para inicialização</label>
                        <textarea class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="exampleFormControlTextarea1">Comentários sobre essa regra...</label>
                        <textarea class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
                    </div>

                </div>



                <div class="row col-md-12"> 
                    <div class="custom-control custom-checkbox col-md-2">
                        <input type="checkbox" class="custom-control-input" name="log-logon" id="log-logon" >
                        <label class="custom-control-label" for="log-logon">Logar</label>
                    </div>

                    <div class="custom-control custom-checkbox col-md-2">
                        <input type="checkbox" class="custom-control-input" name="trace" id="trace" >
                        <label class="custom-control-label" for="trace">Trace</label>
                    </div>

                    <div class="custom-control custom-checkbox col-md-2">
                        <input type="checkbox" class="custom-control-input" name="cursor-sharing" id="cursor-sharing" >
                        <label class="custom-control-label" for="cursor-sharing">Cursor Sharing</label>
                    </div>
                </div>





                <hr />

                <div class="input-field col-md-4">
                    <input type="submit" value="Salvar" class="btn btn-primary">
                </div>

            </form>

            <div class="input-field col-md-4">
                <?php
                    echo "<a href='\admloginslog/$iddb/$idcat'><button class='btn btn-secondary'>Voltar</button></a>";
                ?>
            </div>


                <!-- *********************************************************************************************** -->

                <table class="table table-hover table-bordered display nowrap" id="myTable" style="width:100%"> 
                    <thead>
                        <tr>
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

                <?php
                    if(isset($_SESSION['msg'])):
                        echo "<span style='color:red'> {$_SESSION['msg']}</span>";
                        $_SESSION['msg']="";
                    endif;
                ?>

 <!--           </form> -->
            


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
</script>


<?php include_once 'include/footer_inc.php' ?>



