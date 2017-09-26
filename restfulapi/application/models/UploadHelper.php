<?php

class UploadHelper {

	/**
	 * 取文件扩展名
	 * @throws CHttpException
	 * @return string　扩展名
	 */
	public static function fileMimeInfo(CUploadedFile $cuf){

		$buff           = $ext = '';
		$stdClass       = new stdClass;
		$stdClass->ext  = null;
		$stdClass->mime = null;

		if(!file_exists($cuf->getTempName())) {
			throw new CHttpException(500, Yii::t("chinese","Not found file"));
		}

		$fp = fopen($cuf->getTempName(), 'rb');
		if (!$fp){
			throw new CHttpException(500, Yii::t("chinese","Faild open file"));
		}

		$buff = fread($fp, 0x400); // 读取前 1024 个字节
		fclose($fp);

		if ($ext == '' && strlen($buff) >= 2 ){

			if (substr($buff, 0, 4) == 'MThd' ){
				$stdClass->ext = 'mid';
				$stdClass->mime = "audio/midi";
			}
			elseif (substr($buff, 0, 4) == 'RIFF'){
				$stdClass->ext = 'wav';
				$stdClass->mime = "audio/x-wav";
			}
			elseif (substr($buff ,0, 3) == "\xFF\xD8\xFF"){
				$stdClass->ext = 'jpg';
				$stdClass->mime = "image/jpeg";
			}
			elseif (substr($buff ,0, 4) == 'GIF8'){
				$stdClass->ext = 'gif';
				$stdClass->mime = "image/gif";
			}
			elseif (substr($buff ,0, 8) == "\x89\x50\x4E\x47\x0D\x0A\x1A\x0A"){
				$stdClass->ext = 'png';
				$stdClass->mime = "image/png";
			}
			elseif (substr($buff ,0, 2) == 'BM'){
				$stdClass->ext = 'bmp';
				$stdClass->mime = "image/bmp";
			}
			elseif ((substr($buff ,0, 3) == 'CWS' || substr($buff ,0, 3) == 'FWS')
			){
				$stdClass->ext = 'swf';
				$stdClass->mime = "application/x-shockwave-flash";
			}
			elseif (substr($buff ,0, 4) == "\xD0\xCF\x11\xE0"){   // D0CF11E == DOCFILE == Microsoft Office Document
				if (substr($buff,0x200,4) == "\xEC\xA5\xC1\x00"){
					$stdClass->ext = 'doc';
					$stdClass->mime = "application/msword";
				}
				elseif (substr($buff,0x200,2) == "\x09\x08"){
					$stdClass->ext = 'xls';
					$stdClass->mime = "application/vnd.ms-excel";
				}
				elseif (substr($buff,0x200,4) == "\xFD\xFF\xFF\xFF"){
					$stdClass->ext = 'ppt';
					$stdClass->mime = "application/vnd.ms-powerpoint";
				}
			}
			elseif (substr($buff ,0, 4) == "PK\x03\x04"){
				$stdClass->ext = 'zip';
				$stdClass->mime = "application/zip";
			}
			elseif (substr($buff ,0, 4) == 'Rar!'){
				$stdClass->ext = 'rar';
				$stdClass->mime = "application/octet-stream";
			}
			elseif (substr($buff ,0, 4) == "\x25PDF"){
				$stdClass->ext = 'pdf';
				$stdClass->mime = "application/pdf";
			}
			elseif (substr($buff ,0, 3) == "\x30\x82\x0A"){
				$stdClass->ext = 'cert';
				$stdClass->mime = "application/octet-stream";
			}
			elseif (substr($buff ,0, 4) == 'ITSF'){
				$stdClass->ext = 'chm';
				$stdClass->mime = "aapplication/octet-stream";
			}
			elseif(substr($buff, 0, 4) == "\x23\x21\x41\x4D") {
				$stdClass->ext = 'amr';
				$stdClass->mime = "audio/amr";
			}

		}
		return $stdClass;
	}

	/**
	 * 生成缩略图触发器
	 * 触发器只对图片生成缩略图
	 */
	public static function createThumb(CEvent $sender) {
		$thumbs = $sender->sender->thumbs;
		if(!in_array($sender->sender->ext, ['jpg','png','bmp'])) {
			return false;
		}

		$im = imagecreatefromstring($sender->sender->data->getData());
		foreach($thumbs as $item) {
			$thumb = new ResourceBin($sender->sender->collection);
			$thumb->md5 = $sender->sender->md5;
			$thumb->uid = $sender->sender->uid;
			$thumb->datetime = $sender->sender->datetime;
			$thumb->createThumb($im, $item, $item);
			$thumb->save();
		}
		imagedestroy($im);
		return true;
	}
}
