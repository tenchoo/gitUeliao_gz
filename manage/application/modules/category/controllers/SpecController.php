<?php
/**
* 规格管理
* @access 规格管理
*/

class SpecController extends Controller {

	/**
	 * 菜单组编号
	 * @var interge
	 */
	public $index = 1;

	/**
	* 规格列表
	* @access 规格列表
	*/
	public function actionIndex(){
		$spec = new Spec();
		$list = $spec->getSpecinfo();
		$this->render( 'spec',array( 'list'=>$list ) );
	}

	/**
	* 设置规格,增加/编辑
	* @access 编辑规格
	*/
	public function actionSetspec(){
		$form = Yii::app()->request->getPost('form');

		$model = null;
		if( empty( $form['specId'] ) ) {
			$model = new tbSpec();
		}else{
			if( is_numeric($form['specId']) ) {
				$model = tbSpec::model()->findByPk( $form['specId'] );
			}
		}

		if( !$model ){
			$this->dealMessage('base','The object you requested does not exist');
		}else{
			$model->specName = $form['specName'];
			if( $model->save() ){
				$this->dealSuccess( $this->createUrl('index') );
			}else{
				$this->dealError( $model->getErrors() );
			}
		}
	}


	/**
	* 规格删除,标删
	* @access 删除规格
	*/
	public function actionDel(){
		$specId = (int) Yii::app()->request->getQuery('id');
		$spec = new Spec();
		$result = $spec->specDel( $specId );
		$this->dealSuccess( Yii::app()->request->urlReferrer );
	}

	/**
	* 设置是否有规格图片
	* @access 设置是否有规格图片
	* @param integer $specId
	*/
	private function actionSetspecpicture(){
		$specId = (int) Yii::app()->request->getPost('specId');
		$spec = new Spec();
		$result = $spec->Setspecpicture( $specId );
		$this->dealSuccess( Yii::app()->request->urlReferrer );
	}

	/**
	* 规格值列表
	* @access 规格值列表
	* @param integer $specId 所属的规格ID
	*/
	public function actionValuelist(){
		$specId = (int)Yii::app()->request->getQuery('specId');
		$keyword = Yii::app()->request->getQuery('keyword');

		$spec = new Spec();
		$specData = $spec->getSpedByPk( $specId );
		if( !$specData ){
			throw new CHttpException(404,"the require spec has not exists.");
		}
		$data = $spec->getSpecValuelist( $specId,$keyword );
		$specData = $specData->attributes;
		if( $specData['isColor'] ){
			$specData['colorseries'] = $spec ->getColorSeries();
		}
		$this->render( 'valuelist',array( 'data'=>$data,'specData'=>$specData,'keyword'=>$keyword ) );
	}

	/**
	* 新增编辑规格值
	* @access 编辑规格值
	* @param integer $specvalueId 编辑的值的ID
	* @param integer $specId 新增时所属的规格ID
	*/
	public function actionSetvalue(){
		$specvalueId = (int)Yii::app()->request->getQuery('specvalueId');
		$specId = (int)Yii::app()->request->getQuery('specId');

		$spec = new Spec();
		if( $specvalueId ){
			$specValueModel = $spec->getSpedValueByPk( $specvalueId );
			if( !$specValueModel ){
				throw new CHttpException(404,"the require specvalue has not exists.");
			}
			$specId = $specValueModel->specId;
			$data = $specValueModel->attributes;
		}else{
			$data = '';
			$specValueModel = '';
		}

		$specData = $spec->getSpedByPk( $specId );
		if( !$specData ){
			throw new CHttpException(404,"the require Belong to specifications has not exists.");
		}


		$specForm = Yii::app()->request->getPost('specForm');
		if( $specForm ) {
			if( empty( $specvalueId ) ){
				$specForm['specId'] = $specId;
			}
			$result = $spec->setValue( $specForm ,$specValueModel );
			if( $result=='true' ){
				$this->dealSuccess( $this->createUrl( 'valuelist',array('specId'=>$specId ) ) );
			}else{
				$this->dealError( $result );
				$data = $specForm;
			}
		}
		$data['specName'] = $specData->specName;
		$data['isColor'] = $specData->isColor;

		if( $specData->isColor ){
			$data['colorseries'] = $spec ->getColorSeries();
		}

		$this->render( 'setvalue',array( 'data'=>$data ) );
	}

