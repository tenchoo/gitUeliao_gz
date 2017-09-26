<?php

/**
 * 搜索产品界面
 */
class SearchController extends Controller {

    public $layout = '/layouts/home';

    public function filters() {
        return array();
    }

    /**
     * 图片搜索列表
     * @throws CHttpException
     */
    public function actionImage() {
        $key = Yii::app()->request->getQuery('key');

        $optional = Yii::app()->params["opencv"];
        $opencv = new ispy($optional[0],$optional[1]);
        $result = $opencv->fetch($key);

        if( $result ) {
            $criteria = new CDbCriteria();
            $criteria->addInCondition("uid", $result);

            if(tbOpencvMap::model()->count($criteria)) {
                $criteria->limit = 40;
                $rows = tbOpencvMap::model()->findAll($criteria);
                $productIds = array();
                foreach( $rows as $row ) {
                    array_push($productIds, $row->productId);
                }
                $products = tbProduct::model()->findAllByPk($productIds);
                $this->render("index", array('products'=>$products));
                Yii::app()->end();
            }
        }
        $this->render("index", array('products'=>array()));
    }

    /**
     * Deprecate
     * @throws CHttpException
     */
    public function actionFetchimage() {
        set_time_limit(0);
        $keyImg = Yii::app()->request->getQuery('keyImg');
        if( !is_null($keyImg) ) {

            $optional = Yii::app()->params["opencv"];
            $opencv = new ispy($optional[0],$optional[1]);
            $result = $opencv->fetch($keyImg);

            if( $result ) {
                $criteria = new CDbCriteria();
                $criteria->addInCondition("uid", $result);

                if(tbOpencvMap::model()->count($criteria)) {
                    $criteria->limit = 40;
                    $rows = tbOpencvMap::model()->findAll($criteria);
                    $productIds = array();
                    foreach( $rows as $row ) {
                        array_push($productIds, $row->productId);
                    }
                    $products = tbProduct::model()->findAllByPk($productIds);
                    $this->render("index", array('products'=>$products));
                    Yii::app()->end();
                }
            }
            $this->render("index", array('products'=>array()));
            Yii::app()->end();
        }
        throw new CHttpException(404,"Not found key image");
    }

    /**
     * 将提交的搜索匹配图片存入搜索暂存区
     */
    public function actionUpload() {
        set_time_limit(0);
        $ajax = new AjaxData(false);
        if(Yii::app()->request->getIsPostRequest() && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $fileName = date("YmdHis") . mt_rand(10, 99);
            $upload = CUploadedFile::getInstanceByName("image");
            $imageServer = new searchTmpServer($upload);
            if ($imageServer->save()) {
                $key = $imageServer->getId();
                $ajax->state = true;
                $ajax->data = array('url' => $this->createUrl('image', array('key' => $key)));
            } else {
                $errors = $imageServer->getErrors();
                $error = array_shift($errors);
                $ajax->message = $error[0];
            }
            unlink($upload->tempName);
        }
        else {
            $ajax->message = Yii::t('uploader','Not found upload file');
        }
        echo $ajax->toJson();
    }

    public function actionUpdate() {
        $result = tbProduct::model()->findAll();
        if( $result ) {
            foreach( $result as $item ) {
                $this->__save($item->productId, $item->mainPic);
            }
        }

        $result = tbProductDetail::model()->findAll();
        if( $result ) {
            foreach( $result as $item ) {                
                $pics = json_decode($item->pictures);
                if($pics) {
                    foreach($pics as $pic) {
                        $this->__save($item->productId, $pic);
                    }
                }
                
            }
        }

        echo "数据更新完成";
    }

    /**
     * 获取产品信息
     * @param $url
     * @return array
     */
    private function __fetchProduct( $url ) {
        $productIds = array();
        $optional = Yii::app()->params["opencv"];
        $opencv = new ispy($optional[0],$optional[1]);
        $result = $opencv->fetch($url);

        if( $result ) {
            $criteria = new CDbCriteria();
            $criteria->addInCondition("uid", $result);

            if(tbOpencvMap::model()->count($criteria)) {
                $criteria->limit = 40;
                $rows = tbOpencvMap::model()->findAll($criteria);
                foreach( $rows as $row ) {
                    array_push($productIds, $row->productId);
                }
            }
        }
        if(!$productIds)
            return $productIds;

        return tbProduct::model()->findAllByPk($productIds);
    }

    private function __save($productId, $uid) {
        $offset       = strrpos($uid,'/');
        $uid          = substr($uid,$offset+1);
        $p            = new tbOpencvMap();
        $p->productId = $productId;
        $p->uid       = $uid;
        $check        = tbOpencvMap::model()->findByAttributes(array('productId'=>$productId,'uid'=>$uid));
        if( is_null($check) ) {
            return $p->save();
        }
        return true;
    }
}
