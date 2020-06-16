<?php 

//namespace Hcode\DB;

class Sql {

	const HOSTNAME = "localhost";
	const USERNAME = "root";
	const PASSWORD = "";
	const DBNAME = "securedb";

	private $conn;

	public function __construct()
	{

		$this->conn = new \PDO(
			"mysql:dbname=".Sql::DBNAME.";host=".Sql::HOSTNAME, Sql::USERNAME, Sql::PASSWORD
		);

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
		
			echo "Houve um erro na requisição com o banco de dados! Entre em contato com o Administrador do Sistema!"; 

			$error_message = $stmt->errorInfo()[2];

			$conn2 = new Sql();
			$r = $conn2->sql(basename(__FILE__), "insert into adm_errors (iduser, frommodule, sqltext, message) values (:ID, :MODULENAME, :SQLTEXT, :ERROR_MESSAGE)", array
								(":ID"=>999, 
								 ":MODULENAME"=>$moduloName,
								 ":SQLTEXT"=>$rawQuery,
								 ":ERROR_MESSAGE"=>$error_message
								)
							);
			die();

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

