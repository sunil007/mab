<?php
class User{
		
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
		
		public static function getUserById($id){
			/*Checking in Cache*/
			$cacheFileKey = "User-".$id;
			$cacheResult = FileSystemCaching::getCacheFromObjectsWithTTL($cacheFileKey, 'User', 3600, 'User');
			if($cacheResult != null && gettype($cacheResult) == 'array' && sizeof($cacheResult) > 0){
				return $cacheResult[0];
			}
			
			/*Getting from Database*/
			$query = "select * from user where id = '".$id."'";
			$resultSet = dbo::getResultSetForQuery($query);
			
			if($resultSet != false){
				$row = mysqli_fetch_array($resultSet);
				$user = new User();
				$user->populateByRow($row);
				
				/*Storing in Cache Files*/
				FileSystemCaching::setCacheFromObjectsWithTTL($cacheFileKey, array($user), 3600, 'User');
				return $user;
			}
			return false;
		}
	}
	
?>