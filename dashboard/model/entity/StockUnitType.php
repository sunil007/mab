<?php
class StockUnitType{
		
		public $id;
		public $symbol;
		public $name;
		public $type; 
		
		public function populateByRow($row){
			$this->id = $row['id'];
			$this->symbol = $row['symbol'];
			$this->name = $row['name'];
			$this->type = $row['type'];
		}
		
		function populateByMap($map){
			$this->populateByRow($map);
		}
		
		public function toMapObject(){
			$map = array();
			$map['id'] = $this->id;
			$map['symbol'] = $this->symbol;
			$map['name'] = $this->name;
			$map['type'] = $this->type;
		}
		
		public static function getStockUnitTypeById($id){
			/*Checking in Cache*/
			$cacheFileKey = "StockUnitType-".$id;
			$cacheResult = FileSystemCaching::getCacheFromObjectsWithTTL($cacheFileKey, 'StockUnitType', 3600, 'StockUnitType');
			if($cacheResult != null && gettype($cacheResult) == 'array' && sizeof($cacheResult) > 0){
				return $cacheResult[0];
			}
			
			/*Getting from Database*/
			$query = "select * from stock_unit_type where id = '".$id."'";
			$resultSet = dbo::getResultSetForQuery($query);
			
			if($resultSet != false){
				$row = mysqli_fetch_array($resultSet);
				$stockUnitType = new StockUnitType();
				$stockUnitType->populateByRow($row);
				
				/*Storing in Cache Files*/
				FileSystemCaching::setCacheFromObjectsWithTTL($cacheFileKey, array($stockUnitType), 3600, 'StockUnitType');
				return $stockUnitType;
			}
			return false;
		}
	}
	
?>