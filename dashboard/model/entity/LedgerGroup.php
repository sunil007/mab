<?php
class LedgerGroup{
		
		public $id;
		public $ledger_type;
		public $buid;
		public $name;
		
		public function populateByRow($row){
			$this->id = $row['id'];
			$this->ledger_type = $row['ledger_type'];
			$this->buid = $row['buid'];
			$this->name = $row['name'];
		}
		
		function populateByMap($map){
			$this->populateByRow($map);
		}
		
		public function toMapObject(){
			$map = array();
			$map['id'] = $this->id;
			$map['ledger_type'] = $this->ledger_type;
			$map['buid'] = $this->buid;
			$map['name'] = $this->name;
		}
		
		public static function getLedgerGroupById($id){
			/*Checking in Cache*/
			$cacheFileKey = "LedgerGroup-".$id;
			$cacheResult = FileSystemCaching::getCacheFromObjectsWithTTL($cacheFileKey, 'LedgerGroup', 3600, 'LedgerGroup');
			if($cacheResult != null && gettype($cacheResult) == 'array' && sizeof($cacheResult) > 0){
				return $cacheResult[0];
			}
			
			/*Getting from Database*/
			$query = "select * from ledger_group where id = '".$id."'";
			$resultSet = dbo::getResultSetForQuery($query);
			
			if($resultSet != false){
				$row = mysqli_fetch_array($resultSet);
				$ledgergroup = new LedgerGroup();
				$ledgergroup->populateByRow($row);
				
				/*Storing in Cache Files*/
				FileSystemCaching::setCacheFromObjectsWithTTL($cacheFileKey, array($ledgergroup), 3600, 'LedgerGroup');
				return $ledgergroup;
			}
			return false;
		}
	}
	
?>