<?php
/**
 * Created by PhpStorm.
 * User: yagas
 * Date: 2016/3/8
 * Time: 16:12
 */
class InquiryController extends Controller {
    const PAGE_SIZE = 10;

    public function actionIndex() {
        $criteria = new CDbCriteria();
        $criteria->order = "lastTime DESC";
        $criteria->condition = "memberId=:uid";
        $criteria->params = array(':uid'=>$this->memberId);

        $pages = new CPagination();
        $pages->setItemCount(tbInquiry::model()->count($criteria));
        $pages->setPageSize(self::PAGE_SIZE);
        $pages->applyLimit($criteria);

        $messages = tbInquiry::model()->with('product')->findAll($criteria);
        $messages = array_map(function($row){
            return array(
                'productId' => $row->productId,
                'title' => $row->product->title,
                'content' => $row->lastMessage(false)->content,
                'date'  => date('Y-m-d H:i', $row->lastTime),
                'mainPic' => $this->getImageUrl($row->product->mainPic,50)
            );
        }, $messages);

        $this->showJson(true,null,$messages);
    }
}