
<?php

session_start();
require_once("vendor/autoload.php");


$url = $_SERVER["REQUEST_URI"];

//$app = new \Slim\Slim();


// During instantiation
$app = new \Slim\Slim(array(
    'templates.path' => './view'
));


if ( isset($_SESSION['s_time']) ):

    if (time() - $_SESSION['s_time'] > $_SESSION['s_limite_session']):
        session_unset();
        $_SESSION['msg'] = "Sessão expirada por inatividade!";
        //header("Location: \logout");
        header("Location: /");
        exit();
    else:
        $_SESSION['s_time'] = time();
    endif;

endif;


$app->get('/', function () use ($app) {  
    if (isset($_SESSION['s_iduser'])):
        $app->render('start.php');        
    else:
        $app->render('login.php');
    endif;
});


$app->get('/logout', function () use ($app) {  
    session_unset();
    $app->render('login.php');    
});



// --- 06-jun-21 ---------------------------------------
if ( ! isset($_SESSION['s_idcia']) ):
    $app->run();
    exit();
endif;
// -----------------------------------------------------



$app->get('/changepwd', function () use ($app) {  
    $app->render('changepwd.php');        
});

$app->get('/upd-changepwd/:iduser', function ($iduser) use ($app) {  
    $data = array("data"=>array("iduser"=>$iduser));
    $app->render('../model/reset-pwd.php', $data, 200);
});



/* ---------------------------------------------------------------------------
*  ROTAS PARA CRUD EMPRESA/COMPANHIA
* --------------------------------------------------------------------------- */

// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * 
if (strpos($url, '/company') !== false && !($_SESSION['s_superuser']) ):
    header("Location: /");    
    exit;
endif;
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *


$app->get('/company', function () use ($app){
    $app->render('tab-company.php');    
});


$app->get('/company/insert', function () use ($app) {
    $app->render('ins-company.php');
});

$app->post('/company/insert', function () use ($app) {
    $app->render('ins-company.php');
});

$app->get('/company/update/:p', function ($p) use ($app) {
    $data = array("data"=>array("idcia"=>$p));
    $app->render('upd-company.php', $data, 200);         
});

$app->get('/company/delete/:p', function ($p) use ($app) {
    $data = array("data"=>array("idcia"=>$p));
    $app->render('../model/del-company.php', $data, 200);
    //$app->render('\../model/del-company.php', $data, 200);
});


/* ---------------------------------------------------------------------------
*  ROTAS PARA CRUD USUARIOS
* --------------------------------------------------------------------------- */

// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
if (strpos($url, '/users') !== false && !($_SESSION['s_superuser']) && !($_SESSION['s_admin']) ):
    header("Location: /");    
    exit;
endif;
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *

$app->get('/users', function () use ($app){
    $app->render('tab-users.php');   
});

$app->get('/users/insert', function () use ($app) {
    $app->render('ins-users.php');
});

$app->post('/users/insert', function () use ($app) {
    $app->render('ins-users.php');
});

$app->get('/users/update/:p', function ($p) use ($app) {
    $data = array("data"=>array("iduser"=>$p));
    $app->render('upd-users.php', $data, 200);         
});

$app->get('/users/delete/:p', function ($p) use ($app) {
    $data = array("data"=>array("iduser"=>$p));
    $app->render('../model/del-users.php', $data, 200);
    //$app->render('\../model/del-users.php', $data, 200);
});


/* ---------------------------------------------------------------------------
*  ROTAS PARA CRUD CATEGORIAS DE AMBIENTES
* --------------------------------------------------------------------------- */

// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
if (strpos($url, '/categories') !== false && !($_SESSION['s_superuser']) && !($_SESSION['s_admin']) ):
    header("Location: /");    
    exit;
endif;
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *

$app->get('/categories', function () use ($app){
    $app->render('tab-categories.php');    
});

$app->get('/categories/insert', function () use ($app) {
    $app->render('ins-categories.php');
});

$app->post('/categories/insert', function () use ($app) {
    $app->render('ins-categories.php');
});

$app->get('/categories/update/:p', function ($p) use ($app) {
    $data = array("data"=>array("idcat"=>$p));
    $app->render('upd-categories.php', $data, 200);         
});

$app->get('/categories/delete/:p', function ($p) use ($app) {
    $data = array("data"=>array("idcat"=>$p));
    $app->render('../model/del-categories.php', $data, 200);
    //$app->render('\../model/del-categories.php', $data, 200);
});



/* ---------------------------------------------------------------------------
*  ROTAS PARA CRUD DATABASES
* --------------------------------------------------------------------------- */

// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
if (strpos($url, '/databases') !== false && !($_SESSION['s_superuser']) && !($_SESSION['s_admin']) ):
    header("Location: /");    
    exit;
endif;
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *

$app->get('/databases', function () use ($app){
    $app->render('tab-databases.php');    
});

$app->get('/databases/insert', function () use ($app) {
    $app->render('ins-databases.php');
});

$app->post('/databases/insert', function () use ($app) {
    $app->render('ins-databases.php');
});

$app->get('/databases/update/:p1/:p2', function ($iddb, $idcia) use ($app) {
    $data = array("data"=>array("iddb"=>$iddb, "idcia"=>$idcia));
    $app->render('upd-databases.php', $data, 200);         
});

$app->get('/databases/delete/:p', function ($p) use ($app) {
    $data = array("data"=>array("iddb"=>$p));
    $app->render('../model/del-databases.php', $data, 200);
    //$app->render('\../model/del-databases.php', $data, 200);
});




/* ---------------------------------------------------------------------------
*  ROTAS PARA CRUD (tela de log do logins_log)
* --------------------------------------------------------------------------- */


$app->get('/admlogins', function () use ($app){       
    $app->render('tab-admlogins.php');
});

