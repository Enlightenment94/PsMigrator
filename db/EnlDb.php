<?php

class EnlDb{
	public $host;
	public $username;
	public $password;
	public $database;
	public $port;
	public $conn;

	public function __construct($host, $username, $password, $database, $port){
		$this->host     = $host;
		$this->username = $username;
		$this->password = $password;
		$this->database = $database; 
		$this->port     = $port;
	}

	public function conn(){
		$conn = new mysqli($this->host, $this->username, $this->password, $this->database, $this->port);
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		} else {
			$this->conn = $conn;
			return $conn;
		}
	}

	public function setDb($dbName){
		if (mysqli_select_db($this->conn, $dbName)) {
		    echo "Pomyślnie zmieniono bazę danych na: " . $dbName;
		} else {
		    echo "Wystąpił błąd podczas zmiany bazy danych: " . mysqli_error($this->conn);
		}
	}

	public function execSql($sql) {
		$arr = array();
		//echo $this->conn->real_escape_string($sql) . "</br>";
	    //$res = $this->conn->query($this->conn->real_escape_string($sql));
		//echo $sql;
		//echo "</br>";
		//die();
	    $res = $this->conn->query($sql);
	    if (isset($res->num_rows) ) {
	    	if($res->num_rows> 0){
		        while($row = $res->fetch_assoc()) {
		            //var_dump($row);
		            array_push($arr, $row);
		        }
	    	}
	    } else {
	    	if($this->conn->error != ''){
	        	echo "Query failed: " . $this->conn->error;
	    	}
	    }
	    return $arr;
	}

	public function insertMaker($table, $row){
		$insertQuery = "INSERT INTO " . $table . " (";

		// Pobierz nazwy kolumn
		foreach ($row as $key => $value) {
			//$insertQuery .= "`" . $key . "`, ";
		    $insertQuery .= "" . $key . ", ";
		}

		$insertQuery = rtrim($insertQuery, ', ');  // Usuń ostatnią przecinek

		$insertQuery .= ") VALUES (";

		// Dodaj wartości
		foreach ($row as $value) {
			if(!isset($value)){
		    	$insertQuery .= " " . "DEFAULT" . " , ";
			}else{
				$insertQuery .= " '" . $value . "' , ";
			}
		}
		$insertQuery = rtrim($insertQuery, ', ');  // Usuń ostatnią przecinek

		$insertQuery .= ")";
		return $insertQuery;
	}

	public function insertMakerPDO($table, $row){
		// Pobierz nazwy kolumn
		$columns = array_keys($row);
		$safeColumns = array_map(function($column) {
			return "`$column`";
		}, $columns);
		$columnsString = implode(', ', $safeColumns);
	
		// Utwórz zapytanie SQL z miejscami zastępczymi
		$placeholders = array_fill(0, count($columns), '?');
		$placeholdersString = implode(', ', $placeholders);
	
		$values = array_values($row);
	
		$insertQuery = "INSERT INTO `$table` ($columnsString) VALUES ($placeholdersString)";
		
		return ['query' => $insertQuery, 'values' => $values];
	}

	public function pdoExecuteParam($data){
	    $port = $this->port;
	    $dsn = 'mysql:host=' . $this->host . ';port=' . $port . ';dbname=' .  $this->database; 
	    $username = $this->username;
	    $password = $this->password;

	    try {
	        $dbh = new PDO($dsn, $username, $password);
	        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	        $query = $data['query'];
	        $values = $data['values'];

	        //echo $data['query'] . "\n";

	        $stmt = $dbh->prepare($query);
	        $stmt->execute($values);

	        //echo " Zapytanie wykonane poprawnie. " . "</br>\n";
	    } catch (PDOException $e) {
	        echo "Błąd wykonania zapytania: " . $e->getMessage();
	    }
	}

	public function checkExistingId($table, $columnId, $id){
		$port = $this->port;
	    $dsn = 'mysql:host=' . $this->host . ';port=' . $port . ';dbname=' .  $this->database; 
	    $username = $this->username;
	    $password = $this->password;
		$dbh = new PDO($dsn, $username, $password);
	    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


		$query = "SELECT COUNT(*) AS count FROM $table WHERE $columnId = :id";
		$stmt = $dbh->prepare($query);
		$stmt->bindParam(':id', $id, PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		
		return $result['count'] > 0; 
	}

	public function getNextId($table, $column){
		$port = $this->port;
	    $dsn = 'mysql:host=' . $this->host . ';port=' . $port . ';dbname=' .  $this->database; 
	    $username = $this->username;
	    $password = $this->password;
		$dbh = new PDO($dsn, $username, $password);
	    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


		$query = "SELECT MAX(`$column`) AS max_id FROM $table";
		$stmt = $dbh->prepare($query);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		
		$maxId = $result['max_id'];
		$nextId = $maxId ? $maxId + 1 : 1;
		
		return $nextId;
	}

	public function insertMakerPDONextId($table, $row){
		$values = array_values($row);
			
		foreach($row as $key => $value){
			if(strpos($key, 'id_') === 0){ 
				$existingId = $this->checkExistingId($table, $key, $row[$key]);
				if($existingId){
					$row[$key] = $this->getNextId($table, $key);
				}
			}
		}
	
		die();
		// Pobierz nazwy kolumn
		$columns = array_keys($row);
		$safeColumns = array_map(function($column) {
			return "`$column`";
		}, $columns);
		$columnsString = implode(', ', $safeColumns);
	
		// Utwórz zapytanie SQL z miejscami zastępczymi
		$placeholders = array_fill(0, count($columns), '?');
		$placeholdersString = implode(', ', $placeholders);
	
		$insertQuery = "INSERT INTO `$table` ($columnsString) VALUES ($placeholdersString)";
		
		return ['query' => $insertQuery, 'values' => array_values($row)];
	}
}