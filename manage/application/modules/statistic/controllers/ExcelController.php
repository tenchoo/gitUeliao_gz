<?php
/**
 * 报表导出为excel文件
 */
class ExcelController extends Controller
{

    public function init()
    {
        parent::init();
        Yii::import('libs.vendors.phpexcel.*');
    }

    /**
     * 业务员统计报表 */
    public function actionSaleman()
    {
        $member  = Yii::app()->request->getQuery("member");
        $product = Yii::app()->request->getQuery("product");

        if (!$member) {
            $member = 0;
        }

        if (!$product) {
            $product = '';
        }

        $form             = new SalemanForm;
        $form->attributes = ['member' => $member, 'product' => $product];
        $data_list        = $this->search($form);
        if ($data_list) {
            $lists = array_map(function ($i) {
                return [$i['dealTime'], $i['quantity'], $i['price']];
            }, $data_list);
            array_unshift($lists, ['月份', '销售量', '销售金额']);
            array_unshift($lists, []);
            array_unshift($lists, ['业务员：', $form->account, '报表区间:', $form->start . '--' . $form->end, '客户：', Yii::app()->request->getQuery("memberName"), '产品编号:', $product]);
            array_push($lists, ['结余', $form->quantitys, $form->prices]);

            $excel = new ExcelFile('月度业绩统计表');
            $excel->createExl($lists);
            Yii::app()->end();
        }
    }

    /**
     * 业务员明细报表 */
    public function actionSalemandetail()
    {
        $member  = Yii::app()->request->getQuery("member");
        $product = Yii::app()->request->getQuery("product");

        if (empty($member)) {
            $member = 0;
        }

        if (empty($product)) {
            $product = '';
        }

        $form             = new SalemandetailForm;
        $form->attributes = ['member' => $member, 'product' => $product];
        $data_list        = $this->search($form);
        if ($data_list) {
            $lists = array_map(function ($i) {
                return [$i['orderId'], $i['guest'], $i['serialNumber'], $i['singleNumber'], $i['price'], $i['quantity'], $i['total']];
            }, $data_list);
            array_unshift($lists, ['订单号','客户','产品编号','颜色', '单价', '销售量', '销售金额']);
            array_unshift($lists, []);
            array_unshift($lists, ['业务员：', $form->account, '报表区间:', $form->start . '--' . $form->end, '客户：', Yii::app()->request->getQuery("memberName"), '产品编号:', $product]);
            array_push($lists, ['结余', '', '', '', $form->quantitys, $form->prices]);

            $excel = new ExcelFile('月度业绩明细表');
            $excel->createExl($lists);
            Yii::app()->end();
        }
    }

    /**
     * 业务员统计报表 */
    public function actionSalemandays()
    {
    	$member  = Yii::app()->request->getQuery("member");
    	$product = Yii::app()->request->getQuery("product");

    	if (!$member) {
    		$member = 0;
    	}

    	if (!$product) {
    		$product = '';
    	}

    	$form             = new SalemanForm('days');
    	$form->attributes = ['member' => $member, 'product' => $product];
    	$data_list        = $this->search($form);
    	if ($data_list) {
    		$lists = array_map(function ($i) {
    			return [$i['dealTime'], $i['quantity'], $i['price']];
    		}, $data_list);
    			array_unshift($lists, ['月份', '销售量', '销售金额']);
    			array_unshift($lists, []);
    			array_unshift($lists, ['业务员：', $form->account, '报表区间:', $form->start . '--' . $form->end, '客户：', Yii::app()->request->getQuery("memberName"), '产品编号:', $product]);
    			array_push($lists, ['结余', $form->quantitys, $form->prices]);

    			$excel = new ExcelFile('月度业绩统计表');
    			$excel->createExl($lists);
    			Yii::app()->end();
    	}
    }

