<?php
/**
 * 获取发货单产品单品编号列表
 * @author yagas
 * @package CAction
 *
 */
class PostOrderProductSerial extends CAction implements IAction {
	
	public function run() {
		$this->controller->checkAccess( 'default/warehouse/import' );
		
		$result = tbOrderPost2Product::model()->with('details')->findAllByAttributes( array('postId'=>Yii::app()->getRequest()->getQuery('id')) );
		if( !$result ) {
			return new AjaxData(false,'Not found record');
		}

		$data = array();
		foreach( $result as $item ) {
			$key = $item->postProId;
			$data[$key] = $item->details->productCode;
		}

		$ajax = new AjaxData(true,null,array('data'=>$data));
		return $ajax;
	}
}