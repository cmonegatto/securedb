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

$app->get('/user', function () use ($app){
    $app->render('user.php');    
});

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