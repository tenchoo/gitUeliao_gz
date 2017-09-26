<?php

/**
 * 产品推荐_页面/推荐位
 *
 * @property integer	$recommendId
 * @property integer	$type			类型：0页面，1推荐位
 * @property integer	$state			状态：0正常，1删除
 * @property integer	$parentId		上级分类ID
 * @property integer	$maxNum			允许最大推荐数量
 * @property timestamp	$createTime
 * @property timestamp	$updateTime
 * @property string		$title			分类名称
 * @property string		$mark			标识，不能重复且必须是英文字符
 * @version 0.1
 * @package CActiveRecord
 */

class tbRecommend extends CActiveRecord {

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
		return '{{recommend}}';
	}

	public function relations(){
		return array(
			'page'=>array(self::BELONGS_TO,'tbRecommend','', 'on' => 't.parentId = page.recommendId','select'=>'title'),
		);
	}

	/**
	 * @return array 模型验证规则.
	 */
	public function rules()	{
		if($this->scenario == 'editposition'){
			return array(
				array('type,title,parentId,maxNum,mark','required'),
				array('mark','match','pattern'=>'/^[a-zA-Z_0-9]+$/','message'=>Yii::t('base','Mark must be in English')),
				array('type','in','range'=>array(0,1)),
				array('parentId,maxNum', 'numerical','integerOnly'=>true,'min'=>1),
				array('title,mark','length','min'=>'2','max'=>'15'),
				array('title,mark','safe'),
				array('mark','unique'),
			);
		}
		return array(
			array('type,title,parentId','required'),
			array('type','in','range'=>array(0,1)),
			array('parentId,maxNum', 'numerical','integerOnly'=>true),
			array('title,mark','safe'),
		);
	}

	public function attributeLabels(){
		return array(
			'type'=>'类型',
			'title'=>'名称',
			'mark'=>'标识',
			'maxNum'=>'推荐数量',
		);

	}

	/**
	* 取得当前正在进行中的推荐个数。
	*/
	public function getNum(){
		$count = tbRecommendProduct::model()->count( 'recommendId = '.$this->recommendId );
		return (int)$count;
	}

	/**
	* 推荐的产品详情列表
	*/
	public function getProducts(){
		$data = tbRecommendProduct::model()->with('productInfo')->findAll( 'recommendId = :recommendId',array(':recommendId'=>$this->recommendId));
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
		$data = tbRecommendProduct::model()->findAll( 'recommendId = :recommendId',array(':recommendId'=>$this->recommendId));
		$data = array_map( function ($i){ return $i->productId;},$data  );
		return $data;
	}

	/**
	* 删除某一页面/推荐位
	* @param integer  $id		 分类表PK
	* @param integer  $message	 提示信息
	*/
	public function del( $id,&$message ){
		if(!is_numeric($id) || $id<1 ) return false;

		$model = $this->findByPk( $id,'state = 0 ' );
		if( !$model ) return false;

		if( $model->type =='0' ){
			//页面，判断是否还有推荐位
			$count = $this->count('state = 0 and parentId = '.$id);
			if($count){
				$message = Yii::t('category','This classifier has a sub category, which is not allowed to delete.');
				return false;
			}
		}else{
			if( $model->getNum() ){
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
	* 根据parent取得推荐位列表
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
				$c = $val->count('state = 0 and parentId = '.$val->recommendId);
			}else{
				$val->num = $val->getNum();
			}

			$val = $val->getAttributes(array('recommendId','title','parentId','type','maxNum','num','mark'));
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