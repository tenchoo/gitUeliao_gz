<?php
/**
 * 规格表
 * @author liang
 * @version 0.1
 * @package CActiveRecord
 *
 * @property integer	$specId
 * @property integer	$isColor		是否颜色，是则值关联颜色系列
 * @property integer	$isPicture		是否有规格图片
 * @property integer	$state			状态：0正常，1删除
 * @property string		$specName		规格标题

 *
 */

class tbSpec extends CActiveRecord {

	public static function model( $className = __CLASS__ ) {
		return parent::model( $className );
	}

	public function tableName() {
		return "{{spec}}";
	}

	public function rules() {
		return array(
			array('specName','required'),
			array('specName', 'length', 'max'=>20, 'min'=>2),
		//	array('isColor,isPicture,state', 'in', 'range'=>array(0,1) ),
			array('specName','unique','criteria'=>array('condition'=>'state = 0')),
			array('specName','safe'),

		);
	}

	public function attributeLabels() {
		return array(
			'specId' => '规格ID',
			'isColor' => '是否颜色',
			'isPicture' => '是否有规格图片',
			'state'=>'状态',
			'specName'=>'规格名称',
		);
	}

	public function relations() {
        return array(
            'specvalue'=>array(self::HAS_MANY, 'tbSpecvalue', 'specId','order'=>'serialNumber ASC'),
        );
    }
	public static function  getSpecinfo( $categoryId ='',$withValues='1' ){
		$criteria = new CDbCriteria;
		$criteria->compare('t.state', 0);
		if( $categoryId ){
			$criteria->compare('t2.categoryId', $categoryId);
			$criteria->join = "inner join {{category_spec}} t2 on( t.specId=t2.specId )"; //连接表
		}

		if(  $withValues == '1' ){
			$model = self::model()->with('specvalue')->findAll( $criteria );
		}else{
			$model = self::model()->findAll( $criteria );
		}
		$result = array();
		foreach( $model as $val ){
			$result[$val->specId] = $val->attributes;

			if(  $withValues == '1' ){
				$result[$val->specId]['specvalue'] = array();
				foreach ( $val->specvalue as $vval ){
					$result[$val->specId]['specvalue'][] =  $vval->attributes;
				}
			}
		}
		return $result;
	}
}