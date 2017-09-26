<?php
/**
 * 行业分类属性
 * @author liang
 * @version 0.1
 * @package CActiveRecord

 * @property integer $attributeId		属性ID
 * @property integer $categoryId		行业分类ID
 * @property integer $setGroupId		属性组ID
 * @property integer $isSearch			是否支持搜索0不支持1支持
 * @property integer $type				输入控件的类型,1单选,2复选,3下拉,4广本框，5文本域
 * @property integer $isOther			是否后面加其他
 * @property integer $listOrder			排序值
 * @property integer $state				状态：0正常，1删除
 * @property string  $attrId	属性标题ID
 * @property string  $attrValue			属性值(逗号分隔)
 *
 */

class tbAttribute extends CActiveRecord {

	public $title;

	/**
	 * 获得模型对象实例
	 * @param unknown_type $className
	 * @return Ambigous <static, unknown, multitype:>
	 */
	public static function model( $className = __CLASS__ ) {
		return parent::model( $className );
	}

	/**
	 * 获得数据表名
	 * @return string
	 */
	public function tableName() {
		return "{{attribute}}";
	}

	public function rules() {
		return array(
				array('categoryId,title,type','required'),
				array('title', 'length', 'max'=>20, 'min'=>2),
				array('isOther,isSearch,state', 'in', 'range'=>array(0,1) ),
				array('type', 'in', 'range'=>array(1,2,3,4,5) ),
				array('setGroupId,categoryId,listOrder','numerical','integerOnly'=>true),
				array('title,attrValue', 'safe'),
				array('attrValue','checkValue'),
		);
	}

	/**
	* 验证码 rule 规则
	*/
	public function checkValue($attribute,$params){
		if( !$this->hasErrors() ) {
			if( in_array ( $this->type ,array(1,2,3) ) && $this->attrValue === '' ) {
				$this->addError('attrValue',Yii::t('category','Specifications must be filled in'));
			}
		}
	}



	public function attributeLabels() {
		return array(
			'attributeId' => '属性ID',
			'categoryId' => '行业分类ID',
			'setGroupId' => '属性组ID',
			'isSearch'=>'是否支持搜索',
			'type'=>'控件的类型',
			'isOther'=>'是否后面加其他',
			'listOrder'=>'排序值',
			'attrId'=>'属性标题',
			'title'=>'属性标题',
			'attrValue'=>'属性值',
			'state'=>'状态',
		);
	}



	/*
	* 根据行业分类ID取得属性列表,行业分类属性列表页
	* @param integer $categoryid
	*/
	public static function getLists( $categoryid ){
		$criteria = new CDbCriteria;
		$criteria->order = 'listOrder ASC';
		$criteria->compare( 't.state','0' );
		$criteria->compare( 't.categoryId',$categoryid );
		$model = tbAttribute::model()->findAll( $criteria );

		if( !$model ) return array();

		$attrlist = array();

		$tbAttr = new tbAttr();
		$tbAttrValue = new tbAttrValue();
		foreach ( $model as $val ){
			$info = $val->attributes ;
			$info['title'] = $tbAttr->titleName( $val->attrId );
			$attrValue = $tbAttrValue->getValueById( $val->attrValue );
			$info['attrValue'] = implode( ',',$attrValue );
			$attrlist[] = $info;
		}
		return $attrlist;
	}


	/*
	* 根据行业分类ID取得属性列表,发布产品页面使用
	* @param integer $categoryid
	*/
	public static function getLists2(  $categoryid  ){
		$cmd = Yii::app()->db->createCommand("select attributeId,setGroupId,type,isOther,attrId,attrValue from {{attribute}} where categoryid=$categoryid and state = 0 order by listOrder ASC");
		$data = $cmd->queryAll();
		$result = array();

		$tbAttr = new tbAttr();
		$tbAttrValue = new tbAttrValue();
		foreach( $data as $item ) {
			$item['title'] = $tbAttr->titleName( $item['attrId'] );
			$k = $item['setGroupId'];
			unset( $item['setGroupId'] );
			if( in_array( $item['type'] ,array('1','2','3') ) ){
				if( $item['isOther'] && !empty( $item['attrValue'] ) ){
					$item['attrValue'] .= ',1';
				}
				$item['attrValue'] = $tbAttrValue->getValueById( $item['attrValue'] );
			}
			$result[$k][] = $item;
		}
		return $result;
	}


	/**
	* 属性标记删除
	* @param integer $attributeId
	*/
	public function delAttr( $attributeId ){
		if( empty( $attributeId )  ) {
			return ;
		}
		$attributes  = array('state'=>'1');
		return $this->updateByPk( $attributeId,$attributes );
	}


	/**
	* 新增行业分类时自动继承父类属性--只能新增行业分类时才能调用,不可重复操作。
	* @param integer $categoryId 新增生成的行业分类ID
	* @param integer $parentId 新分类的父ID
	*/
	public function toExtend( $categoryId,$parentId ){
		if( empty( $categoryId ) || empty( $parentId ) ){
			return false;
		}

		$connection = Yii::app()->db;
		$sql =" INSERT INTO {$this->tableName()} (`categoryId`, `setGroupId`, `isSearch`, `type`, `isOther`, `listOrder`, `attrId`, `attrValue` ) select  $categoryId,`setGroupId`, `isSearch`, `type`, `isOther`, `listOrder`, `attrId`, `attrValue` from {$this->tableName()}  where  `categoryId`= $parentId and state ='0' ";
		$command=$connection->createCommand($sql);
		return $command->execute();
	}

