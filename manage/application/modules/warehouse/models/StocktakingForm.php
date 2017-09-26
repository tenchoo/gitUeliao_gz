<?php
/**
 * 仓库盘点单，盘点会强制更改仓库产品数量的修改，盘点单不提供修改功能。
 * 盘点策略：盘点时按产品全盘点，会清除当前仓库此产品的全部库存信息，然后根据盘点结果新增信息。（2015/11/23）
 * 盘点日志：需记录产品盘点前的库存情况和盘点的结果。
 * @author liang
 * @version 0.1
 * @package CFormModel
 */
class StocktakingForm extends CFormModel {

	public $takinger;

	public $serialNumber;

	public $warehouseId;

	public $stocktakingId;

	public $productId;

	public $abnormity;//是否有异常

	public $difference; //是否有出入

	private $_model;

	public function rules()	{
		return array(
			array('warehouseId,serialNumber','required','on'=>'step1'),
			array('warehouseId,serialNumber,takinger','required','on'=>'step2'),
			array('warehouseId,takinger','required','on'=>'all'),
			array('warehouseId', "numerical","integerOnly"=>true),
			array('takinger','length','max'=>'15'),
			array('takinger,serialNumber,products','safe'),
		);
	}

	public function attributeLabels() {
		return array(
			'takinger' => '盘点人',
			'serialNumber' => '产品编号',
			'warehouseId' => '盘点仓库',
		);
	}

	/**
	* 新增盘点单
	*/
	public function add_step1(){
		if( !$this->validate() ) {
			return false ;
		}

		$product = tbProduct::model()->find( array(
							'select'=>'productId',
							'condition'=>'serialNumber=:s and state in(0,1)',
							'params'=>array( ':s'=>$this->serialNumber ),
						) );
		if( !$product ){
			$this->addError('product','产品不存在或已被删除');
			return false;
		}

		$this->productId = $product->productId;

		//判断产品是否有仓库销定，若有，不允许盘点。
		$falg = tbWarehouseLock::model()->exists( 'productId=:id',array(':id'=>$product->productId) );
		if( $falg ){
			$this->addError('product',Yii::t('warehouse','This product exists when the warehouse lock, please lock the release of all the release after the operation') );
			return false;
		}
		return true;
	}

	/**
	* 新增盘点单
	*/
	public function add(){
		$this->takinger = Yii::app()->request->getPost('takinger');

		$this->scenario = 'step2';
		if( !$this->validate() ) {
			return false ;
		}

		$file = CUploadedFile::getInstanceByName('cfile');//获取上传的文件实例
		if( is_null( $file ) ) {
			$this->addError('product','必须上传盘点数据');
			return false;
		}

		$type = $file->getType();
		switch( $type ){
			case 'application/vnd.ms-excel':
				$reader = 'Excel5';
				break;
			case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
				$reader = 'Excel2007';
				break;
			default:
				$this->addError('product','盘点数据格式不正确');
				return false;
				break;
		}

		//step1.1 生成盘点单
		$model = new tbStocktaking();
		$model->productId = $this->productId;
		$model->serialNumber = $this->serialNumber;
		$model->warehouseId = $this->warehouseId;
		$model->stocktakingId = $this->stocktakingId;
		$model->userName = Yii::app()->user->username;
		$model->takinger = $this->takinger;
		$model->checkUser = '';

		$transaction = Yii::app()->db->beginTransaction();
		if(!$model->save()){
			$transaction->rollback();
			$this->addErrors( $model->getErrors() );
			return false;
		}

		$this->stocktakingId = $model->stocktakingId ;
		$this->saveExcel( $file ,$reader,$model->stocktakingId );
		$transaction->commit();
		return true;
	}

