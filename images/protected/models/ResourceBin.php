<?php
/**
 * MongoDB数据库资源存储对象
 */
class ResourceBin extends CApplicationComponent {

	private $thumbs = null;
	private $collection;
	private $propertys = [
		'_id' => null,
		'uid' => null,
		'data' => null,
		'md5' => null,
		'userId' => 0,
		'mime' => null,
		'ext' => null,
		'datetime' => null,
		'thumb' => 0,
		'width' => 0,
		'size' => 0
	];

	public function __construct(CMongoDB $collection) {
		$this->_id = new MongoDB\BSON\ObjectID();
		$this->datetime = time();
		$this->uid = uniqid();
		$this->collection = $collection;
	}

	/**
	 * 设置文件md5值以探防重复提交
	 * 设置资源文件二进行值
	 */
	public function setData($filePath) {
		$this->md5  = md5_file($filePath);
		$bin        = file_get_contents($filePath);
		$this->size = strlen($bin);
		$this->setBinData($bin);

	}

	/**
	 * 单独设置资源文件二进行值
	 */
	public function setBinData($bin) {
		$this->propertys['data'] = new MongoDB\BSON\Binary($bin, MongoDB\BSON\Binary::TYPE_GENERIC);
	}

	public function setThumbs($values) {
		$this->thumbs = $values;
	}

	public function getThumbs() {
		return $this->thumbs;
	}

	public function getCollection() {
		return $this->collection;
	}

	public function __set($name, $value) {
		$action = 'set'.ucfirst($name);
		if(method_exists($this, $action)) {
			return call_user_func([$this, $action], $value);
		}

		if(array_key_exists($name, $this->propertys)) {
			$this->propertys[$name] = $value;
			return true;
		}
		return false;
	}

	public function __get($name) {
		$action = 'get'.ucfirst($name);
		if(method_exists($this, $action)) {
			return call_user_func([$this, $action]);
		}

		if(array_key_exists($name, $this->propertys)) {
			return $this->propertys[$name];
		}
		return null;
	}

	public function save() {
		//通过md5判断不对已经上传的文件进行重复存储操作
		$has = $this->collection->findOne(['md5'=>$this->md5]);
		if(!is_null($has)) {
			foreach(array_keys($this->propertys) as $key) {
				$this->propertys[$key] = $has->$key;
			}
			return true;
		}
		
		$result = $this->collection->insert($this->propertys);
		if($result) {
			$this->afterSave();
		}
		return $result;
	}

	public function read($key) {
		$result = $this->collection->findOne(['uid'=>$key]);
		if($result) {
			foreach($result as $key => $val) {
				$this->propertys[$key] = $val;
			}
		}
		return $this;
	}

	/**
	 * 事件触发，可以绑定生成缩略图事件
	 */
	public function afterSave() {
		if($this->hasEventHandler('onAfterSave'))
			$this->onAfterSave(new CEvent($this));
	}

	public function onAfterSave($event) {
		$this->raiseEvent('onAfterSave', $event);
	}

	/**
	 * 生成固定大小的图像并按比例缩放
	 * @param string  $im 图像元数据
	 * @param int $w 最大宽度
	 * @param int $h 最大高度
	 */
	public  function createThumb(&$im,$w,$h){
		if(empty($im) || empty($w) || empty($h) || !is_numeric($w) || !is_numeric($h)){
			throw new Exception("缺少必须的参数");
		}

		list($im_w,$im_h) = array(imagesx($im),imagesy($im)); //获取图像宽高
		if($im_w > $im_h || $w < $h){
			$new_h = intval(($w / $im_w) * $im_h);
			$new_w = $w;
		}else{
			$new_h = $h;
			$new_w = intval(($h / $im_h) * $im_w);
		}

		//开始创建缩放后的图像
		$dst_im = imagecreatetruecolor($new_w,$new_h);
		imagecopyresampled($dst_im,$im,0,0,0,0,$new_w,$new_h,$im_w,$im_h);

		//添加白边
		$final_image = imagecreatetruecolor($w, $h);
		$color = imagecolorallocate($final_image, 255, 255, 255);
		imagefill($final_image, 0, 0, $color);
		$x = round(($w - $new_w) / 2);
		$y = round(($h - $new_h) / 2);
		imagecopy($final_image, $dst_im, $x, $y, 0, 0, $new_w, $new_h);
		imagedestroy($dst_im);

		ob_start();
		imagejpeg($final_image);
		$bin = ob_get_contents();
		ob_end_clean();

		imagedestroy($final_image);

		$this->thumb = 1;
		$this->width = $w;
		$this->mime  = "image/jpeg";
		$this->ext   = "jpg";
		$this->size  = strlen($bin);
		$this->setBinData($bin);
	}
}
