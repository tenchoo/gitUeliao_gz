<?php
/**
 * 系统消息表
 *
 * The followings are the available columns in table '{{message}}':
 * @property integer	$messageId
 * @property integer	$memberId		收信息的会员ID
 * @property integer	$state			状态:0未查看,1已查看,2删除
 * @property timestamp	$createTime		创建时间
 * @property string		$title			标题
 * @property string		$content		消息内容
 *
 */

class tbMessage extends CActiveRecord
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
		return '{{message}}';
	}

	/**
	 * @return array 模型验证规则.
	 */
	public function rules()
	{
		return array(
			array('content', 'required'),
			array('memberId,state', "numerical","integerOnly"=>true),
			array('title,content', 'safe'),
		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'state' => '状态',
			'title'=>'标题',
			'memberId'=>'客户ID',
			'content'=>'消息内容',
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


	public function search( $pageSize = 20 ){
		$criteria=new CDbCriteria;
		$criteria->compare('memberId',Yii::app()->user->id);
		$criteria->compare('state',array(0,1));

		$model = new CActiveDataProvider($this,array(
					'criteria'=>$criteria,
					'pagination'=>array('pageSize'=>$pageSize,'pageVar'=>'page'),
				));
		$data = $model->getData();
		$return['list'] = array_map(function ($i){ 
							$i->createTime = date('Y/m/d H:i',strtotime( $i->createTime ));
							return $i->attributes;},$data);
		$return['pages']= $model->getPagination();
		return $return;
	}
}