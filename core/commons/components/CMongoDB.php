<?php
class CMongoDB extends CCache {

    public $connectionString;
    public $dbname;
    public $collection;
    public $options = array(
        "ssl"=>false,
        "authMechanism"=>"MONGODB-CR",
        "maxPoolSize"=>256, //最大链接池数量
        "minPoolSize"=>1, //最小链接池数量
        "connectTimeoutMS"=>60000, //最大链接生存时间(毫秒)
        "socketTimeoutMS"=>60000 //最大链接生存时间(毫秒)
    );

    //mongodb connect instance
    private static $_instance;

    private function getCollectionName() {
        return $this->dbname . '.' . $this->collection;
    }

    public function init() {
        parent::init();
        if(!self::$_instance) {
            self::$_instance = $this->createInstance();
        }
    }

    /**
     * 强制重新加载了mongodb对象
     */
    public function reinit() {
        self::$_instance = $this->createInstance();
    }

    /**
     * 创建MongoDB链接
     */
    protected function createInstance($tryNum=10) {
        try {
            $mongodb = new MongoDB\Driver\Manager($this->connectionString, $this->options);
            return $mongodb;
        }
        catch(MongoDB\Driver\Exception\ConnectionTimeoutException $error) {
            $tryNum--;
            if($tryNum<0) {
                return false;
            }

            return $this->createInstance($tryNum);
        }
    }

    /**
     * 选择数据库集合
     */
    public function collection($name) {
        $MongoDB = clone $this;
        $MongoDB->collection = $name;
        $MongoDB->reinit();
        return $MongoDB;
    }

    public function save($data) {
        if(array_key_exists('_id', $data)) {
            return $this->update($data);
        }
        else {
            return $this->insert($data);
        }
    }

    public function insert($data) {
        $item = new MongoDB\Driver\BulkWrite();
        $item->insert($data);
        $result = self::$_instance->executeBulkWrite($this->getCollectionName(), $item);
        return !$result->getWriteErrors()? true : false;
    }

    public function update($data) {
        if(!array_key_exists('_id', $data)) {
            return false;
        }

        $item = new MongoDB\Driver\BulkWrite();
        $item->update(array("_id"=>$data['_id']), array("\$set"=>$data));
        $result = self::$_instance->executeBulkWrite($this->getCollectionName(), $item);
        return !$result->getWriteErrors()? true : false;
    }

    public function delete($filter=array()) {
        $item = new MongoDB\Driver\BulkWrite();
        $item->delete($filter);
        $result = self::$_instance->executeBulkWrite($this->getCollectionName(), $item);
        return !$result->getWriteErrors()? true : false;
    }

    /**
     * 查询记录是否存在
     */
    public function isExsit($field, $value) {
        $result = $this->findOne(array($field=>$value));
        if(is_null($result)) {
            return false;
        }
        return true;
    }

    public function findOne($filter=array()) {
        $query = new MongoDB\Driver\Query($filter,['limit'=>1,'sort'=>['_id'=>1]]);
        $result = self::$_instance->executeQuery($this->getCollectionName(), $query);
        $result = $result->toArray();

        if(!$result) {
            return null;
        }
        return array_shift($result);
    }

    public function find($filter=array(),$options=array()) {
        $query = new MongoDB\Driver\Query($filter, $options);
        $result = self::$_instance->executeQuery($this->getCollectionName(), $query);
        $result = $result->toArray();

        if(!$result) {
            return null;
        }
        return $result;
    }

    public function count($filter=array()) {
        $cmd = new MongoDB\Driver\Command(['count'=>$this->collection,'query'=>$filter]);
        $result = self::$_instance->executeCommand($this->dbname, $cmd);
        return $result->toArray()[0]->n;
    }
}
