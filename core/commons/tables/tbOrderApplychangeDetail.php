<?php
/**
 * 订单修改申请信息明细表
 * @author liang
 * @version 0.1
 * @package CActiveRecord
 *
 * @property integer	$id
 * @property integer	$applyId			修改申请表ID
 * @property integer	$orderProductId		订单明细表ID
 * @property integer	$oldNum				修改前数量
 * @property integer	$applyNum			申请修改数量
 * @property integer	$checkNum			审核后数量
 * @property string		$remark				备注
 *
 */

 class tbOrderApplychangeDetail extends CActiveRecord {

	public static function model( $className = __CLASS__ ) {
		return parent::model( $className );
	}

	public function tableName() {
		return "{{order_applychange_detail}}";
	}


	public function rules() {
		return array(
			array('applyId,orderProductId,oldNum,applyNum,checkNum','required'),
			array('applyId,orderProductId', "numerical","integerOnly"=>true),
			array('oldNum,applyNum,checkNum', "numerical"),
			array('applyNum','compare','compareAttribute'=>'oldNum','operator'=>'<=','message'=>'修改后数量不能大于修改前数量'),
			array('checkNum','compare','compareAttribute'=>'oldNum','operator'=>'<=','message'=>'修改后数量不能大于修改前数量'),
			array('remark','safe'),
		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'applyId' => '修改申请表ID',
			'orderProductId'=>'订单明细表ID',
			'oldNum'=>'修改前数量--',
			'applyNum'=>'申请修改数量',
			'checkNum'=>'修改后数量',
			'remark'=>'备注'

		);
	}

	/**
	 * 保存前的操作
	 */
	protected function beforeSave(){
		if($this->isNewRecord){
			$this->checkNum  = $this->applyNum;
			if( $this->checkNum == '0' ){
				$this->remark  = '取消购买';
			}
		}
		return true;
	}
}