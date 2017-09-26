<?php
/**
 * 会员未读消息队列
 * User: yagas
 * Date: 2016/2/27
 * Time: 16:26
 */
class tbInquiryMember extends CActiveRecord {

    public $id; //消息编号
    public $memebrId; //会员编号

    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{inquiry_member}}';
    }

    public function primaryKey() {
        return 'id';
    }

    public function rules()
    {
        return array(
            ['id,memberId','required'],
            ['id,memberId','numerical','integerOnly'=>true]
        );
    }

    public function fetchAll($memebrId) {
        //锁定表
        $this->getDbConnection()->createCommand("lock tables {$this->tableName()} write")->execute();

        $cmd = $this->getDbConnection()->createCommand("select id from {$this->tableName()} where memberId=:id");
        $cmd->bindValue(':id', $memebrId, PDO::PARAM_INT);
        $ids = $cmd->queryColumn();

        if($ids) {
            //取出未读消息主键后，删除所有未读记录
            $cmd = $this->getDbConnection()->createCommand("delete from {$this->tableName()} where memberId=:id");
            $cmd->bindValue(':id', $memebrId, PDO::PARAM_INT);
//            $cmd->execute();
        }

        //解锁表
        $this->getDbConnection()->createCommand("unlock tables")->execute();

        $criteria = new CDbCriteria();
        $criteria->condition = '1=1';
        $criteria->addInCondition('id', $ids);
        $criteria->order = "id ASC";
        $lists = tbInquiryContent::model()->findAll($criteria);
        $data = [];

        foreach($lists as $item) {
            $room = $item->inquiryId;
            if(!array_key_exists($room, $data)) {
                $data[$room] = array();
            }
            $row = $item->getAttributes(['id','mark','userId','mime','content','createTime']);
            if($row['mime']!=='message') {
                $row['content'] = $row['mime'].'::'.$row['content'];
            }
            array_push($data[$room], $row);
        }
        return $data;
    }
}