	public function saveExcel( $file ,$reader,$stocktakingId ) {

		$excelFile = $file->getTempName();//获取文件名

		//这里就是导入PHPExcel包了，要用的时候就加这么两句，方便吧
		Yii::$enableIncludePath = false;
		Yii::import('libs.vendors.phpexcel.*');
		$phpexcel = new PHPExcel;

		$excelReader = PHPExcel_IOFactory::createReader( $reader );
		$phpexcel = $excelReader->load($excelFile)->getSheet(0);//载入文件并获取第一个sheet
		$total_line = $phpexcel->getHighestRow();
	//	$total_column = $phpexcel->getHighestColumn();

		$detail = new tbStocktakingDetail();
		$detail->stocktakingId = $stocktakingId;
		$detail->positionId = 0;
		$detail->productId = 0;
		$detail->color = '';
		$detail->state = 0;

		$arr = array('0'=>'singleNumber','1'=>'positionTitle','2'=>'productBatch','3'=>'oldNum','4'=>'num');

		for ($row = 3; $row <= $total_line; $row++) {
			$data = array();
			for ($column = 'A'; $column <= 'E'; $column++) {
				$data[] = trim($phpexcel->getCell($column.$row) -> getValue());
			}

			$_detail = clone $detail;

			for ($i = 0; $i <= 4; $i++) {
				if( array_key_exists( $i,$data) ){
					if( $i == '0' ){
						$data[$i] = $this->serialNumber.'-'.$data[$i];
					}else if( $i>= 3 && empty( $data[$i] ) ) {
						$data[$i] = 0;
					}
					$_detail->$arr[$i] = $data[$i];
				}
			}

			$_detail->save();
		}

		//对比仓库名，看仓位名是否有异常
		$t = $detail->tableName();
		$sql = "UPDATE $t t,{{product_stock}} s  SET t.`productId`=s.`productId` WHERE s.`singleNumber`=t.`singleNumber` and t.`stocktakingId` = '$stocktakingId' and t.`productId` = 0 ";
		$command = Yii::app()->db->createCommand($sql);
		$command->execute();

			//查看单品编码是否有异常
		$sql = "UPDATE $t t , {{warehouse_position}} p SET  t.`positionId`=p.`positionId` WHERE t.`stocktakingId` = '$stocktakingId'  and  t.`positionId` = 0 and  p.`title`=t.`positionTitle` and p.state=0 and p.warehouseId = '".$this->warehouseId."' and p.parentId>0";
		$command = Yii::app()->db->createCommand($sql);
		$command->execute();
    }


	public function comfirmData(){
		$op = Yii::app()->request->getPost('op');
		if( !in_array( $op ,array('cancle','doSave') ) || empty( $this->_model ) ) return ;

		if( $op === 'cancle' ){
			$this->_model->state = 1;
			$this->difference = false;
		}else{
			//有异常不能保存
			if( $this->abnormity ){
				$this->addError('product','有异常不能保存');
				return false;
			}
			$this->_model->state = 2;
		}

		$transaction = Yii::app()->db->beginTransaction();
		$this->_model->checkUser = Yii::app()->user->username;
		$this->_model->updateTime = new CDbExpression('NOW()');
		$this->_model->checkUserId = Yii::app()->user->id;

		if( !$this->_model->save() ){
			$transaction->rollback();
			$this->addErrors( $this->_model->getErrors() );
			return false ;
		}

		if( $op === 'doSave' ){
			if( empty(  $this->_model->productId ) ){
				//若为全仓盘点数据，清空全部仓库，再重新全部保存
				if( !$this->saveAll( $transaction  ) ){
					return false ;
				}
			}else{
				//是否有出入,保存差异数据
				if( !$this->saveDifference( $transaction ) ){
					return false ;
				}
			}
		}

		$transaction->commit();
		return true;
	}