$app->post('/admlogins', function () use ($app){       
    $app->render('tab-admlogins.php');
});

/*
$app->POST('/admlogins/insert', function () use ($app){       
    $app->render('\../model/ins-admlogins.php');
});
*/

$app->get('/admlogins/update/:id', function ($id) use ($app) {
    $data = array("data"=>array("id"=>$id));
    $app->render('upd-admlogins.php', $data, 200);         
});

$app->get('/admlogins/delete/:id', function ($id) use ($app) {
    $data = array("data"=>array("id"=>$id));
    $app->render('../model/del-admlogins.php', $data, 200);
    //$app->render('\../model/del-admlogins.php', $data, 200);
});

$app->get('/admlogins/lockuser/:username', function ($username) use ($app) {
    $data = array("data"=>array("username"=>$username));
    $app->render('../model/lock-admlogins.php', $data, 200);
    //$app->render('\../model/lock-admlogins.php', $data, 200);
});



/* ---------------------------------------------------------------------------
*  ROTAS PARA CRUD (tela de log do logins)
* --------------------------------------------------------------------------- */

$app->get('/admloginslog/detail/:id_log/:iddb/:killed/:days', function ($id_log, $iddb, $killed, $days) use ($app){      
    $data = array("data"=>array
                        ( "id_log"=> $id_log, 
                          "iddb"  => $iddb,
                          "killed"  => $killed,
                          "days"  => $days
                        )
                  );

    $app->render('tab-det-admloginslog.php', $data, 200);
});

$app->get('/admloginslog/insclick/:id_log', function ($id_log) use ($app){     
    $data = array("data"=>array
                        ( "id_log"=> $id_log)
                  );

    $app->render('../model/ins-admlogins-click.php', $data, 200);
    //$app->render('\../model/ins-admlogins-click.php', $data, 200);
});


$app->get('/admloginslog/:iddb/:idcat', function ($iddb, $idcat) use ($app){      
    $data = array("data"=>array("iddb"=>$iddb, "idcat"=>$idcat));
    $app->render('tab-admloginslog.php', $data, 200);
});






/* ---------------------------------------------------------------------------
*  ROTAS Arquivar registros da ADM_LOGINS_LOG
* --------------------------------------------------------------------------- */

$app->get('/admloginslog/archive/:id_log/:iddb', function ($id_log, $iddb) use ($app){      
    $data = array("data"=>array
                        ( "id_log"=> $id_log, 
                          "iddb"  => $iddb
                        )
                  );

    $app->render('../model/archive-admloginslog.php', $data, 200);
});







/* ---------------------------------------------------------------------------
*  ROTAS PARA CRUD (tela de CRUD LOGINSLOGINS)
* --------------------------------------------------------------------------- */

$app->get('/loginslogons', function () use ($app){      
    $app->render('tab-loginslogons.php');
});


/* ---------------------------------------------------------------------------
*  ROTAS PARA CRUD (tela de CRUD LOGINSTRACE)
* --------------------------------------------------------------------------- */

$app->get('/loginstrace', function () use ($app){      
    $app->render('tab-loginstrace.php');
});


/* ---------------------------------------------------------------------------
*  ROTAS PARA CRUD (tela de CRUD LOGINS_TO_KILL)
* --------------------------------------------------------------------------- */

$app->get('/loginstokill', function () use ($app){      
    $app->render('tab-loginstokill.php');
});




/* ---------------------------------------------------------------------------
*  ROTAS PARA CRUD (tela de CRUD TOOLS)
* --------------------------------------------------------------------------- */

$app->get('/loginstools', function () use ($app){      
    $app->render('tab-loginstools.php');
});


$app->get('/loginstools/delete/:tool', function ($tool) use ($app){      
    $data = array("data"=>array("tool"=>$tool));
    $app->render('../model/del-loginstools.php', $data, 200);
    //$app->render('\../model/del-loginstools.php', $data, 200);
});


/* ---------------------------------------------------------------------------
*  DASHBOARD para os registros de ADM_LOGINS_LOG de todas ionstâncias por categoria
* --------------------------------------------------------------------------- */


$app->get('/admloginslogall/:idcat', function ($idcat) use ($app){   
    $data = array("data"=>array("idcat"=>$idcat));
    $app->render('tab-admloginslog-all-logs.php', $data, 200);
});


$app->get('/admloginslogall/:idcat/:days', function ($idcat, $days) use ($app){      
    $data = array("data"=>array("idcat"=>$idcat, "days"=>$days));
    $app->render('tab-admloginslog-all.php', $data, 200);
});




/* ---------------------------------------------------------------------------
*  ROTAS PARA CRUD (tela de CRUD BLACKLIST)
* --------------------------------------------------------------------------- */

$app->get('/blacklist', function () use ($app){      
    $app->render('tab-blacklist.php');
});

$app->get('/blacklist/delete/:id', function ($id) use ($app){      
    $data = array("data"=>array("id"=>$id));
    $app->render('../model/del-blacklist.php', $data, 200);
});



/* ---------------------------------------------------------------------------
*  ROTAS PARA KPIs
* --------------------------------------------------------------------------- */

// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
if (strpos($url, '/kpi') !== false && !($_SESSION['s_superuser']) && !($_SESSION['s_admin']) ):
    header("Location: /");    
    exit;
endif;
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *

/*
$app->get('/kpi', function () use ($app){
    $app->render('charts-view.php');    
});
*/

$app->get('/kpi/:idcat/:dayAccess/:dayRules', function ($idcat, $dayAccess, $dayRules) use ($app){      
    $data = array("data"=>array("idcat"=>$idcat, "dayAccess"=>$dayAccess, "dayRules"=>$dayRules ));
    $app->render('charts-view.php', $data, 200);
});


$app->run();


?>