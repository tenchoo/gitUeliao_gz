<?php
/**
 * 操作日志
 * @author liang
 * @version 0.1
 * @package CActiveRecord
 *
 * @property integer		$id
 * @property integer		$objId					操作对象ID
 * @property integer		$userId					操作者 后台userId/前台memberId
 * @property integer		$isManage				是否后台操作：0前台，1后台
 * @property timestamp		$opTime					操作时间
 * @property string			$objType				操作对象类型
 * @property string			$code					操作标识
 * @property string			$remark					操作说明
 *
 */

 class tbOpLog extends CActiveRecord {

	public static function model( $className = __CLASS__ ) {
		return parent::model( $className );
	}

	public function tableName() {
		return "{{op_log}}";
	}

	public function rules() {
		return array(
			array('objId,userId,isManage,objType,code','required'),
			array('objId,userId','numerical','integerOnly'=>true,'min'=>0),
			array('isManage','in','range'=>array(0,1)),
			array('objType,remark,codeobjType,','safe'),
		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'objId' => '申请表ID',
			'userId' => '操作者ID',
			'code' => '操作代码',
			'remark'=>'操作说明',
			'objType'=>'操作对象类型',
		);
	}

	/**
	 * 保存前的操作
	 */
	protected function beforeSave(){
		if($this->isNewRecord){
			$this->opTime = new CDbExpression('NOW()');
		}
		return true;
	}

	public function codeTitle(){
		$arr = array( 'insert'=>'提交', 'modify'=>'编辑', 'invalid'=>'取消', 'pass'=>'审核通过', 'notpass'=>'审核不通过', 'del'=>'删除' );
		return array_key_exists( $this->code,$arr )?$arr[$this->code]:$this->code;
	}

	/**
	* 取得操作日志
	* @param integer $objType 操作对象类型
	* @param integer $objId	  操作对象ID
	* @param boolean $isShowOper 是否显示操作人员
	*/
	public function getOP( $objType,$objId,$isShowOper = false  ){
		if( !is_numeric($objId) || $objId<1 || empty( $objType )  ) return array();

		$oplog = $this->findAll( 'objType = :objType AND objId = :objId',array(':objType'=>$objType,':objId'=>$objId ) );

		$result = array();
		foreach ( $oplog  as $val ){
			$info = $val->attributes;
			$info['codeTitle'] = $val->codeTitle();

			if( $isShowOper ){
				if( $val->isManage =='1' ){
					$info['username'] = tbUser::model()->getUserName( $val->userId );
				}else{
					$m = tbProfile::model()->findByPk( $val->userId );
					if( $m ){
						$info['username'] = $m->username;
					}else{
						$info['username'] = '业务员';
					}
				}
			}

			$result[] = $info;
		}
		return $result ;
	}

}