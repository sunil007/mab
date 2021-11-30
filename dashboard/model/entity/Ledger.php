<?php
class Ledger{
		
		public $id;
		public $buid;
		public $gst_no;
		public $legal_name;
		public $trade_name;
		public $address;
		public $state;
		public $type;
		public $pincode;
		public $opening_balance;
		public $opening_balance_date;
		public $branch_name;
		public $account_no;
		public $ifsc;
		public $ledger_group_id;
		
		public function populateByRow($row){
			$this->id = $row['id'];
			$this->buid = $row['buid'];
			$this->gst_no = $row['gst_no'];
			$this->legal_name = $row['legal_name'];
			$this->trade_name = $row['trade_name'];
			$this->address = $row['address'];
			$this->state = $row['state'];
			$this->type = $row['type'];
			$this->pincode = $row['pincode'];
			$this->opening_balance = $row['opening_balance'];
			$this->opening_balance_date = $row['opening_balance_date'];
			$this->branch_name = $row['branch_name'];
			$this->account_no = $row['account_no'];
			$this->ifsc = $row['ifsc'];
			$this->ledger_group_id = $row['ledger_group_id'];
		}
		
		function populateByMap($map){
			$this->populateByRow($map);
		}
		
		public function toMapObject(){
			$map = array();
			$map['id'] = $this->id;
			$map['buid'] = $this->buid;
			$map['gst_no'] = $this->gst_no;
			$map['legal_name'] = $this->legal_name;
			$map['trade_name'] = $this->trade_name;
			$map['address'] = $this->address;
			$map['state'] = $this->state;
			$map['type'] = $this->type;
			$map['pincode'] = $this->pincode;
			$map['opening_balance'] = $this->opening_balance;
			$map['opening_balance_date'] = $this->opening_balance_date;
			$map['branch_name'] = $this->branch_name;
			$map['account_no'] = $this->account_no;
			$map['ifsc'] = $this->ifsc;
			$map['ledger_group_id'] = $this->ledger_group_id;
		}
		
		public static function getLedgerById($id){
			/*Checking in Cache*/
			$cacheFileKey = "Ledger-".$id;
			$cacheResult = FileSystemCaching::getCacheFromObjectsWithTTL($cacheFileKey, 'Ledger', 3600, 'Ledger');
			if($cacheResult != null && gettype($cacheResult) == 'array' && sizeof($cacheResult) > 0){
				return $cacheResult[0];
			}
			
			/*Getting from Database*/
			$query = "select * from ledger where id = '".$id."'";
			$resultSet = dbo::getResultSetForQuery($query);
			
			if($resultSet != false){
				$row = mysqli_fetch_array($resultSet);
				$ledger = new Ledger();
				$ledger->populateByRow($row);
				
				/*Storing in Cache Files*/
				FileSystemCaching::setCacheFromObjectsWithTTL($cacheFileKey, array($ledger), 3600, 'Ledger');
				return $ledger;
			}
			return false;
		}
	}
	
?>