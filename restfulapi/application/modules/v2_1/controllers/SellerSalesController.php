<?php
class SellerSalesController extends Controllerv1 {

  private $limit = 20; //每屏显示数据条数
  private $userInfo;

  public function init() {
    $this->authenticateToken();

    bcscale(1); //保留小数位数
    $this->userInfo = $this->readOpenid();
    if(!$this->userInfo) {
      $this->showJson(false, Yii::t('seller', 'invalied openid'));
    }
  }

  public function actionIndex() {
    $stype = Yii::app()->request->getQuery('stype','current');
    if(in_array($stype,['current','season','year','custom'])) {
      return call_user_func([$this, 'fetch_'.$stype]);
    }
  }

  /**
   * 获取月度数据
   */
  private function fetch_current() {
    $offset           = $this->getRequestParams('offset',0);
    $criteria         = new CDbCriteria();
    $criteria->limit  = $this->limit;
    $criteria->order  = "`datetime`,`id`";
    $criteria->params = [':uid'=>$this->userInfo['supplierId'], ':year'=>date('Y'), ':month'=>date('m'), ':offset'=>$offset];
    $criteria->group  = "serial";
    $criteria->select = "max(id) as id,serial,sum(total) as `total`,subtotal,price,color,datetime,singleNumber";

    $serial = Yii::app()->request->getQuery('serial');
    if($serial) {
      $criteria->addCondition("serial=:serial");
      $criteria->params[':serial'] = $serial;
    }

    $criteria->addCondition("id>:offset");
    $criteria->addCondition("supplierId=:uid");
    $criteria->addCondition("year(datetime)=:year");
    $criteria->addCondition("month(datetime)=:month");

    $result   = tbSellerSales::model()->findAll($criteria);
    $re_start = date('Y-m-01');
    $re_end   = date('Y-m-t');
    $list     = !$serial? $this->formatToList($result, $re_start, $re_end) : $this->formatToDetail($result, $re_start, $re_end);

    $this->showJson(true,"", $list);
  }

  /**
   * 获取季度数据
   */
  private function fetch_season() {
    $dict             = [[1,3], [4,6], [7,9], [10,12]]; //四季月份分布
    $season           = ceil(date('m')/3)-1; //计算当前季度
    $betweed          = $dict[$season];
    $offset           = $this->getRequestParams('offset',0);
    $criteria         = new CDbCriteria();
    $criteria->limit  = $this->limit;
    $criteria->order  = "datetime,id";
    $criteria->params = [':uid'=>$this->userInfo['supplierId'], ':year'=>date('Y'), ':seasonStrat'=>$betweed[0], ':seasonEnd'=>$betweed[1], ':offset'=>$offset];
    $criteria->select = "id,serial,`total`,subtotal,price,color,datetime,singleNumber";
    $isXML            = Yii::app()->request->getQuery('isXML', 'false');

    $serial = Yii::app()->request->getQuery('serial');
    if($serial) {
      $criteria->addCondition("serial=:serial");
      $criteria->params[':serial'] = $serial;
    }
    else {
      $criteria->group  = "serial";
      $criteria->select = "max(id) as id,serial,sum(total) as `total`,subtotal,price,color,datetime,singleNumber";
    }

    $criteria->addCondition("id>:offset");
    $criteria->addCondition("supplierId=:uid");
    $criteria->addCondition("year(datetime)=:year");
    $criteria->addCondition("month(datetime)>=:seasonStrat");
    $criteria->addCondition("month(datetime)<=:seasonEnd");

    $result   = tbSellerSales::model()->findAll($criteria);
    $re_start = date("Y-{$betweed[0]}-01");
    $re_end   = date("Y-{$betweed[1]}-t", strtotime(date("Y-{$betweed[1]}-1")));
    $list     = !$serial? $this->formatToList($result, $re_start, $re_end, $isXML) : $this->formatToDetail($result, $re_start, $re_end);

    $this->showJson(true,"", $list);
  }

  /**
   * 获取年度数据
   */
  private function fetch_year() {
    $offset           = $this->getRequestParams('offset',0);
    $criteria         = new CDbCriteria();
    $criteria->limit  = $this->limit;
    $criteria->order  = "datetime,id";

    $criteria->addCondition("id>:offset");
    $criteria->addCondition("supplierId=:uid");
    $criteria->addCondition("year(datetime)=:year");
    $criteria->params = [':uid'=>$this->userInfo['supplierId'], ':year'=>date('Y'), ':offset'=>$offset];
    $criteria->select = "id,serial,`total`,subtotal,price,color,datetime,singleNumber";

    $serial = Yii::app()->request->getQuery('serial');
    if($serial) {
      $criteria->addCondition("serial=:serial");
      $criteria->params[':serial'] = $serial;
    }
    else {
      $criteria->group  = "serial";
      $criteria->select = "max(id) as id,serial,sum(total) as `total`,subtotal,price,color,datetime,singleNumber";
    }

    $result   = tbSellerSales::model()->findAll($criteria);
    $re_start = date("Y-01-01");
    $re_end   = date("Y-12-t", strtotime(date('Y-12-1')));
    $list     = !$serial? $this->formatToList($result, $re_start, $re_end) : $this->formatToDetail($result, $re_start, $re_end);

    $this->showJson(true,"", $list);
  }

