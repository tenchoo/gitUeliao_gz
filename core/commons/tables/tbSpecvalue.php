<?php
/**
 * 规格值
 * @author liang
 * @version 0.1
 * @package CActiveRecord
 *
 * @property integer	$specvalueId		 规格值ID
 * @property integer	$specId 			 规格ID
 * @property integer	$colorSeriesId		颜色系列ID
 * @property integer	$hasProduct			是否有关联产品，有关联产品则不允许删除
 * @property string		$title	 			 名称
 * @property string		$code 			 	 值
 * @property string		$serialNumber 		编号
 *
 */

class tbSpecvalue extends CActiveRecord {

	public static function model( $className = __CLASS__ ) {
		return parent::model( $className );
	}

	public function tableName() {
		return "{{specvalue}}";
	}

	public function rules() {
		return array(
			array('specId,title,code','required'),
			array('specId,colorSeriesId','numerical','integerOnly'=>true),
			array('title,code,serialNumber','safe'),
			array('serialNumber','checkFill'),
			array('title,code,serialNumber','checkExists'),
		);
	}

	/**
	* 搜索颜色编号
	*/
	public function search( $keyword ){
		if( empty( $keyword ) ){
			return;
		}
		$criteria=new CDbCriteria;
		$criteria->compare('specId',1);
		if( is_numeric( $keyword )){
			$criteria->addSearchCondition('serialNumber', $keyword);
		}else{
			$criteria->addSearchCondition('title', $keyword);
		}
		$model = self::model()->findAll( $criteria );
		return $model ;
	}

	/**
	* 颜色编号必须填写
	*/
	public function checkFill( $attribute,$params ){
		if( $this->specId =='1' && empty( $this->$attribute ) ){
			$this->addError($attribute,Yii::t('category','Color coding must be filled in'));
			return;
		}
	}

	/**
	* 检查是否存在，同一规格下，名称值编号不能重复,rules 规格
	*/
	public function checkExists( $attribute,$params ){
		if( empty( $this->$attribute ) ){
			return;
		}

		$criteria=new CDbCriteria;
		$criteria->compare($attribute,$this->$attribute);
		$criteria->compare('specId',$this->specId);
		if( $this->specvalueId ){
			$criteria->addCondition("specvalueId !='".$this->specvalueId."'");
		}

		$model = self::model()->find( $criteria );
		if( $model ){
			$label = $this->getAttributeLabel($attribute);
			$this->addError($attribute,Yii::t('base','{attribute} already exists',array('{attribute}'=>$label)));
		}
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels() {
		return array(
			'specId' => '规格ID',
			'title' => '名称',
			'code' => '值',
			'serialNumber'=>'编号',
			'colorSeriesId'=>'颜色系列ID ',
		);
	}

	/**
	* 取得指定ID的规格信息
	* @param array $ids
	*/
	public static function getSpecs( array $ids ){
		if( empty( $ids ) ){
			return ;
		}
		$ids = implode(',',$ids);
		$where = ' specvalueId in ('.$ids.')';
		$sql = "select sv.*,s.specName from {{specvalue}} sv left join {{spec}} s on sv.specId = s.specId where $where";
		$cmd = Yii::app()->db->createCommand( $sql );
		$specdata = $cmd->queryAll();
		$result = array();
		foreach ( $specdata as $val ){
			$result[$val['specvalueId']] = $val;
		}
		return $result;
	}
}