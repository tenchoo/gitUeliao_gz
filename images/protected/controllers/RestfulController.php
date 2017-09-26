<?php
class RestfulController extends CController {

	public function actionCreate() {
		if(false === Yii::app()->request->getIsPostRequest()) {
			throw new CHttpException(404, "not found page");
		}

		$type     = Yii::app()->request->getPost('case');
		$map      = Yii::app()->params["collection_map"];

		if(!$map) {
			Yii::log("not ready collection_map", CLogger::LEVEL_ERROR, __CLASS__.'::actionCreater');
			throw new CHttpException(404, "Not found page");
		}

		if(!array_key_exists($type, $map)) {
			Yii::log("invald upload type:{$type}", CLogger::LEVEL_ERROR, __CLASS__.'::actionCreater');
			throw new CHttpException(404, "Not found page");
		}

		$config = $map[$type];

		$tmpFile  = CUploadedFile::getInstanceByName('file');
		if(is_null($tmpFile)) {
			$ajax = new AjaxData(false, Yii::t("chinese","Not found upload file"));
			echo $ajax->toJson();
			Yii::app()->end();
		}

		$mimeInfo = UploadHelper::fileMimeInfo($tmpFile);
		if(!in_array($mimeInfo->ext, $config['allow'])) {
			$ajax = new AjaxData(false, Yii::t("chinese","Not allow"));
			echo $ajax->toJson();
			Yii::app()->end();
		}

		$record = new ResourceBin(Yii::app()->mongoDB->collection($config['collection']));
		$record->setData($tmpFile->getTempName());
		$record->mime = $mimeInfo->mime;
		$record->ext  = $mimeInfo->ext;

		if(array_key_exists('thumb', $config)) {
			$record->thumbs = $config['thumb'];
			$record->attachEventHandler('onAfterSave', ['UploadHelper','createThumb']);
		}

		if($record->save()) {
			$ajax = new AjaxData(true, Yii::t("chinese","upload file successfully"), "/{$type}/".$record->uid);
			echo $ajax->toJson();
			Yii::app()->end();
		}
		else {
			$ajax = new AjaxData(false, Yii::t("chinese","faild upload, try again"));
			echo $ajax->toJson();
			Yii::app()->end();
		}
	}

	/**
	 * 获取资源接口
	 * 请求方式: GET
	 */
	public function actionIndex() {
		$type  = Yii::app()->request->getQuery('resource');
		$key   = Yii::app()->request->getQuery('key');
		$thumb = Yii::app()->request->getQuery('thumb');
		$map   = Yii::app()->params["collection_map"];

		if(!$map) {
			Yii::log("not ready collection_map", CLogger::LEVEL_ERROR, __CLASS__.'::actionIndex');
			throw new CHttpException(404, "Not found page");
		}

		if(!array_key_exists($type, $map)) {
			Yii::log("invald upload type:{$type}", CLogger::LEVEL_ERROR, __CLASS__.'::actionIndex');
			throw new CHttpException(404, "Not found page");
		}

		$config = $map[$type];

		$mongodb = Yii::app()->mongoDB->collection($config['collection']);
		if(is_null($thumb)) {
			$record  = $mongodb->findOne(['uid'=>$key]);
		}
		else {
			if(!in_array($thumb, $config['thumb'])) {
				Yii::log("invald thumb size:{$thumb}", CLogger::LEVEL_ERROR, __CLASS__.'::actionIndex');
				throw new CHttpException(404, "Not found page");
			}
			else {
				$record  = $mongodb->findOne(['uid'=>$key, 'width'=>$thumb, 'thumb'=>1]);
			}
		}

		if(is_null($record)) {
			throw new CHttpException(404, "not found page");
		}

		//为兼容老格式数据做判断
		if(!property_exists($record, "mime")) {
			$record->mime = $record->metatype;
		}

		header("Content-Type:".$record->mime);
		if ($_SERVER["HTTP_IF_NONE_MATCH"] == $record->md5)
		{
		    header('Etag:'.$record->md5,true,304);
		    exit();
		}
		else {
		    header('Etag:'.$record->md5);
		}
		
		if(!preg_match("/^image/", $record->mime)) {
			header("Content-type: application/octet-stream;charset=gbk");
	        header("Accept-Ranges: bytes");
	        header("Accept-Length: ".$record->size);
	        header("Content-Disposition: attachment; filename=".$record->uid.".".$record->ext);
		}

		echo $record->data->getData();
	}

	/**
	 * 显示错误页面
	 */
	public function actionError() {
		if($error=Yii::app()->errorHandler->error) {
			if(Yii::app()->request->isAjaxRequest) {
				$ajax = new AjaxData(false, Yii::t("chinese",$error['message']));
				echo $ajax->toJson();
				Yii::app()->end();
			}
			else {
				$this->render('libs.commons.views.error', $error);
			}
		}
	}
}
