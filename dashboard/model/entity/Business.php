<?php
class Business{
		
		public $id;
		public $user_id;
		public $legal_name;
		public $trade_name;
		public $address;
		public $state;
		public $pincode;
		public $gstno;
		public $mapid; //THIS IS TO MAP BUSINESS WITH TRANSACTION AND ENTITY TABLE (VARCHAR)
		
		public function populateByRow($row){
			$this->id = $row['id'];
			$this->user_id = $row['user_id'];
			$this->legal_name = $row['legal_name'];
			$this->trade_name = $row['trade_name'];
			$this->address = $row['address'];
			$this->state = $row['state'];
			$this->pincode = $row['pincode'];
			$this->gstno = $row['gst_no'];
			$this->mapid = $row['map_id'];
		}
		
		function populateByMap($map){
			$this->populateByRow($map);
		}
		
		public function toMapObject(){
			$map = array();
			$map['id'] = $this->id;
			$map['user_id'] = $this->user_id;
			$map['legal_name'] = $this->legal_name;
			$map['trade_name'] = $this->trade_name;
			$map['address'] = $this->address;
			$map['state'] = $this->state;
			$map['pincode'] = $this->pincode;
			$map['gst_no'] = $this->gstno;
			$map['map_id'] = $this->mapid;
		}
		
		public static function getMaxId(){
			$maxIdQuery = "select max(id) as id from business";
			$maxIdResultSet = dbo::getResultSetForQueryFromWriteDB($maxIdQuery);
			if($maxIdResultSet){
				$maxRow = mysqli_fetch_array($maxIdResultSet);
				$maxid = $maxRow['id'];
				return $maxid;
			}
			return 0;
		}
		
		public static function getBusinessById($id){
			/*Checking in Cache*/
			$cacheFileKey = "Business-".$id;
			$cacheResult = FileSystemCaching::getCacheFromObjectsWithTTL($cacheFileKey, 'Business', 3600, 'Business');
			if($cacheResult != null && gettype($cacheResult) == 'array' && sizeof($cacheResult) > 0){
				return $cacheResult[0];
			}
			
			/*Getting from Database*/
			$query = "select * from business where id = '".$id."'";
			$resultSet = dbo::getResultSetForQuery($query);
			
			if($resultSet != false){
				$row = mysqli_fetch_array($resultSet);
				$business = new Business();
				$business->populateByRow($row);
				
				/*Storing in Cache Files*/
				FileSystemCaching::setCacheFromObjectsWithTTL($cacheFileKey, array($business), 3600, 'Business');
				return $business;
			}
			return false;
		}
		
		public static function getAllBusinessByUserId($id){
			/*Checking in Cache*/
			$cacheFileKey = "Business-User-".$id;
			$cacheResult = FileSystemCaching::getCacheFromObjectsWithTTL($cacheFileKey, 'Business', 3600, 'Business');
			if($cacheResult != null && gettype($cacheResult) == 'array' && sizeof($cacheResult) > 0){
				return $cacheResult;
			}
			
			/*Getting from Database*/
			$query = "select * from business where user_id = '".$id."'";
			$resultSet = dbo::getResultSetForQuery($query);
			$allBusiness = array();
			if($resultSet != false){
				while($row = mysqli_fetch_array($resultSet)){
					$business = new Business();
					$business->populateByRow($row);
					array_push($allBusiness, $business);
				}
			}
			
			/*Storing in Cache Files*/
			FileSystemCaching::setCacheFromObjectsWithTTL($cacheFileKey, $allBusiness, 3600, 'Business');
			return $business;
		}
		
		public static function addNewBusiness($user_id, $legal_name, $trade_name, $address, $state, $pincode, $gstno){
			
			$legal_name = Utility::cleanSpecialCharactersString($legal_name);
			$trade_name = Utility::cleanSpecialCharactersString($trade_name);
			$address = Utility::cleanSpecialCharactersString($address);
			$state = Utility::cleanSpecialCharactersString($state);
			$pincode = Utility::cleanSpecialCharactersString($pincode);
			$gstno = Utility::cleanSpecialCharactersString($gstno);
			$newid = Business::getMaxId() + 1;
			$mapId = number_format($newid/10, 0, '', ''); //LOGIC TO BREAK USER IN TO SEPARATE TABLES
			
			$query = "INSERT INTO business (`id`, `user_id`, `legal_name`, `trade_name`, `address`, `state`, `pincode`, `gstno`, `mapid`) VALUES ('".$newid."', '".$user_id."', '".$legal_name."', '".$trade_name."', '".$address."', '".$state."', '".$pincode."', '".$gstno."', '".$mapId."');";
			
			/*Transaction Table*/
			$query .=" CREATE TABLE IF NOT EXISTS `transaction_".$mapId."` (
						  `id` int(11) NOT NULL auto_increment,   
						  `bu_id` int(11) NOT NULL,       
						  `bill_no` varchar(32)  NOT NULL default '',     
						  `date`  timestamp NOT NULL,     
						  `type` varchar(32)  NOT NULL default '',
						  `amount` double(7,2) NOT NULL,   
						  `note` varchar(2048)  NOT NULL,
						  `bank_lid` int(11) NOT NULL,  
						  `transporter_lid` int(11) NOT NULL,  
						  `lr_no` varchar(64)  NOT NULL default '',    
						  `lr_date` timestamp NULL,     
						  `vehicle_no` varchar(32)  NOT NULL default '', 
						  `po_no` varchar(32)  NOT NULL default '', 
						  `po_date` timestamp NULL, 
						  `eway_bill_no` varchar(32)  NOT NULL default '', 
						  `eway_bill_date` timestamp NULL, 
						  `round_off` double(3,2)  NOT NULL, 
						  `document_url` varchar(1024)  NOT NULL, 
						   PRIMARY KEY  (`id`),
                           INDEX business_index(bu_id)
						);";
			
			/*Entry Table*/
			$query .=" CREATE TABLE IF NOT EXISTS `entry_".$mapId."` (
						  `id` int(11) NOT NULL auto_increment,   
						  `bu_id` int(11) NOT NULL,    
						  `tran_id` int(11) NOT NULL,    
						  `type` varchar(4)  NOT NULL default '',    
						  `item_id` int(11) NOT NULL,       
						  `item_type` varchar(32)  NOT NULL default '',   
						  `quantity` double(7,3) NOT NULL,  						  
						  `rate` double(7,2) NOT NULL,  						  
						  `cgst` double(3,2)  NOT NULL, 
						  `sgst` double(3,2)  NOT NULL, 
						  `igst` double(3,2)  NOT NULL, 
						  `cess` double(7,2)  NOT NULL, 
						  `tcs` double(3,2)  NOT NULL, 
						  `note` varchar(2048)  NOT NULL,
						   PRIMARY KEY  (`id`),
                           INDEX business_index(bu_id),
                           INDEX item_index(item_id, item_type)
						);";
			dbo::updateMultipleRecords($query);
			return true;
		}
	}
	
?>