<?php

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
    $app->render('/login.php');
});


/* ---------------------------------------------------------------------------
*  ROTAS PARA CRUD EMPRESA/COMPANHIA
* --------------------------------------------------------------------------- */

$app->get('/company', function () use ($app){
    $app->render('company/table.php');    
});


$app->get('/company/insert', function () use ($app) {
    $app->render('company/insert.php');
});

$app->post('/company/insert', function () use ($app) {
    $app->render('company/insert.php');
});

$app->get('/company/update/:p', function ($p) use ($app) {
    $data = array("data"=>array("idcia"=>$p));
    $app->render('/company/update.php', $data, 200);         
});

$app->get('/company/delete/:p', function ($p) use ($app) {
    $data = array("data"=>array("idcia"=>$p));
    $app->render('\../model/company/delete.php', $data, 200);
});


/* ---------------------------------------------------------------------------
*  ROTAS PARA CRUD USUARIOS
* --------------------------------------------------------------------------- */

$app->get('/users', function () use ($app){
    $app->render('users/table.php');    
});

$app->get('/users/insert', function () use ($app) {
    $app->render('users/insert.php');
});

$app->post('/users/insert', function () use ($app) {
    $app->render('users/insert.php');
});

$app->get('/users/update/:p', function ($p) use ($app) {
    $data = array("data"=>array("iduser"=>$p));
    $app->render('users/update.php', $data, 200);         
});

$app->get('/users/delete/:p', function ($p) use ($app) {
    $data = array("data"=>array("iduser"=>$p));
    $app->render('\../model/users/delete.php', $data, 200);
});


// ---------------------------------------------------------------------------

$app->get('/environment', function () use ($app){
    $app->render('environment.php');    
});

$app->get('/database', function () use ($app){
    $app->render('database.php');    
});

$app->get('/lockapp', function () use ($app){
    $app->render('lockapp.php');    
});


$app->run();

?>