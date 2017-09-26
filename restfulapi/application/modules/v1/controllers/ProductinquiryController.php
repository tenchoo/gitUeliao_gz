<?php
/**
 * 询盘
 * @author liang
 * @version 0.1
 * @package Controller
 */
class ProductinquiryController extends Controller {

	const PAGE_SIZE = 10;

	public function init() {
		parent::init();
	}


	public function actionIndex(){
		return $this->actionShow();
	}


	/**
	* 询盘--客户提交文件或文字询盘。
	* 用base64 上传语音文件，识别是否语音文件，查找产品信息，
	* 存储语音文件，建立关系表，表中存储产品ID和类目ID，以分配客服。
	* 表中还需记录客户回复信息
	*/
	public function actionCreate(){
		$type      = Yii::app()->request->getPost('mt');
		$productId = Yii::app()->request->getQuery('id');
//		$chatId    = Yii::app()->request->getPost('chatId');

		switch($type) {
			case 'voice':
			case 'image':
				$ext = $type;
				$upload = CUploadedFile::getInstanceByName("content");
				if($upload instanceof CUploadedFile) {
					$size = $upload->getSize();
					$fieds = ['mt'=>$type, 'ext'=>$upload->extensionName, 'createTime'=>time(),'type'=>$upload->getType()];

					//大于16M的文件以GridFS存储
					if($size>16000000) {
						$GridFS = Yii::app()->mongoDB->getmongoDB('resource')->getGridFS($type);
						$result = $GridFS->storeUpload('content', $fieds);
						if($result instanceof MongoId) {
							$content = 'gridfs::'.$result->__toString();
						}
					}
					else {
						//小于16M的文件以二进制直接存储
						$fieds['_id']  = new MongoId();
						$fieds['data'] = new MongoBinData(file_get_contents($upload->tempName), MongoBinData::GENERIC);
						$fieds['length'] = $size;
						$mongoDB = Yii::app()->mongoDB->getmongoDB('resource')->selectCollection($type);
						$result = $mongoDB->save($fieds);
						if(is_null($result['err'])) {
							$content = 'bin::'.$fieds['_id'];
						}
					}
				}
				break;

			case 'message':
				$content = $this->getRequestParams("content");
				$content = $content;
				$ext     = 'message';
				break;

			default:
				throw new CException('Not support message type');
		}

		$t = Yii::app()->db->beginTransaction();
		$chatId             = md5($productId.$this->memberId);
		$chatRoom           = tbInquiry::model()->findByAttributes(['inquiryId'=>$chatId]);

		if(is_null($chatRoom)) {
			$chatRoom = new tbInquiry();
			$chatRoom->inquiryId = $chatId;
			$chatRoom->memberId  = $this->memberId;
			$chatRoom->productId = $productId;
			if(!$chatRoom->save()) {
				$errors = $chatRoom->getErrors();
				$error = array_shift($errors);

				$t->rollback();
				$this->message = Yii::t('restful','Not found chat room');
				$this->state = false;
				$this->showJson();
			}
		}

		$inquiry            = new tbInquiryContent();
		$inquiry->inquiryId = $chatId;
		$inquiry->memberId  = $this->memberId;
		$inquiry->mark      = 'member';
		$inquiry->userId    = $this->memberId;
		$inquiry->mime      = $ext;
		$inquiry->content   = $content;

		if($inquiry->save()) {
			$this->state = true;

			$chatRoom->hasNew = 1;
			$chatRoom->lastTime = $inquiry->createTime;
			$chatRoom->save();
			$t->commit();
		}
		else {
			$this->state = false;
			$errors = $inquiry->getErrors();
			$error  = array_shift($errors);
			$this->message = $error[0];
		}
		$this->showJson();
	}

	public function actionShow() {
		$productId = Yii::app()->request->getQuery('id');

		if(  $this->userType == tbMember::UTYPE_SALEMAN  ){
			$memberId  = Yii::app()->request->getQuery('memberId');
			$mark = 'salesman';
		}else{
			//客户只能读取自己的询盘。
			$memberId  = $this->memberId;
			$mark = 'member';
		}

		if( empty( $productId ) || empty( $memberId ) ){
			$this->message = '询盘已不存在';
			$this->showJson();
		}




		$inquiryId = md5($productId.$memberId);

		$criteria = new CDbCriteria();
		$criteria->compare('inquiryId',$inquiryId);
		$criteria->order = "id DESC,createTime DESC";

		//截点时间戳，取得截点时间之前的数据
		$time  = Yii::app()->request->getQuery('time');
		if( $time>0 && is_numeric( $time ) ){
			$criteria->addCondition(" createTime < '$time)' ");
		}


		//断点标识，必须以orderId排序才能以orderId为断点标识，往上查找，所以搜索条件为小于此orderId
		$nextid   = Yii::app()->request->getQuery('nextid');
		if( $nextid>0 && is_numeric( $nextid )  ){
			$criteria->addCondition(" id < '$nextid' ");
			//加了nexid后要设查询条数
			$criteria->limit = self::PAGE_SIZE;

			$showPage = false;
		}else{
			//一般nextid与page不一起起使用，因APP端未改动，保留page代码。
			$page   = Yii::app()->request->getQuery('page',0);
			$pages = new CPagination();
			$pages->setItemCount(tbInquiryContent::model()->count($criteria));
			$pages->setPageSize(self::PAGE_SIZE);
			$pages->applyLimit($criteria);

			$showPage = true;
		}


		$messages = tbInquiryContent::model()->findAll($criteria);
		$messages = array_map(function($row){
			$data = $row->getAttributes(array('id','mark','userId','mime'));
			$data['createTime'] = date('Y-m-d H:i', $row->createTime);

			$data['content'] = $row->mime!='message'? $row->mime.'::'.$row->content : $row->content;
			$data['icon'] = $row->userIcon();
			return $data;
		}, $messages);

		$end =  end( $messages );
		if ( !empty ( $end ) ){
			$nextid = $end['id'];
		}

		if( $showPage  ){
			$data = ['page'=>$pages->currentPage,'total'=>$pages->itemCount,'totalpage'=>$pages->pageCount,'nextid'=>$nextid,'list'=>$messages];
		}else{
			$data = ['nextid'=>$nextid,'list'=>$messages];
		}

		$data['hasNext'] = ( count( $messages ) < self::PAGE_SIZE )? false :true;
		$this->showJson(true,null,$data);
	}

	public function actionResource() {
		$id = Yii::app()->request->getQuery('id');
		if($id) {
			list($type,$bin,$id) = explode('::',$id);
			if($bin==='bin') {
				$instance = Yii::app()->mongoDB->getmongoDB('resource')->selectCollection($type);
				$row = $instance->findOne(array('_id'=>new MongoId($id)));
				if(!isset($row['type']))
					$row['type']='image/jpeg';

				$ext      = $row['ext'];
				$filename = $id.'.'.$ext;
				$length   = $row['length'];
				$type     = $row['type'];
				$bin      = $row['data']->bin;
			}
			else {
				$instance = Yii::app()->mongoDB->getmongoDB('resource')->getGridFS($type);
				$row = $instance->get(new MongoId($id));

				$ext      = $row->file['ext'];
				$filename = $id.'.'.$ext;
				$length   = $row->file['length'];
				$type     = $row->file['type'];
				$bin      = $row->getBytes();
			}

			header('Content-type:'.$type);
			header('Content-Disposition: attachment; filename="'.$filename.'"');
			header("Accept-Ranges:bytes");
			header("Accept-Length:".$length);
			echo $bin;
		}
	}
}