  /**
   * 自定义时间段
   */
  private function fetch_custom() {
    $offset           = $this->getRequestParams('offset',0);
    $serial           = $this->getRequestParams('serial');
    $start            = $this->getRequestParams('start','');
    $end              = $this->getRequestParams('end','');
    $criteria         = new CDbCriteria();
    $criteria->limit  = $this->limit;
    $criteria->order  = "datetime ASC";
    $criteria->params = [':uid'=>$this->userInfo['supplierId'], ':serial'=>$serial, ':offset'=>$offset];

    $criteria->addCondition("id>:offset");
    $criteria->addCondition("supplierId=:uid");
    $criteria->addCondition("serial=:serial");
    if($start && $this->checkDate($start)) {
      $criteria->addCondition("datetime>=:start");
      $criteria->params[':start'] =$start;
    }
    if($end && $this->checkDate($end)) {
      $criteria->addCondition("datetime<=:end");
      $criteria->params[':end'] =$end;
    }

    $result = tbSellerSales::model()->findAll($criteria);
    $output = $this->formatToDetail($result, $start, $end);
    $this->showJson(true, '', $output);
  }



  /**
   * 判断日期格式
   */
  private function checkDate($date) {
    if(!preg_match("/\d{4}-\d{1,2}-\d{1,2}/", $date)) {
      $this->showJson(false, Yii::t('restful','invalid date formater'));
      return false;
    }

    list($year, $month, $day) = explode('-', $date);
    $month = intval($month);
    $day = intval($day);

    if($month<1 || $month>12 || $day<1 || $day>31) {
      $this->showJson(false, Yii::t('restful','invalid date formater'));
      return false;
    }

    return true;
  }

  /**
   * 格式化列表返回数据格式
   */
  private function formatToList($result, $start="", $end="",$isXML='false') {
    if($isXML === 'true') {
      $this->formatToListXML($result, $start, $end);
      return true;
    }

    $list = [];
    foreach($result as $record) {
      $color = explode('-', $record->singleNumber);
      array_push($list, ['id'=>$record->id, 'serial'=>$record->serial, 'num'=>sprintf("%.1f",$record->total), 'price'=>sprintf('%.2f',$record->price), 'subtotal'=>sprintf('%.2f',$record->subtotal), 'color'=>$record->color.$color[1], 'date'=>substr($record->datetime,5)]);
    }
    return ['start'=>$start, 'end'=>$end, "page_size"=>$this->limit, "list"=>$list];
  }

  /**
   * 以XML方式输出列表
   * @param $result
   * @param string $start
   * @param string $end
   */
  private function formatToListXML($result, $start="", $end="") {
    $dom = new DomDocument("1.0","utf-8");
    $root = $dom->createElement('documents');
    $root->appendchild($this->pushFiled($dom, 'state', 'true'));
    $root->appendchild($this->pushFiled($dom, 'start', $start));
    $root->appendchild($this->pushFiled($dom, 'end', $end));

    $datas = $dom->createElement('data');
    foreach($result as $record) {
      $row = $dom->createElement('row');
      $row->appendchild($this->pushFiled($dom, 'id', $record->id));
      $row->appendchild($this->pushFiled($dom, 'serial', $record->serial));
      $row->appendchild($this->pushFiled($dom, 'num', round($record->total,1)));
      $row->appendchild($this->pushFiled($dom, 'price', round($record->price,2)));
      $row->appendchild($this->pushFiled($dom, 'subtotal', round($record->subtotal,2)));
      $row->appendchild($this->pushFiled($dom, 'color', $record->color));
      $row->appendchild($this->pushFiled($dom, 'date', $record->datetime));
      $datas->appendchild($row);
    }
    $root->appendchild($datas);
    $dom->appendchild($root);

    header('Content-Type:application/xml');
    echo $dom->saveXML();
    exit(0);
  }

  private function pushFiled($dom, $key, $value) {
    $keyNode = $dom->createElement($key);
    $valNode = $dom->createTextNode($value);
    $keyNode->appendchild($valNode);
    return $keyNode;
  }

  /**
   * 格式化自定义列表返回数据格式
   */
  private function formatToDetail($result, $start="", $end="") {
    $group = array();
    $groupTotal = array();
    foreach($result as $row) {
      $date = substr($row->datetime,0,7);
      if(!array_key_exists($date, $group)) {
        $group[$date] = array();
        $groupTotal[$date] = 0.0;
      }

      $color = explode('-', $row->singleNumber);
      $line = ['id'=>$row->id, 'serial'=>$row->serial, 'num'=>sprintf("%.1f",$row->total), 'price'=>sprintf('%.2f',$row->price), 'subtotal'=>sprintf("%.2f",$row->subtotal), 'color'=>$row->color.$color[1], 'date'=>substr($row->datetime,5)];
      array_push($group[$date], $line);
      $groupTotal[$date] = bcadd($groupTotal[$date], $row->total);
    }

    $output = array();
    foreach($group as $date => $list) {
      array_push($output, ['date'=>$date, 'list'=>$list, 'subtotal'=>$groupTotal[$date]]);
    }

    return ['start'=>$start, 'end'=>$end, "page_size"=>$this->limit, "list"=>$output];
  }
}
