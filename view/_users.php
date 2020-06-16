<?php

include_once 'include/header_inc.php';
include_once 'include/menu_inc.php';

?>
   
<div class="container">
      
<!--
      <div class="page-header">
        <h1>Formulário bootstrap</h1>
    </div> 
-->

    <div class="row">
      <div class="col-sm-8">
        <h3>Cadastro de usuários</h3>       
        <form>
          
          <div class="form-group">
            <label for="nome">Nome</label>
            <input type="text" class="form-control" id="nome">
          </div>

          <div class="form-group">
            <label  for="email">E-mail</label>
            <input type="email" class="form-control" id="email">
          </div>          

          <div class="form-group">
            <label  for="senha">Senha</label>
            <input type="password" class="form-control" id="senha">
          </div>

          <input type="submit" value="Salvar" class="btn btn-primary">
          <input type="reset" value="Limpar" class="btn btn-secondary">
          
<!--
          <div class="checkbox">
            <label>
              <input type="checkbox">Aceito os termos do serviço.
           </label>
          </div>

          <div class="radio">
            <label>
              <input type="radio" name="opc"> PHP
           </label>
            <label>
              <input type="radio" name="opc"> Java
           </label>           
          </div>

          <button type="submit" class="btn btn-default">Cadastrar</button>
-->
        </form>

      </div>



    </div>

    <?php include_once 'include/footer_inc.php' ?>
