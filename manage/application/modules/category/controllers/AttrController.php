<?php
/**
* 属性管理
* @access 属性管理
*/

class AttrController extends Controller {

	/**
	 * 菜单组编号
	 * @var interge
	 */
	public $index = 1;

	/**
	* 属性列表
	* @access 属性列表
	*/
	public function actionIndex(){
		$categoryId = (int)Yii::app()->request->getQuery('categoryId');
		$category =  tbCategory::model()->findByPk( $categoryId );
		if( !$category ) {
			throw new CHttpException(404,"the require category has not exists.");
		}

		//取得属性列表
		$model = new tbAttribute();
		$list = $model->getLists( $categoryId );

		//属性组
		$setgroups = tbSetGroup::model()->getList( 1 );
		$this->render( 'index',array( 'list'=>$list  ,'categoryId'=>$categoryId,'setgroups'=>$setgroups) );
	}

	/**
	* 设置属性,增加/编辑
	* @access 编辑属性
	*/
	public function actionSetattr(){
		$form = Yii::app()->request->getPost('form');

		if( array_key_exists('attributeId', $form ) ){
			$model = null;
			if( is_numeric($form['attributeId']) ) {
				$model = tbAttribute::model()->findByPk( $form['attributeId'] );
			}

			if( !$model ){
				$this->dealMessage('base','The object you requested does not exist');
			}
		}else{
			$model = new tbAttribute();
		}
		$model->attributes = $form;
		if( !array_key_exists('isOther', $form ) ){
			$model->isOther = '0';
		}

		if( !array_key_exists('isSearch', $form ) ){
			$model->isSearch = '0';
		}


		if( $model->save() ){
			$url = $this->createUrl( 'index',array( 'categoryId'=>$form['categoryId'] ) );
			$this->dealSuccess($url);
		}else{
			$errors = $model->getErrors();
			$this->dealError( $errors );
		}
	}


	/**
	* 设置排序
	* @access 属性排序
	* @param integer  $categoryId 分类ID
	* @param integer  $id		 要移动的ID值
	* @param integer  $goto  	移动方向，上升(up)或下降(down)
	*/
	public function actionMove(){
		$categoryId = (int) Yii::app()->request->getPost('categoryId');
		$attributeId = Yii::app()->request->getPost('attributeId');
		$goto = Yii::app()->request->getPost('goto');$goto ='up';
		$model = new tbAttribute();
		$result = $model->orderMove( $categoryId,$attributeId,$goto );
		$this->dealSuccess( $this->createUrl( 'index',array('categoryId'=>$categoryId) ) );
	}



	/**
	* 分类属性表记录标删
	* @access 删除行业分类属性
	*/
	public function actionDelattributes(){
		$categoryId = (int) Yii::app()->request->getPost('categoryId');
		$attributeId = (int) Yii::app()->request->getQuery('attributeId');
		$model = new tbAttribute();
		$result = $model->delAttr( $attributeId );
		$this->dealSuccess( $this->createUrl( 'index',array('categoryId'=>$categoryId) ) );
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

		$result = tbAttribute::model()->extendAllchildren( $categoryId,$extendids );
		if( $result ){
			$this->dealSuccess(  $this->createUrl( 'index',array('categoryId'=>$categoryId) ) );
		}else{
			$this->dealMessage('base','Failed to inherit');
		}
	}
}
