<?php
/**
 * 订单有效期设置
 * @author liang
 * @version 0.1
 * @package CFormModel
 *
 */

class OrderSetting extends CFormModel {

	public $setValue;

	public $unit;

	/**
	* Declares the validation rules.
	* The rules state that account and password are required,
	* and password needs to be authenticated.
	*/
	public function rules()	{
		return array(
			array('setValue,unit','required'),
			array('setValue', "numerical","integerOnly"=>true,'min'=>'1'),
			array('unit', 'in','range'=>array('day','hour','min')),

		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签
	*/
	public function attributeLabels() {
		return array(
			'setValue' => '有效期',
			'unit' => '单位',
		);
	}

	/**
	* 配置信息，要取得哪些配置
	*/
	public function settings(){
		return array(
			'orderValidity'=> array('title'=>'普通订单有效期'),
			'keepGoodsValidity'=> array('title'=>'留货订单有效期'),
			'confirmValidity'=> array('title'=>'确认收货有效期'),
		);
	}

	/**
	* 取得配置信息的值
	* @param array $setArr
	*/
	public function getSetting( array $setArr ){
		$criteria = new CDbCriteria;
		$criteria->compare('variable', array_keys( $setArr ));
		return tbSetting::model()->findAll( $criteria );
	}

	public function save( $dataArr ){
		foreach( $dataArr as $val ){
			$this->attributes = $val;
			if( !$this->validate() ) {
				return false ;
			}
		}
		$setArr = $this->settings();
		$setting = $this->getSetting( $setArr );
		foreach ( $setting as $val ){
			$k = $val->variable;
			$val->setValue = $dataArr[$k]['setValue'];
			$val->unit = $dataArr[$k]['unit'];
			if( $val->save() ){
				unset( $dataArr[$k] );
			}else{
				$this->setErrors ( $val->getErrors() ) ;
				return false;
			}
		}

		if( !empty( $dataArr ) ){
			foreach ( $dataArr as $key=>$val ){
				$model = new tbSetting();
				$model->variable = $key;
				$model->setValue = $val['setValue'];
				$model->unit = $val['unit'];
				$model->title = $setArr[$key]['title'];
				if( !$model->save() ){
					$this->setErrors ( $model->getErrors() ) ;
					return false;
				}

			}
		}
		return true;
	}
}