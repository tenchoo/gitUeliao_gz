<?php
/**
 * ajax 搜索查找
 * @author liang
 * @version 0.1
 * @package CAction
 */
class ajaxSearch extends CAction{
	private $_state = false ;
	private $_data;
	private $_message;
	private $_keyword;

	public function run() {
		if( Yii::app()->user->getIsGuest() ) {
			$this->_message = Yii::t('user','You do not log in or log out');
			goto end;
		}

		$userType = Yii::app()->user->getState('usertype');
		if( $userType == 'member'){
			goto end;
		}

		$optype = Yii::app()->request->getParam('optype');
		$this->_keyword = Yii::app()->request->getParam('keyword');
		if( method_exists ( $this,$optype ) ) {
			$this->$optype();
		}

		end:
		$json=new AjaxData($this->_state,$this->_message,$this->_data);
		echo $json->toJson();
		Yii::app()->end();
	}

	/**
	* 查找业务员
	*/
	public function member(){
		if(!is_numeric( $this->_keyword ) ){
			$class = 'tbProfileDetail';
		}else{
			$class = 'tbMember';
		}

		$f = new ReflectionClass( $class );
		$this->_data = $f->newInstance()->search( $this->_keyword );
		$this->_state = true;
	}

}