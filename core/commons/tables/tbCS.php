<?php

/**
 * 客服数据库表模型
 *
 * @property integer $csId
 * @property integer $type			客服类型：1QQ，2旺旺
 * @property integer $state			状态，是否启用
 * @property integer $isDefault		是否默认：0否，1是
 * @property integer $listOrder		排序值
 * @property string $csName			客服名称
 * @property string $csAccount		客服账号
 * @version 0.1
 * @package CActiveRecord
 */

class tbCS extends CActiveRecord {

	const CS_MAX = 10;//最多允许客服个数

	/**
	 * 构造数据库表模型单例方法
	 * @param system $className
	 * @return static
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return '{{cs}}';
	}

	/**
	 * @return array 模型验证规则.
	 */
	public function rules()	{
		return array(
			array('type,csName,csAccount,state','required'),
			array('state','in','range'=>array(0,1)),
			array('type','in','range'=>array(1,2)),
			array('isDefault,listOrder', 'numerical','integerOnly'=>true),
			array('csName,csAccount','safe'),
			array('csAccount','checkQQ'),
			array('csAccount','unique'),
		);
	}

	/**
	* 验证码 rule 规则
	*/
	public function checkQQ($attribute,$params){
		if( !$this->hasErrors() && $this->type =='1'  ) {
			if( !preg_match('/^[1-9][0-9]{4,12}$/', $this->$attribute) ){
				$this->addError( $attribute,Yii::t('base','QQ number is not legal' ));
				return false;
			}
		}
	}

	public function attributeLabels(){
		return array(
			'type'=>'客服类型',
			'isDefault'=>'是否默认',
			'listOrder'=>'排序值',
			'csName'=>'客服名称',
			'csAccount'=>'账号',
			'state'=>'状态',
		);

	}

	/**
	* 取得全部客服账号
	*/
	public function getlist(){
		$model = $this->findAll(array(
		  'order' => 'listOrder ASC',
		));

		$result = array();
		foreach ( $model as $val ){
			$result[] = $val->attributes;
		}
		return $result;
	}


	/**
	* 取得默认客服账号
	*/
	public function getDefault(){
		$model = $this->find( 'isDefault = 1' );
		if($model){
			return $model->attributes;
		}else{
			return false;
		}
	}


	/**
	* 删除某一客服ID
	* @param integer  $csId		 客服表PK
	*/
	public function del( $csId ){
		$result = $this->deleteByPk( $csId );
		return $result;
	}

	/**
	* 设置默认
	* @param integer  $csId		 客服表PK
	*/
	public function setDefault( $csId ){
		if( empty( $csId ) ){
			return false;
		}
		$transaction = Yii::app()->db->beginTransaction();
		try {
			$set1		 =	array('isDefault'=>'0');
			$condition1	 =	' isDefault = :isDefault';
			$params1	 =	array( ':isDefault'=>'1' );
			$this->updateAll( $set1,$condition1,$params1 );

			$set2		 =	array('isDefault'=>'1');
			$condition2	 =	'csId = :csId ';
			$params2	 =	array( ':csId'=>$csId );
			$this->updateAll( $set2,$condition2,$params2 );
			$transaction->commit();
			return true;
		} catch (Exception $e) {
			$transaction->rollback(); //如果操作失败, 数据回滚
			return false;
		}
	}

	/**
	* 设置排序
	* @param integer  $csId		客服表PK
	* @param integer  $goto  	移动方向，上升(up)或下降(down)
	*/
	public function orderMove( $csId,$goto ){

		$data = $this->getlist( );
		if( empty( $data ) ) {
			return false;
		}
		$arr = array();
		foreach( $data as $val ){
			$key = $val['listOrder'];
			$arr[$key] = $val['csId'];
		}
		ksort($arr);

		$listorder1 = array_search($csId,$arr);
		if( !$listorder1 && is_bool( $listorder1 ) ){
			return false;
		}

		$keysarr = array_keys( $arr );
		$i = array_search($listorder1,$keysarr);
		if( $goto=='up' ){
			$i--;
		}else{
			$i++;
		}

		if( !isset( $keysarr[$i] ) ){
			return false;
		}

		$listorder2 = $keysarr[$i];
		$csId2 = $arr[$listorder2];
		$transaction = Yii::app()->db->beginTransaction();
		try {
			//对调listOrder保存
			$this->updateByPk( $csId,array( 'listOrder'=>$listorder2 ) );
			$this->updateByPk( $csId2,array( 'listOrder'=>$listorder1 ) );
			$transaction->commit();
			return true;
		} catch (Exception $e) {
			$transaction->rollback(); //如果操作失败, 数据回滚
			return false;
		}
	}

	/**
	* 取一客服前台显示
	* @return string 返回客服链接
	*/
	public function showOne(){
		$model = $this->find( array(
		  'order'=>'isDefault desc,listOrder asc',
		));
		if( $model ){
			return $this->kefu( $model->csAccount,$model->type,$model->csName);
		}else{
			return '';
		}
	}

	/**
	* 生成客服链接
	* @param integer $id  客服账号ID
	* @param integer $type  客服账号类型
	*/
	public function kefu($id,$type=1,$csName){
		if( $type == 2 ){
			$link = '<a target="_blank" href="http://amos.im.alisoft.com/msg.aw?v=2&uid='.$id.'&site=cnalichn&s=1&charset=utf-8" ><img border="0" src="http://amos.im.alisoft.com/online.aw?v=2&uid='.$id.'&site=cnalichn&s=1&charset=utf-8" alt="点此咨询"" /></a>';
		}else{
			$link = '
			<a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin='.$id.'&site=qq&menu=yes"><img border="0" src="http://wpa.qq.com/pa?p=2:'.$id.':52" alt="发消息" title="发消息"/>'.$csName.'</a>';
		}

		return $link;
	}

	/**
	* 计算客服总数
	*/
	public function getCount(){
		$count = $this->count();
		return $count;
	}

	/**
	* 新增时设置排序值
	*/
	private function setlistOrder(){
		$model = $this->find( array(
		  'select'=>'MAX(listOrder) as listOrder',
		));
		$this->listOrder = 1 + $model->listOrder;
	}

	protected function beforeSave()	{
		if( $this->isNewRecord ) {

			$this->setlistOrder();
			if(!$this->getDefault() ){
				$this->isDefault = 1;
			}
		}
		return true;
	}
}
?>