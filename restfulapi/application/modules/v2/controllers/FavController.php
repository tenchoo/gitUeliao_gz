<?php
/**
 * 产品收藏
 * @author liang
 * @version 0.1
 * @package Controller
 */
class FavController extends Controller {

	public function init() {
		parent::init();

		if( empty( $this->memberId ) ) {
			$this->message = Yii::t('user','You do not log in or log out');
			$this->showJson();
		}
	}

	/**
	 * 产品收藏列表
	 */
	public function actionIndex(){
		$pageSize = Yii::app()->params['default_page_size'];
		$fav = call_user_func(array('tbProductCollection','getList'),$this->memberId ,$pageSize);
		$data['page'] = $fav['pages']->currentPage+1;
		$data['totalpage'] = ceil($fav['pages']->itemCount/$pageSize);

		foreach ( $fav['list'] as &$val ){
			$val['title'] = '【'.$val['serialNumber'].'】'.$val['title'];
			unset($val['serialNumber']);
			$val['mainPic']  = $this->getImageUrl($val['mainPic'],200 );
			$val['unit'] = tbUnit::getUnitName( $val['unitId'] );
			unset($val['unitId']);
		}
		$data['list'] = $fav['list'];

		$this->data = $data;
		$this->state = true;
		$this->showJson();
	}

	/**
	* 查看是否已收藏
	* @param integer $id 产品ID
	*/
	public function actionShow( $id ){
		$this->state = tbProductCollection::checkCollection( $id,$this->memberId );
		$this->showJson();
	}

	/**
	* 添加收藏
	* @param integer $id 产品ID
	*/
	public function actionCreate(){
		$id = Yii::app()->request->getPost('id');
		$this->state = tbProductCollection::addCollection( $id,$this->memberId );
		$this->showJson();
	}

	/**
	* 取消收藏
	* @param integer $id 产品ID
	*/
	public function actionDelete( $id ){
		$this->state = tbProductCollection::cancleCollection( $id,$this->memberId );
		$this->showJson();
	}

	/**
	* 批量取消收藏
	* @param integer $id 产品ID串
	*/
	public function actionCancle(){
		$id = Yii::app()->request->getPut('ids');
		$this->actionDelete( $id );
	}
}