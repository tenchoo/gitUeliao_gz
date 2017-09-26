<?php
/**
 * 基于MongoDB的缓存对象
 * @author yagas
 * @package CCache
 * @version 0.1
 * @date 2015/07/20
 */
class MongoCache extends CCache {
	public  $CacheId;
	public  $keyPrefix;
	public  $collection;

	private static $_instance;

	public function init() {
		parent::init();
		if(!self::$_instance) {
			self::$_instance = Yii::app()->getComponent($this->CacheId)->collection($this->collection);
		}
	}

	/**
	 * 写入缓存
	 * 如果缓存已经存在则略过
	 * @see CCache::addValue()
	 */
	protected function addValue($key, $value, $expire) {
		if(!self::$_instance->isExsit('key', $key)) {
			return $this->setValue($key, $value, $expire);
		}
		return false;
	}

	/**
	 * 写入缓存
	 * 如果缓存存在则删除旧缓存，插入新的缓存
	 * @see CCache::setValue()
	 */
	protected function setValue($key, $value, $expire) {
		if($expire>0)
			$expire = $expire + time();
		
		if(self::$_instance->isExsit('key', $key))
			$this->deleteValue($key);

		return self::$_instance->insert(["key"=>$key, "value"=>$value, "expire"=>$expire]);
	}

	/**
	 * 读取缓存
	 * @see CCache::getValue()
	 */
	protected function getValue( $key ) {
		$cache = self::$_instance->findOne(array("key"=>$key));

		if(is_null($cache)) {
			return false;
		}

		// $cache->value = $cache->value;
		if($cache->expire!=0 && $cache->expire< time()) {
			self::$_instance->delete(['key'=>$key]);
			return false;
		}

		return $cache->value;
	}

	/**
	 * 删除缓存项
	 * @see CCache::deleteValue()
	 */
	protected function deleteValue( $key ) {
		return self::$_instance->delete(array("key"=>$key));
	}

	/**
	 * 释放缓存，全部清除
	 * @see CCache::flushValues()
	 */
	protected function flushValues() {
		return self::$_instance->delete();
	}

	public function remove( $condition ) {
		return self::$_instance->delete($condition);
	}
}
