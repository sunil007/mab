<?php
class Accountant{
		
		public $id;
		public $mobile;
		public $name;
		public $address;
		
		public function populateByRow($row){
			$this->id = $row['id'];
			$this->mobile = $row['mobile'];
			$this->name = $row['name'];
			$this->address = $row['address'];
		}
		
		function populateByMap($map){
			$this->populateByRow($map);
		}
		
		public function toMapObject(){
			$map = array();
			$map['id'] = $this->id;
			$map['mobile'] = $this->mobile;
			$map['name'] = $this->name;
			$map['address'] = $this->address;
		}
		
		public static function getAccountantById($id){
			/*Checking in Cache*/
			$cacheFileKey = "Accountant-".$id;
			$cacheResult = FileSystemCaching::getCacheFromObjectsWithTTL($cacheFileKey, 'Accountant', 3600, 'Accountant');
			if($cacheResult != null && gettype($cacheResult) == 'array' && sizeof($cacheResult) > 0){
				return $cacheResult[0];
			}
			
			/*Getting from Database*/
			$query = "select * from accountant where id = '".$id."'";
			$resultSet = dbo::getResultSetForQuery($query);
			
			if($resultSet != false){
				$row = mysqli_fetch_array($resultSet);
				$accountant = new Accountant();
				$accountant->populateByRow($row);
				
				/*Storing in Cache Files*/
				FileSystemCaching::setCacheFromObjectsWithTTL($cacheFileKey, array($accountant), 3600, 'Accountant');
				return $accountant;
			}
			return false;
		}
	}
	
?>