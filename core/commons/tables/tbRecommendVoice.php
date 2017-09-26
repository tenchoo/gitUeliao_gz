<?php

/**
 * 产品推荐_语音产品推荐
 *
 * @property integer	$recommendId
 * @property integer	$state			状态：0正常，1删除
 * @property integer	$maxNum			允许最大推荐数量
 * @property timestamp	$createTime
 * @property timestamp	$updateTime
 * @property string		$identity		标识，不能重复且必须是英文字符
 * @property string		$title			名称
 * @property string		$remark			备注
 * @version 0.1
 * @package CActiveRecord
 */

class tbRecommendVoice extends CActiveRecord {

	/**
	* @var int 当前推荐数量
	*/
	public $num = 0	;

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
		return '{{recommend_voice}}';
	}


	/**
	 * @return array 模型验证规则.
	 */
	public function rules()	{
		return array(
			array('title,identity,maxNum','required'),
			array('identity','match','pattern'=>'/^[a-zA-Z_]+$/','message'=>Yii::t('base','Mark must be in English')),
			array('maxNum', 'numerical','integerOnly'=>true,'min'=>1),
			array('title','length','min'=>'2','max'=>'15'),
			array('title,remark','safe'),
			array('identity','unique'),
		);
	}

	public function attributeLabels(){
		return array(
			'identity'=>'标识',
			'title'=>'名称',
			'remark'=>'备注',
			'maxNum'=>'推荐数量',
		);

	}

	/**
	* 取得当前正在进行中的推荐个数。
	*/
	public function getNum(){
		$count = tbRecommendVoiceProduct::model()->count( 'recommendId = '.$this->recommendId );
		return (int)$count;
	}

	/**
	* 推荐的产品语音
	*/
	public function getProducts(){
		$data = tbRecommendVoiceProduct::model()->with('productInfo')->findAll(
							array(
								'condition'=>'recommendId = :recommendId',
								'params'=>array(':recommendId'=>$this->recommendId),
								'order'=>'t.listOrder asc') );
		$list = array();
		foreach ( $data as $val ){
			if( empty( $val->productInfo ) ) continue;

			$info = $val->productInfo->getAttributes( array('productId','price','title','serialNumber','mainPic'));
			$info['recommendTime'] = $val->createTime;
			$info['id'] = $val->id;
			$list[] = $info;
		}
		return $list;
	}

	/**
	* 根据ID或mark取得推荐位信息，并取得当前正在进行中的推荐内容，
	* 返回推荐位设置的大小等信息供前端显示。
	*/
	public function getProductIds(){
		$data = tbRecommendVoiceProduct::model()->findAll( array(
								'condition'=>'recommendId = :recommendId',
								'params'=>array(':recommendId'=>$this->recommendId),
								'order'=>'listOrder asc,recommendId DESC')
								);
		$data = array_map( function ($i){ return $i->productId;},$data  );
		return $data;
	}

	/**
	 * 保存前的操作
	 */
	protected function beforeSave(){
		if($this->isNewRecord){
			$this->createTime = new CDbExpression('NOW()');
		}
		$this->updateTime = new CDbExpression('NOW()');
		return true;
	}


	/**
	 * 根据标识取的推荐产品的声音文件
	 * @param string  $identity 推荐标识
	 */
	public function getByMark( $identity ){
		if( empty($identity) ) return array();

		$model = $this->findByAttributes( array( 'identity'=>$identity,'state'=>'0') );
		if( !$model ) return array();

		//取得推荐的产品ID
		$productIds = $model->getProductIds();
		if( !$productIds ) return array();

		$criteria = new CDbCriteria;
		$criteria->compare('isDel', '0');
		$criteria->compare('isMain', '1');
		$criteria->compare('productId', $productIds);
		$sounds = tbProductSound::model()->findAll( $criteria );
		if( !$sounds ) return array();

		$files = array();
		foreach ( $sounds as $val ){
			$files[$val->productId] = array( 'productId'=>$val->productId ,'title'=>$val->title , 'sound'=>$val->sound, 'updateTime'=>$val->updateTime, 'mainPic'=>$val->product->mainPic );
		}

		foreach ( $productIds as $key=>&$val ){
			if( array_key_exists( $val,$files ) ){
				$val = $files[$val];
			}else{
				unset( $productIds[$key] );
			}
		}

		return $productIds;
	}



}
?>