<?php
/**
 * 仓库盘点单
 *
 * @property integer	$stocktakingId	盘点单ID
 * @property integer	$warehouseId	仓库ID
 * @property integer	$userId			盘点人userId
 * @property integer	$state			状态：0未确认，1 取消盘点，2已保存
 * @property timestamp	$createTime		新建时间
 * @property timestamp	$updateTime		最后更新时间
 * @property string		$userName		盘点人
 * @property string		$checkUser		审核人
 * @property string		$remark			备注
 *
 */

class tbStocktaking extends CActiveRecord
{
	/**
	 * 返回基于自身的AR实例
	 * @param string $className 类名
	 * @return CActiveRecord 实例
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string 返回表名
	 */
	public function tableName()
	{
		return '{{stocktaking}}';
	}

	public function relations(){
		return array(
			'detail'=>array(self::HAS_MANY,'tbStocktakingDetail','stocktakingId'),
		);
	}


	/**
	 * @return array 模型验证规则.
	 */
	public function rules()
	{
		return array(
			array('warehouseId', 'required'),
			array('state','in','range'=>array(0,1,2)),
			array('warehouseId', "numerical","integerOnly"=>true),
			array('checkUser,remark,takinger,serialNumber','safe'),
		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'checkUser' => '审核人',
			'remark' => '备注',
			'warehouseId' => '仓库ID',
		);
	}

	/**
	 * 保存前的操作
	 */
	protected function beforeSave(){
		if($this->isNewRecord){
			$this->createTime = new CDbExpression('NOW()');
			$this->userId = Yii::app()->user->id;
		}
		$this->updateTime = new CDbExpression('NOW()');
		return true;
	}
}