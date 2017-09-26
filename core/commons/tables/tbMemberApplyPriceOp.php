<?php
/**
 * 批发价格申请操作审核记录表---记录包括修改，审核，失效和删除操作。
 * @author liang
 * @version 0.1
 * @package CActiveRecord
 *
 * @property integer		$id
 * @property integer		$applyPriceId			申请表ID
 * @property integer		$userId					后台userId/前台memberId
  * @property integer		$isManage				是否后台操作：0前台，1后台
 * @property timestamp		$createTime				操作时间
 * @property string			$code					操作代码：('insert','modify', 'invalid', 'pass', 'notpass', 'del')
 * @property string			$remark					备注说明
 *
 */

 class tbMemberApplyPriceOp extends CActiveRecord {

	public static function model( $className = __CLASS__ ) {
		return parent::model( $className );
	}

	public function tableName() {
		return "{{member_applyprice_op}}";
	}

	public function rules() {
		return array(
			array('applyPriceId,userId,isManage,code','required'),
			array('applyPriceId,userId','numerical','integerOnly'=>true,'min'=>0),
			array('isManage','in','range'=>array(0,1)),
			array('remark,code','safe'),
		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'applyPriceId' => '申请表ID',
			'userId' => '操作者ID',
			'code' => '操作代码',
			'remark'=>'备注说明',
		);
	}

	/**
	 * 保存前的操作
	 */
	protected function beforeSave(){
		if($this->isNewRecord){
			$this->createTime = new CDbExpression('NOW()');
		}
		return true;
	}

	public function codeTitle(){
		$arr = array( 'insert'=>'新增申请', 'modify'=>'编辑', 'invalid'=>'取消', 'pass'=>'审核通过', 'notpass'=>'审核不通过', 'del'=>'删除' );
		return array_key_exists( $this->code,$arr )?$arr[$this->code]:$this->code;
	}

	/**
	* 取得操作日志
	* @param integer $applyPriceId
	*/
	public static function getOP( $applyPriceId ){
		if( !is_numeric($applyPriceId) || $applyPriceId<1  ) return array();

		$oplog = tbMemberApplyPriceOp::model()->findAll( 'applyPriceId = :applyPriceId',array(':applyPriceId'=>$applyPriceId ) );
		$oplog = array_map( function ( $i ){
					$info = $i->attributes;
					if( $i->isManage =='1' ){
						$info['username'] = tbUser::model()->getUserName( $i->userId );
					}else{
						$m = tbProfile::model()->findByPk( $i->userId );
						if( $m ){
							$info['username'] = $m->username;
						}else{
							$info['username'] = '业务员';
						}

					}
					$info['codeTitle'] = $i->codeTitle();
					return $info;
					},$oplog );
		return $oplog ;
	}

}