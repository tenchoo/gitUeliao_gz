DROP TABLE IF EXISTS `db_delivery_area`;
CREATE TABLE `db_delivery_area` (
  `areaId` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(20) NOT NULL,
  PRIMARY KEY (`areaId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='�ͻ��Զ���Ƭ����' AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `db_delivery_man`;
CREATE TABLE `db_delivery_man` (
  `deliverymanId` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(20) NOT NULL,
  PRIMARY KEY (`deliverymanId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='�ͻ�Ա' AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `db_delivery_order`;
CREATE TABLE `db_delivery_order` (
    `id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `orderId` varchar(20) NOT NULL,
  `areaId` smallint(5) unsigned NOT NULL,
  `state` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `isDel` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `num` int(10) unsigned NOT NULL COMMENT '����',
  `deliverymanId` smallint(5) unsigned NOT NULL,
  `appointment` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deliveryTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `areaTitle` varchar(20) NOT NULL,
  `deliverymanTitle` varchar(20) NOT NULL,
  `name` varchar(20) NOT NULL COMMENT '�ջ���',
  `phone` varchar(20) NOT NULL COMMENT '�ֻ�����',
  `title` varchar(255) NOT NULL COMMENT '��Ʒ����',
  `orderAddress` varchar(255) NOT NULL COMMENT '������ַ',
  `deliveryAddress` varchar(255) NOT NULL COMMENT '�ͻ���ַ',
  `remark` varchar(255) NOT NULL COMMENT '�ͻ�����',
  `shopRemark` varchar(255) NOT NULL COMMENT '�̼ұ�ע',
  PRIMARY KEY (`id`),
  KEY `orderId` (`orderId`)
) ENGINE=INNODB  DEFAULT CHARSET=utf8 COMMENT='�ͻ�Ա' AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `db_delivery_order_op`;
CREATE TABLE `db_delivery_order_op` (
  `id` INT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `deliveryOrderId` INT(10) UNSIGNED NOT NULL,
  `state` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  `deliverymanId` SMALLINT(5) UNSIGNED NOT NULL ,
  `opTime`  TIMESTAMP NOT NULL DEFAULT  CURRENT_TIMESTAMP  COMMENT 'ʱ��',
  `remark`  VARCHAR(255) NOT NULL COMMENT '��ע',
  PRIMARY KEY (`id`),
  KEY `deliveryOrderId` (`deliveryOrderId`)
) ENGINE=INNODB  DEFAULT CHARSET=utf8 COMMENT='�ͻ���ע' AUTO_INCREMENT=1 ;


ALTER TABLE `db_delivery_order_1` ADD `createTime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `appointment` ,
ADD `deliveryTime` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `createTime` ;