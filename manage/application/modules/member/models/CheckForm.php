<?php
/**
 * 审核客户表单校验对象
 * @author liang
 * @package CFormModel
 */
class CheckForm extends CFormModel {

	/**
	 * @var integer 会员ID
	 */
	public $memberId;

	/**
	 * @var integer 审核提交状态
	 */
	public $state;

	/**
	 * @var string 审核提交理由
	 */
	public $reason;
	
	/**
	 * @var boolean 是否已经审核
	 */
	public $hasCheck = false;


	/**
	 * 表单校验规则
	 *
	 * @see CModel::rules()
	 */
	public function rules() {
		if( $this->state == '2' ){
			return array(
				array('state,reason','required'),
				array('state','in','range'=>array(1,2)),
				array('reason','length','min'=>5,'max'=>'80'),
				array('reason','safe'),
			);
		}else{
			return array(
				array('state','required'),
				array('state','in','range'=>array(1,2)),
				array('reason','length','max'=>'80'),
				array('reason','safe'),
			);
		}

	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签
	*/
	public function attributeLabels() {
		return array(
			'state'=>'审核状态',
			'reason'=>'理由',
		);
	}

	/**
	* 审核客户
	*
	*/
	public function check( $member ){
		if( !$this->validate() ){
			return false;
		}

		//使用事务处理
		$transaction = Yii::app()->db->beginTransaction();
		try {
			$model = new tbMemberCheck();
			$model->state = $this->state;
			$model->memberId = $this->memberId;
			if(  $this->reason ){
				$model->reason = $this->reason;
			}
			if(!$model->save()){
				$this->addErrors( $model->getErrors() );
				return false;
			}

			$member->isCheck = $this->state;
			if(!$member->save()){
				$this->addErrors( $member->getErrors() );
				return false;
			}
			$transaction->commit();
			return true;
		} catch (Exception $e) {
			$transaction->rollback();
			throw new CHttpException(503,$e);
			return false;
		}
	}


	
	/**
	* 已审核客户，查找审核原因
	*/
	public function setReason(){
		$this->reason = tbMemberCheck::model()->getOne( $this->memberId );
	}
}