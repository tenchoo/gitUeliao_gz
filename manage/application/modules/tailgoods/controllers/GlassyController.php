<?php
/*
* @access 呆滞产品列表
*/
class GlassyController extends Controller {

	/**
	* @access 呆滞产品列表
	* 计算呆滞产品规则：
	* 1.查找出当前呆滞级别最小级别设置的参数值。
	* 2.根据此值去判断哪些单品视为呆滞，对于有销售记录的，呆滞时间从最后一次销售时间开始算，对于无销售记录的，呆滞时间从产品上架时间开始算。
	* 3.对呆滞在产品在仓库的库存打个标签。
	* 4.对打标签的呆滞产品按group by warehoustId，singleNumber 统计计算数量，得出呆滞报表。
	* 5.生成呆滞报表后，把产品最后销售的同后更新时间同步过来。
	* 6.根据已在尾货销售的产品，给呆滞报表的产品打上已在尾货销售的标签。
	* 7.查询出结果后，根据产品最后销售时间来判断呆滞级别。因呆滞级别可变别，不生成数据存储，显示时判断得出。
	* 8.查找时按级别查找时，把时间作对比查找。
	*
	*/
	public function actionIndex() {
		$flush = Yii::app()->request->getQuery('flush');
		$dt =  Yii::app()->cache->get('glassyDataTime');
		if( $flush === 'glassy' ||  empty( $dt ) ){
			$this->glassyData();
			$this->redirect( $this->createUrl('index') );
			exit;
		}

		//按呆滞级别查找
		$level = Yii::app()->request->getQuery('level');

		//按仓库查找
		$warehouseId = Yii::app()->request->getQuery('warehouseId');

		//按产品编号
		$singleNumber = trim(Yii::app()->request->getQuery('singleNumber'));

		$state = Yii::app()->request->getQuery('state','0');

		//当前配置的呆滞级别
		$glevel = $this->getGlevel();

		//仓库列表
		$warehouse = tbWarehouseInfo::model()->getAll();
		$criteria = new CDbCriteria;
		if( !empty( $level ) && array_key_exists( $level,$glevel ) ){
			//小于当前等设定的时间，大于下一等级的时间
			$t = date('Y-m-d H:i:s',strtotime('-'.$glevel[$level]['conditions'].' days'));
			$criteria->addCondition( 't.lastSaleTime <= \''.$t.'\'' );

			$keys = array_keys( $glevel );
			$k = array_search( $level,$keys );
			if( array_key_exists( $k+1,$keys ) ){
				$k = $keys[$k+1];
				$t1 = date('Y-m-d H:i:s',strtotime('-'.$glevel[$k]['conditions'].' days'));
				$criteria->addCondition( 't.lastSaleTime > \''.$t1.'\''  );
			}
		}

		if( !empty( $warehouseId )  && array_key_exists( $warehouseId,$warehouse ) ){
			$criteria->compare( 't.warehouseId',$warehouseId );
		}

		if( !empty( $singleNumber ) ){
			$criteria->compare( 't.singleNumber',$singleNumber,true );
		}

		if( in_array( $state,array('0','1') ) ){
			$criteria->compare( 't.state',$state );
		}

		$glassyChoose = Yii::app()->session['glassyChoose'];
		if( is_array( $glassyChoose ) && !empty( $glassyChoose ) ){
			$criteria->addNotInCondition( 'id', $glassyChoose );
		}
		$chooseCount = count($glassyChoose);

		$pageSize = (int) tbConfig::model()->get( 'page_size' );
		$criteria->order  = 'lastSaleTime ASC,singleNumber ASC';
		$model = new CActiveDataProvider('tbGlassyList', array(
			'criteria'=>$criteria,
			'pagination'=>array('pageSize'=>$pageSize,'pageVar'=>'page'),
		));

		$list = $this->setList( $model->getData(),array_reverse( $glevel ) );
		$pages = $model->getPagination();

		$this->render( 'index',array('level'=>$level,'glevel'=>$glevel,'warehouseId'=>$warehouseId,'warehouse'=>$warehouse,'list'=>array_values( $list ),'pages'=>$pages,'singleNumber'=>$singleNumber,'dt'=>$dt,'chooseCount' =>$chooseCount ,'state'=>$state));
	}

