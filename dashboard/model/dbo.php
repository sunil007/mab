<?php

class DBReadConfig{	
	public $user;
	public $host;
	public $db;
	public $password;
	
	function __construct(){
		$this->user="root";
		$this->host="localhost";
		$this->db="mabdb";
		$this->password="";
    }
}

class DBConfig{	
	public $user;
	public $host;
	public $db;
	public $password;
	
	function __construct(){
		$this->user="root";
		$this->host="localhost";
		$this->db="mabdb";
		$this->password="";
    }
}

class dbo{
	public function getdboResultSetForQuery($query){
		//echo "QUERYFOUND:".$query;
		if(stripos(trim($query), "select" ) === 0)
			$dbConfig = new DBReadConfig();
		else
			$dbConfig = new DBConfig();
		$link = mysqli_connect($dbConfig->host, $dbConfig->user,$dbConfig->password);
		$link->set_charset("utf8");
		mysqli_select_db($link,$dbConfig->db);
		$result = mysqli_query($link,$query);
		$num_rows = 0;
		if($result)
			$num_rows = mysqli_num_rows($result);
		if($num_rows > 0)
			return $result;
		else
			return false;
	}
	
	public static function getResultSetForQuery($query){
		//echo "QUERYFOUND:".$query;
		
		if(stripos(trim($query), "select" ) === 0)
			$dbConfig = new DBReadConfig();
		else
			$dbConfig = new DBConfig();
		//echo $dbConfig->host;
		$link = mysqli_connect($dbConfig->host, $dbConfig->user,$dbConfig->password);
		$link->set_charset("utf8");
		mysqli_select_db($link,$dbConfig->db);
		$result = mysqli_query($link,$query);
		//var_dump($result);
		$num_rows = 0;
		if($result)
			$num_rows = mysqli_num_rows($result);
		if($num_rows > 0)
			return $result;
		else
			return false;
	}
	
	public static function getResultSetForQueryFromWriteDB($query){
		//echo "QUERYFOUND:".$query;
		
		$dbConfig = new DBConfig();
		$link = mysqli_connect($dbConfig->host, $dbConfig->user,$dbConfig->password);
		mysqli_select_db($link,$dbConfig->db);
		$result = mysqli_query($link, $query);
		$num_rows = 0;
		if($result)
			$num_rows = mysqli_num_rows($result);
		if($num_rows > 0)
			return $result;
		else
			return false;
	}
	
	public static function insertRecord($query){
		
		$dbConfig = new DBConfig();
		$link = mysqli_connect($dbConfig->host, $dbConfig->user,$dbConfig->password);
		mysqli_select_db($link,$dbConfig->db);
		$result = mysqli_query($link, $query);
		return $result;
	}
	
	public static function insertRecordPreparedStatement($query, $paramsArray, $types){
		
		$dbConfig = new DBConfig();
		$link = new mysqli($dbConfig->host,$dbConfig->user,$dbConfig->password,$dbConfig->db);
		$link->set_charset("utf8");
		$stmt = $link->prepare($query);
		$stmt->bind_param($types,...$paramsArray);
		$stmt->execute();
	}
	
	public static function deleteRecord($query){
		
		$dbConfig = new DBConfig();
		$link = mysqli_connect($dbConfig->host, $dbConfig->user,$dbConfig->password);
		mysqli_select_db($link,$dbConfig->db);
		$result = mysqli_query($link, $query);
		return $result;
	}
	
	public static function updateRecord($query){
		
		$dbConfig = new DBConfig();
		$link = mysqli_connect($dbConfig->host, $dbConfig->user,$dbConfig->password);
		mysqli_select_db($link,$dbConfig->db);
		$result = mysqli_query($link, $query);
		return $result;
	}
	
	public static function getResultSetForMultiQuery($query){
		
		//echo "QUERYFOUND:".$query;
		if(stripos(trim($query), "select" ) === 0)
			$dbConfig = new DBReadConfig();
		else
			$dbConfig = new DBConfig();
		$link = mysqli_connect($dbConfig->host, $dbConfig->user,$dbConfig->password);
		mysqli_select_db($link,$dbConfig->db);
		mysqli_multi_query($link, $query);
		$resultArray = array();
		do {
			if ($result = mysqli_store_result($link)) {
				array_push($resultArray, $result);
			}
		}while (mysqli_next_result($link));
		return $resultArray;
	}
	
	public static function updateMultipleRecords($query){
		
		$dbConfig = new DBConfig();
		$link = mysqli_connect($dbConfig->host, $dbConfig->user,$dbConfig->password);
		mysqli_select_db($link,$dbConfig->db);
		mysqli_multi_query($link, $query);
		$resultArray = array();
		do {
			if ($result = mysqli_store_result($link)) {
				array_push($resultArray, $result);
			}
		}while (mysqli_next_result($link));
		return $resultArray;
	}

}
?>