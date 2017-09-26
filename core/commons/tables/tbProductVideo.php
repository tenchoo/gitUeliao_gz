<?php
/**
 * 产品视频描述数据

 * @property integer	$id				编号
 * @property integer	$productId		产品编号
 * @property integer	$isMain			是否主文件
 * @property integer	$sort			排序
 * @property integer	$isDel			是否已删除
 * @property timestamp	$createTime		上传时间
 * @property timestamp	$updateTime		最后更新时间
 * @property string		$video			声音文件
 * @property string		$title			文件标题
 * @version 0.1
 * @package CActiveRecord
 */


class tbProductVideo extends CActiveRecord {

    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{product_video}}';
    }

    public function primaryKey() {
        return 'id';
    }

        public function rules() {
        return array(
            array('productId,title,video,isMain,sort', 'required'),
            array('isMain','in','range'=>array(0,1)),
            array('productId,sort', "numerical","integerOnly"=>true),
            array('title,video', 'safe')
        );
    }

    public function attributeLabels(){
        return array(
            'productId' => '产品编号',
            'isMain' => '是否主文件',
            'sort' => '排序值',
            'video' => '视频文件',
            'title'=>'文件标题'
        );
    }

    /**
     * 通过产品ID获取所有音频文件请求地址
     * 列表内容按sort升序排列
     */
    public function findAllByProductID( $productId ) {
        if( empty($productId ) ) return array();
        $criteria = new CDbCriteria();
        $criteria->compare('productId',$productId );
        $criteria->compare('isDel','0' );
        $criteria->order  = "isMain desc, sort ASC";

        return $this->findAll($criteria);
    }

	/**
     * 设置文件删除标记
     */
	public static function delVideo( $id ){

		$model  = tbProductVideo::model()->findByPk( $id ,'isDel = 0') ;
		if( $model ){
			$model->isDel = 1;
			$model->isMain == 0;
			return $model->save();
		}
		return false;
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
	 * 保存后的操作
	 */
	protected function afterSave(){
		if( $this->isMain == '1' ){
			$this->updateAll( array('isMain'=> '0'),'id!=:id and productId = :productId',array(':id'=> $this->id,':productId'=> $this->productId) );
		}else{
			//查找是否有默认，若没有，把第一个设置成默认
			$this->setMain( $this->productId );
		}
		return parent::afterSave();
	}


	public function setMain( $productId ){
		$model = $this->find( array(
						'condition'=>'isDel = 0 and productId = :productId',
						'params'=> array(':productId'=> $productId ),
						'order'=>'isMain desc, sort ASC' ) );
		if( $model && $model->isMain != '1' ){
			$this->updateByPk( $model->id,array('isMain'=> '1') );
		}
	}
}