	/**
	* 继承到所有子类
	* @param integer $categoryId 需要继承到子类的行业分行ID
	* @param array $ids 指定继承哪几个
	*/
	public function extendAllchildren( $categoryId,$ids = array() ){
		if( empty( $categoryId ) || empty( $ids )  || !is_array( $ids )){
			return false;
		}

		foreach ( $ids as $val ){
			if( !is_numeric($val) || $val<1 ){
				return false;
			}
		}

		$models = $this->findAllByPk ( $ids );
		if( !$models ){
			return false;
		}

		$attrIds = array_map( function($i){ return $i->attrId;}, $models );
		$attrIds = array_unique( $attrIds );
		if( empty( $attrIds ) ){
			return false;
		}

		$extendIds = implode(',',$ids);
		$where = " and `attributeId` in ( $extendIds ) ";

		//取得行业分类ID
		$children = tbCategory::model()->getAllLevelChildrens( $categoryId );
		if( empty( $children ) ){
			return true; //无子分类时，返回true;
		}
		$transaction = Yii::app()->db->beginTransaction();
		$i = 0;
		try {
			//先删除原来的再增加
			$criteria = new CDbCriteria;
			$criteria->compare('categoryId', $children);
			$criteria->compare('attrId', $attrIds);
			$a = $this->deleteAll( $criteria );

			$connection = Yii::app()->db;
			foreach ( $children as $val ){
				$sql =" INSERT INTO {$this->tableName()} (`categoryId`, `setGroupId`, `isSearch`, `type`, `isOther`, `listOrder`, `attrId`, `attrValue` ) select  $val,`setGroupId`, `isSearch`, `type`, `isOther`, `listOrder`, `attrId`, `attrValue` from {$this->tableName()}  where  `categoryId`= $categoryId and state ='0' $where ";
				$command=$connection->createCommand($sql);
				$i +=$command->execute();
			}

		 	$transaction->commit();
			return true;
		} catch (Exception $e) {
			print_r($e);
			$transaction->rollback(); //如果操作失败, 数据回滚
			return false;
		}
	}


	/**
	* 设置排序
	* @param integer  $categoryId 分类ID
	* @param integer  $id		 要移动的ID值
	* @param integer  $goto  	移动方向，上升(up)或下降(down)
	*/
	public function orderMove(  $categoryId,$id,$goto ){
		if( empty( $categoryId ) || empty( $id ) || !in_array( $goto,array( 'up','down' ) ) ){
			return false;
		}

		$data = $this->getLists( $categoryId );
		if( empty( $data ) ) {
			return false;
		}
		$arr = array();
		foreach( $data as $val ){
			$key = $val['listOrder'];
			$arr[$key] = $val['attributeId'];
		}
		ksort($arr);

		$listorder1 = array_search($id,$arr);
		if( !$listorder1 && is_bool( $listorder1 ) ){
			return false;
		}

		$keysarr = array_keys( $arr );
		$i = array_search($listorder1,$keysarr);
		if( $goto=='up' ){
			$i--;
		}else{
			$i++;
		}

		if( !isset( $keysarr[$i] ) ){
			return false;
		}

		$listorder2 = $keysarr[$i];
		$id2 = $arr[$listorder2];
		$transaction = Yii::app()->db->beginTransaction();
		try {
			//对调listOrder保存
			$this->updateByPk( $id,array( 'listOrder'=>$listorder2 ) );
			$this->updateByPk( $id2,array( 'listOrder'=>$listorder1 ) );
			$transaction->commit();
			return true;
		} catch (Exception $e) {
			$transaction->rollback(); //如果操作失败, 数据回滚
			return false;
		}
	}

	/**
	* 新增时设置排序值
	*/
	private function setlistOrder(){
		$model = $this->find( array(
		  'select'=>'MAX(listOrder) as listOrder',
		  'condition' => 'categoryId = :categoryId',
		  'params' => array( ':categoryId'=>$this->categoryId ),
		));
		$this->listOrder = 1 + $model->listOrder;
	}

	private function setId(){
		$this->attrId = tbAttr::model()->getId( $this->title,true );
		if( $this->attrValue != '' ){
			$this->attrValue = tbAttrValue::model()->setIds( $this->attrValue );
		}
	}

	protected function beforeSave()	{
		if( $this->isNewRecord ) {
			$this->setlistOrder();
		}

		$this->setId();
		return true;
	}

	public function fetchAttributes( $categoryId, $filter ) {
		$criteria = new CDbCriteria();
		$criteria->compare('categoryId', $categoryId);
		$criteria->compare('isSearch', 1);
		$criteria->compare('state', 0);
		$criteria->order = "listOrder ASC";

		if( $filter ) {
			$criteria->addNotInCondition( 'attributeId', $filter );
		}
		$result = $this->findAll( $criteria );
		return array_map(function($i){
			$attrTitle = tbAttr::model()->titleName($i->attrId);
			$attrValues = tbAttrValue::model()->getValueById($i->attrValue);
			return array('attributeId'=>$i->attributeId, 'title'=>$attrTitle, 'attrValue'=>$attrValues);
		}, $result);
	}

	public function findByPPath( $ppath ) {
		$ids = array_keys( $ppath );
		$result = $this->findAllByPk( $ids );
		$ids = array_fill_keys($ids, null);
		foreach( $result as $item ) {
			$row = array(
				'attributeId' => $item->attributeId,
				'title'       => $item->title,
				'value'   => urldecode( $ppath[$item->attributeId] )
			);
			$ids[ $item->attributeId ] = $row;
		}
		return array_values( $ids );
	}
}