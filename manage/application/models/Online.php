<?php
class Online extends CModel {
	private static $_mongo;
	public function attributeNames() {
		return;
	}
	public function __construct() {
		if (is_null ( self::$_mongo )) {
			self::$_mongo = Yii::app()->mongoDB->collection("session");
		}
	}

	public function CountOnline($type = "user") {
		return self::$_mongo->count(['type'=>$type,"id"=>["\$exists"=>1]]);
	}

	public function online($type = "user", $offset = 0, $limit=100) {
		$result = self::$_mongo->find(['type'=>$type], ['sort'=>['_id'=>1], 'limit'=>$limit]);
		$userList = array();
		foreach($result as $item) {
			if(!isset($item->id))
				continue;

			$record = array(
				'key'=>$item->key,
				'uid'=>$item->id,
				'type'=>$item->type,
				'isAdmin'=>$item->userType=="administrator"? 1 : 0,
				'username'=>'unknow'
			);

			$session = Session::unserialize($item->value);
			if($type=="user") {
				$record['username'] = $session["manageusername"];
				$record['isAdmin'] = isset($session['isAdmin'])? $session['isAdmin']:false;
			}
			elseif($type=="member") {
				$record['username'] = isset($session['basenickName'])? $session['basenickName']:"unknow";
			}
			else {
				$record['username'] = "unknow";
			}
			array_push($userList, $record);
		}

		return $userList;
	}
}
class Session {
	public static function unserialize($session_data) {
		if(!$session_data) return array();
		$method = ini_get ( "session.serialize_handler" );
		switch ($method) {
			case "php" :
				return self::unserialize_php ( $session_data );
				break;
			case "php_binary" :
				return self::unserialize_phpbinary ( $session_data );
				break;
			default :
				throw new Exception ( "Unsupported session.serialize_handler: " . $method . ". Supported: php, php_binary" );
		}
	}
	private static function unserialize_php($session_data) {
		$return_data = array ();
		$offset = 0;
		while ( $offset < strlen ( $session_data ) ) {
			if (! strstr ( substr ( $session_data, $offset ), "|" )) {
				throw new Exception ( "invalid data, remaining: " . substr ( $session_data, $offset ) );
			}
			$pos = strpos ( $session_data, "|", $offset );
			$num = $pos - $offset;
			$varname = substr ( $session_data, $offset, $num );
			$offset += $num + 1;
			$data = @unserialize ( substr ( $session_data, $offset ) );
			$return_data [$varname] = $data;
			$offset += strlen ( serialize ( $data ) );
		}
		return $return_data;
	}
	private static function unserialize_phpbinary($session_data) {
		$return_data = array ();
		$offset = 0;
		while ( $offset < strlen ( $session_data ) ) {
			$num = ord ( $session_data [$offset] );
			$offset += 1;
			$varname = substr ( $session_data, $offset, $num );
			$offset += $num;
			$data = unserialize ( substr ( $session_data, $offset ) );
			$return_data [$varname] = $data;
			$offset += strlen ( serialize ( $data ) );
		}
		return $return_data;
	}
}