	/**
	* 删除规格值
	* @access 删除规格值
	* @param array $specvalueIds 所要删除的ID
	*/
	public function actionDelvalue(){
		$specvalueIds = Yii::app()->request->getPost('specvalueIds');
		if(empty($specvalueIds)){
			$specvalueIds = (int) Yii::app()->request->getQuery('id');
		}
		$spec = new Spec();
		$result =  $spec->delValue( $specvalueIds );
		$this->dealSuccess(  Yii::app()->request->urlReferrer );
	}

	/**
	* 设置行业分类规格
	* @access 设置行业分类规格
	*/
	public function actionCategoryspec(){
		$categoryId = (int)Yii::app()->request->getQuery('categoryId');
		$category =  tbCategory::model()->findByPk( $categoryId );
		if( !$category ) {
			throw new CHttpException(404,"the require category has not exists.");
		}

		$spec = new Spec();
		$specForm = Yii::app()->request->getPost('specForm');
		if( $specForm ) {
			$result = $spec->setCategorySpec( $categoryId, $specForm['specId'], $specForm['savetype']);
			if( $result == 'true'){
				$this->dealSuccess( $this->createUrl( 'categoryspec',array('categoryId'=>$categoryId,'flag'=>rand(100,9999)) ) );
			}else{
				$this->dealError( $result );
			}
		}

		$speclist = $spec->getSpecinfo();
		if( $speclist ){
			foreach( $speclist as $key=>$val ){
				if( isset( $val['values'] ) ){
					$speclist[$key]['specValues'] = implode(',',$val['values']);
					unset($speclist[$key]['values']);
				}else{
					$speclist[$key]['specValues'] = '';
				}
			}
		}
		//取得当前分类规格
		$data = $spec->getCategorySpec( $categoryId );
		$this->render( 'categoryspec',array( 'speclist'=>$speclist,'data'=>$data,'categoryId'=>$categoryId ) );
	}

	/**
	* 删除行业分类规格
	* @access 删除行业分类规格
	*/
	public function actionDelcategoryspec(){
		$categoryId = (int)Yii::app()->request->getQuery('categoryId');
		$specId = (int) Yii::app()->request->getQuery('specId');
		$spec = new Spec();
		$result = $spec->delCategorySpec( $categoryId,$specId );
		$this->dealSuccess(  $this->createUrl( 'categoryspec',array('categoryId'=>$categoryId) ) );
	}

	/**
	* 新增行业分类规格
	* @access 新增行业分类规格
	*/
	public function actionAddcategoryspec(){
		$categoryId = (int)Yii::app()->request->getPost('categoryId');
		$specId = (int) Yii::app()->request->getPost('specId');
		$spec = new Spec();
		$result = $spec->addCategorySpec( $categoryId,$specId );
		if( $result == 'true' ){
			$this->dealSuccess(  $this->createUrl( 'categoryspec',array('categoryId'=>$categoryId) ) );
		}else{
			$this->dealError( $result );
		}
	}


	/**
	* 继承到所有子类
	* @access 规格属性继承到子类
	* @param integer $categoryId
	* @param array $extendids
	*/
	public function actionExtend(){
		$categoryId = (int)Yii::app()->request->getPost('categoryId');
		$extendids = Yii::app()->request->getPost('extendids');

		$result = tbCategorySpec::model()->extendAllchildren( $categoryId,$extendids );
		if( $result ){
			$this->dealSuccess(  $this->createUrl( 'categoryspec',array('categoryId'=>$categoryId) ) );
		}else{
			$this->dealMessage('base','Failed to inherit');
		}
	}
}
