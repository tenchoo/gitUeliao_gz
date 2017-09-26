DROP TABLE IF EXISTS `db_delivery_area`;
CREATE TABLE `db_delivery_area` (
  `areaId` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(20) NOT NULL,
  PRIMARY KEY (`areaId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='送货自定义片区表' AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `db_delivery_man`;
CREATE TABLE `db_delivery_man` (
  `deliverymanId` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(20) NOT NULL,
  PRIMARY KEY (`deliverymanId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='送货员' AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `db_delivery_order`;
CREATE TABLE `db_delivery_order` (
    `id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `orderId` varchar(20) NOT NULL,
  `areaId` smallint(5) unsigned NOT NULL,
  `state` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `isDel` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `num` int(10) unsigned NOT NULL COMMENT '数量',
  `deliverymanId` smallint(5) unsigned NOT NULL,
  `appointment` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deliveryTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `areaTitle` varchar(20) NOT NULL,
  `deliverymanTitle` varchar(20) NOT NULL,
  `name` varchar(20) NOT NULL COMMENT '收货人',
  `phone` varchar(20) NOT NULL COMMENT '手机号码',
  `title` varchar(255) NOT NULL COMMENT '商品标题',
  `orderAddress` varchar(255) NOT NULL COMMENT '订单地址',
  `deliveryAddress` varchar(255) NOT NULL COMMENT '送货地址',
  `remark` varchar(255) NOT NULL COMMENT '客户留言',
  `shopRemark` varchar(255) NOT NULL COMMENT '商家备注',
  PRIMARY KEY (`id`),
  KEY `orderId` (`orderId`)
) ENGINE=INNODB  DEFAULT CHARSET=utf8 COMMENT='送货员' AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `db_delivery_order_op`;
CREATE TABLE `db_delivery_order_op` (
  `id` INT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `deliveryOrderId` INT(10) UNSIGNED NOT NULL,
  `state` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  `deliverymanId` SMALLINT(5) UNSIGNED NOT NULL ,
  `opTime`  TIMESTAMP NOT NULL DEFAULT  CURRENT_TIMESTAMP  COMMENT '时间',
  `remark`  VARCHAR(255) NOT NULL COMMENT '备注',
  PRIMARY KEY (`id`),
  KEY `deliveryOrderId` (`deliveryOrderId`)
) ENGINE=INNODB  DEFAULT CHARSET=utf8 COMMENT='送货备注' AUTO_INCREMENT=1 ;


ALTER TABLE `db_delivery_order_1` ADD `createTime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `appointment` ,
ADD `deliveryTime` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `createTime` ;