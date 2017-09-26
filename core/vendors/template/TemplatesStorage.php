<?php
/**
 * 网铺模板类
 * @author morven
 *
 */
class TemplatesStorage {
	
	public $select = 'templateId,title,image';
	
	private $instance;
			
	public function __construct() {
		
	}
	
	/**
	 * 创建实例类
	 * @param string $type 调用实例类型
	 * @throws CHttpException
	 */
	public function create( $type ) {		
		$className = ucfirst($type) . 'Templates';	
		if( class_exists($className,true) ) {
			$f = new ReflectionClass( $className );
			$this->instance = $f->newInstance();
			return;
		}
		throw new CHttpException(500,'No Class Instance');
	}
	
	/**
	 * 查询模板列表
	 * @param string $type 模板类型 pc:PC端,mob:手机端
	 * @param int	$page 分页数
	 * @return CActiveDataProvider
	 */
	public function search( $type ='pc', $page=10,$condition=array()){
		$this->create($type);
		$result = $this->instance->search($page,$this->select,$condition);
		return $result;
	}

}

