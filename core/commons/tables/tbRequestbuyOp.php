<?php
/**
 * 请购单数据库表--操作日志
 * @author liang
 * @version 0.1
 *
 * @property int    $orderId  	 请购单ID
 * @property int    $userId   	 用户编号
 * @property string $code  	  	 操作编码 enum('edit','pass', 'close')加入采购和关闭
 * @property time 	$createTime	 操作订单时间
 * @property string $remark		 备注说明
 *
 * @package CActiveRecord
 */
class tbRequestbuyOp extends CActiveRecord {

	public static function model( $className=__CLASS__ ) {
		return parent::model( $className );
	}

	public function init() {
		parent::init();

	}


	public function tableName() {
		return '{{request_buy_op}}';
	}

	public function rules() {
		return array(
			array('orderId,userId,code','required'),
			array('orderId,userId,','numerical',"integerOnly"=>true),
			array('code,remark','safe')
		);
	}

	public function attributeLabels() {
		return array(
			'orderId' => '请购单ID',
			'userId' => '用户编号',
			'code'=>'操作编码',
			'remark'=>'备注说明',
		);
	}

	public static function addOp( $orderId,$code,$remark= '' ){
		$model = new self;
		$model->orderId = $orderId;
		$model->code = $code;
		$model->remark = $remark;
		$model->createTime = time();
		$model->userId = Yii::app()->user->id;
		return $model->save();
	}


	public function codeTitle( $code ){
		$arr = array(
				'insert'=>'创建请购单',
				'update'=>'编辑请购单',
				'pass'=>'审核通过，加入采购',
				'close'=>'关闭采购',
				);
		return array_key_exists( $code,$arr )?$arr[$code]:$code ;
	}

	/**
	* 根据请购单ID取得操作日志
	*
	*/
	public function getOp( $orderId ){
		$result =array();

		$model = $this->findAll( 'orderId =:orderId ',array(':orderId'=>$orderId) );
		foreach ( $model as $k=>$val ){
			$val->createTime = date('Y-m-d H:i',$val->createTime);
			$result[$k] = $val->attributes;
			$result[$k]['codeTitle'] = $this->codeTitle( $val->code );
			$result[$k]['username'] = tbUser::model()->getUsername( $val->userId );
		}
		return $result;
	}
}