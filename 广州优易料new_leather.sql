/*
Navicat MySQL Data Transfer

Source Server         : mysql.localhost
Source Server Version : 50625
Source Host           : mysql.localhost:3306
Source Database       : new_leather

Target Server Type    : MYSQL
Target Server Version : 50625
File Encoding         : 65001

Date: 2016-11-16 15:15:36
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for db_ad
-- ----------------------------
DROP TABLE IF EXISTS `db_ad`;
CREATE TABLE `db_ad` (
  `adId` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '广告id',
  `adPositionId` int(10) unsigned NOT NULL COMMENT '广告位ID',
  `pageId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '所属页面ID',
  `state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态：0正常，1下架,2已删除',
  `listOrder` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序值,从小到大排序',
  `price` decimal(9,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '广告价格',
  `views` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '浏览数',
  `clickNum` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '总点击数量',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updateTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `startTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '开始时间',
  `endTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '结束时间',
  `priceCycle` varchar(10) NOT NULL COMMENT '价格周期',
  `customerTel` varchar(22) NOT NULL COMMENT '客户手机',
  `customerName` varchar(50) NOT NULL COMMENT '客户姓名',
  `title` varchar(100) NOT NULL COMMENT '广告标题',
  `replaceText` varchar(255) NOT NULL COMMENT '图片替换文本',
  `link` varchar(255) NOT NULL DEFAULT '' COMMENT '链接地址',
  `description` varchar(255) NOT NULL COMMENT '描述',
  `image` varchar(255) NOT NULL COMMENT '图片地址',
  PRIMARY KEY (`adId`),
  KEY `adPositionId` (`adPositionId`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8 COMMENT='广告';

-- ----------------------------
-- Table structure for db_ad_position
-- ----------------------------
DROP TABLE IF EXISTS `db_ad_position`;
CREATE TABLE `db_ad_position` (
  `adPositionId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '类型：0页面，1广告位',
  `state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态：0正常，1删除',
  `parentId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上级分类ID',
  `maxNum` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '最大允许广告数量',
  `height` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '广告位高度',
  `width` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '广告位宽度',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updateTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `title` varchar(100) NOT NULL COMMENT '名称',
  `mark` varchar(30) NOT NULL DEFAULT '' COMMENT '标识，不能重复且必须是英文字符',
  PRIMARY KEY (`adPositionId`),
  KEY `mark` (`mark`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8 COMMENT='广告位';

-- ----------------------------
-- Table structure for db_address
-- ----------------------------
DROP TABLE IF EXISTS `db_address`;
CREATE TABLE `db_address` (
  `addressId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `isDefaultSend` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否默认发货地址',
  `isDefaultReturn` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否默认退货地址',
  `areaId` int(10) unsigned NOT NULL COMMENT '区域ID',
  `phoneNumber` char(11) NOT NULL DEFAULT '' COMMENT '手机号码',
  `contactPerson` varchar(30) NOT NULL COMMENT ' 联系人',
  `zipCode` varchar(6) NOT NULL DEFAULT '' COMMENT '邮政编码',
  `fixedTelephone` varchar(30) NOT NULL DEFAULT '' COMMENT '固定电话',
  `companyName` varchar(255) NOT NULL DEFAULT '' COMMENT '公司名称',
  `address` varchar(255) NOT NULL COMMENT '详细地址',
  PRIMARY KEY (`addressId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商家发货地址';

-- ----------------------------
-- Table structure for db_allocation
-- ----------------------------
DROP TABLE IF EXISTS `db_allocation`;
CREATE TABLE `db_allocation` (
  `allocationId` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '调拨单ID',
  `warehouseId` int(10) unsigned NOT NULL COMMENT '调拨仓库ID',
  `targetWarehouseId` int(10) unsigned NOT NULL COMMENT '目标仓库Id',
  `packingId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '分拣单ID',
  `orderId` bigint(19) unsigned NOT NULL DEFAULT '0' COMMENT '订单编号',
  `userId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '调拨人userId',
  `comfirmUserId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '确认调拨人userId',
  `state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态：0待调拨,1待确认调拨,2调拨完成,10取消调拨',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '调拨类型',
  `isCallback` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否已回调',
  `driverUserId` int(10) NOT NULL DEFAULT '0' COMMENT '驾驶员userId',
  `vehicleId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '车辆编号',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '调拨时间',
  `comfirmTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '确认调拨时间',
  `userName` varchar(30) NOT NULL DEFAULT '' COMMENT '调拨人',
  `driverName` varchar(30) NOT NULL DEFAULT '' COMMENT '驾驶员姓名',
  `plateNumber` varchar(30) NOT NULL COMMENT '车牌号',
  `comfirmUser` varchar(30) NOT NULL DEFAULT '' COMMENT '确认调拨人姓名',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  PRIMARY KEY (`allocationId`),
  KEY `warehouseId` (`warehouseId`)
) ENGINE=InnoDB AUTO_INCREMENT=11063 DEFAULT CHARSET=utf8 COMMENT='调拨单';

-- ----------------------------
-- Table structure for db_allocation_detail
-- ----------------------------
DROP TABLE IF EXISTS `db_allocation_detail`;
CREATE TABLE `db_allocation_detail` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `allocationId` int(10) unsigned NOT NULL COMMENT '调拨单ID',
  `productId` int(10) unsigned NOT NULL COMMENT '产品ID',
  `positionId` int(10) unsigned NOT NULL COMMENT '仓位ID',
  `num` decimal(10,2) unsigned NOT NULL COMMENT '调拨数量',
  `wholes` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '整卷数量',
  `positionTitle` varchar(20) NOT NULL COMMENT '仓位名称',
  `singleNumber` varchar(30) NOT NULL COMMENT '单品编码',
  `productBatch` varchar(30) NOT NULL DEFAULT '' COMMENT '产品批次',
  `color` varchar(20) NOT NULL COMMENT '颜色',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  PRIMARY KEY (`id`),
  KEY `allocationId` (`allocationId`)
) ENGINE=InnoDB AUTO_INCREMENT=13106 DEFAULT CHARSET=utf8 COMMENT='调拨单明细';

-- ----------------------------
-- Table structure for db_app_device
-- ----------------------------
DROP TABLE IF EXISTS `db_app_device`;
CREATE TABLE `db_app_device` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `device` varchar(100) NOT NULL COMMENT '设备号',
  `version` varchar(20) NOT NULL COMMENT 'APP版本号',
  `mobileModel` varchar(50) NOT NULL COMMENT '机型',
  `os` varchar(30) NOT NULL COMMENT '系统版本号',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1466 DEFAULT CHARSET=utf8 COMMENT='收集终端设备信息';

-- ----------------------------
-- Table structure for db_app_version
-- ----------------------------
DROP TABLE IF EXISTS `db_app_version`;
CREATE TABLE `db_app_version` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `version` varchar(20) DEFAULT NULL COMMENT '安卓版本号(整形)',
  `versionStr` varchar(20) DEFAULT NULL COMMENT '更新版本号',
  `url` varchar(200) DEFAULT NULL COMMENT '下载地址',
  `force` tinyint(1) DEFAULT NULL COMMENT '是否强制更新',
  `device` enum('ios','android') DEFAULT NULL COMMENT '设备类型',
  `comment` text COMMENT '更新日志',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='版本更新信息';

-- ----------------------------
-- Table structure for db_area
-- ----------------------------
DROP TABLE IF EXISTS `db_area`;
CREATE TABLE `db_area` (
  `areaId` int(10) unsigned NOT NULL COMMENT '区域编码',
  `parentid` int(10) unsigned NOT NULL COMMENT '上级ID',
  `groups` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '区域1华东2华北3华中4华南5东北6西北7西南8港澳台9海外',
  `level` tinyint(1) unsigned NOT NULL COMMENT '等级',
  `listOrder` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `title` varchar(50) NOT NULL COMMENT '标题',
  `pinyin` varchar(100) NOT NULL COMMENT '区域名称拼音',
  PRIMARY KEY (`areaId`),
  KEY `parentid` (`parentid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='中国省市区地名表';

-- ----------------------------
-- Table structure for db_attr
-- ----------------------------
DROP TABLE IF EXISTS `db_attr`;
CREATE TABLE `db_attr` (
  `attrId` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '属性ID',
  `title` varchar(20) NOT NULL COMMENT '属性标题',
  PRIMARY KEY (`attrId`),
  UNIQUE KEY `title` (`title`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8 COMMENT='属性标题表';

-- ----------------------------
-- Table structure for db_attr_value
-- ----------------------------
DROP TABLE IF EXISTS `db_attr_value`;
CREATE TABLE `db_attr_value` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '属性ID',
  `valueName` varchar(20) NOT NULL COMMENT '属性值',
  PRIMARY KEY (`id`),
  UNIQUE KEY `valueName` (`valueName`)
) ENGINE=InnoDB AUTO_INCREMENT=90 DEFAULT CHARSET=utf8 COMMENT='属性值';

-- ----------------------------
-- Table structure for db_attribute
-- ----------------------------
DROP TABLE IF EXISTS `db_attribute`;
CREATE TABLE `db_attribute` (
  `attributeId` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '属性ID',
  `categoryId` int(10) unsigned NOT NULL COMMENT '分类ID',
  `setGroupId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '属性组ID',
  `isSearch` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否支持搜索0不支持1支持',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '4' COMMENT '输入控件的类型,1单选,2复选,3下拉,4广本框，5文本域',
  `isOther` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否后面加其他',
  `listOrder` mediumint(6) unsigned NOT NULL DEFAULT '100' COMMENT '排序值',
  `state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态：0正常，1删除',
  `attrId` int(10) unsigned NOT NULL COMMENT '属性标题ID',
  `title` varchar(30) NOT NULL DEFAULT '' COMMENT '属性名称',
  `attrValue` varchar(255) NOT NULL,
  PRIMARY KEY (`attributeId`),
  KEY `categoryId` (`categoryId`),
  KEY `attrGroupId` (`setGroupId`)
) ENGINE=InnoDB AUTO_INCREMENT=1165 DEFAULT CHARSET=utf8 COMMENT='属性表';

-- ----------------------------
-- Table structure for db_cart
-- ----------------------------
DROP TABLE IF EXISTS `db_cart`;
CREATE TABLE `db_cart` (
  `cartId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `productId` int(10) unsigned NOT NULL COMMENT '产品ID',
  `tailId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '尾货ID',
  `state` tinyint(1) NOT NULL DEFAULT '0' COMMENT '购物车是否已过期：0正常，1已过期',
  `memberId` int(10) unsigned NOT NULL COMMENT '客户ID',
  `num` decimal(10,2) unsigned NOT NULL COMMENT '购买数量',
  `stockId` int(10) unsigned NOT NULL COMMENT '产品单品ID',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '加入购物车时间',
  `singleNumber` varchar(20) NOT NULL DEFAULT '' COMMENT '单品编码',
  PRIMARY KEY (`cartId`),
  KEY `idx_memberId` (`memberId`)
) ENGINE=InnoDB AUTO_INCREMENT=1489 DEFAULT CHARSET=utf8 COMMENT='购物车';

-- ----------------------------
-- Table structure for db_category
-- ----------------------------
DROP TABLE IF EXISTS `db_category`;
CREATE TABLE `db_category` (
  `categoryId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL COMMENT '分类名称',
  `parentId` int(10) unsigned NOT NULL COMMENT '上级ID',
  `listOrder` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `lft` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '左值',
  `rft` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '右值',
  `seoTitle` varchar(50) NOT NULL DEFAULT '' COMMENT 'seo标题',
  `seoKeywords` varchar(100) NOT NULL DEFAULT '' COMMENT 'seo关键词',
  `seoDesc` varchar(255) NOT NULL DEFAULT '' COMMENT 'seo 描述',
  PRIMARY KEY (`categoryId`)
) ENGINE=InnoDB AUTO_INCREMENT=104 DEFAULT CHARSET=utf8 COMMENT='行业分类表';

-- ----------------------------
-- Table structure for db_category_spec
-- ----------------------------
DROP TABLE IF EXISTS `db_category_spec`;
CREATE TABLE `db_category_spec` (
  `categoryId` int(10) unsigned NOT NULL COMMENT '行业分类ID',
  `specId` int(10) unsigned NOT NULL COMMENT '规格ID',
  PRIMARY KEY (`categoryId`,`specId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='行业分类规格关系表';

-- ----------------------------
-- Table structure for db_code
-- ----------------------------
DROP TABLE IF EXISTS `db_code`;
CREATE TABLE `db_code` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `phone` char(11) NOT NULL,
  `code` char(6) NOT NULL,
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `phone` (`phone`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8 COMMENT='用于手机验证码验证';

-- ----------------------------
-- Table structure for db_config
-- ----------------------------
DROP TABLE IF EXISTS `db_config`;
CREATE TABLE `db_config` (
  `configId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` char(4) NOT NULL DEFAULT '' COMMENT '配置分类',
  `key` varchar(20) NOT NULL COMMENT '变量名',
  `unit` varchar(20) NOT NULL COMMENT '单位',
  `value` varchar(255) NOT NULL COMMENT '变量值',
  `valueType` enum('int','num','str','bool') NOT NULL DEFAULT 'str' COMMENT '值类型',
  `comment` varchar(100) NOT NULL DEFAULT '' COMMENT '注释',
  PRIMARY KEY (`configId`),
  UNIQUE KEY `idx_key` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for db_craft
-- ----------------------------
DROP TABLE IF EXISTS `db_craft`;
CREATE TABLE `db_craft` (
  `craftId` tinyint(3) NOT NULL AUTO_INCREMENT,
  `hasLevel` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否分等级',
  `craftCode` char(3) NOT NULL COMMENT '工艺代表编码',
  `parentCode` varchar(3) NOT NULL COMMENT '上级编码',
  `title` varchar(20) NOT NULL COMMENT '工艺标题',
  `icon` varchar(255) NOT NULL DEFAULT '' COMMENT '图标',
  PRIMARY KEY (`craftId`),
  UNIQUE KEY `code` (`craftCode`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8 COMMENT='工艺配置表';

-- ----------------------------
-- Table structure for db_cs
-- ----------------------------
DROP TABLE IF EXISTS `db_cs`;
CREATE TABLE `db_cs` (
  `csId` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '客服类型：1QQ，2旺旺',
  `state` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否启用',
  `isDefault` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否默认：0否，1是',
  `listOrder` smallint(5) unsigned NOT NULL DEFAULT '100' COMMENT '排序值',
  `csName` varchar(20) NOT NULL COMMENT '客服名称',
  `csAccount` varchar(20) NOT NULL COMMENT '客服账号',
  PRIMARY KEY (`csId`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='客服表';

-- ----------------------------
-- Table structure for db_delivery_area
-- ----------------------------
DROP TABLE IF EXISTS `db_delivery_area`;
CREATE TABLE `db_delivery_area` (
  `areaId` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(20) NOT NULL,
  PRIMARY KEY (`areaId`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8 COMMENT='送货自定义片区表';

-- ----------------------------
-- Table structure for db_delivery_man
-- ----------------------------
DROP TABLE IF EXISTS `db_delivery_man`;
CREATE TABLE `db_delivery_man` (
  `deliverymanId` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(20) NOT NULL,
  PRIMARY KEY (`deliverymanId`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COMMENT='送货员';

-- ----------------------------
-- Table structure for db_delivery_order
-- ----------------------------
DROP TABLE IF EXISTS `db_delivery_order`;
CREATE TABLE `db_delivery_order` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `orderId` varchar(30) NOT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=2248 DEFAULT CHARSET=utf8 COMMENT='送货员';

-- ----------------------------
-- Table structure for db_delivery_order_1
-- ----------------------------
DROP TABLE IF EXISTS `db_delivery_order_1`;
CREATE TABLE `db_delivery_order_1` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `orderId` varchar(30) NOT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=1042 DEFAULT CHARSET=utf8 COMMENT='送货员';

-- ----------------------------
-- Table structure for db_delivery_order_op
-- ----------------------------
DROP TABLE IF EXISTS `db_delivery_order_op`;
CREATE TABLE `db_delivery_order_op` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `deliveryOrderId` int(10) unsigned NOT NULL,
  `state` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `deliverymanId` smallint(5) unsigned NOT NULL,
  `opTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '时间',
  `remark` varchar(255) NOT NULL COMMENT '备注',
  PRIMARY KEY (`id`),
  KEY `deliveryOrderId` (`deliveryOrderId`)
) ENGINE=InnoDB AUTO_INCREMENT=1082 DEFAULT CHARSET=utf8 COMMENT='送货备注';

-- ----------------------------
-- Table structure for db_dep_position
-- ----------------------------
DROP TABLE IF EXISTS `db_dep_position`;
CREATE TABLE `db_dep_position` (
  `depPositionId` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '职位ID',
  `departmentId` int(10) unsigned NOT NULL COMMENT '所属部门ID',
  `state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态：0正常，1删除',
  `createTime` datetime NOT NULL COMMENT '生成时间',
  `positionName` varchar(30) NOT NULL COMMENT '职位名称',
  PRIMARY KEY (`depPositionId`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COMMENT='部门职位表';

-- ----------------------------
-- Table structure for db_department
-- ----------------------------
DROP TABLE IF EXISTS `db_department`;
CREATE TABLE `db_department` (
  `departmentId` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '部门ID',
  `state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态：0正常，1删除',
  `createTime` datetime NOT NULL COMMENT '生成时间',
  `departmentName` varchar(30) NOT NULL COMMENT '部门名称',
  PRIMARY KEY (`departmentId`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COMMENT='部门表';

-- ----------------------------
-- Table structure for db_deposit_records
-- ----------------------------
DROP TABLE IF EXISTS `db_deposit_records`;
CREATE TABLE `db_deposit_records` (
  `recordsId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `memberId` int(10) unsigned NOT NULL COMMENT '客户ID',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '结算类型：0按结算单结算，1按月结算',
  `state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态：0正常，1已撤消',
  `settlementId` int(10) unsigned NOT NULL COMMENT '结算单ID/月份',
  `amount` decimal(10,2) unsigned NOT NULL COMMENT '收款金额',
  `userId` int(11) unsigned NOT NULL COMMENT '操作者ID',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '操作者时间',
  `username` varchar(20) NOT NULL COMMENT '操作者名称',
  PRIMARY KEY (`recordsId`),
  KEY `memberId` (`memberId`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COMMENT='财务收款记录表';

-- ----------------------------
-- Table structure for db_deposit_records_undo
-- ----------------------------
DROP TABLE IF EXISTS `db_deposit_records_undo`;
CREATE TABLE `db_deposit_records_undo` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `recordsId` int(10) unsigned NOT NULL COMMENT '收款记录ID',
  `state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态：0未审核，1审核通过，2审核不通过',
  `userId` int(11) unsigned NOT NULL COMMENT '申请操作者ID',
  `checkUserId` int(10) unsigned zerofill NOT NULL DEFAULT '0000000000' COMMENT '审核者ID',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '申请时间',
  `checkTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '审核时间',
  `username` varchar(20) NOT NULL COMMENT '申请操作者名称',
  `checkUsername` varchar(20) NOT NULL DEFAULT '' COMMENT '审核者名称',
  `applyCause` varchar(255) NOT NULL COMMENT '申请理由',
  `checkCause` varchar(255) NOT NULL DEFAULT '' COMMENT '审核理由',
  PRIMARY KEY (`id`),
  KEY `memberId` (`recordsId`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='财务收款记录撤消申请表';

-- ----------------------------
-- Table structure for db_deposit_settleapply
-- ----------------------------
DROP TABLE IF EXISTS `db_deposit_settleapply`;
CREATE TABLE `db_deposit_settleapply` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `memberId` int(10) unsigned NOT NULL COMMENT '客户ID',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '结算类型：0按结算单结算，1按月结算',
  `settlementId` int(10) unsigned NOT NULL COMMENT '结算单ID/月份',
  `state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态：0未审核，1审核通过，2审核不通过',
  `amount` decimal(10,2) unsigned NOT NULL COMMENT '申请金额',
  `userId` int(11) unsigned NOT NULL COMMENT '申请操作者ID',
  `checkUserId` int(10) unsigned zerofill NOT NULL DEFAULT '0000000000' COMMENT '审核者ID',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '申请时间',
  `checkTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '审核时间',
  `username` varchar(20) NOT NULL COMMENT '申请操作者名称',
  `checkUsername` varchar(20) NOT NULL DEFAULT '' COMMENT '审核者名称',
  `applyCause` varchar(255) NOT NULL COMMENT '申请理由',
  `checkCause` varchar(255) NOT NULL DEFAULT '' COMMENT '审核理由',
  PRIMARY KEY (`id`),
  KEY `memberId` (`settlementId`),
  KEY `memberId_2` (`memberId`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='财务收款结算申请表';

-- ----------------------------
-- Table structure for db_distribution
-- ----------------------------
DROP TABLE IF EXISTS `db_distribution`;
CREATE TABLE `db_distribution` (
  `distributionId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `orderId` int(10) unsigned NOT NULL COMMENT '订单ID',
  `deliveryWarehouseId` int(10) unsigned NOT NULL COMMENT '发货仓库',
  `userId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '分配操作人userId',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '分配时间',
  PRIMARY KEY (`distributionId`),
  KEY `orderId` (`orderId`)
) ENGINE=InnoDB AUTO_INCREMENT=11028 DEFAULT CHARSET=utf8 COMMENT='订单产品分配记录';

-- ----------------------------
-- Table structure for db_distribution_detail
-- ----------------------------
DROP TABLE IF EXISTS `db_distribution_detail`;
CREATE TABLE `db_distribution_detail` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `distributionId` int(10) unsigned NOT NULL COMMENT '分配单ID',
  `warehouseId` int(10) unsigned NOT NULL COMMENT '仓库ID',
  `packingerId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '分配时选择的分拣员ID',
  `positionId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '仓位ID',
  `orderProductId` int(10) unsigned NOT NULL COMMENT '订单明细ID',
  `productId` int(10) unsigned NOT NULL COMMENT '对应产品ID',
  `unitRate` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '单位换算',
  `distributionNum` decimal(10,2) unsigned NOT NULL COMMENT '分配数量',
  `singleNumber` varchar(30) NOT NULL DEFAULT '' COMMENT '单品编码',
  `productBatch` varchar(30) NOT NULL COMMENT '产品批次',
  `color` varchar(20) NOT NULL DEFAULT '' COMMENT '颜色',
  `packingerName` varchar(30) NOT NULL DEFAULT '' COMMENT '分配时选择的分拣员姓名',
  PRIMARY KEY (`id`),
  KEY `distributionId` (`distributionId`)
) ENGINE=InnoDB AUTO_INCREMENT=13062 DEFAULT CHARSET=utf8 COMMENT='分配分拣记录明细表';

-- ----------------------------
-- Table structure for db_driver
-- ----------------------------
DROP TABLE IF EXISTS `db_driver`;
CREATE TABLE `db_driver` (
  `driverId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `gender` tinyint(1) NOT NULL DEFAULT '0' COMMENT '性别：0男，1女',
  `state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态：0正常，1删除',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `phone` char(11) NOT NULL COMMENT '手机号码',
  `driverName` varchar(20) NOT NULL COMMENT '驾驶员姓名',
  `idcard` varchar(18) NOT NULL COMMENT '身份证号码',
  PRIMARY KEY (`driverId`),
  KEY `driverName` (`driverName`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='驾驶员信息表';

-- ----------------------------
-- Table structure for db_glassy_level
-- ----------------------------
DROP TABLE IF EXISTS `db_glassy_level`;
CREATE TABLE `db_glassy_level` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `conditions` smallint(6) unsigned NOT NULL COMMENT '呆滞时长,单位为天',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '生成时间',
  `updateTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '最后更新时间',
  `title` varchar(20) NOT NULL COMMENT '级别名称',
  `logo` varchar(255) NOT NULL DEFAULT '' COMMENT '图标地址',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='呆滞级别';

-- ----------------------------
-- Table structure for db_glassy_level_product
-- ----------------------------
DROP TABLE IF EXISTS `db_glassy_level_product`;
CREATE TABLE `db_glassy_level_product` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `glassLevelId` int(10) unsigned NOT NULL COMMENT '呆滞等级ID',
  `conditions` smallint(6) unsigned NOT NULL COMMENT '呆滞时长,单位为天',
  `productId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '产品ID',
  PRIMARY KEY (`id`),
  KEY `productId` (`productId`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='产品呆滞级别';

-- ----------------------------
-- Table structure for db_glassy_list
-- ----------------------------
DROP TABLE IF EXISTS `db_glassy_list`;
CREATE TABLE `db_glassy_list` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `levelId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '呆滞等级ID',
  `warehouseId` int(10) NOT NULL COMMENT '所属仓库',
  `productId` int(10) unsigned NOT NULL COMMENT '产品ID',
  `totalNum` decimal(10,1) unsigned NOT NULL DEFAULT '0.0' COMMENT '呆滞数量',
  `state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态：0正常，1已加入尾货销售',
  `singleNumber` varchar(20) NOT NULL,
  `lastSaleTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '最后销售时间',
  PRIMARY KEY (`id`),
  KEY `singleNumber` (`singleNumber`),
  KEY `warehouseId` (`warehouseId`),
  KEY `levelId` (`levelId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='呆滞产品报表';

-- ----------------------------
-- Table structure for db_glassy_num
-- ----------------------------
DROP TABLE IF EXISTS `db_glassy_num`;
CREATE TABLE `db_glassy_num` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `warehouseId` int(10) NOT NULL COMMENT '所属仓库',
  `totalNum` decimal(10,1) unsigned NOT NULL DEFAULT '0.0' COMMENT '呆滞数量',
  `singleNumber` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `warehouseId` (`warehouseId`),
  KEY `singleNumber` (`singleNumber`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='呆滞产品数量';

-- ----------------------------
-- Table structure for db_glassy_product
-- ----------------------------
DROP TABLE IF EXISTS `db_glassy_product`;
CREATE TABLE `db_glassy_product` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `productId` int(10) unsigned NOT NULL COMMENT '产品ID',
  `state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态：0正常，1已加入尾货销售',
  `singleNumber` varchar(20) NOT NULL,
  `lastSaleTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '最后销售时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `singleNumber` (`singleNumber`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='呆滞产品报表';

-- ----------------------------
-- Table structure for db_group
-- ----------------------------
DROP TABLE IF EXISTS `db_group`;
CREATE TABLE `db_group` (
  `groupId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(20) NOT NULL COMMENT '角色组名称',
  PRIMARY KEY (`groupId`),
  KEY `title` (`title`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=8 ROW_FORMAT=COMPACT COMMENT='前台用户组';

-- ----------------------------
-- Table structure for db_help
-- ----------------------------
DROP TABLE IF EXISTS `db_help`;
CREATE TABLE `db_help` (
  `helpId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `categoryId` int(10) unsigned NOT NULL COMMENT '帮助分类',
  `state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '类型：0正常，1删除',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updateTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `title` varchar(255) NOT NULL COMMENT '标题',
  `content` text NOT NULL COMMENT '内容',
  PRIMARY KEY (`helpId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='帮助信息表';

-- ----------------------------
-- Table structure for db_help_category
-- ----------------------------
DROP TABLE IF EXISTS `db_help_category`;
CREATE TABLE `db_help_category` (
  `categoryId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '类型：0列表，1单页',
  `state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态：0正常，1删除',
  `parentId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上级分类ID',
  `listOrder` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '排序权重值',
  `title` varchar(100) NOT NULL COMMENT '分类名称',
  PRIMARY KEY (`categoryId`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8 COMMENT='帮助分类表';

-- ----------------------------
-- Table structure for db_help_category_page
-- ----------------------------
DROP TABLE IF EXISTS `db_help_category_page`;
CREATE TABLE `db_help_category_page` (
  `categoryId` int(10) unsigned NOT NULL,
  `content` text NOT NULL COMMENT '分类单页内容',
  PRIMARY KEY (`categoryId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='帮助分类表';

-- ----------------------------
-- Table structure for db_inquiry
-- ----------------------------
DROP TABLE IF EXISTS `db_inquiry`;
CREATE TABLE `db_inquiry` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `inquiryId` char(32) NOT NULL COMMENT '询盘标识',
  `memberId` int(10) unsigned NOT NULL COMMENT '客户ID',
  `productId` int(10) unsigned NOT NULL COMMENT '产品ID',
  `hasNew` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否有新消息',
  `lastTime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后更新时间',
  `title` varchar(255) NOT NULL COMMENT '产品标题',
  `serial` varchar(20) NOT NULL COMMENT '产品编号',
  `mainPic` varchar(30) NOT NULL COMMENT '产品主图',
  PRIMARY KEY (`id`),
  UNIQUE KEY `inquiryId` (`inquiryId`),
  KEY `memberId` (`memberId`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COMMENT='询盘标识表';

-- ----------------------------
-- Table structure for db_inquiry_content
-- ----------------------------
DROP TABLE IF EXISTS `db_inquiry_content`;
CREATE TABLE `db_inquiry_content` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `inquiryId` char(32) NOT NULL COMMENT '询盘标识',
  `productId` int(10) unsigned NOT NULL COMMENT '产品编号',
  `memberId` int(10) unsigned NOT NULL COMMENT '会员ID',
  `mark` enum('member','salesman','custom_service') NOT NULL COMMENT '发内容标识',
  `userId` int(10) NOT NULL COMMENT '内容发起者',
  `mime` enum('message','voice','image') NOT NULL COMMENT '内容标识',
  `content` varchar(250) NOT NULL COMMENT '内容',
  `createTime` int(10) unsigned NOT NULL COMMENT '发送时间',
  PRIMARY KEY (`id`),
  KEY `memberId` (`mark`),
  KEY `inquiryId` (`inquiryId`)
) ENGINE=InnoDB AUTO_INCREMENT=444 DEFAULT CHARSET=utf8 COMMENT='询盘标识表';

-- ----------------------------
-- Table structure for db_inquiry_delete
-- ----------------------------
DROP TABLE IF EXISTS `db_inquiry_delete`;
CREATE TABLE `db_inquiry_delete` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `inquiryId` char(32) NOT NULL COMMENT '询盘标识',
  `memberId` int(10) unsigned NOT NULL COMMENT '删除操作者ID',
  `mark` enum('member','salesman','custom_service') NOT NULL COMMENT '操作者用户类型',
  `deleteTime` int(10) unsigned NOT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`),
  KEY `inquiryId` (`inquiryId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='询盘删除记录表';

-- ----------------------------
-- Table structure for db_inquiry_member
-- ----------------------------
DROP TABLE IF EXISTS `db_inquiry_member`;
CREATE TABLE `db_inquiry_member` (
  `id` int(10) unsigned NOT NULL COMMENT '消息编号',
  `memberId` int(10) unsigned NOT NULL COMMENT '会员编号',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='会员未读消息队列';

-- ----------------------------
-- Table structure for db_level
-- ----------------------------
DROP TABLE IF EXISTS `db_level`;
CREATE TABLE `db_level` (
  `levelId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(20) NOT NULL COMMENT '等级名称',
  `logo` varchar(200) NOT NULL DEFAULT '' COMMENT '图标',
  PRIMARY KEY (`levelId`),
  UNIQUE KEY `title` (`title`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=8 ROW_FORMAT=COMPACT COMMENT='客户等级';

-- ----------------------------
-- Table structure for db_logistics
-- ----------------------------
DROP TABLE IF EXISTS `db_logistics`;
CREATE TABLE `db_logistics` (
  `logisticsId` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '物流公司ID',
  `isCOD` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'cash on delivery,是否支付货到付款',
  `isDel` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `title` varchar(50) NOT NULL COMMENT '物流标题',
  `mark` varchar(50) NOT NULL COMMENT '物流标识',
  PRIMARY KEY (`logisticsId`)
) ENGINE=InnoDB AUTO_INCREMENT=10002 DEFAULT CHARSET=utf8 COMMENT='物流公司记录表';

-- ----------------------------
-- Table structure for db_logistics_message
-- ----------------------------
DROP TABLE IF EXISTS `db_logistics_message`;
CREATE TABLE `db_logistics_message` (
  `logisticsMessageId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `deliveryId` int(10) unsigned NOT NULL COMMENT '发货单ID',
  `orderId` bigint(19) unsigned NOT NULL COMMENT '订单号',
  `state` tinyint(1) unsigned NOT NULL COMMENT '当前物流状态:1已发货,2已签收',
  `isDel` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除:0正常,1删除',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`logisticsMessageId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='物流消息';

-- ----------------------------
-- Table structure for db_member
-- ----------------------------
DROP TABLE IF EXISTS `db_member`;
CREATE TABLE `db_member` (
  `memberId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `groupId` tinyint(1) unsigned NOT NULL DEFAULT '2' COMMENT '客户组：1业务员,2客户',
  `userId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '所属业务员ID',
  `state` enum('Normal','Disabled','Deleted') NOT NULL DEFAULT 'Normal' COMMENT '状态:Normal正常；Disabled禁用；Deleted删除',
  `level` tinyint(2) unsigned NOT NULL DEFAULT '1' COMMENT '会员等级',
  `priceType` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '价格类型：0散剪价，1大货价 ',
  `isCheck` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '审核状态：0未审，1审核通过，2审核不通过',
  `isMonthlyPay` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否月结',
  `password` char(32) NOT NULL COMMENT 'md5密码',
  `code` char(6) NOT NULL COMMENT '盐值',
  `ip` char(15) NOT NULL COMMENT '注册时的IP',
  `phone` varchar(50) NOT NULL DEFAULT '' COMMENT '手机',
  `nickName` varchar(50) NOT NULL COMMENT '匿称',
  `register` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '注册日期',
  `email` varchar(50) NOT NULL DEFAULT '' COMMENT '邮箱',
  `paypassword` char(32) NOT NULL DEFAULT '' COMMENT '支付密码',
  `payModel` varchar(255) NOT NULL DEFAULT '' COMMENT '支付方式',
  `monthlyType` varchar(20) DEFAULT '' COMMENT '月结方式',
  PRIMARY KEY (`memberId`),
  KEY `email` (`email`),
  KEY `phone` (`phone`),
  KEY `state` (`state`),
  KEY `userId` (`userId`)
) ENGINE=InnoDB AUTO_INCREMENT=224 DEFAULT CHARSET=utf8 COMMENT='会员帐号表';

-- ----------------------------
-- Table structure for db_member_address
-- ----------------------------
DROP TABLE IF EXISTS `db_member_address`;
CREATE TABLE `db_member_address` (
  `addressId` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `memberId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '会员ID',
  `isDefault` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否默认:0否1是',
  `areaId` int(10) unsigned NOT NULL COMMENT '省市ID',
  `zip` varchar(6) NOT NULL DEFAULT '' COMMENT '邮政编码',
  `name` varchar(20) NOT NULL DEFAULT '' COMMENT '收货人姓名',
  `tel` varchar(15) NOT NULL DEFAULT '' COMMENT '电话',
  `mobile` varchar(11) NOT NULL DEFAULT '' COMMENT '手机',
  `address` varchar(255) NOT NULL DEFAULT '' COMMENT '收货地址',
  PRIMARY KEY (`addressId`)
) ENGINE=InnoDB AUTO_INCREMENT=1182 DEFAULT CHARSET=utf8 COMMENT='客户收货地址表';

-- ----------------------------
-- Table structure for db_member_applyprice
-- ----------------------------
DROP TABLE IF EXISTS `db_member_applyprice`;
CREATE TABLE `db_member_applyprice` (
  `applyPriceId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `productId` int(10) unsigned NOT NULL COMMENT '产品ID',
  `memberId` int(10) unsigned NOT NULL COMMENT '客户ID',
  `salemanId` int(10) unsigned NOT NULL COMMENT '业务员memberId，即申请人',
  `state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态：0未审，1审核通过，2审核不通过,3失效',
  `isDel` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除，0正常，1从前台删除，2从后台删除',
  `applyPrice` decimal(9,2) unsigned NOT NULL COMMENT '申请的价格',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '申请时间',
  PRIMARY KEY (`applyPriceId`),
  KEY `memberId` (`memberId`),
  KEY `productId` (`productId`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8 COMMENT='批发价格申请记录信息表';

-- ----------------------------
-- Table structure for db_member_applyprice_op
-- ----------------------------
DROP TABLE IF EXISTS `db_member_applyprice_op`;
CREATE TABLE `db_member_applyprice_op` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `applyPriceId` int(10) unsigned NOT NULL COMMENT '申请表ID',
  `userId` int(10) unsigned NOT NULL COMMENT '后台userId/前台memberId',
  `isManage` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否后台操作：0前台，1后台',
  `code` varchar(10) NOT NULL COMMENT '操作码',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '操作时间',
  `remark` text NOT NULL COMMENT '备注说明',
  PRIMARY KEY (`id`),
  KEY `applyPriceId` (`applyPriceId`)
) ENGINE=InnoDB AUTO_INCREMENT=128 DEFAULT CHARSET=utf8 COMMENT='批发价格申请操作审核记录表';

-- ----------------------------
-- Table structure for db_member_bill
-- ----------------------------
DROP TABLE IF EXISTS `db_member_bill`;
CREATE TABLE `db_member_bill` (
  `billId` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '账单ID',
  `memberId` int(10) unsigned NOT NULL COMMENT '客户ID',
  `credit` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '账单金额',
  `createTime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '账单生成时间',
  PRIMARY KEY (`billId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='月结额度信用账单';

-- ----------------------------
-- Table structure for db_member_bill_detail
-- ----------------------------
DROP TABLE IF EXISTS `db_member_bill_detail`;
CREATE TABLE `db_member_bill_detail` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `billId` int(10) unsigned NOT NULL COMMENT '账单ID',
  `orderId` bigint(19) unsigned NOT NULL DEFAULT '0' COMMENT '订单ID',
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '账单金额',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '生成时间',
  `mark` varchar(100) NOT NULL COMMENT '说明',
  PRIMARY KEY (`id`),
  KEY `billId` (`billId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='月结额度信用账单明细';

-- ----------------------------
-- Table structure for db_member_check
-- ----------------------------
DROP TABLE IF EXISTS `db_member_check`;
CREATE TABLE `db_member_check` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `memberId` int(10) unsigned NOT NULL COMMENT '客户ID',
  `opId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '操作者ID',
  `state` tinyint(1) unsigned NOT NULL COMMENT '审核状态',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '审核时间',
  `reason` varchar(255) NOT NULL DEFAULT '' COMMENT '理由',
  PRIMARY KEY (`id`),
  KEY `memberId` (`memberId`)
) ENGINE=InnoDB AUTO_INCREMENT=103 DEFAULT CHARSET=utf8 COMMENT='客户审核记录表';

-- ----------------------------
-- Table structure for db_member_credit
-- ----------------------------
DROP TABLE IF EXISTS `db_member_credit`;
CREATE TABLE `db_member_credit` (
  `memberId` int(10) unsigned NOT NULL COMMENT '客户ID',
  `state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态：0正常，1删除',
  `billingCycle` tinyint(2) unsigned NOT NULL DEFAULT '1' COMMENT '结算周期，单位为月',
  `credit` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '信用额度',
  `createTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '首次加入月结时间',
  `updateTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  PRIMARY KEY (`memberId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='会员月结信息表';

-- ----------------------------
-- Table structure for db_member_credit_detail
-- ----------------------------
DROP TABLE IF EXISTS `db_member_credit_detail`;
CREATE TABLE `db_member_credit_detail` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `memberId` int(10) unsigned NOT NULL COMMENT '客户ID',
  `orderId` bigint(19) unsigned NOT NULL COMMENT '订单ID',
  `state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否已入账：0未入账，1已入账',
  `isCheck` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '审核状态：0未审，1已审，2已取消',
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '金额',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '记录时间',
  `updateTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  `mark` varchar(100) NOT NULL COMMENT '说明',
  PRIMARY KEY (`id`),
  KEY `memberId` (`memberId`),
  KEY `orderId` (`orderId`)
) ENGINE=InnoDB AUTO_INCREMENT=693 DEFAULT CHARSET=utf8 COMMENT='会员额度使用明细表';

-- ----------------------------
-- Table structure for db_member_credit_static
-- ----------------------------
DROP TABLE IF EXISTS `db_member_credit_static`;
CREATE TABLE `db_member_credit_static` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL COMMENT '月份',
  `memberId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `userId` varchar(50) NOT NULL COMMENT '用户名',
  `payments` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT '结算金额',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='结算金额汇总';

-- ----------------------------
-- Table structure for db_member_device
-- ----------------------------
DROP TABLE IF EXISTS `db_member_device`;
CREATE TABLE `db_member_device` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `msgtype` tinyint(1) unsigned DEFAULT '1' COMMENT '消息类型,0:透传,1:通知',
  `memberId` int(10) unsigned NOT NULL COMMENT '会员编号',
  `cid` varchar(32) NOT NULL COMMENT '个推CID记录',
  `userType` varchar(20) NOT NULL COMMENT '会员类型',
  `loginTime` int(10) NOT NULL COMMENT '最后登陆时间',
  `os` varchar(20) NOT NULL COMMENT '移动设备系统信息',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8 COMMENT='移动设备登陆记录';

-- ----------------------------
-- Table structure for db_member_saleman
-- ----------------------------
DROP TABLE IF EXISTS `db_member_saleman`;
CREATE TABLE `db_member_saleman` (
  `memberId` int(10) unsigned NOT NULL,
  `printerId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '打印机编号',
  PRIMARY KEY (`memberId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='会员--业务员扩展信息表';

-- ----------------------------
-- Table structure for db_menu
-- ----------------------------
DROP TABLE IF EXISTS `db_menu`;
CREATE TABLE `db_menu` (
  `menuId` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '菜单编号',
  `parentId` int(10) unsigned NOT NULL COMMENT '父菜单',
  `hidden` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否隐藏',
  `title` varchar(45) NOT NULL COMMENT '菜单项',
  `route` varchar(60) NOT NULL DEFAULT '' COMMENT '路由地址',
  `url` varchar(50) NOT NULL DEFAULT '' COMMENT '菜单url地址',
  `orderList` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序值',
  PRIMARY KEY (`menuId`)
) ENGINE=InnoDB AUTO_INCREMENT=219 DEFAULT CHARSET=utf8 COMMENT='系统菜单';

-- ----------------------------
-- Table structure for db_menuMap
-- ----------------------------
DROP TABLE IF EXISTS `db_menuMap`;
CREATE TABLE `db_menuMap` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `menuId` int(10) unsigned NOT NULL COMMENT '菜单ID',
  `route` varchar(45) NOT NULL COMMENT '路由地址',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for db_message
-- ----------------------------
DROP TABLE IF EXISTS `db_message`;
CREATE TABLE `db_message` (
  `messageId` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `senderId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发送者ID,0为系统发送',
  `memberId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '收信息的会员ID',
  `state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态:0未查看,1已查看,2删除',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '标题',
  `content` text NOT NULL COMMENT '消息内容',
  PRIMARY KEY (`messageId`),
  KEY `memberId` (`memberId`)
) ENGINE=InnoDB AUTO_INCREMENT=1027 DEFAULT CHARSET=utf8 COMMENT='系统消息表';

-- ----------------------------
-- Table structure for db_nav
-- ----------------------------
DROP TABLE IF EXISTS `db_nav`;
CREATE TABLE `db_nav` (
  `navId` int(10) NOT NULL AUTO_INCREMENT COMMENT '菜单ID',
  `type` tinyint(3) unsigned DEFAULT '0' COMMENT '分组',
  `parentId` int(10) NOT NULL DEFAULT '0' COMMENT '上级ID',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '标题',
  `route` varchar(90) NOT NULL COMMENT '路由地址',
  `listOrder` smallint(3) NOT NULL DEFAULT '0' COMMENT '排序',
  `state` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态0不显示1显示',
  PRIMARY KEY (`navId`),
  KEY `idx_nav` (`parentId`)
) ENGINE=InnoDB AUTO_INCREMENT=79 DEFAULT CHARSET=utf8 COMMENT='菜单表';

-- ----------------------------
-- Table structure for db_op_log
-- ----------------------------
DROP TABLE IF EXISTS `db_op_log`;
CREATE TABLE `db_op_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `objId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '操作对象ID',
  `userId` int(10) unsigned NOT NULL COMMENT '操作者',
  `isManage` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否后台操作：0前台，1后台',
  `objType` varchar(20) NOT NULL COMMENT '操作对象类型',
  `code` varchar(10) NOT NULL COMMENT '操作标识',
  `opTime` datetime NOT NULL COMMENT '操作时间',
  `remark` varchar(255) NOT NULL COMMENT '操作说明',
  PRIMARY KEY (`id`),
  KEY `objType` (`objType`),
  KEY `objId` (`objId`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8 COMMENT='操作日志';

-- ----------------------------
-- Table structure for db_opencv_map
-- ----------------------------
DROP TABLE IF EXISTS `db_opencv_map`;
CREATE TABLE `db_opencv_map` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` char(13) NOT NULL COMMENT '图片ID',
  `productId` int(10) unsigned NOT NULL COMMENT '产品ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for db_ord_payment
-- ----------------------------
DROP TABLE IF EXISTS `db_ord_payment`;
CREATE TABLE `db_ord_payment` (
  `ordpaymentId` varchar(30) NOT NULL COMMENT '付款单号',
  `memberId` int(10) NOT NULL DEFAULT '0' COMMENT '会员ID',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '支付类型:0:支付宝,1微信支付',
  `price` decimal(18,2) NOT NULL DEFAULT '0.00' COMMENT '付款金额',
  `state` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态:0:待付款,1:已付款,3:作废,4:删除',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '创建时间',
  `updateTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `tradeNo` varchar(50) NOT NULL DEFAULT '' COMMENT '交易流水号',
  `title` varchar(255) NOT NULL DEFAULT '',
  `orderIds` text NOT NULL COMMENT '订单号,多订单以逗号分隔',
  PRIMARY KEY (`ordpaymentId`),
  KEY `ix_ordpayment` (`memberId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='订单付款单';

-- ----------------------------
-- Table structure for db_order
-- ----------------------------
DROP TABLE IF EXISTS `db_order`;
CREATE TABLE `db_order` (
  `orderId` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '订单编号',
  `originatorType` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '下单人类型：0客户下单，1业务员下单',
  `memberId` int(10) unsigned NOT NULL COMMENT '客户编号',
  `userId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '业务员ID',
  `state` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '订单当前状态：0:待审核,1:备货中,2:备货完成,3:待发货,4:待收货,6:已完成,7:关闭',
  `hasRefund` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `isDel` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `deliveryMethod` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '提货方式',
  `orderType` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '订单类型:0:现货订单 1:订货订单 2留货订单',
  `payModel` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '支付方式 ',
  `payState` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '支付状态：0未支付，1已付定金，2已支付，3.已结算',
  `isSettled` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否已开具结算单',
  `isRecognition` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否已财务确认',
  `commentState` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '评论状态：0未评论，1已评论',
  `warehouseId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发货仓库ID',
  `packingWarehouseId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '默认分拣仓库ID',
  `logistics` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '物流公司编号',
  `realPayment` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '实付款，订单总额',
  `freight` decimal(7,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '运费',
  `memo` varchar(200) NOT NULL DEFAULT '' COMMENT '订单留言',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '订单提交时间',
  `payTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '提交支付时间',
  `dealTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '交易完成时间',
  `source` enum('web','wx','ios','android') NOT NULL DEFAULT 'web' COMMENT '订单来源',
  `name` varchar(20) NOT NULL COMMENT '收货人姓名',
  `tel` varchar(20) NOT NULL COMMENT '收货人电话',
  `address` varchar(255) NOT NULL COMMENT '收货地址',
  PRIMARY KEY (`orderId`),
  KEY `memberId` (`memberId`),
  KEY `userId` (`userId`)
) ENGINE=InnoDB AUTO_INCREMENT=1611040002 DEFAULT CHARSET=utf8 COMMENT='订单表';

-- ----------------------------
-- Table structure for db_order_applychange
-- ----------------------------
DROP TABLE IF EXISTS `db_order_applychange`;
CREATE TABLE `db_order_applychange` (
  `applyId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `orderId` int(10) unsigned NOT NULL COMMENT '订单ID',
  `state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态：0未审，1审核通过，2审核不通过,3删除',
  `applyType` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '申请来源：0客户申请，1业务员申请',
  `freight` smallint(5) unsigned NOT NULL COMMENT '运费',
  `memberId` int(10) unsigned NOT NULL COMMENT '申请人memberId',
  `checkUserId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '审核人userId',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '申请时间',
  `checkTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '审核时间',
  `memo` varchar(255) NOT NULL COMMENT '订单备注',
  `address` varchar(255) NOT NULL COMMENT '订单地址',
  `checkInfo` varchar(255) NOT NULL DEFAULT '' COMMENT '审核信息',
  PRIMARY KEY (`applyId`),
  UNIQUE KEY `orderId` (`orderId`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='订单修改申请信息表';

-- ----------------------------
-- Table structure for db_order_applychange_detail
-- ----------------------------
DROP TABLE IF EXISTS `db_order_applychange_detail`;
CREATE TABLE `db_order_applychange_detail` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `applyId` int(10) unsigned NOT NULL COMMENT '修改申请表ID',
  `orderProductId` int(10) unsigned NOT NULL COMMENT '订单明细表ID',
  `isNodify` tinyint(1) NOT NULL DEFAULT '0' COMMENT '已作通知：0需通知，1已通知',
  `oldNum` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '修改前数量',
  `applyNum` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '申请修改数量',
  `checkNum` decimal(10,2) unsigned NOT NULL COMMENT '审核后数量',
  `remark` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8 COMMENT='订单修改申请信息表';

-- ----------------------------
-- Table structure for db_order_applyclose
-- ----------------------------
DROP TABLE IF EXISTS `db_order_applyclose`;
CREATE TABLE `db_order_applyclose` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `orderId` int(10) unsigned NOT NULL COMMENT '订单ID',
  `state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态：0未审，1审核通过，2审核不通过,3删除',
  `checkUserId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '审核人userId',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '申请时间',
  `checkTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '审核时间',
  `reason` varchar(255) NOT NULL COMMENT '申请取消理由',
  `remark` varchar(255) NOT NULL COMMENT '审核说明',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=127 DEFAULT CHARSET=utf8 COMMENT='客户申请取消订单信息审核记录表';

-- ----------------------------
-- Table structure for db_order_applyprice
-- ----------------------------
DROP TABLE IF EXISTS `db_order_applyprice`;
CREATE TABLE `db_order_applyprice` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `orderId` int(10) unsigned NOT NULL COMMENT '订单ID',
  `state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态：0未审，1审核通过，2审核不通过,3删除',
  `applyType` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '申请来源：0前台业务员申请，1后台申请',
  `originatorId` int(10) unsigned NOT NULL COMMENT '申请人memberId/userId',
  `checkUserId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '审核人userId',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '申请时间',
  `checkTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '审核时间',
  `prices` text NOT NULL COMMENT '申请价格详情',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10329 DEFAULT CHARSET=utf8 COMMENT='订单价格申请信息表';

-- ----------------------------
-- Table structure for db_order_batches
-- ----------------------------
DROP TABLE IF EXISTS `db_order_batches`;
CREATE TABLE `db_order_batches` (
  `batchesId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `orderId` int(10) unsigned NOT NULL,
  `exprise` date NOT NULL COMMENT '交货日期',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  PRIMARY KEY (`batchesId`),
  KEY `orderId` (`orderId`)
) ENGINE=InnoDB AUTO_INCREMENT=11336 DEFAULT CHARSET=utf8 COMMENT='分批发货记录信息';

-- ----------------------------
-- Table structure for db_order_buy
-- ----------------------------
DROP TABLE IF EXISTS `db_order_buy`;
CREATE TABLE `db_order_buy` (
  `orderId` char(14) NOT NULL,
  `state` tinyint(2) unsigned NOT NULL COMMENT '订单状态 0:待审核 1:删除 2:已审核 3:已完成 4:已关闭',
  `memberId` int(10) unsigned NOT NULL COMMENT '采购员编号',
  `createTime` int(10) unsigned NOT NULL COMMENT '创建时间',
  `updateTime` int(10) unsigned NOT NULL DEFAULT '0',
  `phone` varchar(20) NOT NULL COMMENT '联系电话',
  `factoryNumber` varchar(45) NOT NULL COMMENT '革厂编号',
  `contacts` varchar(45) NOT NULL COMMENT '联系人',
  `factoryName` varchar(60) NOT NULL COMMENT '革厂名称',
  `address` varchar(100) NOT NULL COMMENT '联系地址',
  `comment` varchar(100) NOT NULL COMMENT '备注',
  PRIMARY KEY (`orderId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='将弃用：采购订单';

-- ----------------------------
-- Table structure for db_order_buy_product
-- ----------------------------
DROP TABLE IF EXISTS `db_order_buy_product`;
CREATE TABLE `db_order_buy_product` (
  `id` char(14) NOT NULL,
  `orderId` char(14) NOT NULL COMMENT '订单编号',
  `dealTime` int(10) unsigned NOT NULL COMMENT '交货时间',
  `total` int(10) unsigned NOT NULL COMMENT '总采购数量',
  `color` varchar(20) NOT NULL COMMENT '颜色',
  `singleNumber` varchar(45) NOT NULL COMMENT '产品编号',
  `corpProductNumber` varchar(45) NOT NULL COMMENT '革厂产品编码',
  `comment` varchar(100) NOT NULL COMMENT '备注',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='将弃用：采购订单明细表';

-- ----------------------------
-- Table structure for db_order_buy_relate
-- ----------------------------
DROP TABLE IF EXISTS `db_order_buy_relate`;
CREATE TABLE `db_order_buy_relate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `orderId` char(14) NOT NULL COMMENT '流水号',
  `orderProductId` char(14) NOT NULL,
  `source` int(3) NOT NULL COMMENT '来源类型',
  `fromOrderId` char(14) NOT NULL COMMENT '来源单号',
  `fromDetailId` char(14) NOT NULL COMMENT '来源明细单号',
  `total` int(5) unsigned NOT NULL DEFAULT '0' COMMENT '采购数量',
  `unitName` varchar(10) NOT NULL DEFAULT '' COMMENT '计价单位',
  `singleNumber` varchar(20) NOT NULL DEFAULT '' COMMENT '单品编码',
  `color` varchar(20) NOT NULL DEFAULT '' COMMENT '颜色',
  `comment` varchar(250) NOT NULL DEFAULT '' COMMENT '来源备注',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='将弃用：订单关联关系表';

-- ----------------------------
-- Table structure for db_order_close
-- ----------------------------
DROP TABLE IF EXISTS `db_order_close`;
CREATE TABLE `db_order_close` (
  `orderId` bigint(19) unsigned NOT NULL COMMENT '订单ID',
  `opId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '操作者ID',
  `opType` tinyint(1) unsigned NOT NULL COMMENT '操作者类型，0客户，1业务员，2 后台管理员取消  3 系统取消',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '取消订单时间',
  `reason` varchar(200) NOT NULL COMMENT '取消订单原因',
  PRIMARY KEY (`orderId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='订单取消操作记录表';

-- ----------------------------
-- Table structure for db_order_comment
-- ----------------------------
DROP TABLE IF EXISTS `db_order_comment`;
CREATE TABLE `db_order_comment` (
  `commentId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `orderId` bigint(19) unsigned NOT NULL COMMENT '订单ID',
  `productId` int(10) unsigned NOT NULL COMMENT '产品ID',
  `tailId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '尾货产品ID',
  `orderProductId` int(10) unsigned NOT NULL COMMENT '订单产品表ID',
  `memberId` int(10) unsigned NOT NULL COMMENT '客户ID',
  `userId` int(10) unsigned NOT NULL COMMENT '回复者userId',
  `state` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态：0正常，1删除',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '评论时间',
  `updateTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `replyTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '回复时间',
  `specifiaction` varchar(100) NOT NULL COMMENT '产品规格',
  `content` text NOT NULL COMMENT '评论内容',
  `reply` text NOT NULL COMMENT '评论回复',
  PRIMARY KEY (`commentId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='订单产品评论表';

-- ----------------------------
-- Table structure for db_order_delivery
-- ----------------------------
DROP TABLE IF EXISTS `db_order_delivery`;
CREATE TABLE `db_order_delivery` (
  `deliveryId` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '发货单ID',
  `orderId` bigint(19) unsigned NOT NULL COMMENT '订单ID',
  `userId` int(10) unsigned NOT NULL COMMENT '后台操作人userId',
  `state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态：0未收货，1已收货',
  `logistics` smallint(5) unsigned NOT NULL COMMENT '物流公司编号',
  `receivedUserId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '收货操作人userId',
  `receivedType` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '确认收货来源；0前台确认收货，1后台确认收货',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '发货时间',
  `receivedTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '收货操作时间',
  `logisticsNo` varchar(30) NOT NULL COMMENT '物流编号',
  `address` varchar(255) NOT NULL DEFAULT '' COMMENT '收货地址',
  PRIMARY KEY (`deliveryId`),
  KEY `orderId` (`orderId`)
) ENGINE=InnoDB AUTO_INCREMENT=10990 DEFAULT CHARSET=utf8 COMMENT='发货单';

-- ----------------------------
-- Table structure for db_order_deliverydetail
-- ----------------------------
DROP TABLE IF EXISTS `db_order_deliverydetail`;
CREATE TABLE `db_order_deliverydetail` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `deliveryId` int(10) unsigned NOT NULL COMMENT '发货单ID',
  `productId` int(11) unsigned NOT NULL COMMENT '产品ID',
  `stockId` int(10) unsigned NOT NULL COMMENT '产品库存规格ID',
  `num` decimal(10,2) unsigned NOT NULL COMMENT '发货数量',
  `receivedNum` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '已收货数量',
  PRIMARY KEY (`id`),
  KEY `deliveryId` (`deliveryId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='发货单产品数量';

-- ----------------------------
-- Table structure for db_order_deposit
-- ----------------------------
DROP TABLE IF EXISTS `db_order_deposit`;
CREATE TABLE `db_order_deposit` (
  `orderId` int(10) unsigned NOT NULL COMMENT '订单编号',
  `state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否已提交支付：0未支付，1已提交支付',
  `payState` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '确定已支付，已收到款',
  `isDel` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `payModel` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '支付方式 ',
  `amount` decimal(9,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '订金金额',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '生成时间',
  PRIMARY KEY (`orderId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='订货订单订金';

-- ----------------------------
-- Table structure for db_order_distribution
-- ----------------------------
DROP TABLE IF EXISTS `db_order_distribution`;
CREATE TABLE `db_order_distribution` (
  `orderId` int(10) unsigned NOT NULL COMMENT '订单ID',
  `state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态: 0待分配，1已分配',
  `userId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '分配操作userId',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '提交时间',
  `opTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '分配时间',
  PRIMARY KEY (`orderId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='待分配分拣订单';

-- ----------------------------
-- Table structure for db_order_keep
-- ----------------------------
DROP TABLE IF EXISTS `db_order_keep`;
CREATE TABLE `db_order_keep` (
  `orderKeepId` char(14) NOT NULL,
  `orderId` char(14) NOT NULL COMMENT '订单编号',
  `state` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '审核状态 0:待审核 1:已审核 2:拒绝通过',
  `buyState` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否已确定购买',
  `userId` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '审核人员',
  `createTime` int(10) unsigned NOT NULL COMMENT '审核时间',
  `expireTime` int(10) unsigned NOT NULL COMMENT '过期时间',
  `buyTime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '确定购买时间',
  `cause` varchar(200) NOT NULL COMMENT '理由',
  PRIMARY KEY (`orderKeepId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='留货审核';

-- ----------------------------
-- Table structure for db_order_keep_delay
-- ----------------------------
DROP TABLE IF EXISTS `db_order_keep_delay`;
CREATE TABLE `db_order_keep_delay` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `orderId` bigint(19) unsigned NOT NULL COMMENT '订单ID',
  `state` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态 0:待审核 1:审核通过 2:审核不通过',
  `userId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '审核者ID',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '申请延期时间',
  `checkTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '审核时间',
  `reason` varchar(255) NOT NULL DEFAULT '' COMMENT '审核理由',
  PRIMARY KEY (`id`),
  KEY `orderId` (`orderId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='留货订单--申请延期记录表';

-- ----------------------------
-- Table structure for db_order_keep_log
-- ----------------------------
DROP TABLE IF EXISTS `db_order_keep_log`;
CREATE TABLE `db_order_keep_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `orderId` bigint(19) unsigned NOT NULL COMMENT '订单ID',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '申请延期时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='留货订单--申请延期记录表';

-- ----------------------------
-- Table structure for db_order_merge
-- ----------------------------
DROP TABLE IF EXISTS `db_order_merge`;
CREATE TABLE `db_order_merge` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '归单状态',
  `actionType` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '处理类型：0未处理，1已调拨，2已发货',
  `orderId` int(10) unsigned NOT NULL COMMENT '订单ID',
  `warehouseId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '分拣仓库ID',
  `mergeUserId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '调度安排的归单人',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '加入归单队列时间',
  `mergeTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '全部归单完成时间',
  PRIMARY KEY (`id`),
  KEY `warehouseId` (`warehouseId`)
) ENGINE=InnoDB AUTO_INCREMENT=4100 DEFAULT CHARSET=utf8 COMMENT='归单订单记录';

-- ----------------------------
-- Table structure for db_order_message
-- ----------------------------
DROP TABLE IF EXISTS `db_order_message`;
CREATE TABLE `db_order_message` (
  `messageId` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `inside` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否内部信息，若是，只在后台显示',
  `orderId` bigint(19) unsigned NOT NULL COMMENT '所属订单ID',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '记录时间',
  `subject` varchar(255) NOT NULL,
  `message` varchar(255) NOT NULL DEFAULT '' COMMENT '信息补充说明',
  PRIMARY KEY (`messageId`),
  KEY `orderId` (`orderId`)
) ENGINE=InnoDB AUTO_INCREMENT=8564 DEFAULT CHARSET=utf8 COMMENT='订单追踪信息表';

-- ----------------------------
-- Table structure for db_order_payment
-- ----------------------------
DROP TABLE IF EXISTS `db_order_payment`;
CREATE TABLE `db_order_payment` (
  `paymentId` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '支付单号',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '支付类型',
  `payMethod` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '支付方法ID',
  `orderId` bigint(19) unsigned NOT NULL COMMENT '订单号',
  `amountType` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '货款类型，0订金，1货款',
  `amount` decimal(18,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '付款金额',
  `logistics` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '物流公司编号',
  `payTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '支付时间',
  `voucher` varchar(200) NOT NULL DEFAULT '' COMMENT '支付凭证图片uri',
  PRIMARY KEY (`paymentId`),
  KEY `orderId` (`orderId`)
) ENGINE=InnoDB AUTO_INCREMENT=10593 DEFAULT CHARSET=utf8 COMMENT='订单支付信息';

-- ----------------------------
-- Table structure for db_order_post
-- ----------------------------
DROP TABLE IF EXISTS `db_order_post`;
CREATE TABLE `db_order_post` (
  `postId` char(14) NOT NULL,
  `orderId` char(14) NOT NULL COMMENT '采购单号',
  `state` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '状态 0:生产中 1:发货中 2:待分配 3:待入库 4:已入库',
  `orderType` tinyint(2) unsigned NOT NULL COMMENT '发货单类型 0:业务员创建 1:工厂创建',
  `userId` int(10) unsigned NOT NULL COMMENT '操作员编号',
  `createTime` int(10) unsigned NOT NULL COMMENT '创建时间',
  `postTime` int(10) unsigned NOT NULL COMMENT '发货时间',
  `logisticId` int(10) unsigned NOT NULL COMMENT '物流公司',
  `logisticNumber` varchar(45) NOT NULL COMMENT '物流单号',
  `logisticName` varchar(80) NOT NULL COMMENT '物流公司名称',
  `comment` varchar(255) NOT NULL COMMENT '备注',
  PRIMARY KEY (`postId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='将弃用：工厂发货单';

-- ----------------------------
-- Table structure for db_order_post_assign
-- ----------------------------
DROP TABLE IF EXISTS `db_order_post_assign`;
CREATE TABLE `db_order_post_assign` (
  `assignId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `postProductId` char(14) NOT NULL COMMENT '订单明细编号',
  `orderbuyProductId` char(14) NOT NULL COMMENT '采购单明细编号',
  `total` int(10) unsigned NOT NULL COMMENT '数量',
  `isAssign` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '匹配状态 0:未匹配 1:已匹配',
  `singleNumber` varchar(30) NOT NULL COMMENT '单品编码',
  PRIMARY KEY (`assignId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='将弃用：发货单匹配信息记录';

-- ----------------------------
-- Table structure for db_order_post_product
-- ----------------------------
DROP TABLE IF EXISTS `db_order_post_product`;
CREATE TABLE `db_order_post_product` (
  `id` char(14) NOT NULL,
  `isAssign` tinyint(1) unsigned NOT NULL COMMENT '单品匹配完成',
  `postId` char(14) NOT NULL COMMENT '订单编号',
  `singleNumber` varchar(20) NOT NULL COMMENT '单品编码',
  `total` int(10) unsigned NOT NULL COMMENT '发货数量',
  `orderbuyId` char(14) NOT NULL COMMENT '采购单号',
  `unitName` varchar(10) NOT NULL COMMENT '计价单位',
  `color` varchar(20) NOT NULL COMMENT '颜色',
  `comment` varchar(100) NOT NULL COMMENT '备注',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='发货单产品记录';

-- ----------------------------
-- Table structure for db_order_post2
-- ----------------------------
DROP TABLE IF EXISTS `db_order_post2`;
CREATE TABLE `db_order_post2` (
  `postId` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '发货单编号',
  `state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态 0:正常 1:删除',
  `orderType` tinyint(2) unsigned NOT NULL COMMENT '类型 0:工厂创建 1:采购创建',
  `userId` int(10) unsigned NOT NULL COMMENT '操作员',
  `purchaseId` int(10) unsigned NOT NULL COMMENT '采购单编号',
  `logisticsCode` varchar(20) NOT NULL COMMENT '物流单号',
  `logisticsName` varchar(30) NOT NULL COMMENT '物流公司',
  `postTime` date NOT NULL COMMENT '发货时间',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '订单创建时间',
  PRIMARY KEY (`postId`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='发货单号';

-- ----------------------------
-- Table structure for db_order_post2_assign
-- ----------------------------
DROP TABLE IF EXISTS `db_order_post2_assign`;
CREATE TABLE `db_order_post2_assign` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `postProId` int(10) unsigned NOT NULL COMMENT '发货单明细单编号',
  `purchaseId` int(10) unsigned NOT NULL COMMENT '待采购队列编号',
  `userId` int(10) unsigned NOT NULL COMMENT '操作人员编号',
  `createTime` int(10) unsigned NOT NULL COMMENT '操作时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for db_order_post2_product
-- ----------------------------
DROP TABLE IF EXISTS `db_order_post2_product`;
CREATE TABLE `db_order_post2_product` (
  `postProId` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '发货单明细单号',
  `source` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '订单来源',
  `isAssign` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '订单匹配',
  `postId` int(10) unsigned NOT NULL COMMENT '发货单号',
  `purchaseId` int(10) unsigned NOT NULL COMMENT '采购单编号',
  `purchaseProId` int(10) unsigned NOT NULL COMMENT '采购单明细编号',
  `postTotal` decimal(10,2) unsigned NOT NULL COMMENT '发货数量',
  `comment` varchar(160) NOT NULL COMMENT '备注',
  PRIMARY KEY (`postProId`),
  KEY `postId` (`postId`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='发货单明细';

-- ----------------------------
-- Table structure for db_order_product
-- ----------------------------
DROP TABLE IF EXISTS `db_order_product`;
CREATE TABLE `db_order_product` (
  `orderProductId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `orderId` int(10) unsigned NOT NULL COMMENT '订单编号',
  `productId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '产品ID',
  `tailId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '尾货ID',
  `price` decimal(9,4) unsigned NOT NULL DEFAULT '0.0000' COMMENT '实际成交单价',
  `salesPrice` decimal(9,4) unsigned NOT NULL DEFAULT '0.0000' COMMENT '销售单价',
  `num` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '购买数量',
  `isSample` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否赠板',
  `stockId` int(10) unsigned NOT NULL COMMENT '产品规格库存表ID',
  `packingNum` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '已拣货数量',
  `deliveryNum` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '已发货数量',
  `receivedNum` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '已收货数量',
  `state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
  `saleType` enum('normal','retail','whole') NOT NULL DEFAULT 'normal' COMMENT '销售类型',
  `color` varchar(15) NOT NULL DEFAULT '' COMMENT '颜色',
  `title` varchar(255) NOT NULL COMMENT '产品标题',
  `serialNumber` varchar(100) NOT NULL DEFAULT '' COMMENT '产品编号',
  `singleNumber` varchar(30) NOT NULL COMMENT '单品编码',
  `mainPic` varchar(200) NOT NULL COMMENT '产品小图',
  `specifiaction` varchar(200) NOT NULL COMMENT '产品规格',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  PRIMARY KEY (`orderProductId`),
  KEY `singleNumber` (`singleNumber`),
  KEY `orderId` (`orderId`)
) ENGINE=InnoDB AUTO_INCREMENT=13501 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for db_order_purchase
-- ----------------------------
DROP TABLE IF EXISTS `db_order_purchase`;
CREATE TABLE `db_order_purchase` (
  `purchaseId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `source` int(3) unsigned NOT NULL DEFAULT '0' COMMENT '类型 0:内部请购 1:低库存采购 2:客户订单',
  `fromOrderId` char(14) NOT NULL DEFAULT '0' COMMENT '来源订单编号',
  `fromDetailId` char(14) NOT NULL DEFAULT '0' COMMENT '订单明细单编号',
  `state` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '状态 0:待处理 1:正在处理 2:已处理',
  `createTime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '添加请购时间',
  `userId` int(10) NOT NULL DEFAULT '0' COMMENT '处理人员',
  `singleNumber` char(14) NOT NULL COMMENT '产品单品编码',
  `color` varchar(20) NOT NULL COMMENT '产品颜色',
  `total` int(10) unsigned NOT NULL COMMENT '采购数量',
  `unitName` varchar(10) NOT NULL COMMENT '计价单位',
  `comment` varchar(250) NOT NULL COMMENT '来源订单备注',
  PRIMARY KEY (`purchaseId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='将弃用：待采购列表';

-- ----------------------------
-- Table structure for db_order_purchase2
-- ----------------------------
DROP TABLE IF EXISTS `db_order_purchase2`;
CREATE TABLE `db_order_purchase2` (
  `purchaseId` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '待采购单编号',
  `state` tinyint(1) DEFAULT '0' COMMENT '订单状态 0:未选中 1:已选中',
  `isAssign` tinyint(1) unsigned NOT NULL COMMENT '匹配产品',
  `userId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '操作员编号',
  `source` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '来源订单类型',
  `orderId` int(10) unsigned NOT NULL COMMENT '来源订单编号',
  `orderProId` int(10) unsigned NOT NULL COMMENT '来源订单明细编号',
  `productId` int(10) unsigned NOT NULL COMMENT '产品ID',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `deliveryTime` date NOT NULL COMMENT '交货日期',
  `productCode` varchar(20) NOT NULL COMMENT '产品编码',
  `color` varchar(30) NOT NULL COMMENT '产品颜色',
  `quantity` decimal(10,2) unsigned NOT NULL COMMENT '采购数量',
  `comment` varchar(160) NOT NULL COMMENT '备注',
  PRIMARY KEY (`purchaseId`),
  KEY `orderId` (`orderId`)
) ENGINE=InnoDB AUTO_INCREMENT=10141 DEFAULT CHARSET=utf8 COMMENT='待采购订单';

-- ----------------------------
-- Table structure for db_order_purchasing
-- ----------------------------
DROP TABLE IF EXISTS `db_order_purchasing`;
CREATE TABLE `db_order_purchasing` (
  `purchaseId` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '采购单编号',
  `state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '订单状态',
  `userId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建订单人员',
  `supplierId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '供应商编号',
  `createTime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单创建时间',
  `supplierName` varchar(100) NOT NULL COMMENT '供应商名称',
  `supplierSerial` varchar(20) NOT NULL COMMENT '供应商编码',
  `supplierContact` varchar(20) NOT NULL COMMENT '联系人',
  `supplierPhone` varchar(30) NOT NULL COMMENT '联系电话',
  `address` varchar(160) NOT NULL DEFAULT '' COMMENT '收货地址',
  `comment` varchar(250) NOT NULL DEFAULT '' COMMENT '订单备注',
  PRIMARY KEY (`purchaseId`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='采购单';

-- ----------------------------
-- Table structure for db_order_purchasing_detail
-- ----------------------------
DROP TABLE IF EXISTS `db_order_purchasing_detail`;
CREATE TABLE `db_order_purchasing_detail` (
  `detailId` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '明细编号',
  `purchaseId` int(10) unsigned NOT NULL COMMENT '采购单编号',
  `purchaseProId` int(10) unsigned NOT NULL COMMENT '采购单明细记录编号',
  `source` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单来源',
  `orderId` int(10) unsigned NOT NULL COMMENT '来源订单编号',
  `orderProId` int(10) unsigned NOT NULL COMMENT '来源订单明细编号',
  `quantity` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '采购数量',
  `comment` varchar(160) NOT NULL COMMENT '来源订单备注',
  PRIMARY KEY (`detailId`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='采购明细单产品关联信息';

-- ----------------------------
-- Table structure for db_order_purchasing_product
-- ----------------------------
DROP TABLE IF EXISTS `db_order_purchasing_product`;
CREATE TABLE `db_order_purchasing_product` (
  `purchaseProId` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '明细编号',
  `purchaseId` int(10) unsigned NOT NULL COMMENT '采购单编号',
  `quantity` decimal(10,2) NOT NULL COMMENT '采购数量',
  `deliveryDate` date NOT NULL COMMENT '交货时间',
  `supplierCode` varchar(20) NOT NULL COMMENT '革厂产品编号',
  `productCode` varchar(20) NOT NULL COMMENT '产品单品编码',
  `color` varchar(30) NOT NULL COMMENT '产品颜色',
  `comment` varchar(150) NOT NULL COMMENT '备注',
  PRIMARY KEY (`purchaseProId`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='采购订单明细';

-- ----------------------------
-- Table structure for db_order_purhasing_product
-- ----------------------------
DROP TABLE IF EXISTS `db_order_purhasing_product`;
CREATE TABLE `db_order_purhasing_product` (
  `purchaseProId` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '明细编号',
  `purchaseId` int(10) unsigned NOT NULL COMMENT '采购单编号',
  `quantity` int(10) NOT NULL COMMENT '采购数量',
  `deliveryDate` date NOT NULL COMMENT '交货时间',
  `supplierCode` varchar(20) NOT NULL COMMENT '革厂产品编号',
  `productCode` varchar(20) NOT NULL COMMENT '产品单品编码',
  `color` varchar(30) NOT NULL COMMENT '产品颜色',
  `comment` varchar(150) NOT NULL COMMENT '备注',
  PRIMARY KEY (`purchaseProId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='采购订单明细';

-- ----------------------------
-- Table structure for db_order_receipt
-- ----------------------------
DROP TABLE IF EXISTS `db_order_receipt`;
CREATE TABLE `db_order_receipt` (
  `receiptId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `orderId` bigint(10) unsigned NOT NULL COMMENT '订单ID',
  `productId` int(10) unsigned NOT NULL COMMENT '产品ID',
  `num` int(10) unsigned NOT NULL COMMENT '分拣数量',
  `userId` int(10) unsigned NOT NULL COMMENT '后台操作人userId',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '提交时间',
  PRIMARY KEY (`receiptId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='订单收货单';

-- ----------------------------
-- Table structure for db_order_refund
-- ----------------------------
DROP TABLE IF EXISTS `db_order_refund`;
CREATE TABLE `db_order_refund` (
  `refundId` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '退货单ID',
  `orderId` int(10) unsigned NOT NULL COMMENT '订单编号',
  `applyType` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '申请人类型：0客户，1业务员',
  `state` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '当前状态',
  `payModel` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '支付方式 ',
  `payState` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否已支付退款',
  `warrantId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '入库单ID',
  `realPayment` decimal(13,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '实付款',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '申请时间',
  `payTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '支付退款时间',
  `cause` varchar(255) NOT NULL COMMENT '退货理由',
  PRIMARY KEY (`refundId`),
  KEY `orderId` (`orderId`)
) ENGINE=InnoDB AUTO_INCREMENT=10010 DEFAULT CHARSET=utf8 COMMENT='订单退货信息表';

-- ----------------------------
-- Table structure for db_order_refund_product
-- ----------------------------
DROP TABLE IF EXISTS `db_order_refund_product`;
CREATE TABLE `db_order_refund_product` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `refundId` int(10) unsigned NOT NULL COMMENT '退货单ID',
  `orderId` int(10) unsigned NOT NULL COMMENT '订单编号',
  `orderProductId` int(10) unsigned NOT NULL,
  `productId` int(10) unsigned NOT NULL,
  `tailId` int(10) unsigned NOT NULL,
  `state` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '当前状态',
  `num` decimal(10,2) unsigned NOT NULL COMMENT '退货数量',
  `price` decimal(10,2) unsigned NOT NULL COMMENT '退货价格',
  `singleNumber` varchar(20) NOT NULL COMMENT '单品编码',
  `color` varchar(10) NOT NULL COMMENT '颜色',
  PRIMARY KEY (`id`),
  KEY `orderId` (`orderId`),
  KEY `refundId` (`refundId`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8 COMMENT='订单退货产品信息表';

-- ----------------------------
-- Table structure for db_order_settlement
-- ----------------------------
DROP TABLE IF EXISTS `db_order_settlement`;
CREATE TABLE `db_order_settlement` (
  `settlementId` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '结算单ID',
  `orderId` bigint(19) unsigned NOT NULL COMMENT '订单ID',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '单据来源：０前台业务员生成，１后台生成',
  `originatorId` int(10) unsigned NOT NULL COMMENT '制单人userId/memberId',
  `state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态：0未发货，1已发货',
  `warehouseId` int(10) unsigned NOT NULL COMMENT '发货仓库',
  `isDone` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否财务结算完成',
  `receipt` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '实际收款金额',
  `freight` decimal(7,2) NOT NULL DEFAULT '0.00' COMMENT '物流费',
  `productPayments` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '结算单商品总金额，不包含物流费',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '结算单生成时间',
  PRIMARY KEY (`settlementId`),
  KEY `orderId` (`orderId`)
) ENGINE=InnoDB AUTO_INCREMENT=10996 DEFAULT CHARSET=utf8 COMMENT='结算单';

-- ----------------------------
-- Table structure for db_order_settlement_detail
-- ----------------------------
DROP TABLE IF EXISTS `db_order_settlement_detail`;
CREATE TABLE `db_order_settlement_detail` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `settlementId` int(10) unsigned NOT NULL COMMENT '结算单ID',
  `orderProductId` int(10) unsigned NOT NULL,
  `num` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '实际结算数量',
  `isSample` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否样板',
  `remark` varchar(255) NOT NULL COMMENT '备注',
  PRIMARY KEY (`id`),
  KEY `settlementId` (`settlementId`)
) ENGINE=InnoDB AUTO_INCREMENT=12921 DEFAULT CHARSET=utf8 COMMENT='结算单明细';

-- ----------------------------
-- Table structure for db_order_settlement_month
-- ----------------------------
DROP TABLE IF EXISTS `db_order_settlement_month`;
CREATE TABLE `db_order_settlement_month` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '结算单ID',
  `month` date NOT NULL COMMENT '结算月份',
  `memberId` int(10) unsigned NOT NULL COMMENT '客户ID',
  `userId` int(10) unsigned NOT NULL COMMENT '业务员memberId',
  `isDone` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否财务结算完成',
  `receipt` decimal(15,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '实际收款金额',
  `payments` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT '结算单商品总金额，不包含物流费',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '结算单生成时间',
  PRIMARY KEY (`id`),
  KEY `orderId` (`month`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='结算单月对账单';

-- ----------------------------
-- Table structure for db_pack
-- ----------------------------
DROP TABLE IF EXISTS `db_pack`;
CREATE TABLE `db_pack` (
  `orderProductId` int(10) unsigned NOT NULL,
  `state` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '分拣状态',
  `orderId` int(10) unsigned NOT NULL,
  `productId` int(10) unsigned NOT NULL COMMENT '产品ID',
  `warehouseId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '分拣仓库ID',
  `positionId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '分区ID',
  `packUserId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '分拣员userId',
  `mergeUserId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '归单员userId',
  `num` decimal(10,2) NOT NULL COMMENT '需分拣数量',
  `packNum` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '实际分拣数量',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '加入分拣队列时间',
  `packTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '提交分拣时间',
  `mergeTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '归单时间',
  `singleNumber` varchar(30) NOT NULL COMMENT '单品编码',
  `color` varchar(20) NOT NULL COMMENT '颜色',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '分拣说明',
  PRIMARY KEY (`orderProductId`),
  KEY `warehouseId` (`warehouseId`),
  KEY `singleNumber` (`singleNumber`),
  KEY `productId` (`productId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='分拣信息表';

-- ----------------------------
-- Table structure for db_pack_detail
-- ----------------------------
DROP TABLE IF EXISTS `db_pack_detail`;
CREATE TABLE `db_pack_detail` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `orderProductId` int(10) unsigned NOT NULL COMMENT '订单明细ID',
  `positionId` int(10) unsigned NOT NULL COMMENT '仓位ID',
  `wholes` smallint(5) NOT NULL DEFAULT '0' COMMENT '整码数量：0为零码',
  `packingNum` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '分拣数量',
  `positionTitle` varchar(30) NOT NULL DEFAULT '' COMMENT '仓位名称',
  `productBatch` varchar(30) NOT NULL COMMENT '产品批次',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13224 DEFAULT CHARSET=utf8 COMMENT='新分拣单明细';

-- ----------------------------
-- Table structure for db_packing
-- ----------------------------
DROP TABLE IF EXISTS `db_packing`;
CREATE TABLE `db_packing` (
  `packingId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `distributionId` int(10) unsigned NOT NULL COMMENT '分配记录ID',
  `orderId` bigint(10) unsigned NOT NULL COMMENT '订单ID',
  `warehouseId` int(10) unsigned NOT NULL COMMENT '分拣仓库',
  `packingerId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '分配时选择的分拣员ID',
  `deliveryWarehouseId` int(10) unsigned NOT NULL COMMENT '发货仓库',
  `userId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '分拣操作人userId',
  `state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态：０待分拣，1已确认分拣,10订单取消',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '分拣单生成时间',
  `packingTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '确认分拣时间',
  `packingerName` varchar(30) NOT NULL DEFAULT '' COMMENT '分配时选择的分拣员姓名',
  PRIMARY KEY (`packingId`),
  KEY `orderId` (`orderId`)
) ENGINE=InnoDB AUTO_INCREMENT=11027 DEFAULT CHARSET=utf8 COMMENT='订单产品分拣单';

-- ----------------------------
-- Table structure for db_packing_detail
-- ----------------------------
DROP TABLE IF EXISTS `db_packing_detail`;
CREATE TABLE `db_packing_detail` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `packingId` int(10) unsigned NOT NULL COMMENT '分拣单ID',
  `orderProductId` int(10) unsigned NOT NULL COMMENT '订单明细ID',
  `productId` int(10) unsigned NOT NULL COMMENT '对应产品ID',
  `positionId` int(10) unsigned NOT NULL COMMENT '仓位ID',
  `unitRate` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '单位换算',
  `packingNum` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '分拣数量',
  `singleNumber` varchar(30) NOT NULL DEFAULT '' COMMENT '单品编码',
  `color` varchar(30) NOT NULL COMMENT '颜色',
  `positionTitle` varchar(30) NOT NULL COMMENT '仓位名称',
  `productBatch` varchar(30) NOT NULL COMMENT '产品批次',
  PRIMARY KEY (`id`),
  KEY `singleNumber` (`singleNumber`),
  KEY `packingId` (`packingId`)
) ENGINE=InnoDB AUTO_INCREMENT=13057 DEFAULT CHARSET=utf8 COMMENT='分拣单明细';

-- ----------------------------
-- Table structure for db_payment
-- ----------------------------
DROP TABLE IF EXISTS `db_payment`;
CREATE TABLE `db_payment` (
  `paymentId` int(10) NOT NULL AUTO_INCREMENT,
  `type` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '支付类型，0为支付类型，非0为支付方法，',
  `available` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否启用',
  `isonline` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `termType` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '使用类型',
  `paymentTitle` varchar(30) NOT NULL COMMENT '标题',
  `logo` varchar(100) NOT NULL DEFAULT '' COMMENT '支付方式LOGO',
  `paymentSet` varchar(255) NOT NULL DEFAULT '' COMMENT '配置信息',
  PRIMARY KEY (`paymentId`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COMMENT='支付方式表';

-- ----------------------------
-- Table structure for db_permission
-- ----------------------------
DROP TABLE IF EXISTS `db_permission`;
CREATE TABLE `db_permission` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `roleId` int(10) unsigned NOT NULL COMMENT '角色ID',
  `menuId` bigint(12) unsigned zerofill NOT NULL COMMENT '菜单ID',
  PRIMARY KEY (`id`),
  KEY `menuId` (`menuId`),
  KEY `roleId` (`roleId`)
) ENGINE=InnoDB AUTO_INCREMENT=1845 DEFAULT CHARSET=utf8 COMMENT='访问权限';

-- ----------------------------
-- Table structure for db_phone_log
-- ----------------------------
DROP TABLE IF EXISTS `db_phone_log`;
CREATE TABLE `db_phone_log` (
  `phonelogId` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `memberId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '会员ID',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '类型:0系统,1用户',
  `account` varchar(11) NOT NULL DEFAULT '' COMMENT '发送账号',
  `content` varchar(255) NOT NULL DEFAULT '' COMMENT '发送内容',
  `state` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态:0失败1:成功',
  `code` varchar(255) DEFAULT NULL COMMENT '返回CODE',
  `createTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`phonelogId`)
) ENGINE=InnoDB AUTO_INCREMENT=600 DEFAULT CHARSET=utf8 COMMENT='手机短信记录表';

-- ----------------------------
-- Table structure for db_piece
-- ----------------------------
DROP TABLE IF EXISTS `db_piece`;
CREATE TABLE `db_piece` (
  `pieceId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态：0正常，1删除',
  `parentId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上级分类ID',
  `updateTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '排序权重值',
  `title` varchar(100) NOT NULL COMMENT '分类名称',
  `mark` varchar(20) NOT NULL COMMENT '标识，标识具有唯一性',
  `content` text NOT NULL,
  PRIMARY KEY (`pieceId`),
  KEY `mark` (`mark`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COMMENT='页面碎片信息表';

-- ----------------------------
-- Table structure for db_points_detail
-- ----------------------------
DROP TABLE IF EXISTS `db_points_detail`;
CREATE TABLE `db_points_detail` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `create_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '时间',
  `points` int(11) NOT NULL COMMENT '消费，或者获取积分',
  `member_id` int(11) NOT NULL COMMENT '会员ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='积分流水';

-- ----------------------------
-- Table structure for db_points_member
-- ----------------------------
DROP TABLE IF EXISTS `db_points_member`;
CREATE TABLE `db_points_member` (
  `member_id` int(11) NOT NULL COMMENT '会员ID',
  `points` int(11) NOT NULL COMMENT '会员积分',
  `update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  `create_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '添加时间',
  PRIMARY KEY (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户积分表';

-- ----------------------------
-- Table structure for db_points_order
-- ----------------------------
DROP TABLE IF EXISTS `db_points_order`;
CREATE TABLE `db_points_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `order_sn` varchar(15) NOT NULL COMMENT '订单编号(时间戳+固长随机数)',
  `member_id` int(11) NOT NULL COMMENT '会员ID',
  `areaId` int(11) NOT NULL COMMENT '收货区域ID',
  `address` varchar(255) NOT NULL COMMENT '收货地址',
  `express_id` int(11) NOT NULL COMMENT '配送ID',
  `express_name` varchar(50) NOT NULL DEFAULT '自提' COMMENT '配送名称',
  `express_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '配送金额',
  `link_name` varchar(50) NOT NULL COMMENT '联系人',
  `link_phone` varchar(50) NOT NULL COMMENT '联系电话',
  `status` enum('1','2') NOT NULL DEFAULT '1' COMMENT '订单状态 1：待发货；2：待确认',
  `message` varchar(255) DEFAULT NULL COMMENT '留言信息',
  `sys_message` varchar(255) DEFAULT NULL COMMENT '管理员备注',
  `create_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '下单时间',
  `update_time` timestamp NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  `total_points` int(11) DEFAULT '0' COMMENT '订单积分',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='积分订单';

-- ----------------------------
-- Table structure for db_points_order_product
-- ----------------------------
DROP TABLE IF EXISTS `db_points_order_product`;
CREATE TABLE `db_points_order_product` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `product_id` int(11) NOT NULL COMMENT '产品ID',
  `order_id` int(11) NOT NULL COMMENT '订单ID',
  `num` int(11) NOT NULL COMMENT '产品数量',
  `default_image` varchar(255) NOT NULL COMMENT '产品默认图片',
  `spec` varchar(50) DEFAULT NULL COMMENT '规格',
  `points` int(11) NOT NULL COMMENT '单个产品所需积分',
  `title` varchar(255) NOT NULL COMMENT '产品标题',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='积分商城 - 订单产品';

-- ----------------------------
-- Table structure for db_points_product
-- ----------------------------
DROP TABLE IF EXISTS `db_points_product`;
CREATE TABLE `db_points_product` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID （积分产品ID）',
  `sys_product_id` int(11) NOT NULL COMMENT '系统产品ID',
  `points` int(11) DEFAULT NULL COMMENT '积分数',
  `default_image` varchar(255) NOT NULL COMMENT '默认图片',
  `images` varchar(255) NOT NULL COMMENT '所有图片，1.jpg;2.jpg',
  `spec` varchar(255) DEFAULT NULL COMMENT '规格, {“尺寸”:"XXL";"颜色":"红色"}',
  `describe` text NOT NULL COMMENT '描述',
  `title` varchar(255) NOT NULL COMMENT '标题',
  `stock` int(11) NOT NULL DEFAULT '999' COMMENT '库存量',
  `create_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '添加时间',
  `update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  `status` enum('1','2') NOT NULL DEFAULT '2' COMMENT '1:正常售卖，2：下架',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='积分产品';

-- ----------------------------
-- Table structure for db_print_order
-- ----------------------------
DROP TABLE IF EXISTS `db_print_order`;
CREATE TABLE `db_print_order` (
  `printId` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '打印单号',
  `orderId` int(10) NOT NULL COMMENT '订单编号',
  `saleOrderId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '销售订单ID',
  `custom_name` varchar(100) NOT NULL COMMENT '公司名称',
  `custom_phone` varchar(32) NOT NULL COMMENT '联系电话',
  `order_type` int(3) unsigned NOT NULL COMMENT '订单类型',
  `create_time` int(10) unsigned NOT NULL COMMENT '创建时间',
  `create_by` varchar(20) NOT NULL COMMENT '制单人',
  `delivery` varchar(30) NOT NULL DEFAULT '' COMMENT '提货方式',
  `hoseware` varchar(30) NOT NULL DEFAULT '' COMMENT '仓管',
  `cutting` varchar(30) NOT NULL DEFAULT '' COMMENT '剪料',
  `quality` varchar(30) NOT NULL DEFAULT '' COMMENT '质检',
  `payMethod` varchar(10) NOT NULL DEFAULT '' COMMENT '支付方式',
  `pushTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '推送打印时间',
  PRIMARY KEY (`printId`)
) ENGINE=InnoDB AUTO_INCREMENT=920 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for db_print_order_detail
-- ----------------------------
DROP TABLE IF EXISTS `db_print_order_detail`;
CREATE TABLE `db_print_order_detail` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `printId` int(10) unsigned NOT NULL COMMENT '打印单号',
  `orderId` int(10) unsigned NOT NULL COMMENT '订单编号',
  `product` varchar(32) NOT NULL COMMENT '产品编号',
  `total` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '数量',
  `detail` varchar(50) NOT NULL DEFAULT '' COMMENT '明细',
  `unit` varchar(10) NOT NULL DEFAULT '' COMMENT '单位',
  `price` decimal(10,4) NOT NULL DEFAULT '0.0000' COMMENT '单价',
  `subprice` decimal(10,4) NOT NULL DEFAULT '0.0000' COMMENT '金额',
  `mark` varchar(200) NOT NULL DEFAULT '' COMMENT '备注',
  `position` varchar(20) NOT NULL DEFAULT '' COMMENT '仓位',
  `batch` varchar(20) NOT NULL DEFAULT '' COMMENT '批次',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2488 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for db_print_tasklist
-- ----------------------------
DROP TABLE IF EXISTS `db_print_tasklist`;
CREATE TABLE `db_print_tasklist` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `printer` varchar(32) NOT NULL COMMENT '打印机编号',
  `printId` varchar(32) NOT NULL COMMENT '打印单号',
  `createTime` int(10) unsigned NOT NULL COMMENT '创建时间',
  `printed` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '已打印',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=876 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for db_printer
-- ----------------------------
DROP TABLE IF EXISTS `db_printer`;
CREATE TABLE `db_printer` (
  `printerId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `state` tinyint(1) unsigned NOT NULL COMMENT '状态：0正常，1停用',
  `printerSerial` varchar(20) NOT NULL COMMENT '打印机编号',
  `mark` varchar(100) NOT NULL COMMENT '备注说明',
  PRIMARY KEY (`printerId`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='打印机列表';

-- ----------------------------
-- Table structure for db_procurement
-- ----------------------------
DROP TABLE IF EXISTS `db_procurement`;
CREATE TABLE `db_procurement` (
  `productId` int(10) unsigned NOT NULL COMMENT '产品ID',
  `supplierId` int(10) unsigned NOT NULL COMMENT '供应商,厂家ＩＤ',
  `price` decimal(7,2) unsigned NOT NULL COMMENT '采购价格',
  `supplierSerialnumber` varchar(20) NOT NULL COMMENT '供应商对应的产品编号',
  PRIMARY KEY (`productId`),
  KEY `corpId` (`supplierId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='产品采购信息';

-- ----------------------------
-- Table structure for db_product
-- ----------------------------
DROP TABLE IF EXISTS `db_product`;
CREATE TABLE `db_product` (
  `productId` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '产品ID',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '产品类型。默认0为主产品，1为附属产品',
  `baseProductId` int(11) NOT NULL DEFAULT '0' COMMENT '主产品ID，针对附属工艺产品',
  `categoryId` int(10) unsigned NOT NULL COMMENT '产品分类',
  `state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '产品状态: 0:销售中，1:已下架，仓库中，3:删除，回收站',
  `unitId` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '计量单位',
  `auxiliaryUnit` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '辅助单位',
  `unitConversion` float NOT NULL DEFAULT '0' COMMENT '单位换算量',
  `expressId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '运费模板ID',
  `salesVolume` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '销售量',
  `custemTime` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否自定义发布时间',
  `price` decimal(7,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '默认价格，散剪价',
  `tradePrice` decimal(7,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '大货价',
  `unitWeight` decimal(7,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '单位重量',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updateTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新时间',
  `publishTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '上架时间(定时发布)',
  `salesTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '上架时间',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '产品标题',
  `serialNumber` varchar(100) NOT NULL COMMENT '产品编号',
  `mainPic` varchar(200) NOT NULL DEFAULT '' COMMENT '产品主图',
  PRIMARY KEY (`productId`),
  UNIQUE KEY `serialNumber` (`serialNumber`),
  KEY `fk_category_idx` (`categoryId`),
  KEY `productDetailId` (`baseProductId`)
) ENGINE=InnoDB AUTO_INCREMENT=98 DEFAULT CHARSET=utf8 COMMENT='产品基本信息';

-- ----------------------------
-- Table structure for db_product_attribute
-- ----------------------------
DROP TABLE IF EXISTS `db_product_attribute`;
CREATE TABLE `db_product_attribute` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `productId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '产品ID',
  `attrId` int(10) unsigned NOT NULL COMMENT '属性标题ID',
  `title` varchar(30) NOT NULL DEFAULT '' COMMENT '属性名称',
  `attrValue` varchar(255) NOT NULL COMMENT '选中的属性值',
  PRIMARY KEY (`id`),
  KEY `productId` (`productId`)
) ENGINE=InnoDB AUTO_INCREMENT=878 DEFAULT CHARSET=utf8 COMMENT='商品属性表';

-- ----------------------------
-- Table structure for db_product_collection
-- ----------------------------
DROP TABLE IF EXISTS `db_product_collection`;
CREATE TABLE `db_product_collection` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `productId` int(10) unsigned NOT NULL COMMENT '产品ID',
  `memberId` int(10) unsigned NOT NULL COMMENT '客户ID',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `memberId` (`memberId`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='商品收藏记录';

-- ----------------------------
-- Table structure for db_product_craft
-- ----------------------------
DROP TABLE IF EXISTS `db_product_craft`;
CREATE TABLE `db_product_craft` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `productId` int(10) unsigned NOT NULL COMMENT '产品ID',
  `craftCode` varchar(10) NOT NULL COMMENT '特殊工艺编号',
  PRIMARY KEY (`id`),
  KEY `productId` (`productId`),
  KEY `craftCode` (`craftCode`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8 COMMENT='产品特殊工艺';

-- ----------------------------
-- Table structure for db_product_detail
-- ----------------------------
DROP TABLE IF EXISTS `db_product_detail`;
CREATE TABLE `db_product_detail` (
  `productId` int(10) unsigned NOT NULL COMMENT '产品ID',
  `expressId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '运费模板ID',
  `testResults` text NOT NULL COMMENT '产品测试',
  `content` text NOT NULL COMMENT '产品描述',
  `phoneContent` text NOT NULL COMMENT '手机端产品描述',
  `pictures` blob NOT NULL COMMENT '产品图片JSON数据',
  PRIMARY KEY (`productId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='产品描述';

-- ----------------------------
-- Table structure for db_product_feature
-- ----------------------------
DROP TABLE IF EXISTS `db_product_feature`;
CREATE TABLE `db_product_feature` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `productId` int(10) unsigned NOT NULL COMMENT '产品编号',
  `url` varchar(200) NOT NULL COMMENT '图片URL地址',
  `feature` mediumblob COMMENT '图片特征数据',
  `update` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否更新特征数据',
  PRIMARY KEY (`id`),
  KEY `idx_productId` (`productId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='图片搜索特征库';

-- ----------------------------
-- Table structure for db_product_packing
-- ----------------------------
DROP TABLE IF EXISTS `db_product_packing`;
CREATE TABLE `db_product_packing` (
  `productId` int(10) unsigned NOT NULL COMMENT '产品ID',
  `warehouseId` int(10) unsigned NOT NULL COMMENT '仓库ID',
  `positionId` int(10) unsigned NOT NULL COMMENT '区域ID',
  PRIMARY KEY (`productId`,`warehouseId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='产品默认分拣区域';

-- ----------------------------
-- Table structure for db_product_sound
-- ----------------------------
DROP TABLE IF EXISTS `db_product_sound`;
CREATE TABLE `db_product_sound` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '编号',
  `productId` int(10) unsigned NOT NULL COMMENT '产品编号',
  `isMain` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否主文件',
  `sort` int(3) unsigned DEFAULT '0' COMMENT '排序',
  `isDel` tinyint(1) unsigned DEFAULT '0' COMMENT '已删除',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '上传时间',
  `updateTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '最后更新时间',
  `sound` varchar(200) NOT NULL COMMENT '声音文件',
  `title` varchar(255) NOT NULL COMMENT '文件标题，后台编辑的',
  PRIMARY KEY (`id`),
  KEY `productId` (`productId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='产品描述声音文件';

-- ----------------------------
-- Table structure for db_product_spec
-- ----------------------------
DROP TABLE IF EXISTS `db_product_spec`;
CREATE TABLE `db_product_spec` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `productId` int(10) unsigned NOT NULL COMMENT '产品ID',
  `specId` int(10) unsigned NOT NULL COMMENT '规格ID',
  `specvalueId` int(10) unsigned NOT NULL COMMENT '规格值ID',
  `specValue` varchar(100) NOT NULL DEFAULT '' COMMENT '实际提交规格值',
  `picture` varchar(100) NOT NULL DEFAULT '' COMMENT '规格图片',
  PRIMARY KEY (`id`),
  KEY `productId` (`productId`)
) ENGINE=InnoDB AUTO_INCREMENT=2944 DEFAULT CHARSET=utf8 COMMENT='产品规格数据';

-- ----------------------------
-- Table structure for db_product_stock
-- ----------------------------
DROP TABLE IF EXISTS `db_product_stock`;
CREATE TABLE `db_product_stock` (
  `stockId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `productId` int(10) unsigned NOT NULL COMMENT '产品ID',
  `state` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态：0正常，1已取消',
  `depositRatio` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '支付订金比例，为百分比',
  `adjustRatio` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '调整单比例，为千分比',
  `glassTime` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '默认呆滞时长，单位为小时',
  `safetyStock` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '安全库存',
  `lastSaleTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '最后销售时间',
  `singleNumber` varchar(20) NOT NULL DEFAULT '' COMMENT '单品编码',
  `relation` varchar(100) NOT NULL COMMENT '产品规格组合',
  PRIMARY KEY (`stockId`),
  UNIQUE KEY `singleNumber` (`singleNumber`),
  KEY `productId` (`productId`)
) ENGINE=InnoDB AUTO_INCREMENT=2942 DEFAULT CHARSET=utf8 COMMENT='产品规格的库存和价格';

-- ----------------------------
-- Table structure for db_product_video
-- ----------------------------
DROP TABLE IF EXISTS `db_product_video`;
CREATE TABLE `db_product_video` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '编号',
  `productId` int(10) unsigned NOT NULL COMMENT '产品编号',
  `isMain` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否主文件',
  `sort` int(3) unsigned DEFAULT '0' COMMENT '排序',
  `isDel` tinyint(1) unsigned DEFAULT '0' COMMENT '已删除',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '上传时间',
  `updateTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '最后更新时间',
  `video` varchar(200) NOT NULL COMMENT '视频文件',
  `title` varchar(255) NOT NULL COMMENT '文件标题，后台编辑的',
  PRIMARY KEY (`id`),
  KEY `productId` (`productId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='产品描述视频文件';

-- ----------------------------
-- Table structure for db_product_view
-- ----------------------------
DROP TABLE IF EXISTS `db_product_view`;
CREATE TABLE `db_product_view` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `productId` int(10) unsigned NOT NULL COMMENT '产品ID',
  `memberId` int(10) unsigned NOT NULL COMMENT '客户ID',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `memberId` (`memberId`)
) ENGINE=InnoDB AUTO_INCREMENT=2307 DEFAULT CHARSET=utf8 COMMENT='商品浏览记录';

-- ----------------------------
-- Table structure for db_profile
-- ----------------------------
DROP TABLE IF EXISTS `db_profile`;
CREATE TABLE `db_profile` (
  `memberId` int(10) unsigned NOT NULL,
  `sex` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '性别：0男,1女',
  `sortingWarehouseId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '分拣仓库ID',
  `username` varchar(20) NOT NULL DEFAULT '' COMMENT '用户名',
  `icon` varchar(100) NOT NULL DEFAULT '' COMMENT '用户头像',
  `qq` varchar(20) NOT NULL DEFAULT '',
  `birthdate` date NOT NULL DEFAULT '0000-00-00' COMMENT '生日',
  PRIMARY KEY (`memberId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for db_profile_detail
-- ----------------------------
DROP TABLE IF EXISTS `db_profile_detail`;
CREATE TABLE `db_profile_detail` (
  `memberId` int(10) unsigned NOT NULL COMMENT '会员ID',
  `stalls` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '有无档口：1有,2无',
  `factory` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '有无工厂：1有,2无',
  `factoryatt` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '工厂属性:1自建,2购买,3租赁',
  `companytype` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '公司性质：1自产自销,2贸易型,3生产型',
  `saleregion` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '销售区域：1内销,2外销',
  `areaId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '省市ID',
  `peoplenumber` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '生产人数',
  `outputvalue` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '年产出',
  `tel` varchar(15) NOT NULL DEFAULT '' COMMENT '电话',
  `shortname` varchar(30) NOT NULL DEFAULT '' COMMENT '公司简称',
  `brand` varchar(20) NOT NULL DEFAULT '' COMMENT '品牌',
  `corporate` varchar(20) NOT NULL DEFAULT '' COMMENT '企业法人',
  `companyname` varchar(255) NOT NULL DEFAULT '' COMMENT '公司名称',
  `mainproduct` varchar(60) NOT NULL DEFAULT '' COMMENT '主营产品：1钱包,2男包,3女包,4其它',
  `gm` varchar(120) NOT NULL DEFAULT '' COMMENT '总经理',
  `pdm` varchar(120) NOT NULL DEFAULT '' COMMENT '采购经理',
  `designers` varchar(120) NOT NULL DEFAULT '' COMMENT '设计人员',
  `cfo` varchar(120) NOT NULL DEFAULT '' COMMENT '财务经理',
  `address` varchar(255) NOT NULL DEFAULT '' COMMENT '详细地址',
  `stallsaddress` varchar(255) NOT NULL DEFAULT '' COMMENT '档口地址',
  PRIMARY KEY (`memberId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='客户信息详情';

-- ----------------------------
-- Table structure for db_purchasing_close
-- ----------------------------
DROP TABLE IF EXISTS `db_purchasing_close`;
CREATE TABLE `db_purchasing_close` (
  `purchaseId` bigint(19) unsigned NOT NULL COMMENT '订单ID',
  `opId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '操作者ID',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '取消时间',
  `reason` varchar(255) NOT NULL COMMENT '取消理由',
  PRIMARY KEY (`purchaseId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='采购单取消操作记录表';

-- ----------------------------
-- Table structure for db_recommend
-- ----------------------------
DROP TABLE IF EXISTS `db_recommend`;
CREATE TABLE `db_recommend` (
  `recommendId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '类型：0页面，1推荐位',
  `state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态：0正常，1删除',
  `parentId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上级分类ID',
  `maxNum` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '最大允许推荐数量',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updateTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `title` varchar(100) NOT NULL COMMENT '名称',
  `mark` varchar(30) NOT NULL DEFAULT '' COMMENT '标识，不能重复且必须是英文字符',
  PRIMARY KEY (`recommendId`),
  KEY `mark` (`mark`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COMMENT='产品推荐_页面/推荐位';

-- ----------------------------
-- Table structure for db_recommend_product
-- ----------------------------
DROP TABLE IF EXISTS `db_recommend_product`;
CREATE TABLE `db_recommend_product` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `recommendId` int(10) unsigned NOT NULL COMMENT '推荐位ID',
  `productId` int(10) unsigned NOT NULL COMMENT '产品ID',
  `listOrder` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序值,从小到大排序',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `recommendId` (`recommendId`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COMMENT='产品推荐';

-- ----------------------------
-- Table structure for db_recommend_voice
-- ----------------------------
DROP TABLE IF EXISTS `db_recommend_voice`;
CREATE TABLE `db_recommend_voice` (
  `recommendId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态：0正常，1删除',
  `maxNum` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '最大允许推荐数量',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updateTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `identity` varchar(20) NOT NULL COMMENT '标识，不能重复且必须是英文字符',
  `title` varchar(100) NOT NULL COMMENT '名称',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  PRIMARY KEY (`recommendId`),
  UNIQUE KEY `code` (`identity`),
  KEY `mark` (`remark`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='产品语音推荐';

-- ----------------------------
-- Table structure for db_recommend_voice_product
-- ----------------------------
DROP TABLE IF EXISTS `db_recommend_voice_product`;
CREATE TABLE `db_recommend_voice_product` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `recommendId` int(10) unsigned NOT NULL COMMENT '推荐位ID',
  `productId` int(10) unsigned NOT NULL COMMENT '产品ID',
  `listOrder` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序值,从小到大排序',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `recommendId` (`recommendId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='产品推荐';

-- ----------------------------
-- Table structure for db_request_buy
-- ----------------------------
DROP TABLE IF EXISTS `db_request_buy`;
CREATE TABLE `db_request_buy` (
  `orderId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `typeId` int(5) unsigned NOT NULL COMMENT '请购单类型 0:内部请购 1:低安全库存 2:客户订单',
  `state` tinyint(2) unsigned NOT NULL COMMENT '请购单状态 0:新建 1:通过审核 2:等待采购 3:采购中 4:已采购 5:已关闭 6:删除',
  `userId` int(10) unsigned NOT NULL COMMENT '用户编号',
  `createTime` int(10) unsigned NOT NULL COMMENT '创建订单时间',
  `updateTime` int(10) NOT NULL COMMENT '更新时间',
  `userName` varchar(20) NOT NULL COMMENT '用户名称',
  `cause` varchar(200) NOT NULL COMMENT '请购原因',
  `comment` varchar(200) NOT NULL DEFAULT '' COMMENT '备注',
  `closeCause` varchar(200) NOT NULL DEFAULT '' COMMENT '关闭理由',
  PRIMARY KEY (`orderId`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for db_request_buy_op
-- ----------------------------
DROP TABLE IF EXISTS `db_request_buy_op`;
CREATE TABLE `db_request_buy_op` (
  `orderId` int(10) unsigned NOT NULL,
  `userId` int(10) unsigned NOT NULL COMMENT '用户编号',
  `createTime` int(10) unsigned NOT NULL COMMENT '操作订单时间',
  `code` varchar(10) NOT NULL COMMENT '操作编码',
  `remark` varchar(200) NOT NULL COMMENT '备注说明',
  KEY `orderId` (`orderId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='请购单操作日志';

-- ----------------------------
-- Table structure for db_request_buy_product
-- ----------------------------
DROP TABLE IF EXISTS `db_request_buy_product`;
CREATE TABLE `db_request_buy_product` (
  `requestProductId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `from` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '来源 0:内部请购 1:低安全库存 2:客户订单',
  `productId` int(10) unsigned NOT NULL COMMENT '对应产品ID',
  `orderId` char(14) NOT NULL COMMENT '请购单编号',
  `total` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '请购数量',
  `createTime` int(10) unsigned NOT NULL COMMENT '创建时间',
  `dealTime` int(10) unsigned NOT NULL COMMENT '交付时间',
  `state` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '请购单状态 0:新建 1:通过审核 2:等待采购 3:采购中 4:已采购 5:已关闭 6:删除',
  `unitName` varchar(10) NOT NULL DEFAULT '' COMMENT '单位',
  `singleNumber` varchar(20) NOT NULL COMMENT '产品编号',
  `color` varchar(20) NOT NULL COMMENT '颜色',
  `comment` varchar(100) NOT NULL DEFAULT '' COMMENT '备注',
  PRIMARY KEY (`requestProductId`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for db_request_lower
-- ----------------------------
DROP TABLE IF EXISTS `db_request_lower`;
CREATE TABLE `db_request_lower` (
  `lowerId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `state` tinyint(2) unsigned NOT NULL COMMENT '状态 0:默认值 1:通过审核 2:等待采购 3:采购中 4:已采购 5:关闭 6:删除',
  `buyTotal` int(5) unsigned NOT NULL COMMENT '采购数量',
  `createTime` int(10) unsigned NOT NULL COMMENT '创建订单时间',
  `singleNumber` varchar(20) NOT NULL COMMENT '单品编码',
  `color` varchar(20) NOT NULL COMMENT '产品颜色',
  PRIMARY KEY (`lowerId`)
) ENGINE=InnoDB AUTO_INCREMENT=10064 DEFAULT CHARSET=utf8 COMMENT='低安全库存采购清单';

-- ----------------------------
-- Table structure for db_restful_account
-- ----------------------------
DROP TABLE IF EXISTS `db_restful_account`;
CREATE TABLE `db_restful_account` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `server` varchar(20) NOT NULL COMMENT '服务机构',
  `account` varchar(20) NOT NULL COMMENT '帐号',
  `password` char(32) NOT NULL COMMENT '密码',
  `isEnable` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否可用',
  `salt` varchar(10) NOT NULL COMMENT '加密盐',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '创建时间',
  `expires` datetime NOT NULL COMMENT '过期时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `account` (`account`)
) ENGINE=InnoDB AUTO_INCREMENT=1003 DEFAULT CHARSET=utf8 COMMENT='API接口帐号管理';

-- ----------------------------
-- Table structure for db_role
-- ----------------------------
DROP TABLE IF EXISTS `db_role`;
CREATE TABLE `db_role` (
  `roleId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '标删，0正常，1删除',
  `departmentId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '所属部门ID',
  `roleName` varchar(30) NOT NULL COMMENT '角色名称',
  `description` varchar(200) NOT NULL DEFAULT '' COMMENT '描述',
  PRIMARY KEY (`roleId`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8 COMMENT='角色组';

-- ----------------------------
-- Table structure for db_role_group
-- ----------------------------
DROP TABLE IF EXISTS `db_role_group`;
CREATE TABLE `db_role_group` (
  `groupId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '标删，0正常，1删除',
  `roleId` int(10) unsigned NOT NULL COMMENT '角色ID',
  `departmentId` int(10) unsigned NOT NULL COMMENT '所属部门ID',
  `deppositionId` int(10) unsigned NOT NULL COMMENT '职位ID',
  `taskId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '新版本将删除',
  PRIMARY KEY (`groupId`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8 COMMENT='用户角色组';

-- ----------------------------
-- Table structure for db_role_map
-- ----------------------------
DROP TABLE IF EXISTS `db_role_map`;
CREATE TABLE `db_role_map` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `groupId` int(10) unsigned NOT NULL COMMENT '角色组ID',
  `roleId` int(10) unsigned NOT NULL COMMENT '角色ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for db_seller_sales
-- ----------------------------
DROP TABLE IF EXISTS `db_seller_sales`;
CREATE TABLE `db_seller_sales` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `supplierId` int(10) unsigned NOT NULL COMMENT '供应商',
  `datetime` date NOT NULL COMMENT '日期',
  `serial` varchar(50) NOT NULL COMMENT '产品编码',
  `total` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '销售量',
  `price` decimal(10,4) unsigned NOT NULL DEFAULT '0.0000' COMMENT '单价',
  `subtotal` decimal(10,4) unsigned NOT NULL DEFAULT '0.0000' COMMENT '小计',
  `color` varchar(30) NOT NULL COMMENT '颜色',
  `singleNumber` varchar(50) NOT NULL COMMENT '单品编号',
  PRIMARY KEY (`id`),
  KEY `supplierId` (`supplierId`,`datetime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='供应商销售量单日统计';

-- ----------------------------
-- Table structure for db_set_group
-- ----------------------------
DROP TABLE IF EXISTS `db_set_group`;
CREATE TABLE `db_set_group` (
  `setGroupId` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '组ID',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1:属性组 2:色系',
  `listOrder` int(10) unsigned NOT NULL DEFAULT '100' COMMENT '排序值',
  `title` varchar(30) NOT NULL COMMENT '名称',
  PRIMARY KEY (`setGroupId`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COMMENT='预定义规格属性组';

-- ----------------------------
-- Table structure for db_setting
-- ----------------------------
DROP TABLE IF EXISTS `db_setting`;
CREATE TABLE `db_setting` (
  `setId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `variable` varchar(100) NOT NULL COMMENT '变量名',
  `title` varchar(100) NOT NULL COMMENT '参数名称',
  `setValue` varchar(255) NOT NULL DEFAULT '' COMMENT '参数值',
  `unit` varchar(20) NOT NULL DEFAULT '' COMMENT '单位',
  PRIMARY KEY (`setId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='配置设置';

-- ----------------------------
-- Table structure for db_spec
-- ----------------------------
DROP TABLE IF EXISTS `db_spec`;
CREATE TABLE `db_spec` (
  `specId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `isColor` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否颜色，是则值关联颜色系列',
  `isPicture` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否有规格图片',
  `state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态：0正常，1删除',
  `specName` varchar(60) NOT NULL COMMENT '规格名称',
  PRIMARY KEY (`specId`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='规格列表';

-- ----------------------------
-- Table structure for db_specvalue
-- ----------------------------
DROP TABLE IF EXISTS `db_specvalue`;
CREATE TABLE `db_specvalue` (
  `specvalueId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `specId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '规格ID',
  `colorSeriesId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '颜色系列ID',
  `hasProduct` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否有关联产品',
  `title` varchar(30) NOT NULL COMMENT '名称',
  `code` varchar(30) NOT NULL COMMENT '值',
  `serialNumber` varchar(30) NOT NULL COMMENT '编号',
  PRIMARY KEY (`specvalueId`),
  KEY `specId` (`specId`)
) ENGINE=InnoDB AUTO_INCREMENT=397 DEFAULT CHARSET=utf8 COMMENT='规格值';

-- ----------------------------
-- Table structure for db_sphinx_product
-- ----------------------------
DROP TABLE IF EXISTS `db_sphinx_product`;
CREATE TABLE `db_sphinx_product` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `ranking` int(10) NOT NULL DEFAULT '0' COMMENT '综合排名指数',
  `productId` int(10) NOT NULL COMMENT '产品ID',
  `saleType` int(1) DEFAULT '0' COMMENT '销售类型',
  `lft` int(10) unsigned DEFAULT NULL COMMENT '左值',
  `rft` int(10) unsigned DEFAULT NULL COMMENT '右值',
  `attrId` varchar(200) DEFAULT NULL COMMENT '属性',
  `attrValue` varchar(200) DEFAULT NULL COMMENT '属性值',
  `saleTime` int(10) unsigned NOT NULL COMMENT '上架时间',
  `saleVolume` int(10) NOT NULL COMMENT '销售量',
  `price` int(10) NOT NULL COMMENT '价格',
  `serial` varchar(30) NOT NULL COMMENT '单品编码',
  `picture` varchar(50) DEFAULT NULL COMMENT '图片',
  `title` varchar(100) NOT NULL COMMENT '产品标题',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=131 DEFAULT CHARSET=utf8 COMMENT='产品索引表';

-- ----------------------------
-- Table structure for db_sphinx_setp
-- ----------------------------
DROP TABLE IF EXISTS `db_sphinx_setp`;
CREATE TABLE `db_sphinx_setp` (
  `TableID` int(4) unsigned NOT NULL AUTO_INCREMENT COMMENT '数据表ID',
  `MaxID` int(10) unsigned NOT NULL COMMENT '更新ID',
  PRIMARY KEY (`TableID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='索引更新记录';

-- ----------------------------
-- Table structure for db_stocktaking
-- ----------------------------
DROP TABLE IF EXISTS `db_stocktaking`;
CREATE TABLE `db_stocktaking` (
  `stocktakingId` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '盘点单ID',
  `warehouseId` int(10) unsigned NOT NULL COMMENT '盘点仓库ID',
  `productId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '产品ID',
  `userId` int(10) unsigned NOT NULL COMMENT '制单人userId',
  `checkUserId` int(10) unsigned NOT NULL DEFAULT '0',
  `state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态：0未确认，1 取消盘点，2已保存',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '调拨时间',
  `updateTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP COMMENT '最后更新时间',
  `serialNumber` varchar(30) NOT NULL COMMENT '产品编码',
  `userName` varchar(30) NOT NULL COMMENT '盘点人',
  `checkUser` varchar(30) NOT NULL COMMENT '审核人',
  `takinger` varchar(30) NOT NULL DEFAULT '' COMMENT '盘点人员',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  PRIMARY KEY (`stocktakingId`),
  KEY `serialNumber` (`serialNumber`),
  KEY `productId` (`productId`)
) ENGINE=InnoDB AUTO_INCREMENT=70 DEFAULT CHARSET=utf8 COMMENT='盘点单';

-- ----------------------------
-- Table structure for db_stocktaking_count
-- ----------------------------
DROP TABLE IF EXISTS `db_stocktaking_count`;
CREATE TABLE `db_stocktaking_count` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `stocktakingId` int(10) unsigned NOT NULL COMMENT '盘点单ID',
  `warehouseId` int(10) unsigned NOT NULL COMMENT '仓库ID',
  `productId` int(10) unsigned NOT NULL COMMENT '产品ID',
  `num` decimal(11,2) unsigned NOT NULL COMMENT '盘点数量',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '计算时间',
  `singleNumber` varchar(30) NOT NULL COMMENT '单品编码',
  PRIMARY KEY (`id`),
  KEY `warehouseId` (`warehouseId`),
  KEY `singleNumber` (`singleNumber`)
) ENGINE=InnoDB AUTO_INCREMENT=1346 DEFAULT CHARSET=utf8 COMMENT='盘点单按单品计数';

-- ----------------------------
-- Table structure for db_stocktaking_detail
-- ----------------------------
DROP TABLE IF EXISTS `db_stocktaking_detail`;
CREATE TABLE `db_stocktaking_detail` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `stocktakingId` int(10) unsigned NOT NULL COMMENT '盘点单ID',
  `productId` int(10) unsigned NOT NULL COMMENT '产品ID',
  `positionId` int(10) unsigned NOT NULL COMMENT '仓位ID',
  `num` decimal(11,2) unsigned NOT NULL COMMENT '盘点数量',
  `oldNum` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '原库存',
  `state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '对比结果',
  `singleNumber` varchar(30) NOT NULL COMMENT '单品编码',
  `unit` varchar(10) NOT NULL DEFAULT '' COMMENT '产品单位',
  `productBatch` varchar(30) NOT NULL COMMENT '产品批次',
  `color` varchar(20) NOT NULL COMMENT '颜色',
  `positionTitle` varchar(20) NOT NULL DEFAULT '' COMMENT '仓位名称 ',
  PRIMARY KEY (`id`),
  KEY `allocationId` (`stocktakingId`)
) ENGINE=InnoDB AUTO_INCREMENT=9417 DEFAULT CHARSET=utf8 COMMENT='盘点单明细';

-- ----------------------------
-- Table structure for db_storage_lock
-- ----------------------------
DROP TABLE IF EXISTS `db_storage_lock`;
CREATE TABLE `db_storage_lock` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `orderId` char(14) NOT NULL COMMENT '订单编号',
  `singleNumber` varchar(30) NOT NULL COMMENT '单品编码',
  `total` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '锁定数量',
  `createTime` int(10) unsigned NOT NULL COMMENT '创建锁定时间',
  PRIMARY KEY (`id`),
  KEY `orderId` (`orderId`)
) ENGINE=InnoDB AUTO_INCREMENT=3340 DEFAULT CHARSET=utf8 COMMENT='产品销售量锁定';

-- ----------------------------
-- Table structure for db_supplier
-- ----------------------------
DROP TABLE IF EXISTS `db_supplier`;
CREATE TABLE `db_supplier` (
  `supplierId` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '供应商，厂家ＩＤ',
  `state` int(1) NOT NULL DEFAULT '0' COMMENT '状态：0正常，删除',
  `factoryNumber` varchar(20) NOT NULL COMMENT '革厂编号',
  `shortname` varchar(150) NOT NULL COMMENT '厂家简称',
  `contact` varchar(10) NOT NULL DEFAULT '' COMMENT '联系人',
  `phone` varchar(20) NOT NULL DEFAULT '' COMMENT '联系电话',
  `adddress` varchar(255) NOT NULL DEFAULT '' COMMENT '联系地址',
  PRIMARY KEY (`supplierId`),
  UNIQUE KEY `factoryNumber` (`factoryNumber`),
  KEY `shortname` (`shortname`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COMMENT='供应商，厂家信息';

-- ----------------------------
-- Table structure for db_supplier_account
-- ----------------------------
DROP TABLE IF EXISTS `db_supplier_account`;
CREATE TABLE `db_supplier_account` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '账号ID',
  `supplierId` int(10) unsigned NOT NULL COMMENT '供应商编号',
  `disabled` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '禁止登陆',
  `account` varchar(20) NOT NULL COMMENT '登陆帐号',
  `password` varchar(45) NOT NULL COMMENT '登陆密码',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='供应商登陆帐号';

-- ----------------------------
-- Table structure for db_sysmenu
-- ----------------------------
DROP TABLE IF EXISTS `db_sysmenu`;
CREATE TABLE `db_sysmenu` (
  `id` bigint(12) unsigned zerofill NOT NULL COMMENT '菜单编号',
  `fatherId` bigint(12) unsigned zerofill NOT NULL DEFAULT '000000000000' COMMENT '父节点',
  `hidden` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否隐藏',
  `type` enum('navigate','group','menu','action') NOT NULL COMMENT '菜单类型',
  `title` varchar(30) NOT NULL COMMENT '菜单内容',
  `url` varchar(100) NOT NULL DEFAULT '' COMMENT '菜单链接',
  `route` varchar(100) NOT NULL DEFAULT '' COMMENT '菜单路由',
  `sortNum` int(10) NOT NULL DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='系统菜单';

-- ----------------------------
-- Table structure for db_sysmenu_count
-- ----------------------------
DROP TABLE IF EXISTS `db_sysmenu_count`;
CREATE TABLE `db_sysmenu_count` (
  `id` bigint(12) unsigned zerofill NOT NULL COMMENT '菜单ID，关联表db_sysmenu',
  `isCount` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否用调用方法去计算',
  `totalNum` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '统计数值',
  `groupName` enum('order','customer','purchase','warehouse','inquiry','factory') NOT NULL COMMENT '所属显示组',
  `title` varchar(20) NOT NULL,
  `route` varchar(100) NOT NULL,
  `funcName` varchar(30) NOT NULL COMMENT 'WorkCount类中对应的方法名',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='管理后台工作台统计数据';

-- ----------------------------
-- Table structure for db_tail
-- ----------------------------
DROP TABLE IF EXISTS `db_tail`;
CREATE TABLE `db_tail` (
  `tailId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `source` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '尾货产品生成来源：1.由呆滞报表生成，2.直接发布成尾货',
  `isSoldOut` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否已售完',
  `productId` int(10) unsigned NOT NULL COMMENT '产品ID',
  `price` decimal(9,2) unsigned NOT NULL COMMENT '单价',
  `tradePrice` decimal(9,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '大货价',
  `salesVolume` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '销售数量',
  `saleType` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '销售类型，零售和整批销售',
  `state` enum('selling','underShelf','del','recycledel') NOT NULL DEFAULT 'selling' COMMENT '产品状态:1:销售中，2:已下架，3:已删除',
  `createTime` int(10) unsigned NOT NULL COMMENT '尾货生成时间',
  `updateTime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后编辑时间',
  PRIMARY KEY (`tailId`)
) ENGINE=InnoDB AUTO_INCREMENT=99 DEFAULT CHARSET=utf8 COMMENT='尾货产品列表';

-- ----------------------------
-- Table structure for db_tail_single
-- ----------------------------
DROP TABLE IF EXISTS `db_tail_single`;
CREATE TABLE `db_tail_single` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tailId` int(10) unsigned NOT NULL COMMENT '尾货产品编号',
  `state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '产品状态:0:正常，1:已删除',
  `createTime` int(10) unsigned NOT NULL COMMENT '尾货生成时间',
  `updateTime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后编辑时间',
  `lastSaleTime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后销售时间',
  `singleNumber` varchar(20) NOT NULL COMMENT '单品编码',
  PRIMARY KEY (`id`),
  KEY `singleNumber` (`singleNumber`),
  KEY `tailId` (`tailId`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='尾货产品明细';

-- ----------------------------
-- Table structure for db_task
-- ----------------------------
DROP TABLE IF EXISTS `db_task`;
CREATE TABLE `db_task` (
  `taskId` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '任务ID',
  `type` enum('module','controller','action') DEFAULT 'action' COMMENT '类型',
  `parentId` int(10) unsigned NOT NULL COMMENT '父级ID',
  `taskName` varchar(20) NOT NULL COMMENT '任务名称',
  `taskRoute` varchar(50) NOT NULL COMMENT '任务路由',
  `orderList` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序值',
  PRIMARY KEY (`taskId`)
) ENGINE=InnoDB AUTO_INCREMENT=394 DEFAULT CHARSET=utf8 COMMENT='权限任务列表';

-- ----------------------------
-- Table structure for db_unit
-- ----------------------------
DROP TABLE IF EXISTS `db_unit`;
CREATE TABLE `db_unit` (
  `unitId` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `unitName` varchar(20) NOT NULL COMMENT '单位名称',
  PRIMARY KEY (`unitId`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='计量单位表';

-- ----------------------------
-- Table structure for db_user
-- ----------------------------
DROP TABLE IF EXISTS `db_user`;
CREATE TABLE `db_user` (
  `userId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `isAdmin` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否超管 0:否 1:是',
  `state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态:0正常,1删除',
  `gender` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '性别:0男，1女',
  `departmentId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '所属部门',
  `depPositionId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '部门职位ID',
  `printerId` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '打印机ID',
  `account` varchar(30) NOT NULL COMMENT '登陆帐号',
  `password` char(32) NOT NULL COMMENT '登陆密码',
  `username` varchar(12) NOT NULL COMMENT '用户名',
  `email` varchar(30) NOT NULL COMMENT '用户邮箱',
  PRIMARY KEY (`userId`),
  KEY `idx_username` (`account`),
  KEY `departmentId` (`departmentId`)
) ENGINE=InnoDB AUTO_INCREMENT=131 DEFAULT CHARSET=utf8 COMMENT='用户信息';

-- ----------------------------
-- Table structure for db_user_packinger
-- ----------------------------
DROP TABLE IF EXISTS `db_user_packinger`;
CREATE TABLE `db_user_packinger` (
  `userId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `warehouseId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '所属仓库',
  `state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态:0正常,1删除',
  PRIMARY KEY (`userId`)
) ENGINE=InnoDB AUTO_INCREMENT=123 DEFAULT CHARSET=utf8 COMMENT='用户信息--分拣员信息';

-- ----------------------------
-- Table structure for db_userbo
-- ----------------------------
DROP TABLE IF EXISTS `db_userbo`;
CREATE TABLE `db_userbo` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for db_userRole
-- ----------------------------
DROP TABLE IF EXISTS `db_userRole`;
CREATE TABLE `db_userRole` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(10) unsigned NOT NULL COMMENT '会员ID',
  `roleId` int(10) unsigned NOT NULL COMMENT '角色ID',
  PRIMARY KEY (`id`),
  KEY `idx_userId` (`userId`)
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for db_vehicle
-- ----------------------------
DROP TABLE IF EXISTS `db_vehicle`;
CREATE TABLE `db_vehicle` (
  `vehicleId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态：0正常，1删除',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `plateNumber` varchar(20) NOT NULL COMMENT '车牌号',
  PRIMARY KEY (`vehicleId`),
  KEY `vehicleNumber` (`plateNumber`)
) ENGINE=InnoDB AUTO_INCREMENT=1007 DEFAULT CHARSET=utf8 COMMENT='车辆信息表';

-- ----------------------------
-- Table structure for db_warehouse
-- ----------------------------
DROP TABLE IF EXISTS `db_warehouse`;
CREATE TABLE `db_warehouse` (
  `warehouseId` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '仓库ID',
  `type` tinyint(1) unsigned NOT NULL COMMENT '类型 0:分区 1:仓位',
  `hasPosition` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '包含仓位',
  `parentId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '所属上级,0为仓库,非0为仓库区域',
  `state` int(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态：0正常，1删除',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '新建时间',
  `title` varchar(20) NOT NULL COMMENT '仓库名称',
  `mapPath` varchar(200) NOT NULL COMMENT '路径',
  PRIMARY KEY (`warehouseId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='仓库表';

-- ----------------------------
-- Table structure for db_warehouse_adjust
-- ----------------------------
DROP TABLE IF EXISTS `db_warehouse_adjust`;
CREATE TABLE `db_warehouse_adjust` (
  `adjustId` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '调整单号',
  `num` int(10) unsigned NOT NULL COMMENT '调整数量',
  `userId` int(10) unsigned NOT NULL COMMENT '操作人userId',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '制单时间',
  `singleNumber` varchar(20) NOT NULL COMMENT '单品编码',
  `remark` varchar(255) NOT NULL COMMENT '备注',
  PRIMARY KEY (`adjustId`),
  KEY `singleNumber` (`singleNumber`)
) ENGINE=InnoDB AUTO_INCREMENT=10288 DEFAULT CHARSET=utf8 COMMENT='仓库调整单';

-- ----------------------------
-- Table structure for db_warehouse_adjust_detail
-- ----------------------------
DROP TABLE IF EXISTS `db_warehouse_adjust_detail`;
CREATE TABLE `db_warehouse_adjust_detail` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `adjustId` int(10) NOT NULL COMMENT '调整单ID',
  `positionId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '仓位ID',
  `warehouseId` int(10) NOT NULL COMMENT '仓库ID',
  `num` decimal(10,2) unsigned NOT NULL COMMENT '调整数量',
  `batch` varchar(30) NOT NULL COMMENT '产品批次',
  `oldbatch` varchar(30) NOT NULL COMMENT '调整前产品批次',
  PRIMARY KEY (`id`),
  KEY `adjustId` (`adjustId`)
) ENGINE=InnoDB AUTO_INCREMENT=10352 DEFAULT CHARSET=utf8 COMMENT='调整单明细';

-- ----------------------------
-- Table structure for db_warehouse_count
-- ----------------------------
DROP TABLE IF EXISTS `db_warehouse_count`;
CREATE TABLE `db_warehouse_count` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `warehouseId` int(10) unsigned NOT NULL COMMENT '仓库ID',
  `productId` int(10) unsigned NOT NULL COMMENT '产品ID',
  `num` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT '当前库存数量',
  `singleNumber` varchar(30) NOT NULL COMMENT '单品编码',
  PRIMARY KEY (`id`),
  KEY `warehouseId` (`warehouseId`),
  KEY `singleNumber` (`singleNumber`)
) ENGINE=InnoDB AUTO_INCREMENT=3129 DEFAULT CHARSET=utf8 COMMENT='仓库产品统计数据';

-- ----------------------------
-- Table structure for db_warehouse_history
-- ----------------------------
DROP TABLE IF EXISTS `db_warehouse_history`;
CREATE TABLE `db_warehouse_history` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tag` varchar(50) NOT NULL COMMENT '标记',
  `source` int(3) unsigned NOT NULL COMMENT '来源',
  `orderId` int(10) unsigned NOT NULL COMMENT '单号',
  `warehouse_in` decimal(10,2) unsigned NOT NULL COMMENT '入库',
  `warehouse_out` decimal(10,2) unsigned NOT NULL COMMENT '出库',
  `timeline` datetime NOT NULL COMMENT '操作时间',
  `operator` varchar(30) NOT NULL COMMENT '操作员',
  `surplus` decimal(15,2) unsigned NOT NULL COMMENT '结余',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3819 DEFAULT CHARSET=utf8 COMMENT='每月结余报表';

-- ----------------------------
-- Table structure for db_warehouse_history_total
-- ----------------------------
DROP TABLE IF EXISTS `db_warehouse_history_total`;
CREATE TABLE `db_warehouse_history_total` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '序号',
  `singleNumber` varchar(100) NOT NULL COMMENT '单品编码',
  `date` date NOT NULL COMMENT '结余日期',
  `total` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT '结余数量',
  PRIMARY KEY (`id`),
  KEY `singleNumber` (`singleNumber`,`date`)
) ENGINE=InnoDB AUTO_INCREMENT=5852 DEFAULT CHARSET=utf8 COMMENT='结余报表';

-- ----------------------------
-- Table structure for db_warehouse_info
-- ----------------------------
DROP TABLE IF EXISTS `db_warehouse_info`;
CREATE TABLE `db_warehouse_info` (
  `warehouseId` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '仓库ID',
  `areaId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '所属地区ID',
  `state` int(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态：0正常，1删除',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '仓库类型',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '新建时间',
  `title` varchar(20) NOT NULL COMMENT '仓库名称',
  PRIMARY KEY (`warehouseId`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8 COMMENT='仓库信息表';

-- ----------------------------
-- Table structure for db_warehouse_lock
-- ----------------------------
DROP TABLE IF EXISTS `db_warehouse_lock`;
CREATE TABLE `db_warehouse_lock` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '锁定来源：1分配单，2分拣单，3调拔单，4.待发货',
  `sourceId` bigint(19) unsigned NOT NULL DEFAULT '0' COMMENT '来源单ID',
  `orderId` bigint(19) unsigned NOT NULL DEFAULT '0' COMMENT '订单ID',
  `warehouseId` int(10) unsigned NOT NULL COMMENT '仓库ID',
  `positionId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '仓位ID ',
  `productId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '产品ID',
  `num` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '锁定数量',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '锁定时间',
  `singleNumber` varchar(30) NOT NULL COMMENT '单品编码',
  `productBatch` varchar(20) NOT NULL COMMENT '产品批次',
  PRIMARY KEY (`id`),
  KEY `warehouseId` (`warehouseId`),
  KEY `sourceId` (`sourceId`),
  KEY `orderId` (`orderId`),
  KEY `productId` (`productId`)
) ENGINE=InnoDB AUTO_INCREMENT=10912 DEFAULT CHARSET=utf8 COMMENT='仓库产品锁定信息';

-- ----------------------------
-- Table structure for db_warehouse_message
-- ----------------------------
DROP TABLE IF EXISTS `db_warehouse_message`;
CREATE TABLE `db_warehouse_message` (
  `messageId` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '消息类型：0为公共信息，1为仓库信息，2为采购信息',
  `warehouseId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '仓库ID',
  `orderId` bigint(19) unsigned NOT NULL DEFAULT '0' COMMENT '订单ID',
  `opstate` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '建议操作:0无,1关闭,2修改,3挂起',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '标题',
  `content` text NOT NULL COMMENT '消息内容',
  PRIMARY KEY (`messageId`),
  KEY `memberId` (`orderId`)
) ENGINE=InnoDB AUTO_INCREMENT=134 DEFAULT CHARSET=utf8 COMMENT='仓库系统消息表';

-- ----------------------------
-- Table structure for db_warehouse_oldrecords
-- ----------------------------
DROP TABLE IF EXISTS `db_warehouse_oldrecords`;
CREATE TABLE `db_warehouse_oldrecords` (
  `oldrecordsId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `stocktakingId` int(10) unsigned NOT NULL COMMENT '盘点单Id',
  `id` int(10) unsigned NOT NULL COMMENT '原warehouse_product表的ID',
  `warehouseId` int(10) unsigned NOT NULL COMMENT '仓库ID',
  `positionId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '仓位ID ',
  `productId` int(10) unsigned NOT NULL COMMENT '产品ID',
  `num` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '当前库存数量',
  `createTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '新建时间',
  `updateTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `singleNumber` varchar(30) NOT NULL COMMENT '单品编码',
  `productBatch` varchar(20) NOT NULL COMMENT '产品批次',
  PRIMARY KEY (`oldrecordsId`),
  KEY `stocktakingId` (`stocktakingId`)
) ENGINE=InnoDB AUTO_INCREMENT=13015 DEFAULT CHARSET=utf8 COMMENT='仓库产品信息备份数据，盘点前需先备份';

-- ----------------------------
-- Table structure for db_warehouse_outbound
-- ----------------------------
DROP TABLE IF EXISTS `db_warehouse_outbound`;
CREATE TABLE `db_warehouse_outbound` (
  `outboundId` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '出库单Id',
  `source` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '入库来源 0:发货出货 1:调拨出库',
  `sourceId` int(10) unsigned NOT NULL COMMENT '对应的来源单号',
  `warehouseId` int(10) unsigned NOT NULL COMMENT '仓库ID',
  `userId` int(10) unsigned NOT NULL COMMENT '操作人userId',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '制单时间',
  `realTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '实际出库时间',
  `operator` varchar(30) NOT NULL DEFAULT '' COMMENT '操作员',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  PRIMARY KEY (`outboundId`)
) ENGINE=InnoDB AUTO_INCREMENT=11862 DEFAULT CHARSET=utf8 COMMENT='出库单';

-- ----------------------------
-- Table structure for db_warehouse_outbound_detail
-- ----------------------------
DROP TABLE IF EXISTS `db_warehouse_outbound_detail`;
CREATE TABLE `db_warehouse_outbound_detail` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `outboundId` int(10) unsigned NOT NULL COMMENT '出库单Id',
  `positionId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '仓位ID',
  `num` decimal(10,2) unsigned NOT NULL COMMENT '出库数量',
  `singleNumber` varchar(30) NOT NULL COMMENT '产品编号 ',
  `color` varchar(20) NOT NULL COMMENT '颜色',
  `productBatch` varchar(30) NOT NULL COMMENT '产品批次',
  PRIMARY KEY (`id`),
  KEY `warrantId` (`outboundId`),
  KEY `singleNumber` (`singleNumber`)
) ENGINE=InnoDB AUTO_INCREMENT=16064 DEFAULT CHARSET=utf8 COMMENT='出库单明细';

-- ----------------------------
-- Table structure for db_warehouse_position
-- ----------------------------
DROP TABLE IF EXISTS `db_warehouse_position`;
CREATE TABLE `db_warehouse_position` (
  `positionId` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '仓位ID',
  `warehouseId` int(10) unsigned NOT NULL COMMENT '所属仓库ID',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '仓位/分区类型',
  `parentId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '所属仓库区域ID',
  `state` int(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态：0正常，1删除',
  `printerId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '默认打印机',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '新建时间',
  `title` varchar(20) NOT NULL COMMENT '仓位名称',
  PRIMARY KEY (`positionId`),
  KEY `warehouseId` (`warehouseId`),
  KEY `title` (`title`)
) ENGINE=InnoDB AUTO_INCREMENT=1749 DEFAULT CHARSET=utf8 COMMENT='仓库仓位信息表';

-- ----------------------------
-- Table structure for db_warehouse_printer
-- ----------------------------
DROP TABLE IF EXISTS `db_warehouse_printer`;
CREATE TABLE `db_warehouse_printer` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `warehouseId` int(10) unsigned NOT NULL COMMENT '仓库ID',
  `printerId` int(10) unsigned NOT NULL COMMENT '打印机ID',
  `isDefault` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否默认',
  PRIMARY KEY (`id`),
  KEY `warehouseId` (`warehouseId`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='仓库打印机设置';

-- ----------------------------
-- Table structure for db_warehouse_product
-- ----------------------------
DROP TABLE IF EXISTS `db_warehouse_product`;
CREATE TABLE `db_warehouse_product` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `warehouseId` int(10) unsigned NOT NULL COMMENT '仓库ID',
  `positionId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '仓位ID ',
  `productId` int(10) unsigned NOT NULL COMMENT '产品ID',
  `num` decimal(13,2) NOT NULL DEFAULT '0.00' COMMENT '当前库存数量',
  `isGlassy` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否呆滞产品',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '新建时间',
  `updateTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `singleNumber` varchar(30) NOT NULL COMMENT '单品编码',
  `productBatch` varchar(20) NOT NULL COMMENT '产品批次',
  PRIMARY KEY (`id`),
  KEY `warehouseId` (`warehouseId`),
  KEY `singleNumber` (`singleNumber`)
) ENGINE=InnoDB AUTO_INCREMENT=15464 DEFAULT CHARSET=utf8 COMMENT='仓库产品信息';

-- ----------------------------
-- Table structure for db_warehouse_repeal
-- ----------------------------
DROP TABLE IF EXISTS `db_warehouse_repeal`;
CREATE TABLE `db_warehouse_repeal` (
  `repealId` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '单号',
  `state` int(2) NOT NULL COMMENT '订单状态 0:待审核 1:已审核 2:拒绝',
  `warrantId` int(10) unsigned NOT NULL COMMENT '入库单号',
  `userId` int(10) unsigned NOT NULL COMMENT '申请人员ID',
  `operator` varchar(50) NOT NULL COMMENT '申请人员',
  `createTime` datetime NOT NULL COMMENT '申请时间',
  `remark` varchar(250) NOT NULL COMMENT '备注说明',
  PRIMARY KEY (`repealId`)
) ENGINE=InnoDB AUTO_INCREMENT=10009 DEFAULT CHARSET=utf8 COMMENT='撤消入库申请';

-- ----------------------------
-- Table structure for db_warehouse_repeal_log
-- ----------------------------
DROP TABLE IF EXISTS `db_warehouse_repeal_log`;
CREATE TABLE `db_warehouse_repeal_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '记录编号',
  `repealId` int(10) unsigned NOT NULL COMMENT '撤消入库单号',
  `action` int(2) unsigned NOT NULL COMMENT '动作：0申请,1拒绝,2通过',
  `operator` varchar(20) NOT NULL COMMENT '操作人',
  `datetime` datetime NOT NULL COMMENT '操作时间',
  `reasons` varchar(250) NOT NULL DEFAULT '' COMMENT '拒绝通过理由',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8 COMMENT='撤消入库申请日志';

-- ----------------------------
-- Table structure for db_warehouse_surplus
-- ----------------------------
DROP TABLE IF EXISTS `db_warehouse_surplus`;
CREATE TABLE `db_warehouse_surplus` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '序号',
  `warehouseId` int(10) unsigned NOT NULL COMMENT '所属仓库ID',
  `singleNumber` varchar(100) NOT NULL COMMENT '单品编码',
  `date` date NOT NULL COMMENT '结余日期',
  `total` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT '结余数量',
  PRIMARY KEY (`id`),
  KEY `singleNumber` (`singleNumber`,`date`),
  KEY `warehouseId` (`warehouseId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='异动报表月结余';

-- ----------------------------
-- Table structure for db_warehouse_user
-- ----------------------------
DROP TABLE IF EXISTS `db_warehouse_user`;
CREATE TABLE `db_warehouse_user` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `warehouseId` int(10) NOT NULL COMMENT '所属仓库ID',
  `positionId` int(10) NOT NULL COMMENT '分区Id',
  `userId` int(10) NOT NULL COMMENT '分拣员id',
  `isMerge` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否归单员',
  `isManage` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否为负责人，0表示分拣员，1表示负责人',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '新建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for db_warehouse_warrant
-- ----------------------------
DROP TABLE IF EXISTS `db_warehouse_warrant`;
CREATE TABLE `db_warehouse_warrant` (
  `warrantId` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '入库单号',
  `source` tinyint(2) unsigned NOT NULL COMMENT '入库来源 0:采购单 1:调拨单 2:盘点单',
  `state` int(3) unsigned NOT NULL DEFAULT '0' COMMENT '入库单状态',
  `warehouseId` int(10) NOT NULL COMMENT '仓库ID',
  `userId` int(10) unsigned NOT NULL COMMENT '操作人userId',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '制单时间',
  `realTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '实际入库时间',
  `postId` char(14) NOT NULL COMMENT '发货单号',
  `operator` varchar(30) NOT NULL COMMENT '操作员',
  `factoryNumber` varchar(20) NOT NULL COMMENT '革厂编号 ',
  `contactName` varchar(30) NOT NULL COMMENT '联系人',
  `phone` varchar(20) NOT NULL COMMENT '联系电话',
  `factoryName` varchar(100) NOT NULL COMMENT '革厂名称',
  `address` varchar(255) NOT NULL COMMENT '联系地址',
  `remark` varchar(255) NOT NULL COMMENT '备注',
  PRIMARY KEY (`warrantId`)
) ENGINE=InnoDB AUTO_INCREMENT=11454 DEFAULT CHARSET=utf8 COMMENT='入库单';

-- ----------------------------
-- Table structure for db_warehouse_warrant_detail
-- ----------------------------
DROP TABLE IF EXISTS `db_warehouse_warrant_detail`;
CREATE TABLE `db_warehouse_warrant_detail` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `warrantId` char(14) NOT NULL COMMENT '入库单ID',
  `positionId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '仓位ID',
  `orderId` char(14) NOT NULL COMMENT '采购单ID',
  `postQuantity` decimal(10,2) unsigned NOT NULL COMMENT '发货数量',
  `num` decimal(10,2) unsigned NOT NULL COMMENT '入库数量',
  `storageTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '入库时间',
  `singleNumber` varchar(30) NOT NULL COMMENT '产品编号 ',
  `color` varchar(20) NOT NULL COMMENT '颜色',
  `corpProductNumber` varchar(30) NOT NULL COMMENT '革厂产品编码',
  `batch` varchar(30) NOT NULL COMMENT '产品批次',
  PRIMARY KEY (`id`),
  KEY `warrantId` (`warrantId`),
  KEY `singleNumber` (`singleNumber`)
) ENGINE=InnoDB AUTO_INCREMENT=15544 DEFAULT CHARSET=utf8 COMMENT='入库单明细';

-- ----------------------------
-- Table structure for deal
-- ----------------------------
DROP TABLE IF EXISTS `deal`;
CREATE TABLE `deal` (
  `num` int(10) unsigned DEFAULT '0',
  `objId` int(10) unsigned NOT NULL DEFAULT '0',
  `objId1` int(10) unsigned DEFAULT '0',
  `objId2` int(10) unsigned DEFAULT '0',
  KEY `objId` (`objId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for sphinx_product
-- ----------------------------
DROP TABLE IF EXISTS `sphinx_product`;
CREATE TABLE `sphinx_product` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `productId` int(10) unsigned NOT NULL COMMENT '产品ID',
  `productType` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '产品类型：0正常产品，1尾货产品',
  `ranking` int(10) unsigned NOT NULL COMMENT '权重值',
  `salesVolume` int(10) unsigned NOT NULL COMMENT '销量',
  `price` int(10) unsigned NOT NULL COMMENT '价格',
  `categoryId` int(10) unsigned NOT NULL COMMENT '类目ID',
  `attributeId` int(10) unsigned NOT NULL COMMENT '属性ID',
  `publish` int(10) unsigned NOT NULL COMMENT '上架时间',
  `attrValue` varchar(50) NOT NULL DEFAULT '' COMMENT '属性值',
  `key` varchar(100) NOT NULL COMMENT '搜索关键词',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for test_ccc
-- ----------------------------
DROP TABLE IF EXISTS `test_ccc`;
CREATE TABLE `test_ccc` (
  `realTime` timestamp NULL DEFAULT '0000-00-00 00:00:00' COMMENT '实际入库时间',
  `warrantId` int(10) unsigned DEFAULT '0' COMMENT '入库单号',
  `factoryName` varchar(100) DEFAULT NULL COMMENT '革厂名称',
  `title` varchar(50) DEFAULT NULL,
  `singleNumber` varchar(30) NOT NULL COMMENT '产品编号 ',
  `num` decimal(10,2) unsigned NOT NULL COMMENT '入库数量',
  `warehouse` varchar(20) DEFAULT NULL COMMENT '仓库名称',
  `position` varchar(20) DEFAULT NULL COMMENT '仓位名称',
  `warehouseId` int(10) unsigned DEFAULT '0' COMMENT '仓库ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- View structure for db_index_post
-- ----------------------------
DROP VIEW IF EXISTS `db_index_post`;
CREATE ALGORITHM=UNDEFINED DEFINER=`admin`@`%` SQL SECURITY DEFINER VIEW `db_index_post` AS (select `Pro`.`postId` AS `postId`,`Purchase`.`productCode` AS `productCode`,`Post`.`orderType` AS `orderType`,`Post`.`userId` AS `userId`,`Post`.`state` AS `state` from ((`db_order_post2_product` `Pro` left join `db_order_post2` `Post` on((`Post`.`postId` = `Pro`.`postId`))) left join `db_order_purchasing_product` `Purchase` on((`Purchase`.`purchaseProId` = `Pro`.`purchaseProId`)))) ;

-- ----------------------------
-- View structure for db_surplus_detail
-- ----------------------------
DROP VIEW IF EXISTS `db_surplus_detail`;
CREATE ALGORITHM=TEMPTABLE DEFINER=`admin`@`%` SQL SECURITY DEFINER VIEW `db_surplus_detail` AS (select 1 AS `type`,`d`.`warrantId` AS `id`,sum(`d`.`num`) AS `num`,`w`.`source` AS `source`,`w`.`postId` AS `sourceId`,`p`.`warehouseId` AS `warehouseId`,`w`.`createTime` AS `createTime`,`d`.`singleNumber` AS `singleNumber`,`w`.`operator` AS `operator` from ((`db_warehouse_warrant_detail` `d` left join `db_warehouse_warrant` `w` on(((`d`.`warrantId` = `w`.`warrantId`) and (`w`.`state` = 0)))) left join `db_warehouse_position` `p` on((`p`.`positionId` = `d`.`positionId`))) where ((`d`.`warrantId` = `w`.`warrantId`) and (`w`.`state` = 0)) group by `d`.`warrantId`,`d`.`singleNumber`,`p`.`warehouseId`) union all (select 2 AS `type`,`d`.`outboundId` AS `id`,sum(`d`.`num`) AS `num`,`w`.`source` AS `source`,`w`.`sourceId` AS `sourceId`,`w`.`warehouseId` AS `warehouseId`,`w`.`createTime` AS `createTime`,`d`.`singleNumber` AS `singleNumber`,`w`.`operator` AS `operator` from (`db_warehouse_outbound_detail` `d` join `db_warehouse_outbound` `w`) where (`d`.`outboundId` = `w`.`outboundId`) group by `d`.`outboundId`,`d`.`singleNumber`,`w`.`warehouseId`) union all (select 3 AS `type`,`sc`.`stocktakingId` AS `id`,`sc`.`num` AS `num`,0 AS `source`,0 AS `sourceId`,`sc`.`warehouseId` AS `warehouseId`,`sc`.`createTime` AS `createTime`,`sc`.`singleNumber` AS `singleNumber`,`s`.`checkUser` AS `operator` from (`db_stocktaking_count` `sc` join `db_stocktaking` `s`) where (`sc`.`stocktakingId` = `s`.`stocktakingId`)) ;

-- ----------------------------
-- Procedure structure for distribution_auto
-- ----------------------------
DROP PROCEDURE IF EXISTS `distribution_auto`;
DELIMITER ;;

;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for pro_flush_glassy
-- ----------------------------
DROP PROCEDURE IF EXISTS `pro_flush_glassy`;
DELIMITER ;;
CREATE DEFINER=`leather168`@`%` PROCEDURE `pro_flush_glassy`()
    COMMENT '生成呆滞报表数据'
BEGIN
    DECLARE var_id,var_conditions,var_productId,done INT; 
    DECLARE v1 INT DEFAULT 1;
DECLARE cur CURSOR FOR 
	SELECT `id`, `conditions` FROM  `db_glassy_level` ORDER BY `conditions` ASC ;	
DECLARE cur_product CURSOR FOR 
	SELECT `productId`,`glassLevelId`, `conditions` FROM  `db_glassy_level_product`	 ORDER BY `productId` ASC,`conditions` ASC ;
	
DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;
TRUNCATE TABLE db_glassy_list;
INSERT INTO `db_glassy_list` ( `warehouseId` , `singleNumber` , `productId` , `totalNum` )
SELECT wp.`warehouseId` , wp.`singleNumber` , wp.`productId` , SUM( wp.`num` ) AS totalNum
FROM `db_warehouse_product` wp
WHERE EXISTS (
SELECT NULL
FROM `db_product_stock` ps
WHERE ps.`lastSaleTime` <= DATE_SUB( NOW( ) , INTERVAL ps.`glassTime` HOUR )
AND ps.glassTime >0
AND ps.singleNumber = wp.singleNumber
)
GROUP BY wp.`singleNumber` , wp.`warehouseId` ;
UPDATE `db_glassy_list` g ,`db_product_stock` ps SET g.lastSaleTime = ps.lastSaleTime WHERE g.singleNumber = ps.singleNumber;
OPEN cur;
	REPEAT	
	FETCH cur INTO var_id,var_conditions;
	 IF v1=1 THEN
	 UPDATE `db_glassy_list`  SET  `levelId`=var_id ;
	 ELSE
	  UPDATE `db_glassy_list`  SET  `levelId`=var_id  WHERE `lastSaleTime`<  DATE_SUB( NOW( ) , INTERVAL var_conditions HOUR )  ;
	 END IF; 
	UNTIL done=1 END REPEAT;
CLOSE cur;
SET done = 0;
OPEN cur_product;
  REPEAT
  FETCH cur_product INTO var_productId,var_id,var_conditions;
    UPDATE `db_glassy_list`  SET  `levelId`=var_id  WHERE `productId` = var_productId AND `lastSaleTime`<  DATE_SUB( NOW( ) , INTERVAL var_conditions HOUR )   ;
  UNTIL done=1 END REPEAT;
CLOSE cur_product;
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for pro_flush_serial
-- ----------------------------
DROP PROCEDURE IF EXISTS `pro_flush_serial`;
DELIMITER ;;
CREATE DEFINER=`leather168`@`%` PROCEDURE `pro_flush_serial`()
    NO SQL
BEGIN
TRUNCATE TABLE sphinx_product;
INSERT INTO sphinx_product (`productId`,`key`,`ranking`,`salesVolume`,`price`,`categoryId`,`attributeId`,`attrValue`,`publish`) SELECT productId,pro.title AS `key`,UNIX_TIMESTAMP(updatetime)+salesVolume AS ranking,salesVolume,price*100 AS price,categoryId,IFNULL(attributeId,0) AS attributeId,IFNULL(`attrValue`,'') AS attrValue,UNIX_TIMESTAMP(updateTime) AS publish FROM db_product pro LEFT JOIN db_product_attribute attr USING(productId) WHERE state=0;
INSERT INTO sphinx_product (`productId`,`key`,`ranking`,`salesVolume`,`price`,`categoryId`,`attributeId`,`attrValue`,`publish`) SELECT productId,pro.serialNumber AS `key`,UNIX_TIMESTAMP(updatetime)+salesVolume AS ranking,salesVolume,price*100 AS price,categoryId,IFNULL(attributeId,0) AS attributeId,IFNULL(`attrValue`,'') AS attrValue,UNIX_TIMESTAMP(updateTime) AS publish FROM db_product pro LEFT JOIN db_product_attribute attr USING(productId) WHERE state=0;
INSERT INTO sphinx_product (`productId`,`key`,`ranking`,`salesVolume`,`price`,`categoryId`,`attributeId`,`attrValue`,`publish`) SELECT productId,stock.singleNumber AS `key`,UNIX_TIMESTAMP(updatetime)+salesVolume AS ranking,salesVolume,pro.price*100 AS price,pro.categoryId,attributeId,IFNULL(`attrValue`,'') AS attrValue,UNIX_TIMESTAMP(updateTime) AS publish FROM db_product_stock stock LEFT JOIN db_product pro USING(productId) JOIN db_product_attribute attr USING(productId) WHERE pro.state=0;
INSERT INTO sphinx_product (`productId`,`productType`,`key`,`ranking`,`salesVolume`,`price`,`categoryId`,`attributeId`,`attrValue`,`publish`) SELECT tail.tailId,1,pro.title AS `key`,tail.createTime+tail.salesVolume AS ranking,tail.salesVolume,tail.price*100 AS price,pro.categoryId,IFNULL(attributeId,0) AS attributeId,IFNULL(`attrValue`,'') AS attrValue,tail.updateTime AS publish FROM `db_tail` tail ,db_product pro LEFT JOIN db_product_attribute attr USING(productId) WHERE tail.state = 'selling' AND tail.productId = pro.productId;
INSERT INTO sphinx_product (`productId`,`productType`,`key`,`ranking`,`salesVolume`,`price`,`categoryId`,`attributeId`,`attrValue`,`publish`) SELECT tail.tailId,1,pro.serialNumber AS `key`,tail.createTime+tail.salesVolume AS ranking,tail.salesVolume,tail.price*100 AS price,pro.categoryId,IFNULL(attributeId,0) AS attributeId,IFNULL(`attrValue`,'') AS attrValue,tail.updateTime AS publish FROM `db_tail` tail ,db_product pro LEFT JOIN db_product_attribute attr USING(productId) WHERE tail.state = 'selling' AND tail.productId = pro.productId;
INSERT INTO sphinx_product (`productId`,`productType`,`key`,`ranking`,`salesVolume`,`price`,`categoryId`,`attributeId`,`attrValue`,`publish`) SELECT tail.tailId,1,single.singleNumber AS `key`,tail.createTime+tail.salesVolume AS ranking,tail.salesVolume,tail.price*100 AS price,pro.categoryId,IFNULL(attributeId,0) AS attributeId,IFNULL(`attrValue`,'') AS attrValue,tail.updateTime AS publish FROM  db_tail_single single LEFT JOIN db_tail tail USING(tailId) LEFT JOIN db_product pro USING(productId) JOIN db_product_attribute attr USING(productId) WHERE single.state=0 AND tail.state = 'selling' ;
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for proc_calculate
-- ----------------------------
DROP PROCEDURE IF EXISTS `proc_calculate`;
DELIMITER ;;

;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for proc_filter_member
-- ----------------------------
DROP PROCEDURE IF EXISTS `proc_filter_member`;
DELIMITER ;;

;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for proc_filter_product
-- ----------------------------
DROP PROCEDURE IF EXISTS `proc_filter_product`;
DELIMITER ;;

;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for proc_filter_saleman
-- ----------------------------
DROP PROCEDURE IF EXISTS `proc_filter_saleman`;
DELIMITER ;;

;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for proc_filter_sell
-- ----------------------------
DROP PROCEDURE IF EXISTS `proc_filter_sell`;
DELIMITER ;;

;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for proc_member_detail
-- ----------------------------
DROP PROCEDURE IF EXISTS `proc_member_detail`;
DELIMITER ;;

;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for proc_member_statistic
-- ----------------------------
DROP PROCEDURE IF EXISTS `proc_member_statistic`;
DELIMITER ;;

;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for proc_product_detail
-- ----------------------------
DROP PROCEDURE IF EXISTS `proc_product_detail`;
DELIMITER ;;

;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for proc_product_statistic
-- ----------------------------
DROP PROCEDURE IF EXISTS `proc_product_statistic`;
DELIMITER ;;

;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for proc_saleman_detail
-- ----------------------------
DROP PROCEDURE IF EXISTS `proc_saleman_detail`;
DELIMITER ;;

;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for proc_saleman_statistic
-- ----------------------------
DROP PROCEDURE IF EXISTS `proc_saleman_statistic`;
DELIMITER ;;

;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for proc_sellmember_statistic
-- ----------------------------
DROP PROCEDURE IF EXISTS `proc_sellmember_statistic`;
DELIMITER ;;

;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for proc_sellproduct_statistic
-- ----------------------------
DROP PROCEDURE IF EXISTS `proc_sellproduct_statistic`;
DELIMITER ;;

;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for proc_settlement
-- ----------------------------
DROP PROCEDURE IF EXISTS `proc_settlement`;
DELIMITER ;;

;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for proc_single_surplus
-- ----------------------------
DROP PROCEDURE IF EXISTS `proc_single_surplus`;
DELIMITER ;;

;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for proc_surplus
-- ----------------------------
DROP PROCEDURE IF EXISTS `proc_surplus`;
DELIMITER ;;

;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for proc_surplus_realtime
-- ----------------------------
DROP PROCEDURE IF EXISTS `proc_surplus_realtime`;
DELIMITER ;;

;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for runonce
-- ----------------------------
DROP PROCEDURE IF EXISTS `runonce`;
DELIMITER ;;

;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for sphinx_rebuild
-- ----------------------------
DROP PROCEDURE IF EXISTS `sphinx_rebuild`;
DELIMITER ;;
CREATE DEFINER=`leather168`@`%` PROCEDURE `sphinx_rebuild`()
BEGIN
TRUNCATE TABLE db_sphinx_product;
INSERT INTO `db_sphinx_product` (productId, saleType, lft, rft, attrId, attrValue, saleTime, saleVolume, price,`serial`, title, picture)
SELECT T.`tailId` AS productId, T.saleType, cat.lft, cat.rft, ifnull(B.`attrId`,0), ifnull(B.attrValue,0),T.`updateTime` AS saleTime, T.`salesVolume` AS saleVolumn, ROUND(T.`price`*100) AS price, A.`serialNumber` AS `serial`, A.`title`, A.mainPic AS picture FROM db_product A
LEFT JOIN (SELECT productId,GROUP_CONCAT(attrId SEPARATOR ',') attrId,GROUP_CONCAT(attrValue SEPARATOR ',') attrValue FROM db_product_attribute GROUP BY productId) AS B ON A.`productId`=B.`productId` INNER JOIN db_tail T ON T.productId=A.productId INNER JOIN db_category cat ON cat.categoryId=A.categoryId WHERE A.`state`=0 AND T.state='selling';
INSERT INTO `db_sphinx_product` (ranking, productId, lft, rft, attrId, attrValue, saleTime, saleVolume, price, `serial`, title, picture)
SELECT (UNIX_TIMESTAMP(A.`salesTime`)+A.salesVolume) AS ranking,A.`productId`,cat.lft, cat.rft, ifnull(B.`attrId`,0),ifnull(B.attrValue,0),UNIX_TIMESTAMP(A.`salesTime`) AS saleTime, A.`salesVolume` AS saleVolumn, ROUND(A.`price`*100) AS price, A.`serialNumber` AS `serial`, A.`title`, A.mainPic AS picture FROM db_product A
LEFT JOIN (SELECT productId,GROUP_CONCAT(attrId SEPARATOR ',') attrId,GROUP_CONCAT(attrValue SEPARATOR ',') attrValue FROM db_product_attribute GROUP BY productId) AS B ON A.`productId`=B.`productId` INNER JOIN db_category cat ON cat.categoryId=A.categoryId WHERE A.`state`=0;
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for st_exception
-- ----------------------------
DROP PROCEDURE IF EXISTS `st_exception`;
DELIMITER ;;

;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for st_task_surplus
-- ----------------------------
DROP PROCEDURE IF EXISTS `st_task_surplus`;
DELIMITER ;;

;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for warehouse_case_on_output
-- ----------------------------
DROP PROCEDURE IF EXISTS `warehouse_case_on_output`;
DELIMITER ;;

;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for warehouse_cash_on_hand
-- ----------------------------
DROP PROCEDURE IF EXISTS `warehouse_cash_on_hand`;
DELIMITER ;;

;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for warehouse_cash_on_input
-- ----------------------------
DROP PROCEDURE IF EXISTS `warehouse_cash_on_input`;
DELIMITER ;;

;;
DELIMITER ;

-- ----------------------------
-- Event structure for event_distribution
-- ----------------------------
DROP EVENT IF EXISTS `event_distribution`;
DELIMITER ;;
CREATE DEFINER=`admin`@`%` EVENT `event_distribution` ON SCHEDULE EVERY 5 SECOND STARTS '2016-11-01 14:33:39' ON COMPLETION PRESERVE ENABLE DO call distribution_auto()
;;
DELIMITER ;