	/**
	* 全仓盘点--盘点确认--保存产品数据
	*/
	private function saveAll( $transaction ){
		$warehouseProduct = new tbWarehouseProduct();
		$warehouseProduct->warehouseId  = $this->_model->warehouseId;

		//备份并清空原来的库存数据
		$warehouseProduct->clearStorge($this->_model->warehouseId,$this->_model->stocktakingId);

		$detail = $this->_model->detail;
		$total = array();
		foreach(  $detail as $val ){
			if( ! ( $val->num > 0) ) continue;

			$total[$val->singleNumber]['productId']= $val->productId;
			$total[$val->singleNumber]['nums'][] = $val->num;
			$row = $warehouseProduct->find( "positionId=:pid and singleNumber=:serial and productBatch=:batch", array(':pid'=>$val->positionId,':serial'=>$val->singleNumber,':batch'=>$val->productBatch) );
			if( $row ){
				$row->num = bcadd( $row->num ,$val->num,2 );
			}else{
				$row               = clone $warehouseProduct;
				$row->attributes   = $val->getAttributes ( array('productId','positionId','singleNumber','productBatch','num') );
			}

			if( !$row->save() ){
				$transaction->rollback();
				$this->addErrors( $row->getErrors() );
				return false ;
			}
		}

		$tbStocktakingCount = new tbStocktakingCount();
		$tbStocktakingCount->stocktakingId = $this->_model->stocktakingId;
		$tbStocktakingCount->warehouseId  = $this->_model->warehouseId;

		$tbWarehouseCount = new tbWarehouseCount();
		$tbWarehouseCount->warehouseId  = $this->_model->warehouseId;

		foreach( $total as $key=>$val){
			$totalNum = array_sum( $val['nums'] );

			$_tcount = clone $tbStocktakingCount;
			$_tcount->num = $totalNum;
			$_tcount->productId = $val['productId'];
			$_tcount->singleNumber = $key;
			if( !$_tcount->save() ){
				$transaction->rollback();
				$this->addErrors( $_tcount->getErrors() );
				return false ;
			}

			$tbWarehouseCount->productId  = $val['productId'];
			//更新仓库的统计数量
			$_wcount = $tbWarehouseCount->find( "warehouseId=:warehouseId and productId=:productId and singleNumber=:serial ", array(':warehouseId'=>$tbWarehouseCount->warehouseId,':productId'=>$tbWarehouseCount->productId,':serial'=>$key) );
			if( $_wcount ){
				if( $_wcount->num == $totalNum ) continue;
			}else{
				$_wcount = clone $tbWarehouseCount;
				$_wcount->singleNumber = $key;
			}

			$_wcount->num = $totalNum;
			if( !$_wcount->save() ){
				$transaction->rollback();
				$this->addErrors( $_wcount->getErrors() );
				return false ;
			}
		}

		return true;
	}

	/**
	* 按产品系列盘点--盘点确认--保存差异数据
	*/
	private function saveDifference( $transaction ){
		if( !$this->difference ) return true;

		$warehouseProduct = new tbWarehouseProduct();
		$warehouseProduct->warehouseId  = $this->_model->warehouseId;
		$warehouseProduct->productId  = $this->_model->productId;

		//备份原来的数据
		$warehouseProduct->backupByProductId( $this->_model->warehouseId,$this->_model->productId, $this->_model->stocktakingId);

		$detail = $this->_model->detail;

		$total = array();
		foreach(  $detail as $val ){
			$total[$val->singleNumber][] = $val->num;
			$row = $warehouseProduct->find( "positionId=:pid and singleNumber=:serial and productBatch=:batch", array(':pid'=>$val->positionId,':serial'=>$val->singleNumber,':batch'=>$val->productBatch) );
			if( $row ){
				if( $row->num == $val->num ) continue;
			}else{
				$row               = clone $warehouseProduct;
				$row->attributes   = $val->getAttributes ( array('positionId','singleNumber','productBatch') );
			}

			$row->num = $val->num;
			if( !$row->save() ){
				$transaction->rollback();
				$this->addErrors( $row->getErrors() );
				return false ;
			}
		}

		$tbStocktakingCount = new tbStocktakingCount();
		$tbStocktakingCount->stocktakingId = $this->_model->stocktakingId;
		$tbStocktakingCount->productId  = $this->_model->productId;
		$tbStocktakingCount->warehouseId  = $this->_model->warehouseId;

		$tbWarehouseCount = new tbWarehouseCount();
		$tbWarehouseCount->productId  = $this->_model->productId;
		$tbWarehouseCount->warehouseId  = $this->_model->warehouseId;

		foreach( $total as $key=>$val){
			$totalNum = array_sum( $val );

			$_tcount = clone $tbStocktakingCount;
			$_tcount->num = $totalNum;
			$_tcount->singleNumber = $key;
			if( !$_tcount->save() ){
				$transaction->rollback();
				$this->addErrors( $_tcount->getErrors() );
				return false ;
			}

			//更新仓库的统计数量
			$_wcount = $tbWarehouseCount->find( "warehouseId=:warehouseId and productId=:productId and singleNumber=:serial ", array(':warehouseId'=>$tbWarehouseCount->warehouseId,':productId'=>$tbWarehouseCount->productId,':serial'=>$key) );
			if( $_wcount ){
				if( $_wcount->num == $totalNum ) continue;
			}else{
				$_wcount = clone $tbWarehouseCount;
				$_wcount->singleNumber = $key;
			}

			$_wcount->num = $totalNum;
			if( !$_wcount->save() ){
				$transaction->rollback();
				$this->addErrors( $_wcount->getErrors() );
				return false ;
			}
		}

		return true;
	}



