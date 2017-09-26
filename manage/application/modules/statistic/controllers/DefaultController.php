<?php
/**
 * 数据统计默认页面
 * @access 数据统计
 * @author yagas
 * @package Controller
 */
class DefaultController extends Controller
{

    /**
     * 业务员销售统计报表
     * @access 业务员销售统计报表
     */
    public function actionIndex()
    {
        $member  = Yii::app()->request->getQuery("member");
        $product = Yii::app()->request->getQuery("product");

        if (empty($member)) {
            $member = 0;
        }

        if (empty($product)) {
            $product = '';
        }

        $form             = new SalemanForm;
        $form->attributes = ['member' => $member, 'product' => $product];
        $this->search($form, 'index');
    }

    /**
     * 业务员销售明细报表
     * @access 业务员销售明细报表
     */
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
        $this->search($form, 'saleman_detail');
    }

    /**
     * 业务员销售按天统计
     */
    public function actionSalemandays() {
    	$member  = Yii::app()->request->getQuery("member");
    	$product = Yii::app()->request->getQuery("product");

    	if (empty($member)) {
    		$member = 0;
    	}

    	if (empty($product)) {
    		$product = '';
    	}

    	$form             = new SalemanForm('days');
    	$form->attributes = ['member' => $member, 'product' => $product];
    	$this->search($form, 'saleman_day');
    }

    /**
     * 业务员销售按天统计详情
     */
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
    	$this->search($form, 'saleman_detail_days');
    }

    /**
     * 客户购买统计报表
     * @access 客户购买统计报表
     */
    public function actionMember()
    {
        $product = Yii::app()->request->getQuery("product");

        if (empty($product)) {
            $product = '';
        }

        $form             = new MemberForm;
        $form->attributes = ['product' => $product];
        $this->search($form, 'member');
    }

    /**
     * 客户购买明细报表
     * @access 客户购买明细报表
     */
    public function actionMemberdetail()
    {
        $product = Yii::app()->request->getQuery("product");

        if (empty($product)) {
            $product = '';
        }

        $form             = new MemberdetailForm;
        $form->attributes = ['product' => $product];
        $this->search($form, 'member_detail');
    }

    /**
     * 产品销售统计报表
     * @access 产品销售统计报表
     */
    public function actionProduct()
    {
        $member = Yii::app()->request->getQuery("member");
        if (empty($member)) {
            $member = 0;
        }

        $form             = new ProductForm;
        $form->attributes = ['member' => $member];
        $this->search($form, 'product');
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
        $this->search($form, 'product_detail');
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
            $variables['data_list'] = $form->findAll();
            $variables['start']     = $form->start;
            $variables['end']       = $form->end;
            $variables['quantitys'] = $form->quantitys;
            $variables['prices']    = $form->prices;
        }

        $this->render('membersort', $variables);
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
            $variables['data_list'] = $form->findAll();
            $variables['start']     = $form->start;
            $variables['end']       = $form->end;
            $variables['quantitys'] = $form->quantitys;
            $variables['prices']    = $form->prices;
        }

        $this->render('productsort', $variables);
    }

    /**
     * 执行统计数据查询操作 */
    protected function search(CFormModel $form, $view)
    {
        $variables = ['data_list' => false];
        $saleman   = Yii::app()->request->getQuery("saleman");
        $member    = Yii::app()->request->getQuery("member");
        $product   = Yii::app()->request->getQuery("product");
        $start     = Yii::app()->request->getQuery("start");
        $end       = Yii::app()->request->getQuery("end");

        if (is_null($saleman) && is_null($member) && is_null($product) && is_null($start) && is_null($end)) {
            $this->render($view, $variables);
            Yii::app()->end();
        }

        $attributes = [ 'start' => $start, 'end' => $end,'saleman'=>$saleman,'member'=>$member,'product'=>$product ];
		foreach (  $attributes as $key=>$val ){
			if( empty( $val ) ) unset(  $attributes[$key] );
		}

  /*       if ($form instanceof SalemanForm || $form instanceof SalemandetailForm) {
            $attributes[] = $saleman;
        }

        if ($form instanceof MemberForm || $form instanceof MemberdetailForm) {
            $attributes['saleman'] = $member;
        }

        if ($form instanceof ProductForm || $form instanceof ProductdetailForm) {
            $attributes['saleman'] = $product;
        } */

        $form->attributes = $attributes;
        if (!$form->validate()) {
            $this->setError($form->getErrors());
        } else {
            $variables['data_list'] = $form->findAll();
            $variables['account']   = $form->account;
            $variables['start']     = $form->start;
            $variables['end']       = $form->end;
            $variables['quantitys'] = $form->quantitys;
            $variables['prices']    = $form->prices;
        }

        $this->render($view, $variables);
    }

}