	private function setList( $model,$levels ){
		$list = array();
		foreach ( $model as $val ){
			if( !array_key_exists( $val->singleNumber,$list ) ){
				$list[$val->singleNumber] = $val->getAttributes(array('productId','singleNumber','state'));
				if( empty( $val->levelId ) ){
					$list[$val->singleNumber]['level'] = $this->getLevelName( $val->lastSaleTime,$levels ) ;
				}else{
					$list[$val->singleNumber]['level'] = $val->levelId;
				}
			}
			$list[$val->singleNumber]['stock'][] = $val->getAttributes(array('warehouseId','totalNum'));
		}
		return $list;
	}

	/**
	* @access 取得呆滞级别
	*/
	private function getGlevel( $flush = false ){
		if( !$flush ){
			$glevel = Yii::app()->cache->get('glevel');
			if( !empty ( $glevel ) ){
				return $glevel;
			}
		}

		$model = tbGlassyLevel::model()->findAll( array(
			'order'=>'conditions asc',
		));

		$list = array();
		foreach( $model as $val ){
			$list[$val->id] = $val->attributes;
		}

		Yii::app()->cache->set('glevel', $list);
		return $list;
	}

	/**
	* 返回具体呆滞级别名称
	*/
	private function getLevelName( $t,$levels ){
		foreach ( $levels as $val ){
			if( $t <= strtotime( '-'.$val['conditions'].' days') ){
				return $val['id'];
			}
		}
	}


	/**
	* @access 生成报表数据--写了存储过程
	*/
	private function glassyData(){
		$glevel  = $this->getGlevel( true );

		Yii::app()->cache->set('glassyDataTime', date('Y-m-d H:i:s'));

		$sql = 'CALL `pro_flush_glassy`();';
		$command = Yii::app()->db->createCommand($sql);
		$command->execute();
	}

	/**
	* @access 从呆滞报表转成尾货
	*/
	public function actionChangetail() {
		if( Yii::app()->request->isPostRequest ) {
			$singleNumber = Yii::app ()->request->getPost ( 'singleNumber' );
		}else{
			$singleNumber = Yii::app ()->request->getQuery ( 'singleNumber' );
		}

		if( empty($singleNumber) ){
			$this->redirect( $this->createUrl('index') );
			exit;
		}

		$c = new CDbCriteria;
		$c->compare('state',0);
		$c->compare('singleNumber',$singleNumber);
		$model = tbGlassyList::model()->findAll( $c );
		if( !$model ){
			$this->redirect( $this->createUrl('index') );
			exit;
		}

		$TailForm = new TailForm();
		$productId = array_unique( array_map( function($i){ return $i->productId;},$model ) );
		if( count($productId) !== 1 ){
			$this->setError( array( array(Yii::t('product','Different series of products cannot be turned into')) ) );
			goto showpage;
		}

		$tailData = Yii::app()->request->getPost('tail');
		if( !empty( $tailData ) && is_array( $tailData ) ) {
			$TailForm->attributes = $tailData;
			$singles = array_unique( array_map( function($i){ return $i->singleNumber;},$model ) );
			if( $TailForm->changetail( $productId['0'],$singles ) ){
				$url = $this->createUrl( 'index' );
				$this->dealSuccess($url);
			}else{
				$errors = $TailForm->getErrors();
				$this->dealError( $errors );
			}
		}

		showpage:

		$glevel = $this->getGlevel();
		$list = $this->setList( $model,array_reverse( $glevel ) );

		//仓库列表
		$warehouse = tbWarehouseInfo::model()->getAll();

		$this->render( 'changetail', array('list'=>$list,'warehouse'=>$warehouse,'glevel'=>$glevel,'tail'=>$TailForm->attributes) );
	}
}