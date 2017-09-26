<?php
/**
 * 表单提交状态机
 * 表单提交分为ajax提交和正常表单提交
 * > ajax提交需要以json格式返回数据
 * > 正常提交可直接渲染模板或进行跳转页面
 * @author yagas
 * @package CApplicationComponent
 * @version 0.1
 */
class FormState extends CApplicationComponent {
	
	/**
	 * 视图模板
	 * @var string
	 */
	private $_view;
	
	/**
	 * 跳转目标路由地址
	 * @var string
	 */
	private $_redirect;
	
	/**
	 * 控制器对象实例
	 * @var CController
	 */
	private $_owne;
	
	/**
	 * 处理数据对象模型
	 * @var CModel
	 */
	private $_model;
	
	/**
	 * 状态机构造函数
	 * @param CController $controller 控制器对象
	 * @param CModel      $model      数据处理对象
	 */
	public function __construct( CController $controller, CModel $model ) {
		$this->_owne  = $controller;
		$this->_model = $model;
	}
	
	/**
	 * 设置状态机属性
	 * @see CComponent::__set()
	 */
	public function __set($name, $value) {
		$property = '_'.$name;
		if( property_exists($this, $property) ) {
			$this->$property = $value;
			return true;
		}
		
		return parent::__set($name, $value);
	}
	
	/**
	 * 代理执行数据处理对象方法
	 * @param string $name       方法名称
	 * @param array  $parameters 参数列表
	 * @return mixed
	 */
	public function __call( $name, $parameters=array() ) {
		if( method_exists( $this->_model, $name) ) {
			if( $parameters ) {
				return call_user_func_array( array($this->_model,$name), $parameters );
			}
			else {
				return call_user_func( array($this->_model,$name) );
			}
		}
		
		return parent::__call( $name, $parameters );
	}
	
	/**
	 * 状态机执行动作
	 */
	public function execute( $action='save' ) {
		if( !$this->_model->validate() ) {
			$error = $this->_model->getErrors();
			$this->_owne->setError( $error );
			$this->show( false, $error );
		}
		else {
			$result = call_user_func( array($this->_model,$action) );
			$this->show( true, $this->_model->getAttributes() );
		}
	}
	
	/**
	 * 输出数据处理结果
	 * @param boolean $return 状态
	 * @param array   $params 返回数据
	 */
	private function show( $return, $params=null ) {
		if( Yii::app()->request->isAjaxRequest ) {
			$callback = new AjaxData( $return, null, $params );
			echo $callback->toJson();
			Yii::app()->end( 200 );
		}
		
		//错误页面优先渲染模板
		if( !$return && !is_null($this->_view) ) {
			$this->_redirect = null;
		}
		$this->render( $this->_view, $params );
	}
	
	/**
	 * 渲染模板
	 * @param string $view
	 * @param string $params
	 */
	private function render( $view, $params=null ) {
		if( !is_null( $this->_redirect ) ) {
			$url = $this->_owne->createUrl( $this->_redirect, $params );
			$this->_owne->redirect( $this->_redirect );
			Yii::app()->end( 200 );
		}
		
		if( !is_null( $view ) ) {
			$this->_owne->render( $view, $params );
			Yii::app()->end( 200 );
		}
		
		throw new CHttpException( 500, Yii::t('base','Not view to render.') );
	}
}