<?php
/**
 * 图片搜索文件暂存服务
 * User: yagas
 * Date: 2016/3/10
 * Time: 11:40
 */
class searchTmpServer extends CModel{
    private $hostInfo = array('db'=>'leather168','collection'=>'search');
    private $sacle_size = 640;
    private $_uploader;
    private $_id;
    private $_ext;

    private static $_mongoDB;

    public function & getMongoDb() {
        if(!self::$_mongoDB) {
            self::$_mongoDB = Yii::app()->mongoDB->collection("search");
        }
        return self::$_mongoDB;
    }

    public function save() {
        if($this->validate()) {

            $data = array(
                '_id' => new MongoDB\BSON\ObjectID(),
                'createTime' => time(),
                'md5' => md5_file($this->_uploader->tempName)
            );

            //查询文件是否已经被提交
            //重复提交的文件将不被重复写入暂存区
            $find = $this->getMongoDb()->findOne(['md5'=>$data['md5']]);
            if(!$find) {
                $im = $this->_ext === 'jpg'? imagecreatefromjpeg($this->_uploader->tempName) : imagecreatefrompng($this->_uploader->tempName);
                $scale = imagesx($im)>$this->sacle_size? $this->sacle_size : 0;
                $fileName = $this->scale($im, $scale);
                $data['data'] = new MongoDB\BSON\Binary(file_get_contents($fileName), MongoDB\BSON\Binary::TYPE_GENERIC);
                unlink($fileName);

                $result = $this->getMongoDb()->save($data);

                if(is_null($result['err'])) {
                    $this->_id = $data['_id'];
                    return true;
                }
                $this->addError('save', Yii::t('uploader','Failed save to mongoDB'));
            }
            else {
                $this->_id = $find->_id;
                return true;
            }

        }
        return false;
    }

    /**
     * 生成缩略图并返回缩略图的路径
     * @param $im
     * @param $width
     * @return string
     */
    public function scale($im, $width) {
        if($width>0) {
            $x = imagesx($im);
            $y = imagesy($im);
            $xy = $x/$y;
            $newX = $width;
            $newY = $newX / $xy;
            $dsc = imagecreatetruecolor($newX, $newY);
            imagecopyresampled($dsc, $im, 0, 0, 0, 0, $newX, $newY, $x, $y);
        }
        else {
            $dsc = $im;
        }

        $tmpfname = tempnam(Yii::app()->runtimePath, "upload");
        imagejpeg($dsc, $tmpfname, 100);
        return $tmpfname;
    }

    public function __construct(CUploadedFile $uploader) {
        $this->_uploader = $uploader;
    }

    /**
     * 对上传文件进行校验
     * @return bool
     */
    public function validate($attributes = NULL, $clearErrors = true) {
        if(is_null($this->_uploader)) {
            $this->addError('uploader', Yii::t('uploader','Not found upload file'));
            return false;
        }

        //对文件后缀名进行校验
        $exts = array('jpg','png');
        $this->_ext = $this->getFileExt($this->_uploader->tempName);
        if(!in_array($this->_ext, $exts)) {
            $this->addError('ext', Yii::t('uploader','Invalid file mime type'));
            return false;
        }
        return true;
    }

    /**
     * Returns the list of attribute names of the model.
     * @return array list of attribute names.
     */
    public function attributeNames() {
        return array('id'=>'存储主键');
    }

    public function getId() {
        return $this->_id;
    }

    /**
     * 取文件扩展名
     * @param string $fileName 文件路径
     * @throws CHttpException
     * @return string　扩展名
     */
    public function getFileExt($fileName=null){

        $str = $format = '';
        $file = @fopen($fileName, 'rb');
        if ($file){
            $str = @fread($file, 0x400); // 读取前 1024 个字节
            @fclose($file);
        }
        else{
            throw new CHttpException(500,'No file');
        }
        if ($format == '' && strlen($str) >= 2 ){

            if (substr($str, 0, 4) == 'MThd' ){
                $format = 'mid';
            }
            elseif (substr($str, 0, 4) == 'RIFF'){
                $format = 'wav';
            }
            elseif (substr($str ,0, 3) == "\xFF\xD8\xFF"){
                $format = 'jpg';
            }
            elseif (substr($str ,0, 4) == 'GIF8'){
                $format = 'gif';
            }
            elseif (substr($str ,0, 8) == "\x89\x50\x4E\x47\x0D\x0A\x1A\x0A"){
                $format = 'png';
            }
            elseif (substr($str ,0, 2) == 'BM'){
                $format = 'bmp';
            }
            elseif ((substr($str ,0, 3) == 'CWS' || substr($str ,0, 3) == 'FWS')
            ){
                $format = 'swf';
            }
            elseif (substr($str ,0, 4) == "\xD0\xCF\x11\xE0"){   // D0CF11E == DOCFILE == Microsoft Office Document
                if (substr($str,0x200,4) == "\xEC\xA5\xC1\x00"){
                    $format = 'doc';
                }
                elseif (substr($str,0x200,2) == "\x09\x08"){
                    $format = 'xls';
                }
                elseif (substr($str,0x200,4) == "\xFD\xFF\xFF\xFF"){
                    $format = 'ppt';
                }
            }
            elseif (substr($str ,0, 4) == "PK\x03\x04"){
                $format = 'zip';
            }
            elseif (substr($str ,0, 4) == 'Rar!'){
                $format = 'rar';
            }
            elseif (substr($str ,0, 4) == "\x25PDF"){
                $format = 'pdf';
            }
            elseif (substr($str ,0, 3) == "\x30\x82\x0A"){
                $format = 'cert';
            }
            elseif (substr($str ,0, 4) == 'ITSF'){
                $format = 'chm';
            }
            elseif (substr($str ,0, 4) == "\x2ERMF"){
                $format = 'rm';
            }

        }
        return $format;
    }
}