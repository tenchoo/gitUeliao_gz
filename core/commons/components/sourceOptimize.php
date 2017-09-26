<?php
/**
 * html资源优化
 * 将页面上所有的link提取到<head>元素中
 * 将页面上所有的script提取到</body>元素前
 * YII_DEBUG 为 false 的情况下，会将资源的路径进行替换
 * @author yagasx
 * @version 0.1
 */
class sourceOptimize extends CModel {
	
	private $_context;
	private $_prefix='';
	private $_dict=array();
	
	public function attributeNames() {
		return false;
	}
	
	/**
	 * 资源域名
	 * @param unknown $prefix
	 */
	public function __construct( $prefix=null ) {
		if( !is_null($prefix) ) {
			$this->_prefix = $this->makeDomain( $prefix );
		}
	}
	
	/**
	 * 页面内容
	 * @param string $context
	 */
	public function setContext( $context ) {
		$this->_context = $context;
	}
	
	/**
	 * 获取页面内容
	 * @return Ambigous <string, string>
	 */
	public function getContext() {
		return $this->_context;
	}
	
	/**
	 * 设置资源路径map文件
	 * @param string $aliasPath
	 * @throws CException
	 * @example
	 * $res = new sourceOptimize();
	 * $res->context = $output;
	 * $res->dict = "application.data.map";
	 * $res->run();
	 * $output = $res->context;
	 */
	public function setDict( $aliasPath ) {
		$filePath = Yii::getPathOfAlias( $aliasPath );
		if( !file_exists( $filePath.'.php') ) {
			throw new CException( "Not found source dict file" );
		}
		$this->_dict = require $filePath . '.php';
	}
	
	public function setPrefix( $prefix ) {
		$this->_prefix = $this->makeDomain( $prefix );
	}
	
	/**
	 * 查找各元素座标点
	 * @param string $start
	 * @param string $end
	 * @return array
	 */
	protected function findTag( $start, $end ) {
		$point  = array();
		$offset = 0;

		while( ($ps = strpos( $this->_context, $start, $offset ) ) !== FALSE ) {
			$offset     = $ps;
			$pe         = strpos( $this->_context, $end, $offset );
			if( $pe === false ) {
				break;
			}
			$point[$ps] = $pe + strlen( $end );
			$offset     = $point[ $ps ];
		}
		ksort( $point );
		return $point;
	}
	
	/**
	 * 通过元素座标提取资源
	 * @param array $points
	 * @return array
	 */
	protected function parseTag( $points ) {
		$tags = array();
		foreach ( $points as $ps => $pe ) {	
			$len = $pe - $ps;
			$tag = substr( $this->_context, $ps, $len );
			if( !preg_match('/data-no="op"/', $tag) )
				array_push( $tags, $tag );
		}
		return $tags;
	}
	
	/**
	 * 替换页面元素路径
	 * @param string $tag
	 * @return mixed
	 */
	protected function replaceUrl( $tag ) {
		$replaceCount = 1;
		
		preg_match('/(src|href)=\"([^\"]+)\"/', $tag, $match);
		if( $match ) {
			$uri = array_pop( $match );
			$absUri = $this->transform( $uri );
			$tag = str_replace( $uri, $absUri, $tag, $replaceCount );
		}
		return $tag;
	}
	
	/**
	 * 使用map转换页面元素路径
	 * @param string $tag
	 * @return string
	 */
	protected function transform( $tag ) {
		if( YII_DEBUG ) {
			goto end;
		}
		
		$key = md5( substr($tag,1) );
		if( array_key_exists($key, $this->_dict) ) {
			$tag = $this->_dict[ $key ];
		}
		
		end:
		if( preg_match('/^http/', $tag) ) {
			return $tag;
		}
		return $this->_prefix . $tag;
	}
	
	/**
	 * 执行资源优化过程
	 */
	public function run() {
		$tags           = array();
		$point          = $this->findTag("<link", ">");
		$tags['</head>']  = $this->parseTag( $point );
		$point          = $this->findTag("<script", "</script>");
		$tags['</body>'] = $this->parseTag( $point );
		
		foreach ( $tags as $falg => $list ) {
			foreach ( $list as & $item ) {
				$index  = strpos($this->_context, $item);
				$this->contextRemoveBy( $index, strlen($item) );
				$item = $this->replaceUrl( $item );
			}
			$list = array_unique( $list );
			$index = strpos($this->_context, $falg);
			if( $index !== FALSE ) {
				$replace = implode("\n", $list);
				$this->contextInsertBy( $index, $replace );
			}
		}
		
		$this->transformImage();
	}
	
	/**
	 * 从字符串中移除一段数据
	 * @param int $offset
	 * @param int $length
	 */
	protected function contextRemoveBy( $offset, $length ) {
		$newContext = substr( $this->_context, 0, $offset);
		$eof        = $offset + $length;
		while( $char = substr($this->_context,$eof,1) ) {
			if( in_array( ord($char), array(10,13,32) ) ) {
				$eof++;
				continue;
			}
			break;
		}

		$newContext .= substr( $this->_context, $eof );
		$this->_context = $newContext;
	}
	
	/**
	 * 从字符串指定位置插入一段数据
	 * @param int $index
	 * @param string $string
	 */
	protected function contextInsertBy( $index, $string ) {
		$newContext = substr( $this->_context, 0, $index );
		$newContext .= $string;
		$newContext .= substr( $this->_context, $index );
		$this->_context = $newContext;
	}
	
	/**
	 * 替换图片路径
	 */
	protected function transformImage() {
		$tags   = array();
		$point  = $this->findTag("<img", "/>");
		$images = $this->parseTag( $point );
		if( $images ) {
			foreach ( $images as $item ) {
				$tag = preg_replace_callback( "/src=\"([^\"]+)\"/", array( $this, 'replaceImage' ), $item );
				if( $tag !== $item ) {
					$this->_context = str_replace( $item, $tag, $this->_context );
				}
			}
		}
	}
	
	/**
	 * 图片路径处理，非http起头的url都会添加资源域名
	 * @param array $match
	 * @return string
	 */
	protected function replaceImage( $match ) {
		$src = array_pop( $match );
		if( substr($src,0,4) !== 'http' ) {
			$src = $this->_prefix . $src;
		}
		return "src=\"${src}\"";
	}
	
	/**
	 * 生成当前项目域名
	 * @param string $url
	 * @return string
	 */
	protected function makeDomain( $url ) {
		
		$urlInfo  = parse_url( $url );
		$hostInfo = parse_url( Yii::app()->request->hostInfo );
		$urlInfo['host'] = strstr( $urlInfo['host'], '.', true );
		$urlInfo['host'] .= strstr( Yii::app()->request->hostInfo, '.' );
		return $hostInfo['scheme'] . '://' . $urlInfo['host'];
	}
}