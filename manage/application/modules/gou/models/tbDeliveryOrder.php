<?php
/**
 * 送货单基本信息
 * @author liang
 * @version 0.1
 * @package CActiveRecord
 *
 * @property integer	$id
 * @property string		$orderId
 * @property integer	$areaId
 * @property integer	$state
 * @property integer	$num				数量
 * @property integer	$deliverymanId
 * @property string		$areaTitle
 * @property string		$deliverymanTitle
 * @property string		$name 				收货人
 * @property string		$phone				手机号码
 * @property string		$title				商品标题
 * @property string		$appointment		预约送货时间
 * @property string		$createTime			添加时间
 * @property string		$deliveryTime		送货时间

 * @property string		$orderAddress		订单地址
 * @property string		$deliveryAddress	送货地址
 * @property string		$remark				客户留言
 * @property string		$shopRemark			商家备注
 *
 */

 class  tbDeliveryOrder extends CActiveRecord {

	public $isPhone = false;//是否前端手机端显示

	public static function model( $className = __CLASS__ ) {
		return parent::model( $className );
	}

	public function tableName() {
		return "{{delivery_order}}";
	}

	public function rules() {
		return array(
			array('orderId,areaId,num,title,orderAddress,phone,name,deliverymanId,state','required'),
			array('areaId,deliverymanId,state', "numerical","integerOnly"=>true),
			array('num', "numerical","integerOnly"=>true,'min'=>1),
			array('title,phone', 'length', 'max'=>255, 'min'=>1),
			array('state','in','range'=>array(0,1,2,3)),
			array('title,orderId,areaId,num,title,orderAddress,phone,name,deliveryAddress,remark,appointment,shopRemark','safe'),
			array('orderId','checkExists'),
		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'num' => '数量',
			'name' => '收货人',
			'phone' => '手机号码',
			'title' => '商品标题',
			'orderAddress' => '订单地址',
			'deliveryAddress' => '送货地址',
			'remark' => '客户留言',
			'orderId'=>'订单号',
			'areaId'=>'片区',
			'deliverymanId'=>'送货员',
			'state'=>'配送状态',
			'appointment'=>'预约送货时间'
		);
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
		if( $this->id ){
			$criteria->addCondition("id !='".$this->id."'");
		}

		$model = $this->exists( $criteria );
		if( $model ){
			$label = $this->getAttributeLabel($attribute);
			$this->addError($attribute,Yii::t('base','{attribute} already exists',array('{attribute}'=>$label)));
		}
	}

	/**
	* 取得状态
	*/
	public function getStates(){
		return array(
			'0'=>'待送',
			'1'=>'已送',
			'2'=>'挂起',
			'3'=>'放弃',
		);
		return $result;
	}

	/**
	* excel导入数据
	*/
	public function Import(){
		//数据类型
		$dataType = Yii::app()->request->getPost('type');
		if( !in_array($dataType,array('default','goujiazi','youzan','weidian')) ){
			$this->addError('orderId','未知数据来源格式');
			return false;
		}

		$file = CUploadedFile::getInstanceByName('cfile');//获取上传的文件实例
		if( is_null( $file ) ) {
			$this->addError('orderId','必须上传数据');
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
				$this->addError('orderId','数据格式不正确');
				return false;
				break;
		}

		$excelFile = $file->getTempName();//获取文件名

		//这里就是导入PHPExcel包了
		Yii::$enableIncludePath = false;
		Yii::import('libs.vendors.phpexcel.*');
		$phpexcel = new PHPExcel;

		$excelReader = PHPExcel_IOFactory::createReader( $reader );
		$phpexcel = $excelReader->load($excelFile)->getSheet(0);//载入文件并获取第一个sheet
		$total_line = $phpexcel->getHighestRow();

		//字段说明
		$arr = array(
				'default'=> array(
						array('title'=>'收货人','key'=>'name'),
						array('title'=>'固话','key'=>'','noSave'=>true ),
						array('title'=>'手机号码','key'=>'phone'),
						array('title'=>'订单地址','key'=>'orderAddress'),
						array('title'=>'商品','key'=>'title'),
						array('title'=>'客户留言','key'=>'remark'),
						array('title'=>'商家备注','key'=>'shopRemark'),
						),
				'goujiazi'=> array(
						array('title'=>'订单号','key'=>'orderId'),
						array('title'=>'收件人','key'=>'name'),
						array('title'=>'手机号码','key'=>'phone'),
						array('title'=>'订单地址','key'=>'orderAddress'),
						array('title'=>'发货信息','key'=>'title'),
						array('title'=>'数量','key'=>'num'),
						array('title'=>'买家留言','key'=>'remark'),
						array('title'=>'商家备注','key'=>'shopRemark'),
						),
				'youzan'=> array(
						array('title'=>'订单号','key'=>'orderId','column'=>'A'),
						array('title'=>'收件人','key'=>'name','column'=>'S'),
						array('title'=>'订单地址','key'=>'orderAddress','column'=>'W'),
						array('title'=>'手机号码','key'=>'phone','column'=>'AD'),
						array('title'=>'商品','key'=>'title','column'=>'AI'),
						array('title'=>'商家备注','key'=>'shopRemark','column'=>'AK'),
						array('title'=>'数量','key'=>'num','column'=>'AL'),
						array('title'=>'买家留言','key'=>'remark','column'=>'AO')
						),
				'weidian'=> array(
						array('title'=>'订单编号','key'=>'orderId' ,'column'=>'A'),
						array('title'=>'收件人姓名','key'=>'name','column'=>'I'),
						array('title'=>'收件人手机','key'=>'phone','column'=>'J'),
						array('title'=>'收货详细地址','key'=>'orderAddress','column'=>'Q'),
						array('title'=>'商品总件数','key'=>'num','column'=>'T'),
						array('title'=>'订单描述','key'=>'title','column'=>'U'),
						array('title'=>'订单备注','key'=>'remark','column'=>'Z'),
						array('title'=>'备注','key'=>'shopRemark','column'=>'AB'),
						),
			);

		$saveNums = 0;
		for ($row = 2; $row <= $total_line; $row++) {
			$_model = clone $this;

			$data = array();
			$n = count( $arr[$dataType] );

			$column = 'A';
			for ($i = 0; $i < $n; $i++) {
				if( !empty( $arr[$dataType][$i]['key'] ) && !array_key_exists('noSave',$arr[$dataType][$i] ) ){
					if( array_key_exists('column',$arr[$dataType][$i] ) ){
						$column = $arr[$dataType][$i]['column'];
					}
					$_model->$arr[$dataType][$i]['key'] = trim( (string)$phpexcel->getCell($column.$row) -> getValue() );
				}
				$column++;
			}

			switch( $dataType ){
				case 'default':
					$n = strpos( $_model->title ,"【");
					$n1 = strpos( $_model->title ,"】" );
					$title = substr( $_model->title,0,$n);

					$num = (int)substr( $_model->title,$n+3,$n1-$n+3 );
					$_model->title = $title;
					$_model->num = $num;
					$_model->orderId =0;
					break;
				case 'weidian':
					$_model->orderAddress = str_replace( ' ','',$_model->orderAddress );
					$_model->orderAddress = str_replace( '广东深圳','广东省深圳',$_model->orderAddress );
					break;
				case 'youzan':
					$t = trim( (string)$phpexcel->getCell('AP'.$row) -> getValue() );
					if( !empty ( $t ) ){
						$_model->remark .= ';'.$t;
					}
					break;
			}

			$_model->state = 0;
			$_model->areaId = 0;
			$_model->deliverymanId = 0;
			$_model->deliveryAddress = $_model->orderAddress;
			$_model->areaTitle ='';
			$_model->deliverymanTitle ='';

			$_model->setDuplicateArea();

			if( $_model->save() ){
				$saveNums++;
			}else{
				echo '第'.$row.'行保存失败<br>';
				print_r( $_model->errors );
				echo '<br>';
			}
		}
		exit( '成功保存条数：'.$saveNums );
		return true;
	}

	/**
	* 根据相同的地址设置areaId
	* 只在excel导入数据时使用
	*/
	public function setDuplicateArea(){
		if( empty( $this->orderAddress ) ) return;

		$model = $this->find( array(
					'condition'=>'orderAddress = :orderAddress and areaId>0',
					'params'=> array( ':orderAddress'=>$this->orderAddress ),
					'order'=>'id desc'
					) );
		if( !$model ) return ;

		$this->areaId = $model->areaId;
		$this->areaTitle = $model->areaTitle;
		$this->deliveryAddress = $model->deliveryAddress;
	}

	/**
	* 搜索列表
	* @param array $conditions 查询条件
	* @param integer $perSize 每页条数
	* @param boolean $exportExcel 是否导出excel
	*/
	public function serach( array $conditions ,$perSize = 20,$exportExcel = false ){
		$criteria = new CDbCriteria;
		$criteria->order = 'areaTitle asc, deliveryAddress asc';

		if( array_key_exists( 'isDel',$conditions ) && in_array($conditions['isDel'],array(0,1) ) ){
			$criteria->compare( 't.isDel',$conditions['isDel'] );
		}


		$result['types'] = array('orderId'=>'按订单编号','name'=>'按姓名','phone'=>'按手机','orderAddress'=>'按订单地址','deliveryAddress'=>'按送货地址');
		if( !empty( $conditions['keyword'] )  && array_key_exists( $conditions['type'] ,$result['types'] ) ){
			$criteria->compare( 't.'.$conditions['type'],$conditions['keyword'],true );
		}

		$result['states'] = $this->getStates();
		if( array_key_exists( $conditions['state'] ,$result['states'] ) ){
			$criteria->compare( 't.state',$conditions['state'] );
		}else{
			$conditions['state'] = null;
		}

		$result['mems'] =  tbDeliveryman::model()->getDeliverymans();
		if( array_key_exists( $conditions['deliverymanId'] ,$result['mems'] ) ){
			if( $conditions['deliverymanId'] == '-1' ){
				$criteria->addCondition("t.deliverymanId >0 ");
			}else{
				$criteria->compare( 't.deliverymanId',$conditions['deliverymanId'] );
			}

			$deliveryman = $result['mems'][$conditions['deliverymanId']];
		}else{
			$deliveryman = '';
		}

		$result['areas'] = tbDeliveryArea::model()->getDeliveryAreas();
		if( array_key_exists( $conditions['areaId'] ,$result['areas'] ) ){
			if( $conditions['areaId'] == '-1' ){
				$criteria->addCondition("t.areaId >0 ");
			}else{
				$criteria->compare( 't.areaId',$conditions['areaId'] );
			}

		}

		if( $exportExcel === true ){
			$data = $this->findAll( $criteria );
		}else{
			$models = new CActiveDataProvider($this , array(
				'criteria'=>$criteria,
				'pagination'=>array( 'pageSize'=>$perSize,'pageVar'=>'page' ),
			));
			$data = $models->getData();

			$result['pages'] = $models->getPagination();
			$result['list'] = array_map( function($i){
				if( $i->appointment == '0000-00-00 00:00:00') {
					$i->appointment = '';
				}else{
					if( $this->isPhone ){
						$i->appointment = date( 'm-d',strtotime( $i->appointment ) );
					}else{
						$i->appointment = date( 'Y-m-d',strtotime( $i->appointment ) );
					}
				}

				if( $this->isPhone ){
					if( strlen($i->orderId)> 4 ){
						$i->orderId = substr( $i->orderId, -4);
						$i->orderId = '*'.$i->orderId;
					}
					$i->title = mb_substr( $i->title, 0, 8, 'utf-8' );
				}
				$info = $i->attributes;
				$info['ops'] = tbDeliveryOrderOp::model()->getAllops( $i->id );
				return $info;},$data);
		}

		$criteria->select = 'sum( num ) as num';
		$totalModel = $this->find( $criteria );
		$result['totalNum'] = $totalModel->num;

		if( $exportExcel !== true ){
			if( $this->isPhone ){
				$this->setDeliveryArea( $conditions['deliverymanId'] ,$conditions['state'] ,$result['areas'] );
			}
			return $result;
		}

		Yii::$enableIncludePath = false;
		Yii::import('libs.vendors.phpexcel.*');

		$ExcelFile = new ExcelFile( '订单数据' );
		$ExcelFile->setWidth( 'A',11 );
		$ExcelFile->setWidth( 'B',20 );
		$ExcelFile->setWidth( 'C',10 );
		$ExcelFile->setWidth( 'D',20 );
		$ExcelFile->setWidth( 'E',10 );
		$ExcelFile->setWidth( 'F',45 );
		$ExcelFile->setWidth( 'G',42 );
		$ExcelFile->setWidth( 'H',20 );

		$saveData = array(
						array('总订单数：',count($data).'单','总件数：',$result['totalNum'].'件','送货员:',$deliveryman,'导出时间：',date('Y-m-d H:i:s'),'isTitle'=>true),
						array('序号','收货人','片区','手机号码','数量','订单地址','商家备注','客户留言','isTitle'=>true),
					);
		foreach ( $data as $key=>$val ){
			$val->orderAddress = str_replace( '广东省深圳市','',$val->orderAddress );
			$state = $result['states'][$val['state']];
			$saveData[] = array( $key+1,$val['name'], $val['areaTitle'],$val['phone'],$val['num'],$val['orderAddress'],$val['shopRemark'], $val['remark']);
		}
		$ExcelFile->createMergeExl( $saveData );
		exit;
	}

	/**
	* 根据相同的地址设置areaId
	* 只在excel导入数据时使用
	*/
	public function setDeliveryArea( $deliverymanId,$state ,&$areas ){
		if( empty( $deliverymanId ) ) return;

		$condition = 'deliverymanId = :deliverymanId and areaId>0 and isDel=0';
		$params = array( ':deliverymanId'=>$deliverymanId );
		if( !is_null( $state ) ){
			$condition .= ' and state = :state';
			$params[':state'] = $state;
		}

		$models = $this->findAll( array(
					'select'=>'areaId',
					'condition'=>$condition,
					'params'=> $params,
					'order'=>'id desc',
					'group'=>'areaId'
					) );
		if( !$models ) return ;
		$ids = array_map( function( $i ){ return $i->areaId;}, $models );

		foreach ( $areas as $k=>$val ){
			if( !in_array( $k,$ids ) ){
				unset( $areas[$k] );
			}
		}
	}

	/**
	* 按片区统计订单数和商品总件数
	*/
	public function groupArea( $state ){
		$criteria = new CDbCriteria;
		$criteria->select  = 'count(id) as id,sum(num) as num ,areaId,max(deliverymanId) as deliverymanId';
		$criteria->compare( 't.isDel','0' );
		if( is_numeric( $state ) ){
			$criteria->compare( 't.state',$state );
		}

		$criteria->group  = 'areaId';
		$models = $this->findAll( $criteria );
		if( empty($models) ) return array();

		$data = array();
		foreach ( $models as $k=>$val ){
			$data[$val->areaId] = array( 'c'=>(int)$val->id,'total'=>(int)$val->num,'deliverymanId'=>(int)$val->deliverymanId );
		}
		return $data;
	}

	/**
	* 按业务员统计订单数和商品总件数
	*/
	public function groupMens( $state ){
		$criteria = new CDbCriteria;
		$criteria->select  = 'count(id) as id,sum(num) as num ,deliverymanId';
		$criteria->compare( 't.isDel','0' );
		if( is_numeric( $state ) ){
			$criteria->compare( 't.state',$state );
		}

		$criteria->group  = 'deliverymanId';
		$criteria->order  = 'deliverymanId desc';
		$models = $this->findAll( $criteria );
		if( empty($models) ) return array();

		return array_map( function ( $val ){
					return array('deliverymanId'=>$val->deliverymanId ,'c'=>(int)$val->id,'total'=>(int)$val->num );
					},$models );
	}

	/**
	* “已送”记录按天/司机统计，统计报告（只显示日期、已送订单数/件数，不需要显示订单明细）在司机端也显示，便于司机考核记发工资
	*/
	public function statistics( $deliverymanId, $deliveryTime,$deliveryTime2 ){

		if( empty( $deliveryTime ) ){
			$this->addError( 'deliveryTime' ,'请选择送货开始日期' );
			return array();
		}

		if( empty( $deliverymanId ) ){
			$this->addError( 'deliverymanId' ,'请选择送货员' );
			return array();
		}

		$data = array();

		$t1 = strtotime( $deliveryTime ) ;
		$t2 = strtotime( $deliveryTime2 ) ;
		for( $t1;$t1<=$t2;$t1=$t1+86400 ){
			$t = date("Y-m-d",$t1 );
			$_data = $this->getStatisticsData( $deliverymanId,$t );
			if( empty( $data ) ){
				$_data = array( 'c'=>'','total'=>'' );
			}else{
				if( empty($_data['c']) )  $_data['c'] = '';
				if( empty($_data['total']) )  $_data['total'] = '';
			}

			$_data['t'] = $t;
			$data[] = $_data;
		}
		return $data;
	}

	private function getStatisticsData( $deliverymanId,$t1 ){
		$criteria = new CDbCriteria;
		$criteria->select  = 'count(id) as id,sum(num) as num ';
		$criteria->compare( 't.isDel','0' );
		$criteria->compare( 't.state','1' );

		$t2 = date("Y-m-d H:i:s",strtotime( $t1 )+86400 ) ; //包含选择的当天

		$criteria->addCondition("t.deliveryTime>='$t1'");
		$criteria->addCondition("t.deliveryTime<'$t2'");

		if( is_numeric( $deliverymanId ) && $deliverymanId >0 ){
			$criteria->compare( 't.deliverymanId',$deliverymanId );
		}

		$model = $this->find( $criteria );
		if( empty($model) ) return array();

		return array( 'c'=>(int)$model->id,'total'=>(int)$model->num );
	}

	/**
	* 整个片区批量设置送货员，只分配待送的且是未关闭的订单
	* @param integer $deliverymanId 设置的业务员ID
	* @param integer $areaId		需要设置的片区ID
	*/
	public function setDeliverymanByArea( $deliverymanId ,$areaId ){
		if( !is_numeric( $areaId ) || !is_numeric( $deliverymanId ) ) return false;

		$mems =  tbDeliveryman::model()->getDeliverymans();
		unset( $mems[-1] );

		if( !array_key_exists( $deliverymanId,$mems) ) return false;

		$deliverymanTitle = ($deliverymanId>0)?$mems[$deliverymanId]:'';
		return $this->updateAll( array('deliverymanId'=>$deliverymanId,'deliverymanTitle'=>$deliverymanTitle ),
								'areaId=:areaId and state = :state and isDel = :isDel',
								array( ':areaId'=>$areaId,':state'=>0,':isDel'=>0 )
								);
	}

		/**
	 * 保存前的操作
	 */
	protected function beforeSave(){
		if($this->isNewRecord){
			if( empty ( $this->orderId) ){
				$this->orderId = 'bos'.time().rand(10000,99999);
			}
		}
		return true;
	}
}