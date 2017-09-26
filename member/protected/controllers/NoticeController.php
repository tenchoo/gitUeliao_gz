<?php
class NoticeController extends EController {

	/**
	 * 错误信息提示页
	 */
	public function actionError() {
		if($error=Yii::app()->errorHandler->error)
		{
			if(Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else
				$this->render('libs.commons.views.error', $error);
		}
	}
}