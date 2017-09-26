<?php
/**
 *  文件系统接口
 * @author morve
 * @package
 */
class ApiController extends CController
{
	
	/**
	 * 读取图像文件
	 * @param string $id　UID
	 * @param string $type　类型
	 * @param int $width 缩略图大小
	 * @return img　
	 */
	public function actionImg()
	{		
		$id = Yii::app()->request->getQuery('id');
		$type = Yii::app()->request->getQuery('case');
		$width = Yii::app()->request->getQuery('width');		
		$storage = new Storage();
		$attributes = null;
		if($width){
			$attributes = array('width'=>$width);
		}else{
			$attributes = array('thumb'=>0);
		}
		echo $storage->find($id,$type,$attributes);			
		exit;		
	}
	
	
	
	/**
	 * 上传文件
	 * @return json
	 */
	public function actionUpfile(){
		//取上传文件
		
		$type = Yii::app()->request->getPost('case','res');
		$tmpFile   = !empty($_FILES['file'])?CUploadedFile::getInstanceByName('file'):'';		
		$storage = new Storage();
		$ext = $storage->getFileExt($tmpFile->tempName);
		if($ext==''){
			$this->outmsg(Yii::t('msg', 'File Type Error'));			
		}
		$checkext = $storage->checkFile($ext);
		if($checkext == true){
			$type = 'office';
		}
		//取系统配置，如无系统配置，取默认2M
		$fileMaxSize = Yii::app()->params['filemaxsize'];
		$fileMaxSize = empty($fileMaxSize)?"2000000":$fileMaxSize;
		if($tmpFile->size > $fileMaxSize){
			$this->outmsg(Yii::t('msg', 'File Size Limit'));			
		}
		$id = uniqid();	
		$uid = $storage->save($type,$tmpFile->tempName,array('uid'=>$id,'filename'=>$tmpFile->name,'thumb'=>0,'metatype'=>$tmpFile->type,'ext'=>$ext),$ext);
		$this->outmsg('/'.$type.'/'.$uid,true);
		
		
	}
	
// 	/**
// 	 * 删除上传文件 ｛暂停使用｝
// 	 * @return json
// 	 */
// 	public function actionDelete($id){
// 		$type = !empty($_GET['case'])?Yii::app()->request->getParam('case'):'';
// 		if(empty($type)){
// 			$this->outmsg(Yii::t('msg', 'File Type Error'));	
// 		}
// 		$storage = new Storage();
// 		$file = $storage->delete($id,$type);
// 		if($file){
// 			$this->outmsg(Yii::t('msg', 'Success'),true);
// 		}else{
// 			$this->outmsg(Yii::t('msg', 'Error'));
// 		}
// 		echo $str;
// 		exit;
// 	}
	
	/**
	 * 输入提示信息
	 * @param string $data  信息
	 * @param unknown_type $state 状态  默认是错误
	 */
	public function outmsg( $data, $state=false){
		$json = new AjaxData($state,null,$data);
		echo $json->toJson();
		Yii::app()->end();
	}
	
	public function actionError() {
		header("Content-Type:text/html;charset=utf-8");
		if( $error = Yii::app()->errorHandler->error ) {
			
		}
	}
	
}

