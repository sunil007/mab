<?php

class FileSystemCaching{	
	
	public static $cacheObjectFolderPath = __DIR__."/../cacheObjects";
	
	public static function getHashForKey($key){
		return hash("sha256",$key);
	}
	
	public static function setCacheForStudents($key, $stringValue){
		if(session_status() == PHP_SESSION_NONE)
			session_start();
		if(isset($_SESSION['type']) && ($_SESSION['type'] == 'STUDENT' || $_SESSION['type'] == 'PARENT')){	
			FileSystemCaching::setCache($key, $stringValue);
			return true;
		}
		return false;
	}
	
	public static function setCache($key, $stringValue){
		if(isset($_SESSION['clientName'])){
			$clientName = $_SESSION['clientName'];
			$cacheKey = $key.$clientName;
			$clientCacheObjectFolderPath = FileSystemCaching::$cacheObjectFolderPath."/".$clientName;
			$cacheFileName = FileSystemCaching::getHashForKey($cacheKey);
			if (!file_exists($clientCacheObjectFolderPath)) {
				mkdir($clientCacheObjectFolderPath, 0755, true);
			}
			$cacheFileLocation = $clientCacheObjectFolderPath."/".$cacheFileName.".cacheObj";
			$cacheFile = fopen($cacheFileLocation,"w");
			fwrite($cacheFile,$stringValue);
			fclose($cacheFile);
			return true;
		}
    }
	
	public static function getCacheForStudents($key){
		if(session_status() == PHP_SESSION_NONE)
			session_start();
		if(isset($_SESSION['type']) && ($_SESSION['type'] == 'STUDENT' || $_SESSION['type'] == 'PARENT')){	
			return FileSystemCaching::getCache($key);
		}
		return false;
	}
	
	public static function getCache($key){
		if(isset($_SESSION['clientName'])){
			$clientName = $_SESSION['clientName'];
			$cacheKey = $key.$clientName;
			$clientCacheObjectFolderPath = FileSystemCaching::$cacheObjectFolderPath."/".$clientName;
			$cacheFileName = FileSystemCaching::getHashForKey($cacheKey);
			$cacheFileLocation = $clientCacheObjectFolderPath."/".$cacheFileName.".cacheObj";
			if (file_exists($cacheFileLocation)) {
				$cachefile = fopen($cacheFileLocation, "r") or die("");
				$stringValue = fread($cachefile,filesize($cacheFileLocation));
				fclose($cachefile);
				return $stringValue;
			} else {
				return false;
			}
		}else{
			return false;
		}
	}
	
	public static function deleteCache($key){
		if(isset($_SESSION['clientName'])){
			$clientName = $_SESSION['clientName'];
			$cacheKey = $key.$clientName;
			$cacheFileName = FileSystemCaching::getHashForKey($cacheKey);
			$cacheFileLocation = FileSystemCaching::$cacheObjectFolderPath."/".$cacheFileName.".cacheObj";
			if (file_exists($cacheFileLocation)) {
				unlink($cacheFileLocation);
				return true;
			} else {
				return false;
			}
		}else{
			return false;
		}
	}
	
	public static function setCacheFromObjectsWithTTL($cacheKey, $objectArray, $ttl, $subFolder){
		$objectArrayMap = array();
		foreach($objectArray as $key=>$obj){
			if(method_exists($obj, "toMapObject")){
				$objMap = $obj->toMapObject();
				$objectArrayMap[$key] = $objMap;
			}else{
				return false;
			}
		}
		$cacheString = json_encode($objectArrayMap);
		FileSystemCaching::setCacheWithTTL($cacheKey, $cacheString, $ttl, $subFolder);
	}