	/**
	* 备份原有的数据
	*
	*/
/* 	private function rebackData(){
		if( empty($this->stocktakingId) || empty($this->productId) || empty( $this->warehouseId)
		$tbWarehouseProduct = new tbWarehouseProduct();
		$singleNumber =  array_map( function ( $i ){ return $i['singleNumber'];},$products);
		$singleNumber = array_unique($singleNumber);
		foreach ( $singleNumber as $val ){
			tbWarehouseProduct::model()->clearStorge( $this->warehouseId,$val, $stocktakingId);
		}
	} */

/* 	private function addDetial( $stocktakingId,$products ){
		if( empty( $stocktakingId )&& !is_array( $products )){
			return false;
		}

		$detail = new tbStocktakingDetail();
		$detail->stocktakingId = $stocktakingId;
		foreach ( $products as $key=>$val){
			$_detail = clone $detail ;
			$_detail->attributes = $val;
			if( !$_detail->save() ) {
				$this->addErrors( $_detail->getErrors() );
				return false ;
			}
		}
		return true;
	} */

	public function getModelData( $id,$state = null ){
		if( !is_numeric( $id ) || $id < 1 ) return ;

		$condition = '';
		if( !is_null( $state) ){
			$condition = 'state='.$state;
		}

		$model = tbStocktaking::model()->findByPk( $id,$condition );
		if( is_null( $model ) ) return ;

		$this->warehouseId = $model->warehouseId ;
		$this->stocktakingId =  $model->stocktakingId ;
		$this->productId = $model->productId ;



		$data = $model->attributes;
		if( $model->serialNumber == '' ){
			$data['serialNumber'] = '全库盘点';
		}


		$data['abnormity'] = false; //是否有异常
		$this->difference = false;

		$detail = $model->detail;

		$stateTitle = '正常';
		$products = array( '2'=>array(),'1'=>array(),'0'=>array() );

		$total = array();

		foreach ( $detail as $val ){
			$p = $val->attributes;
			$p['stateTitle'] = $this->setStateTitle( $val,$p['state'],$data['abnormity'] );
			$products[$p['state']][] = $p;
			$total[] = $val->num;
		}

		$data['total'] = array_sum( $total );
		$data['unit'] = '';

		$product = tbProduct::model()->find( array(
							'select'=>'unitId',
							'condition'=>'productId=:productId',
							'params'=>array( ':productId'=>$model->productId ),
						) );
		if( $product ){
			$unit = tbUnit::model()->findByPk( $product->unitId );
			if( $unit ){
				$data['unit'] = $unit->unitName;
			}
		}

		$data['products'] = array_merge( $products['2'],$products['1'],$products['0']);
		$this->abnormity = $data['abnormity'] ;


		$this->_model = $model;
		return $data;
	}

	private function setStateTitle( $model,&$state,&$abnormity ){
		if( empty( $model->productId )){
			$abnormity = true;
			$state = 2;
			return '异常(单品编码不存在)';;
		}

		if(  $this->productId!='0' && $this->productId != $model->productId  ){
			$abnormity = true;
			$state = 2;
			return '异常(不是同一组产品)';
		}

		if( empty( $model->positionId )){
			$abnormity = true;
			$state = 2;
			return '异常(仓位不存在)';
		}

		if( $model->oldNum != $model->num ){
			$state = 1;
			$this->difference = true; //有出入
			return '出入';
		}

		$state = 0;
		return '正常';
	}

