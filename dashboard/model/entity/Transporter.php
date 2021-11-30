<?php
class Transporter{
		
		public $id;
		public $buid;
		public $name;
		public $gst_no;
		public $address;
		
		public function populateByRow($row){
			$this->id = $row['id'];
			$this->buid = $row['buid'];
			$this->gst_no = $row['gst_no'];
			$this->name = $row['name'];
			$this->address = $row['address'];
		}
		
		function populateByMap($map){
			$this->populateByRow($map);
		}
		
		public function toMapObject(){
			$map = array();
			$map['id'] = $this->id;
			$map['buid'] = $this->buid;
			$map['gst_no'] = $this->gst_no;
			$map['name'] = $this->name;
			$map['address'] = $this->address;
		}
		
		public static function getTransporterById($id){
			/*Checking in Cache*/
			$cacheFileKey = "Transporter-".$id;
			$cacheResult = FileSystemCaching::getCacheFromObjectsWithTTL($cacheFileKey, 'Transporter', 3600, 'Transporter');
			if($cacheResult != null && gettype($cacheResult) == 'array' && sizeof($cacheResult) > 0){
				return $cacheResult[0];
			}
			
			/*Getting from Database*/
			$query = "select * from transporter where id = '".$id."'";
			$resultSet = dbo::getResultSetForQuery($query);
			
			if($resultSet != false){
				$row = mysqli_fetch_array($resultSet);
				$transporter = new Transporter();
				$transporter->populateByRow($row);
				
				/*Storing in Cache Files*/
				FileSystemCaching::setCacheFromObjectsWithTTL($cacheFileKey, array($transporter), 3600, 'Transporter');
				return $transporter;
			}
			return false;
		}
	}
	
?>