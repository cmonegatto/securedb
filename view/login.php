<?php

//session_start();

include_once 'include/header_inc.php';
include_once 'include/menu_inc.php';

?>

<style>
    body {
            background-image:url("img/telaprincipal.jpg");
            background-attachment: fixed;
            background-size: 100%;
            background-repeat: no-repeat;
            background-color: black;
            /*opacity: 0.3;*/
				
		}
</style>


    <!-- css aplicado para classe .login -->
    <div class="container login">
        

        <div class="row">
            
            <div class="col-sm-12">
                <h3>Inicie sua sessão</h3> 
                <hr/>      

                <form method="POST" action="controller/login.php">
                
                    <div class="form-group">
                        <label for="email">E-mail</label>
                        <input type="email" class="form-control" id="email" name="email" required autofocus>                    
                    </div>          
                    
                    <div class="form-group">
                        <label  for="senha">Senha</label>
                        <input type="password" class="form-control" id="senha" name="senha" required >
                    </div>
                    
                    <?php
                        if(isset($_SESSION['msg'])):
                            echo "<span style='color:red'> {$_SESSION['msg']}</span>";
                            session_unset();
                        endif;
                    ?>

                    <div>
                        <button type="submit" class="btn btn-primary" style="margin-top:20px">Entrar</button>
                    </div>

                </form>
            </div>
        </div>

    </div>

<!--
<section>
-->

<?php include_once 'include/footer_inc.php' ?>
