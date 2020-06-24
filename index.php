<?php

session_start();
require_once("vendor/autoload.php");

//$app = new \Slim\Slim();


// During instantiation
$app = new \Slim\Slim(array(
    'templates.path' => './view'
));


/*
$app->get('/hello/:name', function ($name) {
    echo "Hello, " . $name;
});

*/

/*
//Exemplo passagem parametros
$app->get('/route/:parametro', function ($parametro) use ($app) {

    $data = array("data"=>array("parametro"=>$parametro)); 
    //set o arquivo de template
    $app->render('arquivo.php', $data, 200); 
});
*/

$app->get('/', function () use ($app) {  
    if (isset($_SESSION['s_iduser'])):
        $app->render('/start.php');        
    else:
        $app->render('/login.php');
    endif;
});

$app->get('/logout', function () use ($app) {  
    session_unset();
    $app->render('/login.php');        
});



/* ---------------------------------------------------------------------------
*  ROTAS PARA CRUD EMPRESA/COMPANHIA
* --------------------------------------------------------------------------- */

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
    $app->render('\../model/del-company.php', $data, 200);
});


/* ---------------------------------------------------------------------------
*  ROTAS PARA CRUD USUARIOS
* --------------------------------------------------------------------------- */

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
    $app->render('\../model/del-users.php', $data, 200);
});


/* ---------------------------------------------------------------------------
*  ROTAS PARA CRUD CATEGORIAS DE AMBIENTES
* --------------------------------------------------------------------------- */


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
    $app->render('\../model/del-categories.php', $data, 200);
});



/* ---------------------------------------------------------------------------
*  ROTAS PARA CRUD DATABASES
* --------------------------------------------------------------------------- */

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
    $app->render('\../model/del-databases.php', $data, 200);
});





/* ---------------------------------------------------------------------------
*  ROTAS PARA CRUD xxx
* --------------------------------------------------------------------------- */

$app->get('/lockapp', function () use ($app){
    $app->render('lockapp.php');    
});

$app->get('/admlogins/:iddb/:idcat', function ($iddb, $idcat) use ($app){    
    $data = array("data"=>array("iddb"=>$iddb, "idcat"=>$idcat));
    $app->render('tab-admlogins.php', $data, 200);
});

$app->run();

?>