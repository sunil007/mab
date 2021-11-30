<?php
class Stock{
		
		public $id;
		public $buid;
		public $name;
		public $unit_type_id;
		public $type; //GOODS/SERVICE
		public $hsn;
		public $opening_quantity;
		public $opening_value;
		public $opening_date;
		
		public function populateByRow($row){
			$this->id = $row['id'];
			$this->buid = $row['buid'];
			$this->name = $row['name'];
			$this->unit_type_id = $row['unit_type_id'];
			$this->type = $row['type'];
			$this->hsn = $row['hsn'];
			$this->opening_quantity = $row['opening_quantity'];
			$this->opening_value = $row['opening_value'];
			$this->opening_date = $row['opening_date'];
		}
		
		function populateByMap($map){
			$this->populateByRow($map);
		}
		
		public function toMapObject(){
			$map = array();
			$map['id'] = $this->id;
			$map['buid'] = $this->buid;
			$map['name'] = $this->name;
			$map['unit_type_id'] = $this->unit_type_id;
			$map['type'] = $this->type;
			$map['hsn'] = $this->hsn;
			$map['opening_quantity'] = $this->opening_quantity;
			$map['opening_value'] = $this->opening_value;
			$map['opening_date'] = $this->opening_date;
		}
		
		public static function getStockById($id){
			/*Checking in Cache*/
			$cacheFileKey = "Stock-".$id;
			$cacheResult = FileSystemCaching::getCacheFromObjectsWithTTL($cacheFileKey, 'Stock', 3600, 'Stock');
			if($cacheResult != null && gettype($cacheResult) == 'array' && sizeof($cacheResult) > 0){
				return $cacheResult[0];
			}
			
			/*Getting from Database*/
			$query = "select * from stock where id = '".$id."'";
			$resultSet = dbo::getResultSetForQuery($query);
			
			if($resultSet != false){
				$row = mysqli_fetch_array($resultSet);
				$stock = new Stock();
				$stock->populateByRow($row);
				
				/*Storing in Cache Files*/
				FileSystemCaching::setCacheFromObjectsWithTTL($cacheFileKey, array($stock), 3600, 'Stock');
				return $stock;
			}
			return false;
		}
	}
	
?>