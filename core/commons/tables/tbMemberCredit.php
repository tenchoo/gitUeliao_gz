
<?php
/**
 * 会员月结信用度信息表
 * @author liang
 * @version 0.1
 * @package CActiveRecord
 *
 * @property integer	$memberId			客户ID
 * @property integer	$state				状态：0正常，1删除
 * @property integer	$billingCycle		结算周期，单位为月
 * @property integer	$credit				信用额度D
 * @property time		$createTime			首次加入月结时间
 * @property time		$updateTime			更新时间
 * @property time		$createTime			首次加入月结时间
 * @property time		$updateTime			更新时间
 *
 */

 class tbMemberCredit extends CActiveRecord {

	 const STATE_NORMARL = 0;
	 CONST STATE_DEL = 1;

	 /**
	 * 当前可用信用额度
	 */
	 public $validCredit = 0;

	public function tableName() {
		return "{{member_credit}}";
	}

	public static function model($className = __CLASS__) {
        return parent::model($className);
    }

	public function rules() {
		return array(
			array('memberId,billingCycle,credit','required'),
			array('memberId,billingCycle,credit', "numerical","integerOnly"=>true),
			array('billingCycle','numerical',"integerOnly"=>true,'min'=>'1','max'=>'12'),
			array('credit', "numerical","integerOnly"=>true,'min'=>'1','max'=>'100000000'),
			array('memberId','unique'),
		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'memberId' => '客户ID ',
			'billingCycle' => '结算周期',
			'credit' => '信用额度',
		);
	}

	/**
	* 查询月结用户当前额度信息
	* @param integer $memberId 客户ID
	* @param string $userType 操作者用户类型
	* @return array
	*/
	public static function creditInfo( $memberId,$userType='' ){
		$model = tbMemberCredit::model()->findByPk ( $memberId,'state = :s',array( ':s'=>self::STATE_NORMARL ) );
		if ( !$model ) return ;

		$info = array( 'credit'=>$model->credit );
		$info['usedCredit'] = tbMemberCreditDetail::usedCredit( $memberId,$userType );
		$info['validCredit'] = bcsub ( $model->credit, $info['usedCredit'] );

		return $info;
	}


	/**
	 * 保存前的操作
	 */
	protected function beforeSave(){
		if( $this->isNewRecord ){
			$this->createTime = new CDbExpression('NOW()');
		}
		$this->updateTime = new CDbExpression('NOW()');

		return parent::beforeSave();
	}

}