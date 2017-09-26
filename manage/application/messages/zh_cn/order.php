<?php
$lang['REQUEST_BUY_NORMAL'] = "待审核";
$lang['REQUEST_BUY_CLOSE'] = "已关闭";
$lang['REQUEST_BUY_WAITING'] = "待采购";
$lang['REQUEST_BUY_CHECHED'] = "已审核";
$lang['REQUEST_BUY_PROCCESSING'] = "采购中";
$lang['REQUEST_BUY_FINISHED'] = "已采购";
$lang['REQUEST_BUY_CHECKED'] = "待采购";

$lang['REQUEST_FORM_COMPANY']   = '内部请购';
$lang['REQUEST_FORM_REPERTORY'] = '低安全库存';
$lang['REQUEST_FORM_ORDER']     = '客户订货';
$lang['ORDER_BUY_NORMAL']       = '生产中';
$lang['ORDER_BUY_FINISHED']     = '已发货';
$lang['ORDER_BUY_CHECKED']      = '审核通过';
$lang['ORDER_BUY_CLOSE']      = '已取消';

$lang['Choose product,please']  = '您还没有添加任何产品';
$lang['Not found record by:{serial}'] = '无法找到编号为[{serial}]的产品';
$lang['faild push order to purchase'] = '推送内部请购单到待采购列表失败了';
$lang['faild change request buy order state'] = '无法改变内部请购单状态';
$lang['Failed to create post order'] = '创建发货单失败';
$lang['Failed to save post-order detail'] = '无法存储订单明细信息';
$lang['Failed to change purchase state'] = '无法变更采购单状态信息';
$lang['assigned'] = '已匹配';
$lang['unassign'] = '未匹配';
$lang['has multiple supplier'] = '警告！您添加了不同供应商进行供货的产品';
$lang['Failed save assign info'] = '保存订单匹配信息失败';
$lang['Failed save post order state'] = '无法更新发货单状态信息';
$lang['Not found post Record'] = '没有找到相关的发货单记录';
$lang['Not found purchase Record'] = '没有找到相关的采购单记录';
$lang['Not found product record'] = '没有找到相关的产品信息';
$lang['Assign quantity overflow'] = '匹配数量超过发货数量，匹配失败';
$lang['Failed save warrant detail information'] = '存储入库单明细信息失败';
$lang['Failed push lock'] = '添加商品锁定量失败';
$lang['Please fill in the audit results'] = '请填写审核反馈信息';
$lang['Not execute method in new record'] = '新记录无法执行该动作';
$lang['Not found record'] = '无法找到指定的记录';
$lang['Not found product color by {serial}'] = '产品{serial}颜色已被删除，无法增加入库';
$lang['Unable to save buy order product'] = '无法保存采购订单明细记录';
$lang['Unable to save buy order'] = '无法保存采购订单记录';
$lang['Verification code is not correct'] = '提货码不正确';
$lang['this order is not need to send sms'] = '当前订单不需要发送';
$lang['delevery code has send'] = '提货码已发送';
$lang['This purchase order has a shipping record, which is not allowed to cancel.'] = '此采购单已有发货记录，不允许取消';
$lang['No purchase of the product information, if you do not need to purchase, please cancel the order directly!'] = '无购买产品信息，若不需购买请直接取消订单!';
$lang['Order form has been issued, can not modify the order information!'] = '订单已开具结算单，不能修改订单信息!';
$lang['Warehouse distribution sorting, can not modify the order information!'] = '仓库已分配分拣，不能修改订单信息!';
$lang['Order form has been issued, can not modify the order information!'] = '订单已结算，不允许再作修改';

$lang['The single already exist, a receipt only into a warehouse'] = '该单已经存在了，一张入库单只能入一个仓库';





return $lang;
