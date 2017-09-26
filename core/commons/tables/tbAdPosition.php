<?php

/**
 * 帮助分类表模型
 *
 * @property integer	$adPositionId
 * @property integer	$type			类型：0页面，1广告位
 * @property integer	$state			状态：0正常，1删除
 * @property integer	$parentId		广告页面ID
 * @property integer	$maxNum			最大允许广告数量
 * @property integer	$height			广告位高度
 * @property integer	$width			广告位宽度
 * @property timestamp	$createTime
 * @property timestamp	$updateTime
 * @property string		$title			分类名称
 * @property string		$mark			标识，不能重复且必须是英文字符
 * @version 0.1
 * @package CActiveRecord
 */

class tbAdPosition extends CActiveRecord {

	/**
	* 当前正在进行中广告个数
	*/

	public $num	;

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
		return '{{ad_position}}';
	}

	public function relations(){
		return array(
			'page'=>array(self::BELONGS_TO,'tbAdPosition','', 'on' => 't.parentId = page.adPositionId','select'=>'title'),
		);
	}

	/**
	 * @return array 模型验证规则.
	 */
	public function rules()	{
		if($this->scenario == 'editposition'){
			return array(
				array('type,title,parentId,maxNum,mark,height,width','required'),
				array('mark','match','pattern'=>'/^[a-zA-Z_0-9]+$/','message'=>Yii::t('base','Mark must be in English')),
				array('type','in','range'=>array(0,1)),
				array('parentId,maxNum,height,width', 'numerical','integerOnly'=>true,'min'=>1),
				array('title,mark','length','min'=>'2','max'=>'15'),
				array('title,mark','safe'),
				array('mark','unique'),
			);
		}
		return array(
			array('type,title,parentId','required'),
			array('type','in','range'=>array(0,1)),
			array('parentId,maxNum,height,width', 'numerical','integerOnly'=>true),
			array('title,mark','safe'),
		);
	}

	public function attributeLabels(){
		return array(
			'type'=>'类型',
			'height'=>'广告位高度',
			'width'=>'广告位宽度',
			'title'=>'名称',
			'mark'=>'标识',
		);

	}

	/**
	* 取得当前正在进行中的广告个数。
	*/
	public function getNum(){
		$count = tbAd::model()->getNum( $this->adPositionId );
		return $count;
	}

	/**
	* 根据ID或mark取得广告位信息，并取得当前正在进行中的广告内容，
	* 返回广告位设置的大小等信息供前端显示。
	*/
	public function getAds(){
		$count = tbAd::model()->getNum( $this->adPositionId );
		return $count;
	}

	/**
	* 删除某一页面/广告位
	* @param integer  $id		 分类表PK
	* @param integer  $message	 提示信息
	*/
	public function del( $id,&$message ){
		if(!is_numeric($id) || $id<1 ) return false;

		$model = $this->findByPk( $id,'state = 0 ' );
		if( !$model ) return false;

		if( $model->type =='0' ){
			//页面，判断是否还有广告位
			$count = $this->count('state = 0 and parentId = '.$id);
			if($count){
				$message = Yii::t('category','This classifier has a sub category, which is not allowed to delete.');
				return false;
			}
		}else{
			//查找是否有广告内容
			$count = tbAd::model()->count( 'state=0 and adPositionId = '.$id );
			if($count){
				$message = Yii::t('category','Under this classification and information, please delete the information');
				return false;
			}

		}

		$model->state = '1';
		if( $model->save() ){
			$message = null;
			return true;
		}
		return false;
	}


	/**
	* 根据parent取得广告位列表
	* @param integer  $parentId
	*/
	public static function getByParentId( $parentId ){
		$criteria = new CDbCriteria;
		$criteria->compare('t.parentId',$parentId);
		$criteria->compare('t.state','0');

		$data = self::model()->findAll( $criteria );
		foreach($data as &$val){
			$c = 0;
			if( $parentId=='0' ){
				$c = $val->count('state = 0 and parentId = '.$val->adPositionId);
			}else{
				$val->num = $val->getNum();
			}

			$val = $val->getAttributes(array('adPositionId','title','parentId','type','maxNum','num','mark'));
			$val['childrens'] = ($c)?true:false;
		}
		return $data;
	}




	/**
	 * 保存前的操作
	 */
	protected function beforeSave(){
		if($this->isNewRecord){
			$this->createTime = new CDbExpression('NOW()');
			if($this->parentId>0){
				$this->type = '1';
			}
		}
		$this->updateTime = new CDbExpression('NOW()');
		return true;
	}
}
?>