<?php
/**
 * 遍历目录提取模块/控制器/方法
 * @author yagas
 * @package CApplicationComponent
 * @version 0.1
 */
class ScanTask extends CApplicationComponent {
	private $_filters   = array();
	private $_basePath  = "./";
	private $_actionMap = array();
	private $_pattern   = "/*";
	private $_source;
	
	/**
	 * 排队目录列表
	 * @param array $list
	 * @example $list = array('ajax','role')
	 */
	public function filters( $list ) {
		$this->_filters = $list;
	}
	
	/**
	 * 遍历起始路径
	 * @param string $path
	 */
	public function basePath( $path ) {
		$this->_basePath = $path;
	}
	
	public function pattern( $pattern ) {
		$this->_pattern = $pattern;
	}
	
	/**
	 * 提取模块列表
	 */
	protected function parseModules() {
		$modules = glob( $this->_basePath . DS . '*', GLOB_NOSORT );
		
		if( $modules === FALSE ) {
			return array();
		};
		foreach ( $modules as $item ) {
			$dirname = basename( $item );
			if( !in_array($dirname, $this->_filters) ) {
				$controllers = $this->parseController( $dirname,$item );
				$this->_actionMap[ $dirname ] = array( 'comment'=>$this->_readModuleAccess($item), 'route'=>'/modules/'.$dirname, 'controllers' => $controllers );
				
			}
		}
	}
	
	/**
	 * 提取控制器列表
	 * @param string $moduleName 模块路由
	 * @param string $path       模块路径
	 */
	protected function parseController( $moduleName,$path ) {
		$controllers = glob( $path.DS.'controllers'.DS.'*Controller.php');
		$data        = array();
		if( $controllers ) {
			$control = array();
			foreach( $controllers as $controller ) {
				$className = basename( $controller, '.php' );
				$module = array('controller'=>'/'.$moduleName.'/'.strtolower(substr($className,0,-10)));

				//load code
				$source = file_get_contents( $controller );
				$source = token_get_all( $source );
				
				$module['comment'] = $this->_readAccess( $source[1][1] );
				$module['actions'] = array();
				
				for( $i=1; $i<count($source); $i++ ) {
					if( !is_array( $source[$i] ) || $source[$i][0]== T_WHITESPACE ) {
						continue;
					}
					
					if( $source[$i][0]==T_PUBLIC ) {
						$item = array('action'=>'','route'=>'','comment'=>'');
						$comment = $source[$i-2];
						if( $comment[0] === T_DOC_COMMENT ) {
							$comment = $this->_readAccess( $comment[1] );
							$item['comment'] = $comment;
						}
						$i+=2;
						if( $source[$i][0] == T_FUNCTION ) {
							$i += 2;
							$item['action'] = $this->_readFunctionName( $source[$i] );
							if( preg_match('/^action[^s]+$/', $item['action']) ) {
								$item['route'] = $this->_actionRoute($module['controller'], $item['action']);
								array_push( $module['actions'], $item );
							}
						}
					}
				}
				array_push( $data, $module );
			}
		}
		return $data;
	}
	
	/**
	 * 开始提取数据
	 */
	public function execute() {
		$this->parseModules();
		return $this->_actionMap;
	}
	
	private function _readFunctionName( $source ) {
		return $source[1];
	}
	
	private function _actionRoute( $module, $action ) {
		return sprintf("%s/%s", $module, substr(strtolower($action),6));
	}
	
	private function _readAccess( $comment ) {
		$comment = str_replace( "\t", '', $comment );
		if( preg_match( '/@access (.+)/i', $comment, $match ) ) {
			return array_pop( $match );
		}
		return '';
	}
	
	private function _readModuleAccess( $path ) {
		$module = ucfirst( basename($path) ).'Module.php';
		$filePath = $path.DS.$module;
		if( !file_exists($filePath) ) {
			return basename($path);
		}
		$source = token_get_all( file_get_contents( $filePath ) );
		$result = $this->_readAccess( $source[1][1] );
		if( empty($result) ) {
			$result = basename($path);
		}
		return $result;
	}
}