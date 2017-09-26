<?php

class DefaultController extends CController
{
	public $layout  = "//layouts/home";	
	public $pageKeyword = '';
	public $pageDesc = '';
	//导航焦点
	public $active_cur = '';
	
	public function actionIndex()
	{
		
		if(isset($_POST['yt0'])){		
			
			$image = new Storage();
			$tmpFile   = !empty($_FILES['file'])?CUploadedFile::getInstanceByName('file'):'';
			$image->save('I',$tmpFile->tempName,array('uid'=>uniqid(),'thumb'=>0,'metatype'=>$tmpFile->type));
			
			
			
		}	
		$this->render('index');
	}
}