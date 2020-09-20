<?php 

//namespace Hcode\DB;

class Sql {


	private $conn;

	public function __construct($db="", $hostname="", $username="", $password="", $dbname="", $port="")
	{

		if ($db.$hostname.$username.$password.$dbname == "" ):
			$hostname	= $_SESSION['s_hostname'];
			$username	= $_SESSION['s_username'];
			$password	= $_SESSION['s_password'];
			$dbname		= $_SESSION['s_dbname'];
			$db			= $_SESSION['s_db'];

			$this->conn = new \PDO(
				"{$db}:dbname={$dbname};host={$hostname}", $username, $password
			);
	
		elseif ($db=='OCI'):

			$tns = " (DESCRIPTION =(ADDRESS_LIST =(ADDRESS = (PROTOCOL = TCP) (HOST = ".$hostname.")(PORT = ".$port.")))(CONNECT_DATA = (SID=".$dbname.")))";

			$db = strtolower($db);
			try{
				$this->conn = new \PDO(
					//"{$db}:dbname={$dbname};$tns", $username, $password
					"$db:dbname=".$tns,$username,$password
				);
			}catch(PDOException $e){
				//echo ($e->getMessage());
				$_SESSION['msg'] = "Ocorreu um erro na conexão com banco de dados. Verifique os dados de conexão - " . ($e->getMessage());
				$_SESSION['msg'] = $_SESSION['msg']."$db:dbname=".$tns .'/'. $username .'/**************';
			}

		elseif ($db=='SQLSRV'):

			$db = strtolower($db);
			try{
				$this->conn = new \PDO(
					"$db:Database=$dbname;server=$hostname\SQLEXPRESS;ConnectionPooling=0", $username, $password
//					"sqlsrv:Database=dbphp7;server=localhost\SQLEXPRESS;ConnectionPooling=0", "sa", "root"					
				);
			}catch(PDOException $e){
				//echo ($e->getMessage());
				$_SESSION['msg'] = "Ocorreu um erro na conexão com banco de dados. Verifique os dados de conexão - " . ($e->getMessage());
				$_SESSION['msg'] = $_SESSION['msg']."$db:dbname=".$dbname .'/'. $username .'/**************';

			}
			


		endif;

		//$conn = new PDO("sqlsrv:Database=dbphp7;server=localhost\SQLEXPRESS;ConnectionPooling=0", "sa", "root");

		

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


			if ( strpos($error_message, 'ORA-00001') || strpos($error_message, 'uplicate')>0 ):
				$_SESSION['msg'] = "Erro na transação com banco de dados: Esse registro já existe! - ".$error_message;
			else:
				$_SESSION['msg'] = "Erro na transação com banco de dados: " . $error_message;
			endif;

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