	/**
	* 生成excel文件
	*/
	public function defaultTemp(){
		Yii::$enableIncludePath = false;
		Yii::import('libs.vendors.phpexcel.*');

		$ExcelFile = new ExcelFile( '默认模板' );
		$saveData = array(
						array('仓库：','样板仓','产品：','K000','生成时间：',''),
						array('颜色编号','仓位','批次','仓库数量','盘点数量'),
						array('401','001','B001','100','100'),
					);
		$ExcelFile->createExl( $saveData );
	}

	/**
	* 下载模板
	* @param string $warehouse 仓库名称
	*/
	public function downTemps( $warehouse ){
		if( empty( $this->warehouseId )){
			$this->addError('warehouseId','仓库必须选择');
			return false;
		}

		$ids = Yii::app()->request->getPost('productId');
		if( empty( $ids )){
			$this->addError('productId','产品必须选择');
			return false;
		}

		$criteria = new CDbCriteria;
		$criteria->select = 't.productId,t.serialNumber';//默认*
		$criteria->compare('productId',$ids);
		$productModel = tbProduct::model()->findAll( $criteria );

		$products = array();
		foreach( $productModel as $val ){
			$products[$val->productId] = $val->serialNumber;
		}

		if( empty($products) ){
			$this->addError('productId','无产品模板供下载');
			return false;
		}

		$c = new CDbCriteria;
		$c->compare('productId',$ids);
		$c->compare('warehouseId',$this->warehouseId);
		$c->order = 'productId asc';

		$data = array();
		$t = date('Y-m-d H:i:s');
		$saveDir =  date('YmdHis');

		$models = tbWarehouseProduct::model()->findAll( $c );
		$arr = array('0'=>'singleNumber','1'=>'positionTitle','2'=>'productBatch','3'=>'oldNum','4'=>'num');
		$position = new tbWarehousePosition();
		foreach( $models as $val ){
			$_data = array();
			$_data[] = str_replace( $products[$val->productId].'-', '', $val->singleNumber );
			$_data[] = $position->positionName( $val->positionId,$w );
			$_data[] = $val->productBatch;
			$_data[] = $val->num;

			$data[$val->productId][] = $_data;
		}

		$i = count( $products );
		$isSave = ( $i>1 )?true:false;

		foreach( $products as $k=>$serial ){
			$saveData = ( array_key_exists( $k, $data ) ) ? $data[$k]:array();

			//压入表头数据
			array_unshift( $saveData ,array('仓库：',$warehouse,'产品：',$serial,'生成时间：',$t),array('颜色编号','仓位','批次','仓库数量','盘点数量') );

			$fileName = $warehouse.'_'.$serial;
			$ExcelFile = new ExcelFile( $fileName,$saveDir,$isSave );
			$ExcelFile->setSheetName( $serial );
			$ExcelFile->createExl( $saveData );
		}

		if( $i>1 ){
			//打包下载
			$dir = $ExcelFile->dirRoot.'/'.$saveDir;
			$zipfileName = $ExcelFile->dirRoot.'/temps_'.$saveDir.'.zip';
			HZip::zipDir( $dir, $zipfileName,true);
		}

	}

	/**
	* 全仓盘点--库存导出
	*/
	public function exportExcel( $warehouseId,$warehouseName ){
		if( empty ( $warehouseId ) || !is_numeric( $warehouseId ) ) return ;
		$sql = "SELECT t1.`title` AS areaTitle,t.`title` AS positionTitle,p.`singleNumber`,p.`productBatch`,p.num
				FROM {{warehouse_product}} p
				LEFT JOIN {{warehouse_position}} t ON ( p.`positionId` = t.`positionId`)
				LEFT JOIN {{warehouse_position}} t1 ON ( t1.`positionId` = t.`parentId`)
				WHERE p.`warehouseId` = '$warehouseId'
				ORDER BY t.`parentId` ASC,p.`positionId` ASC,p.`productId` ASC,p.`singleNumber` ASC";

		$command = Yii::app()->db->createCommand($sql);
		$saveData = $command->queryAll();

		//压入表头数据
		array_unshift( $saveData ,array('分区','仓位','单品编码','批次','当前库存数量','实际盘点数量') );

		array_unshift( $saveData ,array('仓库：',$warehouseName,'导出时间：', date('Y-m-d H:i:s') ),array() );


		Yii::$enableIncludePath = false;
		Yii::import('libs.vendors.phpexcel.*');
		$ExcelFile = new ExcelFile( $warehouseName );
		$ExcelFile->setSheetName( $warehouseName );
		$ExcelFile->createExl( $saveData );
		exit;
	}

