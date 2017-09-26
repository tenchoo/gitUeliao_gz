<?php

/**
 * 物流公司配置信息表数据库模型
 *
 * @property integer $logisticsId	物流公司ID
 * @property integer $isCOD			cash on delivery,是否支付货到付款
 * @property integer $isDel			是否删除
 * @property string $title			物流标题
 * @property string $mark			物流标识
 * @version 0.1
 * @package CActiveRecord
 */

class tbLogistics extends CActiveRecord {

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
		return '{{logistics}}';
	}

	/**
	 * @return array 模型验证规则.
	 */
	public function rules()	{
		return array(
			array('title,mark','required'),
			array('isCOD','in','range'=>array(0,1)),
			array('title,mark','length','min'=>3,'max'=>20),
			array('title,mark','safe'),

		);
	}


	public function attributeLabels(){
		return array(
			'logisticsId'=>'物流公司ID',
			'isCOD'=>'是否支付货到付款',
			'title'=>'物流标题',
			'mark'=>'物流标识',
		);

	}

	/**
	* 物流信息列表
	* @param  integer $isCOD 是否支付货到付款
	*/
	public function getList( $isCOD = '' ){
		$condition = 'isDel = :isDel';
		$param = array(':isDel'=>'0');
		if( in_array($isCOD ,array('0','1') ) ){
			$condition .= ' and isCOD = :isCOD';
			$param[':isCOD'] = $isCOD;
		}
		$model = $this->findAll( $condition,$param );
		
		$result = array();
		foreach ( $model as $val ){
			$result[$val->logisticsId] = $val->title;
		}
		return $result;

	}

	public function findOne( $id ){
		$model = $this->findByPk( $id );
		if( $model ){
			return $model->attributes;
		}
	}

	/**
	 * 查找物流
	 * @param  array $condition 查找条件
	 * @param  integer $pageSize 每页显示条数
	 */
	public static function search( $keyword = '' , $pageSize= 10 ) {
		$criteria=new CDbCriteria;
		$criteria->compare('t.isDel','0');
		if( $keyword ){
			$criteria->compare('t.title',$keyword,true);
		}

		$model = new CActiveDataProvider('tbLogistics', array(
			'criteria'=>$criteria,
			'pagination'=>array('pageSize'=>$pageSize),
		));

		$data = $model->getData();
		$result['list'] = array();
		$result['pages'] = $model->getPagination();

		if( $data ){
			foreach ( $data as $val ){
				$result['list'][] = $val->attributes;
			}
		}
		return $result;
	}
}
?>