    /**
     * 业务员明细报表 */
    public function actionSalemandetaildays()
    {
    	$member  = Yii::app()->request->getQuery("member");
    	$product = Yii::app()->request->getQuery("product");

    	if (empty($member)) {
    		$member = 0;
    	}

    	if (empty($product)) {
    		$product = '';
    	}

    	$form             = new SalemandetailForm('days');
    	$form->attributes = ['member' => $member, 'product' => $product];
    	$data_list        = $this->search($form);
    	if ($data_list) {
    		$lists = array_map(function ($i) {
    			return [$i['orderId'], $i['guest'], $i['serialNumber'], $i['singleNumber'], $i['price'], $i['quantity'], $i['total']];
    		}, $data_list);
    			array_unshift($lists, ['订单号','客户名称', '产品编号', '颜色', '单价', '销售量', '销售金额']);
    			array_unshift($lists, []);
    			array_unshift($lists, ['业务员：', $form->account, '报表区间:', $form->start . '--' . $form->end, '客户：', Yii::app()->request->getQuery("memberName"), '产品编号:', $product]);
    			array_push($lists, ['结余', '', '', '', '',$form->quantitys, $form->prices]);

    			$excel = new ExcelFile('月度业绩明细表');
    			$excel->createExl($lists);
    			Yii::app()->end();
    	}
    }

    /**
     * 客户统计报表 */
    public function actionMember()
    {
        $product = Yii::app()->request->getQuery("product");

        if (empty($product)) {
            $product = '';
        }

        $form             = new MemberForm;
        $form->attributes = ['product' => $product];
        $data_list        = $this->search($form);
        if ($data_list) {
            $lists = array_map(function ($i) {
                return [$i['dealTime'], $i['quantity'], $i['price']];
            }, $data_list);
            array_unshift($lists, ['月份', '交易量', '交易金额']);
            array_unshift($lists, []);
            array_unshift($lists, ['客户：', $form->account, '报表区间:', $form->start . '--' . $form->end, '产品编号:', $product]);
            array_push($lists, ['结余', $form->quantitys, $form->prices]);

            $excel = new ExcelFile('月度客户交易统计表');
            $excel->createExl($lists);
            Yii::app()->end();
        }
    }

    /**
     * 客户明细报表 */
    public function actionMemberdetail()
    {
        $product = Yii::app()->request->getQuery("product");

        if (empty($product)) {
            $product = '';
        }

        $form             = new MemberdetailForm;
        $form->attributes = ['product' => $product];
        $data_list        = $this->search($form);
        if ($data_list) {
            $lists = array_map(function ($i) {
                return [$i['orderId'], $i['serialNumber'], $i['singleNumber'], $i['price'], $i['quantity'], $i['total']];
            }, $data_list);
            array_unshift($lists, ['订单号', '产品编号', '颜色', '单价', '销售量', '销售金额']);
            array_unshift($lists, []);
            array_unshift($lists, ['客户：', $form->account, '报表区间:', $form->start . '--' . $form->end, '产品编号:', $product]);
            array_push($lists, ['结余', '', '', '', $form->quantitys, $form->prices]);

            $excel = new ExcelFile('月度客户交易明细表');
            $excel->createExl($lists);
            Yii::app()->end();
        }
    }

    /**
     * 产品统计报表 */
    public function actionProduct()
    {
        $member = Yii::app()->request->getQuery("member");
        if (empty($member)) {
            $member = 0;
        }

        $form             = new ProductForm;
        $form->attributes = ['member' => $member];
        $data_list        = $this->search($form);
        if ($data_list) {
            $lists = array_map(function ($i) {
                return [$i['dealTime'], $i['quantity'], $i['price']];
            }, $data_list);
            array_unshift($lists, ['月份', '销售量', '销售金额']);
            array_unshift($lists, []);
            array_unshift($lists, ['产品：', $form->account, '报表区间:', $form->start . '--' . $form->end, '客户:', Yii::app()->request->getQuery("memberName")]);
            array_push($lists, ['合计', $form->quantitys, $form->prices]);

            $excel = new ExcelFile('月度产品销售统计表');
            $excel->createExl($lists);
            Yii::app()->end();
        }
    }