	/**
	* 全仓盘点--库存导入
	*/
	public function importExcel(){

		if( !$this->validate() ) {
			return false ;
		}

		$file = CUploadedFile::getInstanceByName('cfile');//获取上传的文件实例
		if( is_null( $file ) ) {
			$this->addError('product','必须上传盘点数据');
			return false;
		}

		$type = $file->getType();
		switch( $type ){
			case 'application/vnd.ms-excel':
				$reader = 'Excel5';
				break;
			case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
				$reader = 'Excel2007';
				break;
			default:
				$this->addError('product','盘点数据格式不正确');
				return false;
				break;
		}

		//step1.1 生成盘点单
		$model = new tbStocktaking();
		$model->productId = $this->productId;
		$model->serialNumber = '';
		$model->warehouseId = $this->warehouseId;
		$model->stocktakingId = $this->stocktakingId;
		$model->userName = Yii::app()->user->username;
		$model->takinger = $this->takinger;
		$model->checkUser = '';

		$transaction = Yii::app()->db->beginTransaction();
		if(!$model->save()){
			$transaction->rollback();
			$this->addErrors( $model->getErrors() );
			return false;
		}

		$this->stocktakingId = $model->stocktakingId ;
		$this->saveExcel2( $file ,$reader,$model->stocktakingId );
		$transaction->commit();
		return true;
	}

	/**
	* 全仓盘点--库存导入--明细
	*/
	private function saveExcel2( $file ,$reader,$stocktakingId ) {

		$excelFile = $file->getTempName();//获取文件名

		//这里就是导入PHPExcel包了，要用的时候就加这么两句，方便吧
		Yii::$enableIncludePath = false;
		Yii::import('libs.vendors.phpexcel.*');
		$phpexcel = new PHPExcel;

		$excelReader = PHPExcel_IOFactory::createReader( $reader );
		$phpexcel = $excelReader->load($excelFile)->getSheet(0);//载入文件并获取第一个sheet
		$total_line = $phpexcel->getHighestRow();
	//	$total_column = $phpexcel->getHighestColumn();

		$detail = new tbStocktakingDetail();
		$detail->stocktakingId = $stocktakingId;
		$detail->positionId = 0;
		$detail->productId = 0;
		$detail->color = '';
		$detail->state = 0;

		$arr = array('0'=>'areaNumber','1'=>'positionTitle','2'=>'singleNumber','3'=>'productBatch','4'=>'oldNum','5'=>'num');

		for ($row = 4; $row <= $total_line; $row++) {
			$data = array();
			for ($column = 'A'; $column <= 'F'; $column++) {
				$data[] = trim($phpexcel->getCell($column.$row) -> getValue());
			}

			$_detail = clone $detail;

			for ($i = 1; $i <= 5; $i++) {
				if( array_key_exists( $i,$data) ){
					if( $i>= 4 && empty( $data[$i] ) ) {
						$data[$i] = 0;
					}
					$_detail->$arr[$i] = $data[$i];
				}
			}

			//产品批次暂时先不让输入，统一设为默认值：
			$_detail->productBatch = tbWarehouseProduct::DEATULE_BATCH;
			$_detail->save();
		}

		//对比仓库名，看仓位名是否有异常
		$t = $detail->tableName();
		$sql = "UPDATE $t t,{{product_stock}} s  SET t.`productId`=s.`productId` WHERE s.`singleNumber`=t.`singleNumber` and t.`stocktakingId` = '$stocktakingId' and t.`productId` = 0 ";
		$command = Yii::app()->db->createCommand($sql);
		$command->execute();

			//查看单品编码是否有异常
		$sql = "UPDATE $t t , {{warehouse_position}} p SET  t.`positionId`=p.`positionId` WHERE t.`stocktakingId` = '$stocktakingId'  and  t.`positionId` = 0 and  p.`title`=t.`positionTitle` and p.state=0 and p.warehouseId = '".$this->warehouseId."' and p.parentId>0";
		$command = Yii::app()->db->createCommand($sql);
		$command->execute();
    }
}