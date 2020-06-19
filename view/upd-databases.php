<?php

//session_start();

include_once 'include/header_inc.php';
include_once 'include/menu_inc.php';
include_once 'class/Sql.php';
include_once 'function/utils.php';

$iddb = $data['iddb'];
$idcia = $data['idcia'];

$_SESSION['iddb'] = $iddb;
//$_SESSION['idcia'] = $idcia;


$conn=new Sql();

$result = $conn->select("SELECT * FROM adm_databases WHERE iddb={$iddb}");

$idcat = $result[0]["idcat"];
$dbname		= $result[0]["dbname"];
$hostname	= $result[0]["hostname"];
$username	= $result[0]["username"];
$password	= encrypt_decrypt('decrypt', $result[0]["password"]);

?>

<div class="container">

    <div>
        <h3>Cadastro de Banco de Dados (edit)</h3>         
        <hr />
    </div>

    <form method="post" action="\model/upd-databases.php">

    <div class="row">
            <div style="margin-left:2px" class="row col-md-12">   
                <label for="idcat">Categoria DB</label>
            </div>

            <?php 
                //$_SESSION['idcia'] = 0;
                include_once 'model/list-databases-catbox.php';
            ?>

        </div>


        <div class="row">   

            <div class="form-group col-md-4">
                <label for="dbname">Nome DB</label>
                <input type="text" name="dbname" class="form-control" id="dbname" value="<?php echo $dbname ?>" required>
            </div>

            <div class="form-group col-md-4">
                <label  for="hostname">HOSTNAME</label>
                <input type="text" name="hostname" class="form-control" id="hostname" value="<?php echo $hostname ?>" required>
            </div>          

        </div>


        <div class="row">

            <div class="form-group col-md-4">
                <label  for="username">Username</label>
                <input type="text" name="username" class="form-control" id="username" value="<?php echo $username ?>" required>
            </div>          

            <div class="form-group col-md-4">
                <label  for="password">Senha</label>
                <input type="text" name="password" class="form-control" id="password" value="<?php echo $password ?>"  required>
            </div>          

        </div>


        <hr />

        <div class="row">       
            <div class="input-field col-md-4">
                <input type="submit" value="Salvar" class="btn btn-primary">                
                <a href="\databases" class="btn btn-secondary">Voltar</a>
            </div>
        <div>


        <?php

            if(isset($_SESSION['msg'])):
                echo "<span style='color:red'> {$_SESSION['msg']}</span>";
                $_SESSION['msg']="";
            endif;
        ?>


    </form>

</div>


<?php include_once 'include/footer_inc.php' ?>

