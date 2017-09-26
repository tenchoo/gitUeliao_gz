<?php
/**
 * 询盘管理
 * @access 网站询盘管理
 * @package Controller
 * @since 0.1
 * @version 0.1
 * User: yagas
 * Date: 2016/2/29
 * Time: 15:59
 */

class DefaultController extends Controller {

	/**
	 * 询盘列表
	 * @access 询盘列表
	 */
    public function actionIndex() {
    	$criteria = new CDbCriteria();
    	$criteria->order = "hasNew DESC, lastTime DESC";

    	$pages = new CPagination();
    	$pages->setItemCount( tbInquiry::model()->count($criteria));
    	$pages->setPageSize(tbConfig::model()->get('page_size'));
    	$pages->applyLimit($criteria);

		$dataList = tbInquiry::model()->with('member','product')->findAll($criteria);
		$this->render('index', ['dataList'=>$dataList, 'pages'=>$pages]);
    }

    /**
     * 询盘查看
     * @access 询盘查看
     */
    public function actionView() {
    	$id = Yii::app()->request->getParam("id");

    	$room = tbInquiry::model()->with('member','product')->find("id=:id",[':id'=>$id]);
    	if(!$room) {
    		throw new CHttpException(404,"the require product has not exists.");
    	}

		$criteria = new CDbCriteria();
    	$criteria->condition = 'inquiryId=:id';
    	$criteria->params = array(':id'=>$room->inquiryId);
    	$criteria->order = "createTime DESC";

		$pages = new CPagination();
    	$pages->setItemCount( tbInquiryContent::model()->count($criteria));
    	$pages->setPageSize( tbConfig::model()->get('page_size') );
    	$pages->applyLimit($criteria);

    	$messages = tbInquiryContent::model()->findAll($criteria);
		krsort ($messages);//倒过来显示
		$this->render('view', ['dataList'=>$messages,'room'=>$room, 'pages'=>$pages]);
    }

    /**
     * 回复询盘
     * @access 回复询盘
     */
    public function actionReply() {
		$form = Yii::app()->request->getPost('form');
		$inquiry = tbInquiry::model()->findByAttributes(['inquiryId'=>$form['id']]);
		if($inquiry) {
			$content = new tbInquiryContent();
			$content->productId = $inquiry->productId;
			$content->memberId  = Yii::app()->user->id;
			$content->mark = 'custom_service';
			$content->userId = $inquiry->memberId;
			$content->mime = 'message';
			$content->content = $form['content'];

			if($content->save()) {
				$inquiry->hasNew = 0;
				$inquiry->lastTime = time();
				$inquiry->save();

				Yii::app()->session->add('alertSuccess', true);
				$this->redirect($this->createUrl('index'));
			}

			$this->setError(array('message'=>Yii::t('base','failed inquiry save')));
			$this->redirect($this->createUrl('index'));
		}
		else {
			$this->setError(array('message'=>Yii::t('order','Not found record')));
			$this->redirect($this->createUrl('index'));
		}
    }

    /**
     * 提取mongoDB中的资源文件
	 * @access 提取内容多媒体文件
     */
    public function actionSource() {
    	$id = Yii::app()->request->getQuery('id');
    
		if($id) {
            list($type,$bin,$id) = explode('::',$id);
            if($bin==='bin') {
                $instance = Yii::app()->mongoDB->collection("im_".$type);
                $row = $instance->findOne(array('_id'=>new MongoDB\BSON\ObjectID($id)));

                if(!isset($row->type) || preg_match("/^image/",$row->type))
                    $row->type='image/jpeg';

                $ext      = $row->ext;
                $filename = $id.'.'.$ext;
                $length   = $row->length;
                $type     = $row->type;
                $bin      = $row->data->getData();
            }

            header('Content-type:'.$type);
    
        if(!preg_match("/^image/", $type)) {
            header('Content-Disposition: attachment; filename="'.$filename.'"');
            header("Accept-Ranges:bytes");
            header("Accept-Length:".$length);
        }
            echo $bin;
        }
    }

	/**
	 * 删除询盘消息
	 * @access 删除询盘消息
	 * @throws CDbException
	 */
/* 	public function actionDelete() {
		$id = Yii::app()->request->getQuery('id');
		$message = tbInquiryContent::model()->findByPk($id);
		if($message instanceof tbInquiryContent) {
			if($message->delete()) {
				Yii::app()->session->add('alertSuccess',true);
			}
		}
		$this->setError(array('message'=>Yii::t('base','Not found record or not delete')));
		$this->redirect(Yii::app()->request->getUrlReferrer());
	} */


	/**
	 * @throws CHttpException
	 * @access 编辑回复内容
	 */
	public function actionEditor() {
		$id = Yii::app()->request->getParam("id");
		$cid = Yii::app()->request->getParam("cid");

		$room = tbInquiry::model()->with('member','product')->find("id=:id",[':id'=>$id]);
		if($room) {
			$criteria = new CDbCriteria();
			$criteria->condition = 'inquiryId=:id';
			$criteria->params = array(':id'=>$room->inquiryId);
			$criteria->order = "createTime ASC";
		}
		else {
			throw new CHttpException(404,'not found page');
		}

		$message = tbInquiryContent::model()->findByPk($cid);

		if(Yii::app()->request->isPostRequest) {
			$message = tbInquiryContent::model()->findByPk($cid);

			if($message instanceof tbInquiryContent) {
				$message->content = Yii::app()->request->getPost('content');
				if($message->save()) {
					Yii::app()->session->add('alertSuccess',true);
					$this->redirect($this->createUrl('index'));
				}
			}
			$this->setError(array('message'=>Yii::t('base','Fatal')));
			$this->redirect(Yii::app()->request->getUrlReferrer());
		}

		$this->render('editor', ['dataList'=>$message,'room'=>$room,'cid'=>$cid]);
	}

	public function getImageUrl($url) {
		return CHtml::image($this->showImage($url),'icon',['width'=>50,'height'=>50]);
	}
}