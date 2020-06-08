<?php


$utf8 = header("Content-Type: text/html; charset=utf-8");
$link = new mysqli('localhost','root', '', 'securedb',);
$link->set_charset('utf8');


/*
<?php



	class db {
		 private $host = 'localhost';
		 private $usuario = 'root';
		 private $senha = '';
		 private $database = 'twitter_clone';


		 public function conecta_mysql(){

		 	// criar a conexão
		 	$con = mysqli_connect($this->host, $this->usuario, $this->senha, $this->database);

		 	// ajustar o charset de comunicação entre a alicação e o DB
		 	mysqli_set_charset($con, 'utf8');

		 	// verificar se houve erro de conexão
		 	if(mysqli_connect_errno()){
		 		echo 'Erro ao tentar se conectar com o DB MySQL: ' . mysqli_connect_errno();		
		 	}

		 	return $con;

		 }
	}

*/
	
?>