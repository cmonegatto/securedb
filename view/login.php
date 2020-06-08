<?php

include_once 'include/header_inc.php';
include_once 'include/menu_inc.php';

?>

<div class="container">
      
    <div class="row">
      <div class="col-sm-4" id="id-login">
        <h3>Inicie sua sessão</h3>       

        <form>
            
            <div class="form-group">
                <label for="email">E-mail</label>
                <input type="email" class="form-control" id="email">
                
            </div>          

            <div class="form-group">
                <label  for="senha">Senha</label>
                <input type="password" class="form-control" id="senha">
            </div>


            <button type="submit" class="btn btn-primary">Entrar</button>

        </form>

</div>

  

<?php
    include_once 'include/footer_inc.php';
?>