	public static function setCacheWithTTL($key, $stringValue, $ttl, $subFolder){
		if(isset($_SESSION['clientName'])){
			$clientName = $_SESSION['clientName'];
			$cacheKey = $key."-".$clientName;
			$clientCacheObjectFolderPath = FileSystemCaching::$cacheObjectFolderPath."/".$clientName;
			if($subFolder != false && $subFolder != "." && $subFolder != null)
				$clientCacheObjectFolderPath = FileSystemCaching::$cacheObjectFolderPath."/".$clientName."/".$subFolder;
			
			//$cacheFileName = FileSystemCaching::getHashForKey($cacheKey);
			$cacheFileName = $cacheKey;
			if (!file_exists($clientCacheObjectFolderPath)) {
				mkdir($clientCacheObjectFolderPath, 0755, true);
			}
			$cacheFileLocation = $clientCacheObjectFolderPath."/".$cacheFileName.".cacheObj";
			$cacheFile = fopen($cacheFileLocation,"w");
			fwrite($cacheFile,date("Y-m-d H:i:s")."\n");
			fwrite($cacheFile,$stringValue);
			fclose($cacheFile);
			return true;
		}
	}

	public static function getCacheFromObjectsWithTTL($cacheKey, $objectType, $ttl, $subFolder){
		$objectArray = array();
		$objectMapString = FileSystemCaching::getCacheWithTTL($cacheKey, $ttl, $subFolder);
		if($objectMapString != false && $objectMapString != null){
			$objectMap = json_decode($objectMapString, true);
			foreach($objectMap as $key=>$objMap){
				$obj = new $objectType();
				if(method_exists($obj, "populateByMap")){
					$obj->populateByMap($objMap);
					$objectArray[$key] = $obj;
				}else{
					return null;
				}
			}
			return $objectArray;
		}else{
			return null;
		}
	}
	
	public static function getCacheWithTTL($key, $ttl, $subFolder){
		if(isset($_SESSION['clientName'])){
			$clientName = $_SESSION['clientName'];
			$cacheKey = $key."-".$clientName;
			$clientCacheObjectFolderPath = FileSystemCaching::$cacheObjectFolderPath."/".$clientName;
			if($subFolder != false && $subFolder != "." && $subFolder != null)
				$clientCacheObjectFolderPath = FileSystemCaching::$cacheObjectFolderPath."/".$clientName."/".$subFolder;
			
			//$cacheFileName = FileSystemCaching::getHashForKey($cacheKey);
			$cacheFileName = $cacheKey;
			$cacheFileLocation = $clientCacheObjectFolderPath."/".$cacheFileName.".cacheObj";
			try{
				if (file_exists($cacheFileLocation)) {
					$lines = file($cacheFileLocation);
					if(sizeof($lines) == 2){
						$validTime = (new DateTime($lines[0]))->add(new DateInterval('PT'.$ttl.'S'));
						$cacheString = $lines[1];
						$currentTime = new DateTime();
						if($currentTime > $validTime){
							//echo 'CACHE EXPIRES';
							return false;
						}else{
							//echo 'CACHE VALID';
							/*VALIDATING JSON*/
							return $cacheString;
						}
						
					}else{
						return false;
					}
				} else {
					return false;
				}
			}catch(Exception $e){
				return false;
			}
		}else{
			return false;
		}
	}

	public static function deleteCacheWithTTL($key, $subFolder){
		if(isset($_SESSION['clientName'])){
			$clientName = $_SESSION['clientName'];
			$cacheKey = $key."-".$clientName;
			$clientCacheObjectFolderPath = FileSystemCaching::$cacheObjectFolderPath."/".$clientName;
			if($subFolder != false && $subFolder != "." && $subFolder != null)
				$clientCacheObjectFolderPath = FileSystemCaching::$cacheObjectFolderPath."/".$clientName."/".$subFolder;
			
			$cacheFileName = $cacheKey;
			$cacheFileLocation = $clientCacheObjectFolderPath."/".$cacheFileName.".cacheObj";
			try{
				if (file_exists($cacheFileLocation)) {
					unlink($cacheFileLocation);
				}
				return true;
			}catch(Exception $e){
				return false;
			}
		}else{
			return false;
		}
	}
}
?>