<?php

	class Utility{

		public static function convertUITimeInToBDTime($time){
			/*INPUT : 05:15 PM 
			  OUTPUT : 17:15 	
			*/
			$time_hr = substr($time,0,2);
			$time_min = substr($time,3,2);
			$time_ampm = substr($time,6,2);
			if($time_hr == '12')
				$time_hr = 0;
			if($time_ampm =="PM")
				$time_hr = $time_hr +12;
			$retuenString = $time_hr.":".$time_min.":00";
			return $retuenString;
		}
		
		public static function getValuesFromMapKeyFilterWithSufixAsString($mapArray, $sufix){
			$returnString = "";
			$len = strlen($sufix);
			foreach($mapArray as $key=>$value){
				$keyPart1 = substr($key, 0, $len);
				$keyPart2 = substr($key, $len);
				if($keyPart1 == $sufix && is_numeric($keyPart2))
					$returnString .= $value.",";
			}
			if(substr($returnString, -1) == ",")
				$returnString = substr_replace($returnString ,"",-1);
			return $returnString;
		}
		
		
		public static function putCookie($name, $value, $ttl, $path){
			$name = "_CM_".$name;
			if($path == null)
				$path = "/";
			setcookie($name, $value, time() + ($ttl), $path);
		}
		
		public static function getCookie($name){
			$name = "_CM_".$name;
			if(!isset($_COOKIE[$name])){
				return false;
			}
			$cookieValue = $_COOKIE[$name];
			return $cookieValue;
		}
		
		public static function deleteCookie($name){
			Utility::putCookie($name, "INVALID", -1000, "/");
		}
		
		public static function cleanSpecialCharactersString($string){
			$string = str_replace("'","",$string);
			$string = str_replace("=","",$string);
			$string = str_replace("\"","",$string);
			return $string;
		}
		
		public static function convertSecondsInToHumanString($seconds){
			if(is_numeric($seconds)){
				if($seconds == 0)return "0 Sec";
				
				$secondString = "";
				$hrs = floor($seconds / 3600);
				$mins = floor(($seconds / 60) % 60);
				$seconds = $seconds % 60;
				if($hrs > 0)
					$secondString .= $hrs." Hr ";
				if($mins > 0)
					$secondString .= $mins." Min ";
				if($seconds > 0)
					$secondString .= $seconds." Sec ";
				return $secondString;
			}
			return $seconds;
		}
		
		public static function currencyFormat($amount, $decimalPoint){
			$numberObtained = number_format($amount, $decimalPoint,".","");
			$decimalPoint = "";
			$actualNumber = "";
			$array = explode(".", $numberObtained);
			$actualNumber = $array[0];
			if(sizeof($array) > 1)
				$decimalPoint = $array[1];
			$newNumber = substr($actualNumber, -3);
			$actualNumber = substr($actualNumber, 0, -3);
			
			while($actualNumber != ""){
				$newNumber = substr($actualNumber, -2).",".$newNumber;
				$actualNumber = substr($actualNumber, 0, -2);
			}
			if($decimalPoint != "")
				$newNumber = $newNumber.".".$decimalPoint;
			return $newNumber;
		}
		
		public static function currencyFormatWithSymbol($amount, $decimalPoint){
			return "Rs. ".Utility::currencyFormat($amount, $decimalPoint);
		}
		
	}
?>