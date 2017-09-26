<?php
/**
 * 基于Sphinx的索引检索服务
 * @author yagas
 * @package CApplicationComponent
 * @version 0.1
 * @date 2015/08/26
 */
class ZSphinxSearcher extends CApplicationComponent {
	public $host = '192.168.1.230';
	public $port = 9312;

	private $_search;
	
	public function init() {
		parent::init();
		$this->_search = $this->getConnection();
	}

	/**
	 * 建立到sphinx服务器的连接
	 */
	public function & getConnection() {
		$sphinx = new SphinxClient();
		$sphinx->SetServer( $this->host, $this->port );
		$sphinx->setMatchMode( SPH_MATCH_ALL );
		return $sphinx;
	}

	/**
	 * 对索引数据进行检索
	 * @param string $keyword 检索关键词
	 * @param string $dict    检索库名称
	 * @return boolean
	 */
	public function query( $keyword, $dict="*" ) {
		return $this->_search->query( $keyword, $dict );
	}

	/**
	 * 设置返回结果集偏移量和数目
	 * @param int $offset 结果集的偏移量
	 * @param int $limit 返回的匹配项数目
	 * @param int $max_matches 设置控制搜索过程中searchd在内存中所保持的匹配项数目
	 * @param int $cutoff 该设置是为高级性能优化而提供的. 它告诉searchd 在找到并处理 cutoff 个匹配后就强制停止
	 * @return boolean
	 */
	public function setLimits ( $offset , $limit, $max_matches = 20, $cutoff = 20 ) {
		return $this->_search->setLimits( $offset, $limit, $max_matches, $cutoff );
	}

	/**
	 * 代理访问 SphinxClient 对象方法
	 */
	public function __call( $func, $params ) {
		if( method_exists($this->_search, $func) ) {
			return call_user_func_array( array( $this->_search, $func ), $params );
		}
		parent::__call( $func, $params );
	}
}

/**
 * 索引检索模式
 * @author yagas
 *
 */
class searchModel {
	const MATCH_ALL       = SPH_MATCH_ALL;
	const MATCH_ANY       = SPH_MATCH_ANY;
	const MATCH_PHRASE    = SPH_MATCH_PHRASE;
	const MATCH_BOOLEAN   = SPH_MATCH_BOOLEAN;
	const MATCH_EXTENDED  = SPH_MATCH_EXTENDED;
	const MATCH_FULLSCAN  = SPH_MATCH_FULLSCAN;
	const MATCH_EXTENDED2 = SPH_MATCH_EXTENDED2;
}