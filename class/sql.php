<?php 

//namespace Hcode\DB;

class Sql {


	private $conn;

	public function __construct($db="", $hostname="", $username="", $password="", $dbname="")
	{
		if ($db.$hostname.$username.$password.$dbname == ""):
			$hostname	= "localhost";
			$username	= "root";
			$password	= "";
			$dbname		= "securedb";
			$db			= "mysql";

			$this->conn = new \PDO(
				"{$db}:dbname={$dbname};host={$hostname}", $username, $password
			);
	
		else:

			$tns = " (DESCRIPTION =(ADDRESS_LIST =(ADDRESS = (PROTOCOL = TCP) (HOST = ".$hostname.")(PORT = 1521)))(CONNECT_DATA = (SID = ".$dbname.")))";

			try{
				$this->conn = new \PDO(
					//"{$db}:dbname={$dbname};$tns", $username, $password
					"oci:dbname=".$tns,$username,$password
				);
			}catch(PDOException $e){
				echo ($e->getMessage());
			}
			

		endif;


		

	}

	private function setParams($statement, $parameters = array())
	{

		foreach ($parameters as $key => $value) {
			
			$this->bindParam($statement, $key, $value);

		}

	}

	private function bindParam($statement, $key, $value)
	{

		$statement->bindParam($key, $value);

	}


	public function sql($moduloName, $rawQuery, $params = array())
	{

		$stmt = $this->conn->prepare($rawQuery);

		$this->setParams($stmt, $params);

		$result = $stmt->execute();

		
		if (!$result): 	

			
			$error_message = $stmt->errorInfo()[2];	
			$_SESSION['msg'] = "Erro na transação com banco de dados: " . $error_message;

			$conn2 = new Sql();
			$r = $conn2->sql( basename(__FILE__), 
							 "INSERT INTO adm_errors (iduser, frommodule, sqltext, message) 
							  VALUES (:IDUSER, :MODULENAME, :SQLTEXT, :ERROR_MESSAGE)", 
							  array
								(":IDUSER"=>$_SESSION['s_iduser'],
								 ":MODULENAME"=>$moduloName,
								 ":SQLTEXT"=>$rawQuery,
								 ":ERROR_MESSAGE"=>$error_message
								)
							);

		endif;
		
		return $stmt->fetchAll(PDO::FETCH_ASSOC);


	}



	/*
	public function sql($rawQuery, $params = array())
	{

		$stmt = $this->conn->prepare($rawQuery);

		$this->setParams($stmt, $params);

		return $stmt; //->execute();

	}
*/

	public function select($rawQuery, $params = array()):array
	{

		$stmt = $this->conn->prepare($rawQuery);

		$this->setParams($stmt, $params);

		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);

	}

}

 ?>

