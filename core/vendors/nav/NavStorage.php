<?php
/**
 * 菜单工厂类
 * @author morven
 *
 */
class NavStorage {
	
	private $instance;

	public $type;
			
	public function __construct($type='home') {
		$this->type = $type;
	}
	
	/**
	 * 创建实例类
	 * @param string $type 调用实例类型
	 * @throws CHttpException
	 */
	public function create() {		
		$className = ucfirst($this->type) . 'Nav';	
		if( class_exists($className,true) ) {
			$f = new ReflectionClass( $className );
			$this->instance = $f->newInstance();
			return;
		}
		throw new CHttpException(500,'No Class Instance');
	}
	
	
	/**
	 * 查询菜单 
	 * @param array $condition 条件
	 * @return array
	 */
	public function search( $condition=array() ){
		$this->create();
		$result = $this->instance->search($condition);
		return $result;
	}
	
	
	/**
	 * 查询所有菜单
	 * @param array $condition 查询条件
	 * @return array
	 */
	public function findAll( $condition=array() ){
		$this->create();
		$result = $this->instance->findAll($condition);
		return $result;
	}
	
	/**
	 * 保存菜单
	 * @param array $attributes 保存内容
	 * @param integer $id ID
	 * @return array
	 */
	public function save( $attributes , $id =null){ 
		$this->create();
		$result = $this->instance->save($condition,$id);
		return $result;
	}
	
	
	/**
	 * 查询单条数据
	 * @param int $id 主键ID
	 * @return static
	 */
	public function findOne($id){
		$this->create();
		$result = $this->instance->findOne($id);
		return $result;
	}
	
	

}