    /**
     * 产品销售明细报表
     * @access 产品销售明细报表
     */
    public function actionProductdetail()
    {
        $member = Yii::app()->request->getQuery("member");
        if (empty($member)) {
            $member = 0;
        }

        $form             = new ProductdetailForm;
        $form->attributes = ['member' => $member];
        $data_list        = $this->search($form);
        if ($data_list) {
            $lists = array_map(function ($i) {
                return [$i['orderId'], $i['serialNumber'], $i['singleNumber'], $i['price'], $i['quantity'], $i['total']];
            }, $data_list);
            array_unshift($lists, ['订单号', '产品编号', '颜色', '单价', '销售量', '销售金额']);
            array_unshift($lists, []);
            array_unshift($lists, ['产品：', $form->account, '报表区间:', $form->start . '--' . $form->end, '客户:', Yii::app()->request->getQuery("memberName")]);
            array_push($lists, ['合计', '', '', '', $form->quantitys, $form->prices]);

            $excel = new ExcelFile('月度产品销售明细表');
            $excel->createExl( $lists );
            Yii::app()->end();
        }
    }


    /**
     * 客户交易排行榜
     * @access 客户交易排行榜
     */
    public function actionMembersort()
    {
        $variables = ['data_list' => false];
        $product   = Yii::app()->request->getQuery("product");
        $start     = Yii::app()->request->getQuery("start");
        $end       = Yii::app()->request->getQuery("end");

        if (is_null($start) && is_null($end)) {
            $this->render('membersort', $variables);
            Yii::app()->end();
        }

        if (empty($product)) {
            $product = '';
        }

        $form             = new MembersortForm;
        $form->attributes = ['product' => $product, 'start'=>$start, 'end'=>$end];

        if (!$form->validate()) {
            $this->setError($form->getErrors());
        } else {
            $data_list        = $this->search($form);
	        if ($data_list) {
	        	$lists = [];
	        	foreach($data_list as $index => $item) {
	        		array_push($lists, [$index, $item['memberName'], $item['quantity'], $item['price']]);
	        	}
	            array_unshift($lists, ['名次', '客户名称', '成交量', '交易金额']);
	            array_unshift($lists, []);
	            array_unshift($lists, ['报表区间:', $form->start . '--' . $form->end,'产品：', $product]);
	            array_push($lists, ['结余', '', $form->quantitys, $form->prices]);

	            $excel = new ExcelFile('客户销量排行表');
	            $excel->createExl($lists);
	            Yii::app()->end();
	        }
        }
    }

    /**
     * 产品交易排行榜
     * @access 产品交易排行榜
     */
    public function actionProductsort()
    {
        $variables = ['data_list' => false];
        $product   = Yii::app()->request->getQuery("product");
        $member    = Yii::app()->request->getQuery("member");
        $start     = Yii::app()->request->getQuery("start");
        $end       = Yii::app()->request->getQuery("end");

        if (is_null($start) && is_null($end)) {
            $this->render('productsort', $variables);
            Yii::app()->end();
        }

        if (empty($product)) {
            $product = '';
        }

        if (empty($member)) {
            $member = 0;
        }

        $form             = new ProductsortForm;
        $form->attributes = ['product' => $product, 'member'=>$member, 'start'=>$start, 'end'=>$end];

        if (!$form->validate()) {
            $this->setError($form->getErrors());
        } else {
            $data_list        = $form->findAll();
	        if ($data_list) {
	        	$lists = [
							['报表区间:',$form->start . '--' . $form->end,'产品：', $product, '客户:', Yii::app()->request->getQuery("memberName")],
							[],
							['名次', '产品', '成交量', '交易金额']
						];
				$quantitys = $prices = 0;
	        	foreach($data_list as $index => $item) {
					$quantitys = bcadd( $item['num'],$quantitys,1 );
					$prices = bcadd( $item['total'],$prices,2 );
	        		array_push($lists, [$index+1, $item['serialNumber'], $item['num'], $item['total']]);
	        	}

	            array_push($lists, ['合计', '', $quantitys, $prices]);

	            $excel = new ExcelFile('产品销量排行');
	            $excel->createExl($lists);
	            Yii::app()->end();
	        }
        }
    }





