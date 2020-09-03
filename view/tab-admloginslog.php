<?php

include_once 'include/header_inc.php';
include_once 'include/menu_inc.php';

$_SESSION['iddb']  = $data['iddb'];
$_SESSION['idcat'] = $data['idcat'];

?>
   
<div class="container">

    <div class="row">

        <div class="col-md-12" >

            <form method="post" action="\admlogins">


                <!-- <h3>Lista de acessos ao banco de dados</h3>  -->

                <h3>Gestão de Acessos</h3>

                <hr />


                <div class="row" style="padding-bottom:20px">

                    <div style="margin-left:2px" class="row col-md-1">   
                        <label for="idcat">Categoria</label>
                    </div>


                    <select class="col-md-4 input-large form-control" id="idcat" name="idcat" style="margin-bottom: 15px; margin-left:15px"  autofocus required>'
                        <?php 
                            include_once 'model/list-cat-combo.php';
                        ?>
                    </select>


                    <div style="margin-left:2px" class="row col-md-1">   
                        <label for="iddb">Database</label>
                    </div>


                    <select class="col-md-4 input-large form-control" id="iddb" name="iddb" style="margin-bottom: 15px; margin-left:15px"  autofocus required>';
                        <!--<option value="">Escolha a CATEGORIA</option>-->
                    </select>

<!--
                    <div>
                    <button style="margin-left:20px" type="button" class="btn btn-success" id="pxesquisar">xPesquisar</button>
                    </div>
-->
                    <div>
                        <a href="#" type="button" class="btn btn-success" style="margin-left:20px" >
                            <span class="fa fa-refresh" style="color:white" id="pesquisar"> refresh</span>
                            </a>
                    </div>

                </div>


                <!-- <table class="table table-dark display nowrap tab-admloginslog" id="myTable" style="width:100% ">  -->
                <table class="table table-dark  tab-admloginslog" id="myTable" style="width:100%"> 
                    <thead>
                        <tr>
<!--                        <th scope="col">#</th> -->
                            <th scope="col"></th>                            
                            <th scope="col"></th>
                            <th scope="col"></th>
                            <th scope="col">Qtd</th>
                            <th scope="col">Username</th>
                            <th scope="col">OsUser</th>
                            <th scope="col">Machine</th>
                            <th scope="col">Program</th>
                            <th scope="col">Module</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php 
                            if ($_SESSION['iddb'] <> 0):
                                include_once 'model/list-admloginslog.php';                            
                            endif;
                        ?>

                    </tbody>

                </table>
                <button type="submit" class="btn btn-primary">Gestão das Regras</button>

                <span class="fa fa-square" style="color:yellow; padding-left:50px"></span>                
                Sem Regra
                <span class="fa fa-square" style="color:red"></span>
                Bloquear
                <span class="fa fa-square" style="color:green"></span>
                Liberar


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

<?php 
//$_SESSION['iddb']  = 0;
//$_SESSION['idcat'] = 0;
?>


<script>

    $("#pesquisar").click(function() {
        var idb     =   $("#iddb").val();
        var idcat   =   $("#idcat").val();
        
        //alert ("iddb: " + idb + " - idcat: " + idcat);

        if (idb !== "" && idcat !== "") {
            window.location=`\../../admloginslog/${idb}/${idcat}`;
        };    

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


/*
    $("#c").on("change", function() {
        var idcat = $("#idcat").val();
        
        $.ajax({ 
            url: '../model/list-db-combo.php',
            type: 'POST',
            data: {id:idcat},
            beforeSend: function(){
                $("#iddb").html('carregando...');
            },
            success: function(data)
            {
                $("#iddb").css({'display':'block'});
                $("#iddb").html(data);
            },
            error: function (data)
            {
                $("#iddb").css({'display':'block'});
                $("#iddb").html("houve um erro ao carregar");
            }
        });
        alert (idcat);

    });
*/
</script>



<?php include_once 'include/footer_inc.php' ?>

