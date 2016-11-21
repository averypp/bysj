#海贝壳项目 数据库变更sql文件----

#by echo add 2016-06-22 
#社区表结构
CREATE TABLE `sea_community` (
 `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
 `is_deleted` char(1) NOT NULL DEFAULT 'N' COMMENT '是否删除,N:未删除，Y:删除',
 `gmt_create` datetime NOT NULL DEFAULT '1970-01-01 12:00:00' COMMENT '记录创建时间，如果时间是1970年则表示纪录未修改',
 `gmt_modified` datetime NOT NULL DEFAULT '1970-01-01 12:00:00' COMMENT '记录修改时间,如果时间是1970年则表示纪录未修改',
 `creator` int(11) NOT NULL DEFAULT '0' COMMENT '创建人，0表示无创建人值',
 `modifier` int(11) NOT NULL DEFAULT '0' COMMENT '修改人,如果为0则表示纪录未修改',
 `name` varchar(64) NOT NULL COMMENT '平台名称',
 `content` varchar(128) DEFAULT NULL COMMENT '相应站点对应的注册URL',
 `weight` varchar(32) DEFAULT NULL COMMENT 'MarketplaceId',
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT '社区表';


#发送的验证码记录
CREATE TABLE `sea_verify_code` (
 `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
 `is_deleted` char(1) NOT NULL DEFAULT 'N' COMMENT '是否删除,N:未删除，Y:删除',
 `gmt_create` datetime NOT NULL DEFAULT '1970-01-01 12:00:00' COMMENT '记录创建时间，如果时间是1970年则表示纪录未修改',
 `gmt_modified` datetime NOT NULL DEFAULT '1970-01-01 12:00:00' COMMENT '记录修改时间,如果时间是1970年则表示纪录未修改',
 `creator` int(11) NOT NULL DEFAULT '0' COMMENT '创建人，0表示无创建人值',
 `modifier` int(11) NOT NULL DEFAULT '0' COMMENT '修改人,如果为0则表示纪录未修改',
 `code` varchar(64)   NOT NULL COMMENT '验证码',
 `mobile` varchar(60) NOT NULL COMMENT '手机号',
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT '验证码记录';

#登录记录表
CREATE TABLE `sea_login_form` (
 `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
 `is_deleted` char(1) NOT NULL DEFAULT 'N' COMMENT '是否删除,N:未删除，Y:删除',
 `gmt_create` datetime NOT NULL DEFAULT '1970-01-01 12:00:00' COMMENT '记录创建时间，如果时间是1970年则表示纪录未修改',
 `gmt_modified` datetime NOT NULL DEFAULT '1970-01-01 12:00:00' COMMENT '记录修改时间,如果时间是1970年则表示纪录未修改',
 `creator` int(11) NOT NULL DEFAULT '0' COMMENT '创建人，0表示无创建人值',
 `modifier` int(11) NOT NULL DEFAULT '0' COMMENT '修改人,如果为0则表示纪录未修改',
 `mobile` varchar(64)   NOT NULL DEFAULT '' COMMENT '手机号',
 `password` varchar(64)   NOT NULL DEFAULT '' COMMENT '密码',
 `rememberMe` tinyint(1)  NOT NULL DEFAULT '1' COMMENT '记住我',
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT '登录记录表';

#用户表
CREATE TABLE `sea_user` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `is_deleted` char(1) NOT NULL DEFAULT 'N' COMMENT '是否删除,N:未删除，Y:删除',
  `gmt_create` datetime NOT NULL DEFAULT '1970-01-01 12:00:00' COMMENT '记录创建时间，如果时间是1970年则表示纪录未修改',
  `gmt_modified` datetime NOT NULL DEFAULT '1970-01-01 12:00:00' COMMENT '记录修改时间,如果时间是1970年则表示纪录未修改',
  `creator` int(11) NOT NULL DEFAULT '0' COMMENT '创建人，0表示无创建人值',
  `modifier` int(11) NOT NULL DEFAULT '0' COMMENT '修改人,如果为0则表示纪录未修改',
  `username` varchar(60) DEFAULT '' COMMENT '用户名',
  `email` varchar(60) DEFAULT '' COMMENT '邮箱',
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '用户状态，0：未审核',
  `password` varchar(32) NOT NULL DEFAULT '' COMMENT '密码',
  `real_name` varchar(60) DEFAULT NULL COMMENT '真实姓名',
  `mobile` varchar(60) NOT NULL DEFAULT '' COMMENT '手机号',
  `qq` varchar(60) DEFAULT NULL COMMENT 'qq号码',
  `reg_time` datetime NOT NULL DEFAULT '1970-01-01 12:00:00' COMMENT '注册时间',
  `last_login` datetime NOT NULL DEFAULT '1970-01-01 12:00:00' COMMENT '最后登陆时间',
  `last_ip` varchar(15) DEFAULT NULL COMMENT '最后登陆ip',
  `logins` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '登陆次数',
  `portrait` varchar(255) DEFAULT NULL COMMENT '头像',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT '用户表';

#店铺表
CREATE TABLE `sea_store` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '自增店铺id',
  `is_deleted` char(1) NOT NULL DEFAULT 'N' COMMENT '是否删除,N:未删除，Y:删除',
  `gmt_create` datetime NOT NULL DEFAULT '1970-01-01 12:00:00' COMMENT '记录创建时间，如果时间是1970年则表示纪录未修改',
  `gmt_modified` datetime NOT NULL DEFAULT '1970-01-01 12:00:00' COMMENT '记录修改时间,如果时间是1970年则表示纪录未修改',
  `creator` int(11) NOT NULL DEFAULT '0' COMMENT '创建人，0表示无创建人值',
  `modifier` int(11) NOT NULL DEFAULT '0' COMMENT '修改人,如果为0则表示纪录未修改',
  `store_name` varchar(32) NOT NULL COMMENT '店铺名称',
  `user_id` int(10) NOT NULL COMMENT '用户id',
  `platform_id` int(10) NOT NULL COMMENT '平台id',
  `site_id` int(10) NOT NULL COMMENT '站点id',
  `merchant_id` varchar(30) DEFAULT NULL COMMENT '平台商户id',
  `accesskey_id` varchar(32) DEFAULT NULL COMMENT '平台接入码',
  `secret_key` varchar(128) DEFAULT NULL COMMENT '平台安全码',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='店铺平台认证信息表';

#平台站点表
CREATE TABLE `sea_plat_form` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `is_deleted` char(1) NOT NULL DEFAULT 'N' COMMENT '是否删除,N:未删除，Y:删除',
  `gmt_create` datetime NOT NULL DEFAULT '1970-01-01 12:00:00' COMMENT '记录创建时间，如果时间是1970年则表示纪录未修改',
  `gmt_modified` datetime NOT NULL DEFAULT '1970-01-01 12:00:00' COMMENT '记录修改时间,如果时间是1970年则表示纪录未修改',
  `creator` int(11) NOT NULL DEFAULT '0' COMMENT '创建人，0表示无创建人值',
  `modifier` int(11) NOT NULL DEFAULT '0' COMMENT '修改人,如果为0则表示纪录未修改',
  `pid` int(10) NOT NULL COMMENT '父级id',
  `platform_name` varchar(64) NOT NULL COMMENT '平台名称',
  `site_url` varchar(128) DEFAULT NULL COMMENT '相应站点对应的注册URL',
  `marketplace_id` varchar(32) DEFAULT NULL COMMENT 'MarketplaceId',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='平台站点表';


#公告管理
CREATE TABLE `sea_notice` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `is_deleted` char(1) NOT NULL DEFAULT 'N' COMMENT '是否删除,N:未删除，Y:删除',
  `gmt_create` datetime NOT NULL DEFAULT '1970-01-01 12:00:00' COMMENT '记录创建时间，如果时间是1970年则表示纪录未修改',
  `gmt_modified` datetime NOT NULL DEFAULT '1970-01-01 12:00:00' COMMENT '记录修改时间,如果时间是1970年则表示纪录未修改',
  `creator` int(11) NOT NULL DEFAULT '0' COMMENT '创建人，0表示无创建人值',
  `modifier` int(11) NOT NULL DEFAULT '0' COMMENT '修改人,如果为0则表示纪录未修改',
  `name` varchar(64) NOT NULL COMMENT '标题',
  `content` varchar(128) NOT NULL COMMENT '内容',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='公告管理';


CREATE TABLE `sea_msg` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `fid` int(11) NOT NULL,
  `tid` int(11) NOT NULL,
  `title` varchar(225) NOT NULL,
  `content` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `send_time` int(11) NOT NULL,
  `reply` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

#商品图片表
CREATE TABLE `sea_goods_picture` (
  `id` int(11) UNSIGNED NOT NULL,
  `is_deleted` char(1) NOT NULL DEFAULT 'N' COMMENT '是否删除,N:未删除，Y:删除',
  `gmt_create` datetime NOT NULL DEFAULT '1970-01-01 12:00:00' COMMENT '记录创建时间',
  `gmt_modified` datetime NOT NULL DEFAULT '1970-01-01 12:00:00' COMMENT '记录修改时间,如果时间是1970年则表示纪录未修改',
  `shop_id` int(11) NOT NULL DEFAULT '0' COMMENT '门店id',
  `sku_id` int(11) DEFAULT '0' COMMENT 'sku_id（关联sea_goods_sku表）  ',
  `goods_id` int(11) NOT NULL DEFAULT '0' COMMENT '商品id',
  `image_url` varchar(200) NOT NULL DEFAULT '' COMMENT '商品图片URL',
  `goods_picture_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '商品图片类型：1、父产品图片 0、产品图片'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='商品图片信息表';
#商品售卖信息
CREATE TABLE `sea_goods_sold_info` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `is_deleted` char(1) NOT NULL DEFAULT 'N' COMMENT '是否删除,N:未删除，Y:删除',
  `gmt_create` datetime NOT NULL DEFAULT '1970-01-01 12:00:00' COMMENT '记录创建时间',
  `gmt_modified` datetime NOT NULL DEFAULT '1970-01-01 12:00:00' COMMENT '记录修改时间,如果时间是1970年则表示纪录未修改',
  `creator` int(11) NOT NULL DEFAULT '0' COMMENT '创建人，0表示无创建人值',
  `modifier` int(11) NOT NULL DEFAULT '0' COMMENT '修改人,如果为0则表示纪录未修改',
  `shop_id` int(11) NOT NULL DEFAULT '0' COMMENT '门店id',
  `goods_info_id` int(11) NOT NULL DEFAULT '0' COMMENT '商品信息id',
  `external_product_id` varchar(32) NOT NULL COMMENT '商品编码',
  `color_name` varchar(50) DEFAULT '' COMMENT '商品颜色',
  `size_name` varchar(50) DEFAULT '' COMMENT '商品size',
  `standard_price` decimal(10,2) DEFAULT '0.00' COMMENT '商品价格',
  `sale_price` decimal(10,2) DEFAULT '0.00' COMMENT '商品促销价格',
  `sale_from_date` datetime DEFAULT '1970-01-01 12:00:00' COMMENT '商品促销开始时间',
  `sale_end_date` datetime DEFAULT '1970-01-01 12:00:00' COMMENT '商品促销结束时间',
  `quantity` int(11) NOT NULL DEFAULT '0' COMMENT '库存',
  `item_sku` varchar(64) NOT NULL DEFAULT '' COMMENT 'SKU编码',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='商品售卖信息';

#商品常规信息
CREATE TABLE `sea_goods_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '商品id，程序生成',
  `is_deleted` char(1) NOT NULL DEFAULT 'N' COMMENT '是否删除,N:未删除，Y:删除',
  `gmt_create` datetime NOT NULL DEFAULT '1970-01-01 12:00:00' COMMENT '记录创建时间',
  `gmt_modified` datetime NOT NULL DEFAULT '1970-01-01 12:00:00' COMMENT '记录修改时间,如果时间是1970年则表示纪录未修改',
  `creator` int(11) NOT NULL DEFAULT '0' COMMENT '创建人，0表示无创建人值',
  `modifier` int(11) NOT NULL DEFAULT '0' COMMENT '修改人,如果为0则表示纪录未修改',
  `shop_id` int(11) NOT NULL DEFAULT '0' COMMENT '门店id',
  `supply_link` varchar(200) DEFAULT '' COMMENT '货源链接',
  `item_name` varchar(120) NOT NULL DEFAULT '' COMMENT '产品名称',
  `item_type` varchar(40) NOT NULL DEFAULT '' COMMENT '产品类别：数据来源 产品分类',
  `feed_product_type` varchar(40) DEFAULT '' COMMENT '变体模版',
  `is_brand` char(1) NOT NULL DEFAULT 'N' COMMENT '是否品牌入驻卖家,N:不是，Y:是',
  `department_name` varchar(64) DEFAULT NULL COMMENT '必须属性',
  `product_description` text COMMENT '产品描述',
  `stocking_time` int(11) NOT NULL DEFAULT '0' COMMENT '备货时间',
  `brand_name` varchar(20) NOT NULL DEFAULT '' COMMENT '商品品牌',
  `manufacturer` varchar(64) NOT NULL DEFAULT '' COMMENT '商品生产商',
  `condition_type` varchar(32) NOT NULL DEFAULT '1' COMMENT '商品状态 :new used',
  `list_price` decimal(10,2) DEFAULT '0.00' COMMENT '制造商建议零售价',
  `external_product_id_type` varchar(20) NOT NULL DEFAULT '' COMMENT '商品编码类型：UPC／EAN...',
  `website_shipping_weight` varchar(200) NOT NULL DEFAULT '' COMMENT '邮寄重量',
  `website_shipping_weight_unit_of_measure` varchar(20) NOT NULL DEFAULT '' COMMENT '邮寄单位',
  `generic_keywords` varchar(128) DEFAULT NULL COMMENT '商品关键字，多个以，号链接',
  `bullet_point1` varchar(64) DEFAULT NULL COMMENT '第1个bullet_point参数',
  `bullet_point2` varchar(64) DEFAULT NULL COMMENT '第2个bullet_point参数',
  `bullet_point3` varchar(64) DEFAULT NULL COMMENT '第3个bullet_point参数',
  `bullet_point4` varchar(64) DEFAULT NULL COMMENT '第4个bullet_point参数',
  `bullet_point5` varchar(64) DEFAULT NULL COMMENT '第5个bullet_point参数',
  `pub_status` int(2) DEFAULT '0' COMMENT '发布状态草稿箱 0待发布 1 处理中 2发布失败 3 发布成功4',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='商品常规信息';



#亚马逊分类表
CREATE TABLE `sea_amazon_btg` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_deleted` char(1) NOT NULL DEFAULT 'N' COMMENT '是否删除,N:未删除，Y:删除',
  `gmt_create` datetime NOT NULL DEFAULT '1970-01-01 12:00:00' COMMENT '记录创建时间',
  `gmt_modified` datetime NOT NULL DEFAULT '1970-01-01 12:00:00' COMMENT '记录修改时间,如果时间是1970年则表示纪录未修改',
  `creator` int(11) NOT NULL DEFAULT '0' COMMENT '创建人，0表示无创建人值',
  `modifier` int(11) NOT NULL DEFAULT '0' COMMENT '修改人,如果为0则表示纪录未修改',
  `node_id` bigint(16) DEFAULT NULL,
  `parent_id` bigint(16) DEFAULT NULL,
  `top_id` bigint(16) DEFAULT NULL,
  `level` int(11) DEFAULT NULL,
  `leaf` tinyint(4) DEFAULT NULL,
  `keyword` varchar(255) DEFAULT NULL,
  `node_name` varchar(255) DEFAULT NULL,
  `node_path` varchar(255) DEFAULT NULL,
  `tpl_id` int(11) NOT NULL COMMENT '大分类ID 来自 sea_amazon_feeds_templates 的ID',
  `site_id` int(11) DEFAULT NULL COMMENT '站点ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='商品常规信息';

#大分类与站点关系表
CREATE TABLE `sea_amazon_feeds_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_deleted` char(1) NOT NULL DEFAULT 'N' COMMENT '是否删除,N:未删除，Y:删除',
  `gmt_create` datetime NOT NULL DEFAULT '1970-01-01 12:00:00' COMMENT '记录创建时间',
  `gmt_modified` datetime NOT NULL DEFAULT '1970-01-01 12:00:00' COMMENT '记录修改时间,如果时间是1970年则表示纪录未修改',
  `creator` int(11) NOT NULL DEFAULT '0' COMMENT '创建人，0表示无创建人值',
  `modifier` int(11) NOT NULL DEFAULT '0' COMMENT '修改人,如果为0则表示纪录未修改',
  `name` varchar(255) DEFAULT '' COMMENT '分类名称',
  `title` varchar(255) DEFAULT '',
  `version` varchar(32)  DEFAULT '' COMMENT '适用版本',
  `site_id` int(11) NOT NULL DEFAULT 0 COMMENT '站点ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='商品常规信息';

#
CREATE TABLE `sea_amazon_feed_tpl_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_deleted` char(1) NOT NULL DEFAULT 'N' COMMENT '是否删除,N:未删除，Y:删除',
  `gmt_create` datetime NOT NULL DEFAULT '1970-01-01 12:00:00' COMMENT '记录创建时间',
  `gmt_modified` datetime NOT NULL DEFAULT '1970-01-01 12:00:00' COMMENT '记录修改时间,如果时间是1970年则表示纪录未修改',
  `creator` int(11) NOT NULL DEFAULT '0' COMMENT '创建人，0表示无创建人值',
  `modifier` int(11) NOT NULL DEFAULT '0' COMMENT '修改人,如果为0则表示纪录未修改',
  `field` varchar(255) DEFAULT NULL,
  `label` varchar(255)    DEFAULT NULL,
  `definition` varchar(1023) DEFAULT NULL,
  `accepted` varchar(511)  DEFAULT NULL,
  `example` varchar(511)  DEFAULT NULL,
  `group_id` varchar(64) DEFAULT NULL,
  `required` varchar(32) DEFAULT NULL,
  `group` varchar(511) DEFAULT NULL,
  `tpl_id` int(11) DEFAULT NULL,
  `site_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

#
CREATE TABLE `sea_amazon_feed_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_deleted` char(1) NOT NULL DEFAULT 'N' COMMENT '是否删除,N:未删除，Y:删除',
  `gmt_create` datetime NOT NULL DEFAULT '1970-01-01 12:00:00' COMMENT '记录创建时间',
  `gmt_modified` datetime NOT NULL DEFAULT '1970-01-01 12:00:00' COMMENT '记录修改时间,如果时间是1970年则表示纪录未修改',
  `creator` int(11) NOT NULL DEFAULT '0' COMMENT '创建人，0表示无创建人值',
  `modifier` int(11) NOT NULL DEFAULT '0' COMMENT '修改人,如果为0则表示纪录未修改',
  `field` varchar(255) DEFAULT NULL,
  `label` varchar(255) DEFAULT NULL,
  `type` varchar(255)  DEFAULT NULL,
  `values` text DEFAULT NULL,
  `tpl_id` int(11) DEFAULT NULL,
  `site_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

#*******************以上是表结构*****************

#by echo add 2016-06-22
INSERT INTO `sea_plat_form` (`is_deleted`, `gmt_create`, `gmt_modified`, `creator`, `modifier`, `pid`, `platform_name`, `site_url`, `marketplace_id`) VALUES
('N', '1970-01-01 12:00:00', '1970-01-01 12:00:00', 1, 1, 0, 'Amazon', NULL, NULL),
('N', '1970-01-01 12:00:00', '1970-01-01 12:00:00', 1, 1, 1, 'US', 'https://developer.amazonservices.com', NULL),
('N', '1970-01-01 12:00:00', '1970-01-01 12:00:00', 1, 1, 1, 'Canada', 'https://developer.amazonservices.ca', NULL);


INSERT INTO `sea_community` (`is_deleted`, `gmt_create`, `gmt_modified`, `creator`, `modifier`, `name`, `content`, `weight`) VALUES
('N', '2016-06-13 14:04:43', '2016-06-13 14:04:43', 1, 1, 'Amazon', 'Q群:168168168', 100),
('N', '2016-06-13 14:05:01', '2016-06-13 14:21:15', 1, 1, 'eBay', '微信号：seaShell', 100);

INSERT INTO `sea_notice` (`is_deleted`, `gmt_create`, `gmt_modified`, `creator`, `modifier`, `name`, `content`) VALUES
('N', '2016-06-20 20:38:48', '2016-06-20 20:38:48', 1, 1, '[上线]静态页面上线', '尽情期待--大家辛苦了'),
('N', '2016-06-22 21:10:58', '2016-06-22 21:10:58', 1, 1, '[优化]注册免登录', '尽情期待--大家辛苦了'),


# 2016-06-23
INSERT INTO `sea_amazon_feeds_templates` (`name`, `title`, `version`, `site_id`) VALUES
('ConsumerElectronics', 'Consumer Electronics', '2015.1224', 29),
('Clothing', 'Clothing', '2015.1208', 29);

#update `sea_amazon_btg` set `site_id` = 2;

#2016-06-23
--
-- 表的结构 `sea_amazon_feeds`
--

CREATE TABLE `sea_amazon_feeds` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_deleted` char(1) NOT NULL DEFAULT 'N' COMMENT '是否删除,N:未删除，Y:删除',
  `gmt_create` datetime NOT NULL DEFAULT '1970-01-01 12:00:00' COMMENT '记录创建时间',
  `gmt_modified` datetime NOT NULL DEFAULT '1970-01-01 12:00:00' COMMENT '记录修改时间,如果时间是1970年则表示纪录未修改',
  `creator` int(11) NOT NULL DEFAULT '0' COMMENT '创建人，0表示无创建人值',
  `modifier` int(11) NOT NULL DEFAULT '0' COMMENT '修改人,如果为0则表示纪录未修改',
  `good_id` int(10) NOT NULL COMMENT '商品id',
  `FeedSubmissionId` varchar(64) DEFAULT NULL,
  `FeedType` varchar(64) DEFAULT NULL,
  `template_name` varchar(64) DEFAULT NULL,
  `FeedProcessingStatus` varchar(64) DEFAULT NULL,
  `results` mediumtext,
  `success` varchar(16) DEFAULT NULL,
  `status` varchar(64) DEFAULT NULL,
  `SubmittedDate` datetime DEFAULT NULL,
  `CompletedProcessingDate` datetime DEFAULT NULL,
  `date_created` datetime DEFAULT NULL,
  `MarketplaceIdList` varchar(255) DEFAULT NULL,
  `account_id` int(11) DEFAULT NULL,
  `line_count` int(11) DEFAULT NULL,
  `data` mediumblob,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;



#ALTER TABLE `sea_verify_code` ENGINE = INNODB;
#添加索引
#alter table sea_verify_code add index index_code (`code`)

#20160624

ALTER TABLE `sea_plat_form` add `api_host` varchar(32) DEFAULT NULL COMMENT '服务请求地址';

ALTER TABLE `sea_store` add `marketplace_id` varchar(32) DEFAULT NULL COMMENT '例子ATVPDKIKX0DER';
ALTER TABLE `sea_store` add `api_host` varchar(32) DEFAULT NULL COMMENT '例子mws.amazonservices.com';

#20160625
ALTER TABLE `sea_goods_info` ADD `dealing_status` ENUM('upload', 'translate') DEFAULT NULL COMMENT '处理中状态';

ALTER TABLE `sea_goods_info` DROP `department_name`;
ALTER TABLE `sea_goods_info` ADD `parent_skus` varchar(32) DEFAULT NULL COMMENT 'parent_skus';
ALTER TABLE `sea_goods_sold_info` ADD `department_name` varchar(64) DEFAULT NULL COMMENT 'department_name参数';

#20160627
CREATE TABLE `sea_queue_postgoods` (
 `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
 `goods_id` int(11) unsigned NOT NULL COMMENT '产品id',
 `shop_id` int(11) unsigned NOT NULL COMMENT '店铺id',
 `post_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '提交状态（1：提交，2：获取提交结果, 3：已完结）',
 `create_at` int(11) unsigned NOT NULL COMMENT '创建时间',
 `post_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '提交时间',
 `submission_id` varchar(64) DEFAULT NULL COMMENT '提交id',
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='产品提交队列表'


#20160628
ALTER TABLE `sea_goods_info` ADD `variation_theme` varchar(16) DEFAULT NULL COMMENT '变体名称size color sizecolor';
ALTER TABLE `sea_goods_info` ADD `category_id` varchar(16) DEFAULT NULL COMMENT '子分类id';
ALTER TABLE `sea_goods_info` ADD `category_name` varchar(256) DEFAULT NULL COMMENT '子分类名称（逐级）';


#线上已更新到此处2016-06-28
ALTER TABLE `sea_goods_info` CHANGE `item_type` `item_type` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '产品类别：数据来源 产品分类';

#20160701
ALTER TABLE `sea_goods_sold_info` ADD `parent_child` varchar(32) DEFAULT NULL COMMENT 'Parent/Child';
ALTER TABLE `sea_goods_sold_info` ADD `relationship_type` varchar(32) DEFAULT NULL COMMENT 'Accessory/Variation';
ALTER TABLE `sea_goods_info` CHANGE `parent_skus` `parent_sku` VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'parent_sku';

CREATE TABLE `sea_goods_sync_online` (
 `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
 `is_deleted` char(1) NOT NULL DEFAULT 'N' COMMENT '是否删除,N:未删除，Y:删除',
  `gmt_create` datetime NOT NULL DEFAULT '1970-01-01 12:00:00' COMMENT '记录创建时间',
  `gmt_modified` datetime NOT NULL DEFAULT '1970-01-01 12:00:00' COMMENT '记录修改时间,如果时间是1970年则表示纪录未修改',
  `creator` int(11) NOT NULL DEFAULT '0' COMMENT '创建人，0表示无创建人值',
  `modifier` int(11) NOT NULL DEFAULT '0' COMMENT '修改人,如果为0则表示纪录未修改',
 `asin` char(10) NOT NULL COMMENT '亚马逊产品编号',
 `title` varchar(255)  DEFAULT NULL COMMENT '标题',
 `image_url` varchar(255) NOT NULL COMMENT '商品图片',
 `goods_id` int(11) unsigned NOT NULL COMMENT '本地产品id（关联sea_goods_info表）',
 `buybox` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否有购物车',
 `FBA` double(6,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '亚马逊物流运费',
 `FBM` double(6,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '商家自己物流运费',
 `fulfilled_by` enum('MERCHANT','AMAZON') NOT NULL DEFAULT 'AMAZON' COMMENT '物流方式',
 `last_sync_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '最后同步时间',
 `create_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
 `update_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
 `delete_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除时间',
 PRIMARY KEY (`id`),
 UNIQUE KEY `asin` (`asin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='同步亚马逊线上商品主表';

CREATE TABLE `sea_goods_sync_sku` (
 `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
 `is_deleted` char(1) NOT NULL DEFAULT 'N' COMMENT '是否删除,N:未删除，Y:删除',
  `gmt_create` datetime NOT NULL DEFAULT '1970-01-01 12:00:00' COMMENT '记录创建时间',
  `gmt_modified` datetime NOT NULL DEFAULT '1970-01-01 12:00:00' COMMENT '记录修改时间,如果时间是1970年则表示纪录未修改',
  `creator` int(11) NOT NULL DEFAULT '0' COMMENT '创建人，0表示无创建人值',
  `modifier` int(11) NOT NULL DEFAULT '0' COMMENT '修改人,如果为0则表示纪录未修改',
 `sku` varchar(64) NOT NULL COMMENT 'sku子商品标识',
 `goods_online_id` int(11) unsigned NOT NULL COMMENT '同步商品id（关联sea_goods_sync_online表）',
 `price` double(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '产品销售价格',
 `sale_price` double(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '产品促销价格',
 `stock` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '库存',
 `sales_rank` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '销量排名',
 `create_at` int(11) unsigned NOT NULL,
 `update_at` int(11) unsigned NOT NULL,
 `delete_at` int(11) unsigned NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='同步亚马逊线上商品子表';

ALTER TABLE `sea_goods_sync_online` ADD `description` VARCHAR(2000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '商品描述' AFTER `title`;
ALTER TABLE `sea_goods_sync_online` ADD `item_type` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `description`;
ALTER TABLE `sea_goods_sync_online` ADD `bullet_point1` VARCHAR(500) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '卖点1' AFTER `description`, ADD `bullet_point2` VARCHAR(500) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '卖点2' AFTER `bullet_point1`, ADD `bullet_point3` VARCHAR(500) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '卖点3' AFTER `bullet_point2`, ADD `bullet_point4` VARCHAR(500) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '卖点4' AFTER `bullet_point3`, ADD `bullet_point5` VARCHAR(500) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '卖点5' AFTER `bullet_point4`;
ALTER TABLE `sea_goods_sync_online` ADD `keywords1` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '关键词1' AFTER `description`, ADD `keywords2` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '关键词2' AFTER `keywords1`, ADD `keywords3` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '关键词3' AFTER `keywords2`, ADD `keywords4` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '关键词4' AFTER `keywords3`, ADD `keywords5` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '关键词5' AFTER `keywords4`;
ALTER TABLE `sea_goods_sync_online` CHANGE `goods_id` `goods_id` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '本地产品id（关联sea_goods_info表）';
ALTER TABLE `sea_goods_sync_online` ADD `shipping_fee` DOUBLE(6,2) UNSIGNED NOT NULL DEFAULT '0' COMMENT '运费' AFTER `FBM`;

ALTER TABLE `sea_goods_sync_sku` CHANGE `update_at` `update_at` INT(11) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `sea_goods_sync_sku` CHANGE `delete_at` `delete_at` INT(11) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `sea_goods_sync_online` CHANGE `create_at` `create_at` INT(11) UNSIGNED NOT NULL COMMENT '创建时间';

ALTER TABLE `sea_goods_sync_sku` ADD `sales_begin_date` DATETIME NULL COMMENT '促销开始时间' AFTER `price`, ADD `sales_end_date` DATETIME NULL COMMENT '促销结束时间' AFTER `sales_begin_date`;
ALTER TABLE `sea_goods_sync_sku` ADD `current_price` DOUBLE(10,2) UNSIGNED NOT NULL COMMENT '当前价格' AFTER `sale_price`;

ALTER TABLE `sea_goods_sync_online` ADD `shop_id` INT(11) UNSIGNED NOT NULL COMMENT '店铺id' AFTER `goods_id`;
CREATE TABLE `sea_queue_sync` (
 `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
 `shop_id` int(11) unsigned NOT NULL COMMENT '店铺id',
 `sync_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '同步成功的时间',
 `report_id` varchar(128) NOT NULL DEFAULT '' COMMENT '亚马逊返回的用于下次提交的id',
 `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '同步状态',
 `gmt_create` datetime NOT NULL COMMENT '创建时间',
 `gmt_modified` datetime NOT NULL COMMENT '更新时间',
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `sea_queue_sync` CHANGE `report_id` `report_id` VARCHAR(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '亚马逊返回的用于下次提交的id';

CREATE TABLE `sea_queue_updategoods` (
 `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
 `shop_id` int(11) unsigned NOT NULL COMMENT '店铺id',
 `sku_ids` varchar(2000) DEFAULT NULL COMMENT '商品sku_id（多个用,分割）',
 `goods_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '商品id',
 `type` enum('basic','price','sale','stock') NOT NULL COMMENT '更新的数据类型（basic：基本信息，price：价格，sale：促销价格，storc：库存）',
 `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '更新的状态',
 `gmt_create` datetime NOT NULL COMMENT '创建时间',
 `gmt_modified` datetime NOT NULL COMMENT '更新时间',
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品更新队列表';


#2016-07-02
ALTER TABLE `sea_goods_sync_online` CHANGE `title` `title` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '标题';
ALTER TABLE `sea_queue_updategoods` CHANGE `type` `type` ENUM('basic','price','sale','stock','saleprice') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '更新的数据类型（basic：基本信息，price：价格，sale：促销价格，storc：库存，saleprice：价格&促销价格）';
ALTER TABLE `sea_goods_sync_online` CHANGE `title` `title` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '标题';

#2016-07-04
ALTER TABLE `sea_goods_sync_sku` CHANGE `current_price` `current_price` DOUBLE(10,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '当前价格';

ALTER TABLE `sea_goods_sync_online` DROP `bullet_point2`, DROP `bullet_point3`, DROP `bullet_point4`, DROP `bullet_point5`;
ALTER TABLE `sea_goods_sync_online` CHANGE `bullet_point1` `bullet_points` VARCHAR(2000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '卖点';
ALTER TABLE `sea_goods_sync_online` DROP `keywords2`, DROP `keywords3`, DROP `keywords4`, DROP `keywords5`;
ALTER TABLE `sea_goods_sync_online` CHANGE `keywords1` `keywords` VARCHAR(500) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '关键词';


#2016-07-05
ALTER TABLE `sea_goods_sync_sku` ADD `shipping_fee` DOUBLE(6,2) UNSIGNED NOT NULL DEFAULT '0' COMMENT '运费' AFTER `current_price`;

#2016-07-07
ALTER TABLE `sea_goods_sync_sku` ADD `asin` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '亚马逊产品编号' AFTER `modifier`;

ALTER TABLE `sea_goods_sync_online` CHANGE `bullet_points` `bullet_points` VARCHAR(2000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '卖点（数据是通过serialize函数格式化之后的）';

#2016-07-08
ALTER TABLE `sea_amazon_feeds` ADD `tpl_id` INT(11) UNSIGNED NOT NULL COMMENT '分类模板id' AFTER `data`;
ALTER TABLE `sea_goods_info` ADD `check_error_msg` VARCHAR(1000) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '检测错误消息（serialize序列化）' AFTER `category_name`;

#2016-07-09 echo add

CREATE TABLE `sea_queue_sync_middle` (
 `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
 `shop_id` int(11) unsigned NOT NULL COMMENT '店铺id',
 `task_id` int(11) NOT NULL COMMENT '',
 `content` text NOT NULL COMMENT '序列化后的在线售卖商品',
 `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '更新的状态',
 `gmt_create` datetime NOT NULL COMMENT '创建时间',
 `gmt_modified` datetime NOT NULL COMMENT '更新时间',
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品更新 中间临时表（存放在线售卖商品信息）';


#2016-07-12
CREATE TABLE `sea_followseller_monitor` (
 `id` int(11) unsigned NOT NULL,
 `user_id` int(10) unsigned NOT NULL COMMENT '用户id',
 `asin` char(10) NOT NULL COMMENT 'asin值',
 `country` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '所属国家（1：美国，2：英国，...）',
 `item_name` varchar(255) DEFAULT NULL COMMENT '产品名称',
 `image_url` varchar(255) DEFAULT NULL COMMENT '产品图片',
 `exclude_seller` varchar(255) DEFAULT NULL COMMENT '排除卖家的seller_id(多个用,连接)',
 `seller_count` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '跟卖卖家数量',
 `buybox_seller` varchar(60) DEFAULT NULL COMMENT '购物车所属卖家（卖家名称）',
 `last_monitor_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '最后监控时间',
 `is_monitor` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否监听（0：否，1：是）',
 `is_deleted` enum('Y','N') NOT NULL DEFAULT 'N',
 `gmt_create` datetime NOT NULL,
 `gmt_modified` datetime DEFAULT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='跟卖监控主表';

CREATE TABLE `sea_followseller_detail` (
 `id` int(11) unsigned NOT NULL,
 `monitor_id` int(11) unsigned NOT NULL COMMENT '监控id（关联sea_followseller_monitor表）',
 `seller_name` varchar(60) NOT NULL COMMENT '卖家名称',
 `seller_id` varchar(30) NOT NULL COMMENT '卖家名称',
 `price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '单价',
 `shopping_fee` decimal(6,2) NOT NULL DEFAULT '0.00' COMMENT '邮费',
 `isFBA` tinyint(1) unsigned NOT NULL COMMENT '是否是FBA（1：是，0：否）',
 `follow_sell_at` int(11) unsigned NOT NULL COMMENT '跟卖时间',
 `follow_sell_end_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '跟卖结束时间',
 `last_monitor_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '最后监控时间',
 `is_deleted` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '是否删除（Y：是，N：否）',
 `gmt_create` datetime NOT NULL,
 `gmt_modified` datetime DEFAULT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='跟卖详细信息表';

ALTER TABLE `sea_followseller_monitor` ADD `fba_count` SMALLINT(6) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'FBA总数' AFTER `seller_count`;

#2016-07-12
ALTER TABLE `sea_followseller_monitor` MODIFY `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `sea_followseller_detail` MODIFY `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT;

#2016-07-14

ALTER TABLE `sea_followseller_monitor` ADD `amazon_seller_count` SMALLINT(6) UNSIGNED NOT NULL COMMENT '亚马逊实际跟卖数量' AFTER `seller_count`;
ALTER TABLE `sea_followseller_monitor` CHANGE `amazon_seller_count` `amazon_seller_count` SMALLINT(6) UNSIGNED NOT NULL DEFAULT '0' COMMENT '亚马逊实际跟卖数量';


ALTER TABLE `sea_followseller_monitor` ADD `low_price` DECIMAL(10,2) UNSIGNED NOT NULL DEFAULT '0' COMMENT '最低总价' AFTER `buybox_seller`;
ALTER TABLE `sea_followseller_detail` CHANGE `seller_name` `seller_name` VARCHAR(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '卖家名称';


ALTER TABLE `sea_followseller_monitor` ADD `modifier` INT(11) NOT NULL DEFAULT '0' COMMENT '修改人,如果为0则表示纪录未修改' AFTER `gmt_modified`;
#@all 线上数据库已同步到此，后续sql 加在后面 2016-07-15
#-------------------------------------------------------------------------------#

#2016-07-19
CREATE TABLE `sea_goods_entity` (
 `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '商品id，程序生成',
 `is_deleted` ENUM('Y','N') NOT NULL DEFAULT 'N' COMMENT '是否删除,N:未删除，Y:删除',
 `gmt_create` datetime  COMMENT '记录创建时间',
 `gmt_modified` datetime  COMMENT '记录修改时间',
 `shop_id` int(11) NOT NULL DEFAULT '0' COMMENT '门店id',
 `supply_link` varchar(200) DEFAULT '' COMMENT '货源链接',
 `item_name` varchar(120) NOT NULL DEFAULT '' COMMENT '产品名称',
 `item_type` varchar(255) NOT NULL DEFAULT '' COMMENT '产品类别：数据来源 产品分类',
 `feed_product_type` varchar(40) DEFAULT '' COMMENT '变体模版',
 `is_brand` ENUM('Y','N') NOT NULL DEFAULT 'N' COMMENT '是否品牌入驻卖家,N:不是，Y:是',
 `product_description` text COMMENT '产品描述',
 `stocking_time` int(11) NOT NULL DEFAULT '0' COMMENT '备货时间',
 `brand_name` varchar(20) NOT NULL DEFAULT '' COMMENT '商品品牌',
 `manufacturer` varchar(64) NOT NULL DEFAULT '' COMMENT '商品生产商',
 `condition_type` varchar(32) NOT NULL DEFAULT '1' COMMENT '商品状态 :new used',
 `list_price` decimal(10,2) DEFAULT '0.00' COMMENT '制造商建议零售价',
 `external_product_id_type` varchar(20) NOT NULL DEFAULT '' COMMENT '商品编码类型：UPC／EAN...',
 `website_shipping_weight` varchar(200) NOT NULL DEFAULT '' COMMENT '邮寄重量',
 `website_shipping_weight_unit_of_measure` varchar(20) NOT NULL DEFAULT '' COMMENT '邮寄单位',
 `generic_keywords` varchar(128) DEFAULT NULL COMMENT '商品关键字，多个以，号链接',
 `bullet_points` varchar(500) DEFAULT NULL COMMENT 'bullet_point参数（serialize序列化）',
 `pub_status` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '发布状态草稿箱 0待发布 1 处理中 2发布失败 3 发布成功4',
 `dealing_status` enum('upload','translate') DEFAULT NULL COMMENT '处理中状态',
 `parent_sku` varchar(32) DEFAULT NULL COMMENT 'parent_sku',
 `variation_theme` varchar(16) DEFAULT NULL COMMENT '变体名称size color sizecolor',
 `category_id` varchar(16) DEFAULT NULL COMMENT '子分类id',
 `category_name` varchar(256) DEFAULT NULL COMMENT '子分类名称（逐级）',
 `check_error_msg` varchar(1000) DEFAULT NULL COMMENT '检测错误消息（serialize序列化）',
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品主表';

CREATE TABLE `sea_goods_params` (
 `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
 `goods_id` int(11) unsigned NOT NULL COMMENT '产品id（关联sea_goods_entity表）',
 `is_deleted` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '是否删除,N:未删除，Y:删除',
 `gmt_create` datetime NOT NULL,
 `gmt_modified` datetime NOT NULL,
 `field` varchar(64) NOT NULL COMMENT '参数字段名',
 `value` varchar(64) NOT NULL COMMENT '字段对应的值',
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品参数表';

CREATE TABLE `sea_goods_sku` (
 `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
 `is_deleted` ENUM('Y','N') NOT NULL DEFAULT 'N' COMMENT '是否删除,N:未删除，Y:删除',
 `gmt_create` datetime  COMMENT '记录创建时间',
 `gmt_modified` datetime  COMMENT '记录修改时间',
 `shop_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '门店id',
 `goods_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '商品信息id',
 `external_product_id` varchar(32) NOT NULL COMMENT '商品编码',
 `standard_price` decimal(10,2) DEFAULT '0.00' COMMENT '商品价格',
 `sale_price` decimal(10,2) DEFAULT '0.00' COMMENT '商品促销价格',
 `sale_from_date` datetime COMMENT '商品促销开始时间',
 `sale_end_date` datetime COMMENT '商品促销结束时间',
 `quantity` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '库存',
 `item_sku` varchar(64) NOT NULL DEFAULT '' COMMENT 'SKU编码',
 `parent_child` varchar(32) DEFAULT NULL COMMENT 'Parent/Child',
 `relationship_type` varchar(32) DEFAULT NULL COMMENT 'Accessory/Variation',
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品sku';

CREATE TABLE `sea_goods_spec` (
 `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
 `sku_id` int(11) unsigned NOT NULL COMMENT 'sku_id（关联sea_goods_sku表）',
 `is_deleted` enum('Y','N') NOT NULL DEFAULT 'N',
 `gmt_create` datetime NOT NULL,
 `gmt_modified` datetime DEFAULT NULL,
 `field` varchar(64) NOT NULL,
 `value` varchar(64) NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品规格(变体)表';

ALTER TABLE `sea_goods_spec` ADD `goods_id` INT(11) UNSIGNED NOT NULL COMMENT 'goods_id（关联sea_goods_entity表）' AFTER `sku_id`;


ALTER TABLE `sea_goods_picture` ADD `sku_id` INT(11) NULL DEFAULT '0' COMMENT 'sku_id（关联sea_goods_sku表） ' AFTER `shop_id`;
ALTER TABLE `sea_goods_entity` CHANGE `variation_theme` `variation_theme` VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '变体名称size color sizecolor';
ALTER TABLE `sea_goods_entity` ADD `tpl_id` INT(11) NOT NULL COMMENT '模板id' AFTER `variation_theme`;

#2016-07-20
ALTER TABLE `sea_queue_sync_middle` CHANGE `content` `content` MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '序列化后的在线售卖商品';

#2016-07-21(差评监控)
CREATE TABLE `sea_bad_review_monitor` (
 `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
 `asin` int(11) NOT NULL,
 `last_monitor_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '最后监控时间',
 `review_total` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '最新差评数量',
 `user_id` int(11) unsigned NOT NULL COMMENT '用户id',
 `create_at` int(11) unsigned NOT NULL COMMENT '创建时间',
 `update_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
 PRIMARY KEY (`id`),
 UNIQUE KEY `asin_unique_key` (`asin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='差评监控表';

CREATE TABLE `sea_bad_review` (
 `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
 `monitor_id` int(11) unsigned NOT NULL COMMENT '监控id（关联sea_bad_review）',
 `image_url` varchar(128) NOT NULL COMMENT '产品图片',
 `review_info` text NOT NULL COMMENT '评论内容',
 `star` tinyint(1) unsigned NOT NULL COMMENT '星级',
 `asin` char(10) NOT NULL COMMENT '产品标识',
 `profile_id` char(14) NOT NULL COMMENT 'profile_id',
 `buyer` varchar(64) NOT NULL COMMENT '评论者名称',
 `review_date` date NOT NULL COMMENT '评论日期',
 `reply_url` varchar(200) NOT NULL COMMENT '评论回复url',
 `buyer_url` varchar(200) NOT NULL COMMENT '评论者首页url',
 `review_id` char(14) NOT NULL COMMENT '评论id',
 `create_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '记录创建时间',
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='差评表';

ALTER TABLE `sea_bad_review_monitor` CHANGE `asin` `asin` VARCHAR(20) NOT NULL;
ALTER TABLE `sea_bad_review` CHANGE `asin` `asin` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '产品标识';
ALTER TABLE `sea_bad_review` CHANGE `profile_id` `profile_id` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'profile_id';
ALTER TABLE `sea_bad_review` CHANGE `review_id` `review_id` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '评论id';

ALTER TABLE `sea_bad_review` ADD `title` VARCHAR(200) NOT NULL COMMENT '评论标题' AFTER `image_url`;

#2016-07-22
ALTER TABLE `sea_bad_review_monitor` ADD `expire_at` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '有效时间' AFTER `update_at`;
ALTER TABLE `sea_bad_review_monitor` ADD `last_date` DATE NULL COMMENT '最后评论时间' AFTER `expire_at`;
ALTER TABLE `sea_bad_review_monitor` ADD `is_read` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT '是否读取（1：是，0：否）' AFTER `last_date`;

#-------------------------------------------------------------#
#以上结构已同步线上 （同步时间：2016-07-22）
#-------------------------------------------------------------#

#2016-07-25
CREATE TABLE `sea_queue_badreview_sms` (
 `user_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
 `mobile` varchar(12) NOT NULL,
 `expire_lock` int(11) unsigned NOT NULL DEFAULT '0',
 `create_at` int(11) unsigned NOT NULL,
 `badreview_total` smallint(6) unsigned NOT NULL,
 `monitor_count` smallint(6) unsigned NOT NULL,
 `count` smallint(6) unsigned NOT NULL DEFAULT '0',
 PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='差评短信队列表';

CREATE TABLE `sea_sms_record` (
 `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
 `content` varchar(200) NOT NULL,
 `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
 `mobile` varchar(20) NOT NULL COMMENT '手机号码',
 `create_at` int(11) NOT NULL COMMENT '发送时间',
 `type` varchar(32) NOT NULL COMMENT '发送类型',
 `tpl_id` int(11) NOT NULL DEFAULT '0' COMMENT '短信模板id',
 `status` tinyint(1) unsigned NOT NULL COMMENT '发送状态（1：成功，0：失败）',
 `reason` varchar(300) NOT NULL DEFAULT '' COMMENT '发送失败原因',
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='短信记录表';
ALTER TABLE `sea_queue_badreview_sms` ADD `fail_count` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '发送失败次数' AFTER `count`;


#2016-07-26
ALTER TABLE `sea_sms_record` ADD `send_day` DATE NOT NULL COMMENT '发送日期' AFTER `id`;
#-------------------------------------------------------------#
#以上结构已同步线上 （同步时间：2016-07-26
#-------------------------------------------------------------#

#2016-10-09
ALTER TABLE `sea_goods_entity` CHANGE `tpl_id` `tpl_id` INT(11) NOT NULL DEFAULT '0' COMMENT '模板id';
ALTER TABLE `sea_goods_entity` CHANGE `stocking_time` `stocking_time` INT(11) NOT NULL DEFAULT '0' COMMENT '备货时间';
#2016-10-10
ALTER TABLE `sea_goods_sync_sku` ADD `shop_id` INT(11) NOT NULL DEFAULT '0' AFTER `delete_at`;
#2016-10-11
ALTER TABLE `sea_goods_sync_online` CHANGE `description` `description` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '商品描述';
#2016-10-12

ALTER TABLE `sea_goods_picture` modify  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY;
CREATE TABLE `sea_task_monitor` (
 `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
 `class_name` varchar(50) NOT NULL COMMENT '队列类名',
 `args` varchar(500) NOT NULL COMMENT '队列参数（serialize序列化）',
 `queue_name` varchar(50) NOT NULL COMMENT '队列名称',
 `create_at` int(11) unsigned NOT NULL COMMENT '创建时间',
 `sleep_time` int(11) unsigned NOT NULL COMMENT '休眠时间（单位s）',
 `begin_at` int(11) unsigned NOT NULL COMMENT '起始时间',
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='任务重试休眠队列监听';

ALTER TABLE `sea_goods_picture` CHANGE  `id` `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

alter table `sea_queue_sync_middle` change `task_id` `task_id` int(11) null;
#-------------------------------------------------------------#
#以上结构已同步线上 （同步时间：2016-10-20)
#-------------------------------------------------------------#


#2016-10-21
ALTER TABLE `sea_followseller_detail` CHANGE `seller_name` `seller_name` VARCHAR(256) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '卖家名称';
ALTER TABLE `sea_bad_review_monitor` ADD `locked` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否锁住' AFTER `is_read`;
#-------------------------------------------------------------#
#以上结构已同步线上 （同步时间：2016-10-22)
#-------------------------------------------------------------#


#2016-11-02
ALTER TABLE `sea_followseller_monitor` CHANGE `asin` `asin` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'asin值';

#2016-11-08
ALTER TABLE `sea_goods_sync_sku` ADD `is_adjustment_price` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '收否开启智能调价（0：不开启，1：开启）' AFTER `asin`;
#2016-11-10
CREATE TABLE `sea_bidding` (
 `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
 `goods_id` int(11) unsigned NOT NULL COMMENT '在线商品id',
 `sku_id` int(11) unsigned NOT NULL COMMENT 'sku_id关联sea_goods_sync_sku表',
 `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态（0：暂停，1：开启）',
 `cost` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '成本',
 `mix_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '最低价格',
 `max_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '最大价格',
 `rules_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '调价规则id',
 `competitors_count` mediumint(6) unsigned NOT NULL DEFAULT '0' COMMENT '最大竞争手数量',
 `my_price` decimal(10,2) NOT NULL COMMENT '我的价格（含运费）',
 `lower_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '最低价格（含运费）',
 `buybox_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '黄金购物车价格（含运费）',
 `create_at` int(11) unsigned NOT NULL COMMENT '创建时间',
 `update_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
 `last_modifyprice_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '最后一次修改价格的时间',
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='智能调价商品表';

CREATE TABLE `sea_bidding_log` (
 `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
 `modify_at` int(11) unsigned NOT NULL COMMENT '改价时间',
 `goods_title` varchar(500) NOT NULL COMMENT '商品名称',
 `asin` varchar(20) NOT NULL COMMENT '商品asin',
 `sku` varchar(60) NOT NULL COMMENT '商品sku',
 `mix_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '最小价格',
 `max_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '最大价格',
 `rules_name` varchar(60) NOT NULL COMMENT '规则名称',
 `adjust_status` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '调价状态',
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='智能调价日志表';

CREATE TABLE `sea_bidding_rules` (
 `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
 `name` varchar(50) NOT NULL COMMENT '规则名称',
 `description` varchar(500) DEFAULT NULL COMMENT '描述',
 `buybox_set` tinyint(2) unsigned DEFAULT NULL COMMENT '购物车设定(1：降低或提高黄金购物车价格，2：提高我的黄金购物车价格最大化利润，3：降低我的黄金购物车内价格以保持竞争力，4：不要改变我的黄金购物车价格，5：暂停黄金购物车设定)',
 `buybox_set_value1` decimal(6,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '降低多少价格',
 `bybox_set_value2` decimal(6,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '提高多少价格',
 `buybox_set_math` enum('$','%') NOT NULL DEFAULT '$' COMMENT '运算方式',
 `bubbox_item` enum('max','stop') NOT NULL DEFAULT 'max' COMMENT '当调整后的价格高于最大价格的处理方式',
 `competitors` varchar(40) NOT NULL COMMENT '多个以逗号分割',
 `create_at` int(11) unsigned NOT NULL COMMENT '创建时间',
 `update_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='智能调价规则主表';

CREATE TABLE `sea_bidding_rules_type` (
 `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
 `type` varchar(20) NOT NULL COMMENT '类型名称（basic,protected,fba_vs_fba,fba_vs_fbm,fbm_vs_fba,fbm_vs_fbm）',
 `rules_id` int(11) unsigned NOT NULL COMMENT '关联sea_bidding_rules表',
 `is_open` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否开启（0：关闭，1：开启）',
 `create_at` int(11) unsigned NOT NULL COMMENT '创建时间',
 `update_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
 PRIMARY KEY (`id`),
 KEY `rules_id` (`rules_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `sea_bidding_rules_item` (
 `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
 `rules_id` int(11) unsigned NOT NULL COMMENT '规则id关联sea_bidding_rules表',
 `type_id` int(2) unsigned NOT NULL COMMENT '类型关联sea_bidding_rules_type表',
 `symbol` enum('-','+') DEFAULT NULL COMMENT '运算符',
 `value` decimal(6,2) NOT NULL DEFAULT '0.01' COMMENT '用于运算的值',
 `math` enum('$','%') DEFAULT NULL COMMENT '计算方式',
 `compare` enum('gt','lt','eq','none','both','after_le') DEFAULT NULL COMMENT '比较规则（gt：大于最小价格，lt：小于最小价格，eq：等于最小价格，none：无竞争者，both：不再最大价格跟最小价格之间，after_le：当调整后的价格等于或低于最小价格）',
 `options` enum('auto','min','max','stop','customize') DEFAULT NULL COMMENT '选项（auto：使用自动竞争，min：使用最小价格，max：使用最大价格，stop：不智能调价，customize：自定义您的价格）',
 `item` enum('competitor','min','max') DEFAULT NULL COMMENT '使用价格比较的条件（competitor：对手的竞争价格，min：最小价格，max：最大价格）',
 `create_at` int(11) unsigned NOT NULL COMMENT '创建时间',
 `update_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
 PRIMARY KEY (`id`),
 KEY `rules_id` (`rules_id`),
 KEY `type_id` (`type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='智能调价规则详细表';

ALTER TABLE `sea_bidding_rules_item` CHANGE `math` `math1` ENUM('$','%') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '计算方式';
ALTER TABLE `sea_bidding_rules_item` ADD `math2` ENUM('$','%') NULL COMMENT '计算方式2' AFTER `math1`;
ALTER TABLE `sea_bidding` ADD `shop_id` INT(11) UNSIGNED NOT NULL COMMENT '店铺id关联sea_store表' AFTER `sku_id`;

ALTER TABLE `sea_bidding` CHANGE `my_price` `my_price` DECIMAL(10,2) NOT NULL COMMENT '我的价格（不含运费）';
ALTER TABLE `sea_bidding` CHANGE `lower_price` `lower_price` DECIMAL(10,2) NOT NULL DEFAULT '0.00' COMMENT '最低价格（不含运费）';
ALTER TABLE `sea_bidding` CHANGE `buybox_price` `buybox_price` DECIMAL(10,2) NOT NULL DEFAULT '0.00' COMMENT '黄金购物车价格（不含运费）';
ALTER TABLE `sea_bidding` ADD `my_price_fare` DECIMAL(6,2) UNSIGNED NOT NULL DEFAULT '0' COMMENT '我的价格' AFTER `my_price`;
ALTER TABLE `sea_bidding` ADD `lower_price_far` DECIMAL(6,2) UNSIGNED NOT NULL DEFAULT '0' COMMENT '最低价格运费' AFTER `lower_price`;
ALTER TABLE `sea_bidding` ADD `buybox_price_fare` DECIMAL(6,2) UNSIGNED NOT NULL DEFAULT '0' COMMENT '黄金购物车价格运费' AFTER `buybox_price`;

#2016-11-11
ALTER TABLE `sea_bidding_rules` ADD `shop_id` INT(11) UNSIGNED NOT NULL COMMENT '店铺id' AFTER `name`;
ALTER TABLE `sea_bidding_rules` CHANGE `competitors` `competitors` VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '多个以逗号分割（Amazon：亚马逊自营，FBA：亚马逊发货，FBM：商家自发货，non_featured_sellers：非特色卖家）';
ALTER TABLE `sea_bidding_rules_item` drop column `math2`;
ALTER TABLE `sea_bidding_rules_item` CHANGE `math1` `math` ENUM('$','%') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '计算方式';
ALTER TABLE `sea_bidding_rules` CHANGE `buybox_set_math` `buybox_set_math1` ENUM('$','%') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '$' COMMENT '运算方式';
ALTER TABLE `sea_bidding_rules` ADD `buybox_set_math2` ENUM('$','%') NULL COMMENT '计算方式2' AFTER `buybox_set_math1`;
ALTER TABLE `sea_bidding_rules_item` CHANGE `value` `value` DECIMAL(6,2) NULL COMMENT '用于运算的值';
ALTER TABLE `sea_bidding_rules_item` CHANGE `type_id` `type_id` INT(11) UNSIGNED NOT NULL COMMENT '类型关联sea_bidding_rules_type表';
ALTER TABLE `sea_bidding_rules_item` CHANGE `compare` `compare` ENUM('gt','lt','eq','none','both','after_le') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '比较规则（gt：大于最小价格，lt：小于最小价格，eq：等于最小价格，none：无竞争者，both：不再最大价格跟最小价格之间，after_le：当调整后的价格等于或低于最小价格）';

#2016-11-14
ALTER TABLE `sea_bidding_rules` CHANGE `bybox_set_value2` `buybox_set_value2` DECIMAL(6,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '提高多少价格';
ALTER TABLE `sea_bidding_rules` CHANGE `bubbox_item` `buybox_item` ENUM('max','stop') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'max' COMMENT '当调整后的价格高于最大价格的处理方式';

#2016-11-16
ALTER TABLE `sea_bidding` ADD `locked` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '锁定时间' AFTER `last_modifyprice_at`;
ALTER TABLE `sea_bidding` ADD INDEX(`locked`);
ALTER TABLE `sea_bidding_log` ADD `before_price` DECIMAL(10,2) NOT NULL DEFAULT '0' COMMENT '调价之前' AFTER `adjust_status`;
ALTER TABLE `sea_bidding_log` ADD `after_price` DECIMAL(10,2) NOT NULL DEFAULT '0' COMMENT '调价之后' AFTER `before_price`;
ALTER TABLE `sea_bidding_log` ADD `change_price` DECIMAL(6,2) NOT NULL DEFAULT '0' COMMENT '变动价格' AFTER `after_price`;
ALTER TABLE `sea_bidding_log` ADD `date` DATE NOT NULL COMMENT '调价日期' AFTER `modify_at`;
ALTER TABLE `sea_bidding_log` ADD `shop_id` INT(11) UNSIGNED NOT NULL COMMENT '店铺id' AFTER `id`;

#2016-11-17
ALTER TABLE `sea_goods_sync_sku` ADD `fulfillment_channel` VARCHAR(50) NULL COMMENT '物流方式' AFTER `shop_id`;
#2016-11-19
ALTER TABLE `sea_bidding` ADD INDEX(`sku_id`);
ALTER TABLE `sea_bidding` ADD INDEX(`shop_id`);