    /**
     * 获取数据 */
    protected function search(CFormModel $form)
    {
        $variables = ['data_list' => false];
        $saleman   = Yii::app()->request->getQuery("saleman");
        $member    = Yii::app()->request->getQuery("member");
        $product   = Yii::app()->request->getQuery("product");
        $start     = Yii::app()->request->getQuery("start");
        $end       = Yii::app()->request->getQuery("end");

        if (is_null($saleman) && is_null($member) && is_null($product) && is_null($start) && is_null($end)) {
            Yii::app()->end();
        }
		
		$attributes = [ 'start' => $start, 'end' => $end,'saleman'=>$saleman,'member'=>$member,'product'=>$product ];
		foreach (  $attributes as $key=>$val ){
			if( empty( $val ) ) unset(  $attributes[$key] );
		}
		
		$form->attributes = $attributes;
        if (!$form->validate()) {
            $this->setError($form->getErrors());
        } else {
			return $data_list = $form->findAll();
/*             $variables['data_list'] = $form->findAll();
            $variables['account']   = $form->account;
            $variables['start']     = $form->start;
            $variables['end']       = $form->end;
            $variables['quantitys'] = $form->quantitys;
            $variables['prices']    = $form->prices; */
        }  
    }

	/**
	* excel 输出样例
	*/
	public function actionTemp(){
		$type   = Yii::app()->request->getQuery("type");
		if( in_array ( $type , array('1','2','3'))){
			$func = 'temp_'.$type;
			$this->$func();
			exit;
		}

		echo '<a href= "/statistic/excel/temp.html?type=1" >temp 1</a><br/>';
		echo '<a href= "/statistic/excel/temp.html?type=2" >temp 2</a><br/>';
		echo '<a href= "/statistic/excel/temp.html?type=3" >temp 3</a><br/>';
	}

	private function temp_1(){
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


	private function temp_2(){
		Yii::$enableIncludePath = false;
		Yii::import('libs.vendors.phpexcel.*');

		$ExcelFile = new ExcelFile( '合并样例' );
		$ExcelFile->setSheetName('测试合并');
		$saveData = array(
						array('仓库：','样板仓','产品：','K000','生成时间：','isTitle'=>true),
						array('颜色编号','仓位','批次','仓库数量','盘点数量','isTitle'=>true),
						array('401','001','B001','100','100'),
						array('401','002','B001','100','100'),
						array('401','003',
							'mergeDetails'=>array( array('B001','100','100'),
													array('B002','101','101'),
													 array('B003','102','102')
						) ),
					);
		$ExcelFile->createMergeExl( $saveData );
	}

	private function temp_3(){
		Yii::$enableIncludePath = false;
		Yii::import('libs.vendors.phpexcel.*');

		$ExcelFile = new ExcelFile( '合并样例' );
		$ExcelFile->setSheetName('测试合并');
		$saveData = array(
						array('仓库：','样板仓','产品：','K000','生成时间：','isTitle'=>true),
						array('颜色编号','仓位','批次','仓库数量','盘点数量','isTitle'=>true),
						array('401','001','B001','100','100'),
						array('401','002','B001','100','100'),
						array('401','003',
							'mergeDetails'=>array( array('B001','100','100'),
													array('B002','101','101'),
													 array('B003','102','102')
						) ),
					);
		$ExcelFile->setData( $saveData );
		$saveData = array(
						array('仓库2：','样板仓2','产品2：','K0002','生成时间2：','isTitle'=>true),
						array('颜色编号2','仓位2','批次2','仓库数量2','盘点数量2','isTitle'=>true),
						array('401','001','B001','100','100'),
						array('401','002','B001','100','100'),
						array('401','003',
							'mergeDetails'=>array( array('B001','100','100'),
													array('B002','101','101'),
													 array('B003','102','102')
						) ),
					);
		$ExcelFile->addSheet('test1', $saveData);

		$saveData = array(
						array('仓库3：','样板仓3','产品3：','K0002','生成时间3：','isTitle'=>true),
						array('颜色编号3','仓位32','批次3','仓库数量3','盘点数量3','isTitle'=>true),
						array('401','001','B001','100','100'),
						array('401','002','B001','100','100'),
						array('401','003',
							'mergeDetails'=>array( array('B001','100','100'),
													array('B002','101','101'),
													 array('B003','102','102')
						) ),
					);
		$ExcelFile->addSheet('sheet3', $saveData);
		$ExcelFile->outFile();
	}
}
