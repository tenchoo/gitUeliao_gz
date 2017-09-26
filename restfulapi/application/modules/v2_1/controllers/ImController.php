<?php
/**
 * APP消息推送接口
 * User: yagas
 * Date: 2016/5/4
 * Time: 16:01
 */
require_once Yii::getPathOfAlias('system').'/../vendors/getui/IGt.Push.php';

class ImController extends Controller {

    const PAGE_SIZE = 10;

    /**
     * 询盘--客户提交文件或文字询盘。
     * 用base64 上传语音文件，识别是否语音文件，查找产品信息，
     * 存储语音文件，建立关系表，表中存储产品ID和类目ID，以分配客服。
     * 表中还需记录客户回复信息
     */
    public function actionCreate(){
        $mime      = $this->getRequestParams('mt');
        $productId = $this->getRequestParams('id');
        $from      = $this->getRequestParams('from','member');

        if($from==='saleman') {
            $from = 'salesman';
        }

        $userId    = $this->getRequestParams('userId',$this->memberId);
        if($userId==="0") {
            $userId = $this->memberId;
        }

        switch($mime) {
            case 'voice':
            case 'image':
                $ext = $mime;
                $upload = CUploadedFile::getInstanceByName("content");
                $fileMemi = UploadHelper::fileMimeInfo($upload);
                if($upload instanceof CUploadedFile) {
                    $fieds = [
                        'mt' => $mime,
                        'ext' => $fileMemi->ext,
                        'createTime' => time(),
                        'type' => $fileMemi->mime,
                        'length' => $upload->getSize(),
            'data' => file_get_contents($upload->getTempName())
                    ];

                    $record = new AppInquiryResource();
                    $record->setAttributes($fieds);
                    $result = $record->save();
                    if (!$result) {
                        $this->showJson(false, Yii::t('restful', 'Failed to save resource'));
                    }
                    $content = $result;
                }
                else {
                    $this->showJson(false, Yii::t('restful', 'Not found inquiry mutildata'));
                }
                break;

            case 'message':
                $content = $this->getRequestParams("content");
                break;

            default:
                throw new CException('Not support message type');
        }

        $t = Yii::app()->db->beginTransaction();
        $inquiry = new tbInquiryContent($from);
        $inquiry->setAttributes(array(
            'productId' => $productId,
            'memberId'  => $this->memberId,
            'userId'    => $userId,
            'mark'      => $from,
            'mime'      => $mime,
            'content'   => $content
        ));

        if(!$inquiry->save()) { //询盘内容提交失败
            $errors = $inquiry->getErrors();
            $error = array_shift($errors);
            $t->rollback();
            $this->message = Yii::t('restful',$error[0]);
            $this->state = false;
            $this->showJson();
        }

        $t->commit();
        $this->state = true;
        $this->message = null;
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

            $data['content'] = $row->mime!='message'? $row->mime.'::'.$row->content : urldecode($row->content);
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
        // $mongoDB = Yii::app()->mongoDB->getMongoDB();
        if($id) {
            list($type,$bin,$id) = explode('::',$id);
            if($bin==='bin') {
                // $instance = $mongoDB->selectCollection(Yii::app()->mongoDB->dbname, "im_".$type);
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
}

/**
 * Class AppInquiryResource
 * @property string $mt 类型前缀
 * @property string $ext 文件扩展名
 * @property string $filename 文件名
 * @property integer $length 文件大小
 * @property string $type 文件类型
 * @property mixed $bin 文件数据
 * @property integer $createTime 添加时间
 */
class AppInquiryResource extends CModel{

    public $ext;
    public $mt;
    public $length;
    public $type;
    public $data;
    public $createTime;

    public function attributeNames() {
        return ['mt','ext','length','type','data'];
    }

    /**
     * 数据校验规则
     * @return array
     */
    public function rules() {
        return [
            ['ext,length,type,data', 'required'],
            ['createTime','numerical','integerOnly'=>true],
            ['mt,','safe']
        ];
    }

    /**
     * 存储询盘资源数据
     * 文件体积大于16M的数据，以gridfs方式存储
     * 文件体积小于16M的数据，以MongoBinData方式存储
     * @return bool|string|void
     */
    public function save() {
        if(!$this->validate()) {
            return false;
        }

        $fieds = [
            '_id'=> new MongoDB\BSON\ObjectID(),
            'mt'=>$this->mt,
            'ext'=>$this->ext,
            'createTime'=>time(),
            'type'=>$this->type,
            'length'=>$this->length
        ];


        //判断文件体积大小
        if($this->length < 16000000) {
            $collection = Yii::app()->mongoDB->collection("im_".$this->mt);
            $fieds['data'] = new MongoDB\BSON\Binary($this->data, MongoDB\BSON\Binary::TYPE_GENERIC);
            $result = $collection->insert($fieds);
            if(is_null($result['err'])) {
                return 'bin::'.$fieds['_id'];
            }
        }
        return;
    }


    /**
     * 读取询盘资源数据
     * @param $id
     * @return AppInquiryResource
     */
    public function read($id) {
        list($type,$bin,$id) = explode('::',$id);
            $collection = Yii::app()->mongoDB->collection("im_".$type);
            $row = $collection->findOne(array('_id'=>new MongoDB\BSON\ObjectID($id)));

            if(is_null($row)) {
                $this->showJson(flase, "not found resourct");
            }

        $this->bin = $row->data->getData();
        return $this;
    }
}
