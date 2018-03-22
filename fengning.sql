-- phpMyAdmin SQL Dump
-- version phpStudy 2014
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2018 �?03 �?22 �?07:49
-- 服务器版本: 5.5.53
-- PHP 版本: 5.6.27

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `fengning`
--

-- --------------------------------------------------------

--
-- 表的结构 `fengning_admin`
--

CREATE TABLE IF NOT EXISTS `fengning_admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nickname` varchar(20) DEFAULT NULL COMMENT '昵称',
  `name` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `thumb` int(11) NOT NULL DEFAULT '1' COMMENT '管理员头像',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL COMMENT '修改时间',
  `login_time` int(11) DEFAULT NULL COMMENT '最后登录时间',
  `login_ip` varchar(100) DEFAULT NULL COMMENT '最后登录ip',
  `admin_cate_id` int(2) NOT NULL DEFAULT '1' COMMENT '管理员分组',
  `admin_group_id` int(2) DEFAULT '0' COMMENT '组织机构',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `id` (`id`) USING BTREE,
  KEY `admin_cate_id` (`admin_cate_id`) USING BTREE,
  KEY `nickname` (`nickname`) USING BTREE,
  KEY `create_time` (`create_time`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18 ;

--
-- 转存表中的数据 `fengning_admin`
--

INSERT INTO `fengning_admin` (`id`, `nickname`, `name`, `password`, `thumb`, `create_time`, `update_time`, `login_time`, `login_ip`, `admin_cate_id`, `admin_group_id`, `status`) VALUES
(1, 'admin', 'admin', '9eb2b9ad495a75f80f9cf67ed08bbaae', 2, 1510885948, 1521552127, 1521696100, '0.0.0.0', 1, NULL, 1),
(16, 'test', 'test', '9eb2b9ad495a75f80f9cf67ed08bbaae', 3, 1521546733, 1521546733, 1521547355, '0.0.0.0', 1, NULL, 1);

-- --------------------------------------------------------

--
-- 表的结构 `fengning_admin_cate`
--

CREATE TABLE IF NOT EXISTS `fengning_admin_cate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `permissions` text COMMENT '权限菜单',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  `desc` text COMMENT '备注',
  PRIMARY KEY (`id`),
  KEY `id` (`id`) USING BTREE,
  KEY `name` (`name`) USING BTREE,
  KEY `create_time` (`create_time`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;

--
-- 转存表中的数据 `fengning_admin_cate`
--

INSERT INTO `fengning_admin_cate` (`id`, `name`, `permissions`, `create_time`, `update_time`, `desc`) VALUES
(1, '超级管理员', '54,55,56,6,7,8,11,13,14,16,17,19,20,21,25,26,53,28,29', 0, 1521548328, '超级管理员，拥有最高权限！');

-- --------------------------------------------------------

--
-- 表的结构 `fengning_admin_group`
--

CREATE TABLE IF NOT EXISTS `fengning_admin_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  `desc` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `fengning_admin_group`
--

INSERT INTO `fengning_admin_group` (`id`, `name`, `create_time`, `update_time`, `desc`) VALUES
(1, '北京院', 0, 0, NULL);

-- --------------------------------------------------------

--
-- 表的结构 `fengning_admin_log`
--

CREATE TABLE IF NOT EXISTS `fengning_admin_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_menu_id` int(11) NOT NULL COMMENT '操作菜单id',
  `admin_id` int(11) NOT NULL COMMENT '操作者id',
  `ip` varchar(100) DEFAULT NULL COMMENT '操作ip',
  `operation_id` varchar(200) DEFAULT NULL COMMENT '操作关联id',
  `create_time` int(11) NOT NULL COMMENT '操作时间',
  PRIMARY KEY (`id`),
  KEY `id` (`id`) USING BTREE,
  KEY `admin_id` (`admin_id`) USING BTREE,
  KEY `create_time` (`create_time`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=65 ;

--
-- 转存表中的数据 `fengning_admin_log`
--

INSERT INTO `fengning_admin_log` (`id`, `admin_menu_id`, `admin_id`, `ip`, `operation_id`, `create_time`) VALUES
(1, 28, 1, '0.0.0.0', '1', 1521546097),
(2, 4, 1, '0.0.0.0', '53', 1521546592),
(3, 28, 1, '0.0.0.0', '1', 1521546620),
(4, 53, 1, '0.0.0.0', '1', 1521546700),
(5, 53, 1, '0.0.0.0', '1', 1521546706),
(6, 49, 1, '0.0.0.0', '3', 1521546720),
(7, 25, 1, '0.0.0.0', '16', 1521546733),
(8, 53, 1, '0.0.0.0', '16', 1521546740),
(9, 50, 1, '0.0.0.0', '', 1521547210),
(10, 50, 1, '0.0.0.0', '', 1521547334),
(11, 50, 16, '0.0.0.0', '', 1521547355),
(12, 50, 1, '0.0.0.0', '', 1521547425),
(13, 4, 1, '0.0.0.0', '22', 1521547864),
(14, 4, 1, '0.0.0.0', '1', 1521547879),
(15, 4, 1, '0.0.0.0', '1', 1521547887),
(16, 4, 1, '0.0.0.0', '2', 1521547960),
(17, 4, 1, '0.0.0.0', '54', 1521548049),
(18, 4, 1, '0.0.0.0', '55', 1521548145),
(19, 4, 1, '0.0.0.0', '56', 1521548199),
(20, 5, 1, '0.0.0.0', '51', 1521548209),
(21, 5, 1, '0.0.0.0', '4', 1521548213),
(22, 56, 1, '0.0.0.0', '5', 1521548218),
(23, 28, 1, '0.0.0.0', '1', 1521548328),
(24, 56, 1, '0.0.0.0', '3', 1521548350),
(25, 55, 1, '0.0.0.0', '2', 1521548368),
(26, 55, 1, '0.0.0.0', '6', 1521548398),
(27, 55, 1, '0.0.0.0', '9', 1521548417),
(28, 55, 1, '0.0.0.0', '30', 1521548584),
(29, 55, 1, '0.0.0.0', '23', 1521548740),
(30, 55, 1, '0.0.0.0', '27', 1521548843),
(31, 55, 1, '0.0.0.0', '23', 1521548870),
(32, 55, 1, '0.0.0.0', '23', 1521548916),
(33, 55, 1, '0.0.0.0', '25', 1521548933),
(34, 55, 1, '0.0.0.0', '26', 1521548955),
(35, 55, 1, '0.0.0.0', '53', 1521548970),
(36, 56, 1, '0.0.0.0', '24', 1521548979),
(37, 50, 1, '0.0.0.0', '', 1521550829),
(38, 55, 1, '0.0.0.0', '7', 1521551892),
(39, 55, 1, '0.0.0.0', '8', 1521551901),
(40, 7, 1, '0.0.0.0', '1', 1521552127),
(41, 53, 1, '0.0.0.0', '16', 1521553855),
(42, 53, 1, '0.0.0.0', '16', 1521553862),
(43, 53, 1, '0.0.0.0', '16', 1521553871),
(44, 53, 1, '0.0.0.0', '16', 1521553883),
(45, 53, 1, '0.0.0.0', '16', 1521553888),
(46, 49, 1, '0.0.0.0', '4', 1521553929),
(47, 25, 1, '0.0.0.0', '17', 1521553949),
(48, 26, 1, '0.0.0.0', '17', 1521553963),
(49, 50, 1, '0.0.0.0', '', 1521594183),
(50, 55, 1, '0.0.0.0', '6', 1521594256),
(51, 11, 1, '0.0.0.0', '', 1521595387),
(52, 55, 1, '0.0.0.0', '7', 1521597809),
(53, 55, 1, '0.0.0.0', '8', 1521597829),
(54, 55, 1, '0.0.0.0', '7', 1521597848),
(55, 56, 1, '0.0.0.0', '6', 1521597983),
(56, 55, 1, '0.0.0.0', '7', 1521598011),
(57, 11, 1, '0.0.0.0', '', 1521599021),
(58, 55, 1, '0.0.0.0', '57', 1521614383),
(59, 50, 1, '0.0.0.0', '', 1521680564),
(60, 56, 1, '0.0.0.0', '57', 1521681384),
(61, 56, 1, '0.0.0.0', '50', 1521681490),
(62, 55, 1, '0.0.0.0', '58', 1521681681),
(63, 58, 1, '0.0.0.0', '', 1521681702),
(64, 58, 1, '0.0.0.0', '', 1521696100);

-- --------------------------------------------------------

--
-- 表的结构 `fengning_admin_menu`
--

CREATE TABLE IF NOT EXISTS `fengning_admin_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `module` varchar(50) NOT NULL COMMENT '模块',
  `controller` varchar(100) NOT NULL COMMENT '控制器',
  `function` varchar(100) NOT NULL COMMENT '方法',
  `parameter` varchar(50) DEFAULT NULL COMMENT '参数',
  `description` varchar(250) DEFAULT NULL COMMENT '描述',
  `is_display` int(1) NOT NULL DEFAULT '1' COMMENT '1显示在左侧菜单2只作为节点',
  `type` int(1) NOT NULL DEFAULT '1' COMMENT '1权限节点2普通节点',
  `pid` int(11) NOT NULL DEFAULT '0' COMMENT '上级菜单0为顶级菜单',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  `icon` varchar(100) DEFAULT NULL COMMENT '图标',
  `is_open` int(1) NOT NULL DEFAULT '0' COMMENT '0默认闭合1默认展开',
  `orders` int(11) NOT NULL DEFAULT '0' COMMENT '排序值，越小越靠前',
  PRIMARY KEY (`id`),
  KEY `id` (`id`) USING BTREE,
  KEY `module` (`module`) USING BTREE,
  KEY `controller` (`controller`) USING BTREE,
  KEY `function` (`function`) USING BTREE,
  KEY `is_display` (`is_display`) USING BTREE,
  KEY `type` (`type`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='系统菜单表' AUTO_INCREMENT=59 ;

--
-- 转存表中的数据 `fengning_admin_menu`
--

INSERT INTO `fengning_admin_menu` (`id`, `name`, `module`, `controller`, `function`, `parameter`, `description`, `is_display`, `type`, `pid`, `create_time`, `update_time`, `icon`, `is_open`, `orders`) VALUES
(1, '系统管理', '', '', '', '', '系统设置。', 1, 2, 0, 0, 1521547887, 'fa-cog', 0, 0),
(2, '功能列表', 'admin', 'menu', 'index', '', '功能管理。', 1, 2, 1, 0, 1521548368, 'fa-paw', 0, 0),
(7, '个人信息', 'admin', 'admin', 'personal', '', '个人信息修改。', 2, 2, 0, 1516949435, 1521598011, 'fa-user', 1, 0),
(8, '修改密码', 'admin', 'admin', 'editpassword', '', '管理员修改个人密码。', 2, 2, 0, 1516949702, 1521597829, 'fa-unlock-alt', 0, 0),
(9, '系统设置', '', '', '', '', '系统相关设置。', 1, 2, 1, 1516949853, 1521548417, 'fa-cog', 0, 0),
(10, '网站设置', 'admin', 'webconfig', 'index', '', '网站相关设置首页。', 1, 2, 9, 1516949994, 1516949994, 'fa-bullseye', 0, 0),
(11, '修改网站设置', 'admin', 'webconfig', 'publish', '', '修改网站设置。', 2, 1, 10, 1516950047, 1516950047, '', 0, 0),
(12, '邮件设置', 'admin', 'emailconfig', 'index', '', '邮件配置首页。', 1, 2, 9, 1516950129, 1516950129, 'fa-envelope', 0, 0),
(13, '修改邮件设置', 'admin', 'emailconfig', 'publish', '', '修改邮件设置。', 2, 1, 12, 1516950215, 1516950215, '', 0, 0),
(14, '发送测试邮件', 'admin', 'emailconfig', 'mailto', '', '发送测试邮件。', 2, 1, 12, 1516950295, 1516950295, '', 0, 0),
(15, '短信设置', 'admin', 'smsconfig', 'index', '', '短信设置首页。', 1, 2, 9, 1516950394, 1516950394, 'fa-comments', 0, 0),
(16, '修改短信设置', 'admin', 'smsconfig', 'publish', '', '修改短信设置。', 2, 1, 15, 1516950447, 1516950447, '', 0, 0),
(17, '发送测试短信', 'admin', 'smsconfig', 'smsto', '', '发送测试短信。', 2, 1, 15, 1516950483, 1516950483, '', 0, 0),
(18, 'URL 设置', 'admin', 'urlsconfig', 'index', '', 'url 设置。', 1, 2, 9, 1516950738, 1516950804, 'fa-code-fork', 0, 0),
(19, '新增/修改url设置', 'admin', 'urlsconfig', 'publish', '', '新增/修改url设置。', 2, 1, 18, 1516950850, 1516950850, '', 0, 0),
(20, '启用/禁用url美化', 'admin', 'urlsconfig', 'status', '', '启用/禁用url美化。', 2, 1, 18, 1516950909, 1516950909, '', 0, 0),
(21, ' 删除url美化规则', 'admin', 'urlsconfig', 'delete', '', ' 删除url美化规则。', 2, 1, 18, 1516950941, 1516950941, '', 0, 0),
(22, '用户管理', '', '', '', '', '用户和组织机构管理。', 1, 2, 0, 1516950991, 1521547864, 'fa-users', 0, 0),
(23, '组织机构', 'admin', 'admin', 'index', '', '组织机构管理', 1, 2, 22, 1516951071, 1521548916, ' fa fa-user-circle-o', 0, 0),
(25, '新增/修改管理员', 'admin', 'admin', 'publish', '', '新增/修改系统管理员。', 2, 1, 23, 1516951224, 1521548933, '', 0, 0),
(26, '删除管理员', 'admin', 'admin', 'delete', '', '删除管理员。', 2, 1, 23, 1516951253, 1521548955, '', 0, 0),
(27, '角色管理', 'admin', 'admin', 'admincate', '', '权限分组。', 1, 2, 22, 1516951353, 1521548843, 'fa-dot-circle-o', 0, 0),
(28, '新增/修改权限组', 'admin', 'admin', 'admincatepublish', '', '新增/修改权限组。', 2, 1, 27, 1516951483, 1516951483, '', 0, 0),
(29, '删除权限组', 'admin', 'admin', 'admincatedelete', '', '删除权限组。', 2, 1, 27, 1516951515, 1516951515, '', 0, 0),
(30, '操作日志', 'admin', 'admin', 'log', '', '系统管理员操作日志。', 1, 2, 22, 1516951754, 1521548584, 'fa-pencil', 0, 0),
(53, '禁用/开启', 'admin', 'admin', 'audit', '', '禁用或开启用户', 2, 1, 23, 1521546592, 1521548970, 'fa fa-lock', 0, 0),
(54, '排序', 'admin', 'menu', 'orders', '', '系统功能排序。', 2, 1, 2, 1521548049, 1521548049, '', 0, 0),
(55, '新增/修改系统功能', 'admin', 'menu', 'publish', '', '新增/修改系统功能。', 2, 1, 2, 1521548145, 1521548145, '', 0, 0),
(56, '删除系统功能', 'admin', 'menu', 'delete', '', '删除系统功能。', 2, 1, 2, 1521548199, 1521548199, '', 0, 0),
(49, '附件上传', 'admin', 'common', 'upload', '', '附件上传。', 2, 2, 0, 1516954491, 1521529896, '', 0, 0),
(58, '用户登录', 'admin', 'common', 'login', '', '用户登录', 2, 2, 0, 0, 1521681681, '', 0, 0);

-- --------------------------------------------------------

--
-- 表的结构 `fengning_attachment`
--

CREATE TABLE IF NOT EXISTS `fengning_attachment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `module` char(15) NOT NULL DEFAULT '' COMMENT '所属模块',
  `filename` char(50) NOT NULL DEFAULT '' COMMENT '文件名',
  `filepath` char(200) NOT NULL DEFAULT '' COMMENT '文件路径+文件名',
  `filesize` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文件大小',
  `fileext` char(10) NOT NULL DEFAULT '' COMMENT '文件后缀',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '会员ID',
  `uploadip` char(15) NOT NULL DEFAULT '' COMMENT '上传IP',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0未审核1已审核-1不通过',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0',
  `admin_id` int(11) NOT NULL COMMENT '审核者id',
  `audit_time` int(11) NOT NULL COMMENT '审核时间',
  `use` varchar(200) DEFAULT NULL COMMENT '用处',
  `download` int(11) NOT NULL DEFAULT '0' COMMENT '下载量',
  PRIMARY KEY (`id`),
  KEY `id` (`id`) USING BTREE,
  KEY `status` (`status`) USING BTREE,
  KEY `filename` (`filename`) USING BTREE,
  KEY `create_time` (`create_time`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='附件表' AUTO_INCREMENT=5 ;

--
-- 转存表中的数据 `fengning_attachment`
--

INSERT INTO `fengning_attachment` (`id`, `module`, `filename`, `filepath`, `filesize`, `fileext`, `user_id`, `uploadip`, `status`, `create_time`, `admin_id`, `audit_time`, `use`, `download`) VALUES
(1, 'admin', '79811855a6c06de53047471c4ff82a36.jpg', '\\uploads\\admin\\admin_thumb\\20180104\\79811855a6c06de53047471c4ff82a36.jpg', 13781, 'jpg', 1, '127.0.0.1', 1, 1515046060, 1, 1515046060, 'admin_thumb', 0),
(2, 'admin', '0c5c0171e5ff3ee9856e58cad3bda271.jpg', '\\uploads\\admin\\admin_thumb\\20180320\\0c5c0171e5ff3ee9856e58cad3bda271.jpg', 15040, 'jpg', 1, '0.0.0.0', 1, 1521538164, 1, 1521538164, 'admin_thumb', 0),
(3, 'admin', '35a1c75dcc2f82bc7dfbe64ac3f2e168.jpg', '\\uploads\\admin\\admin_thumb\\20180320\\35a1c75dcc2f82bc7dfbe64ac3f2e168.jpg', 18694, 'jpg', 1, '0.0.0.0', 1, 1521546719, 1, 1521546719, 'admin_thumb', 0),
(4, 'admin', 'a97e9a2e9822497a61f861f2ed27d44a.jpg', '\\uploads\\admin\\admin_thumb\\20180320\\a97e9a2e9822497a61f861f2ed27d44a.jpg', 15040, 'jpg', 1, '0.0.0.0', 1, 1521553929, 1, 1521553929, 'admin_thumb', 0);

-- --------------------------------------------------------

--
-- 表的结构 `fengning_datatables_example`
--

CREATE TABLE IF NOT EXISTS `fengning_datatables_example` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `office` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

--
-- 转存表中的数据 `fengning_datatables_example`
--

INSERT INTO `fengning_datatables_example` (`id`, `name`, `position`, `office`) VALUES
(1, '张三', '1', '绿地'),
(2, '李三', '1', '绿地'),
(3, 'a', '1', 'aaa'),
(4, 'b', '1', 'bbb'),
(5, 'c', '2', 'ccc'),
(6, 'asdf', '2', 'wer'),
(7, 'ertwe', '2', 'sdfa'),
(8, 'gtrhtr', '1', 'asdf'),
(9, 'ewrw', '3', 'dfs'),
(10, 'yjytj', '3', 'fdgd'),
(11, '45t', '3', 'gg'),
(12, 'hyjy', '2', 're');

-- --------------------------------------------------------

--
-- 表的结构 `fengning_datatables_example_join`
--

CREATE TABLE IF NOT EXISTS `fengning_datatables_example_join` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- 转存表中的数据 `fengning_datatables_example_join`
--

INSERT INTO `fengning_datatables_example_join` (`id`, `name`) VALUES
(1, '大哥'),
(2, '二哥'),
(3, '三哥');

-- --------------------------------------------------------

--
-- 表的结构 `fengning_emailconfig`
--

CREATE TABLE IF NOT EXISTS `fengning_emailconfig` (
  `email` varchar(5) NOT NULL COMMENT '邮箱配置标识',
  `from_email` varchar(50) NOT NULL COMMENT '邮件来源也就是邮件地址',
  `from_name` varchar(50) NOT NULL,
  `smtp` varchar(50) NOT NULL COMMENT '邮箱smtp服务器',
  `username` varchar(100) NOT NULL COMMENT '邮箱账号',
  `password` varchar(100) NOT NULL COMMENT '邮箱密码',
  `title` varchar(200) NOT NULL COMMENT '邮件标题',
  `content` text NOT NULL COMMENT '邮件模板',
  KEY `email` (`email`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `fengning_emailconfig`
--

INSERT INTO `fengning_emailconfig` (`email`, `from_email`, `from_name`, `smtp`, `username`, `password`, `title`, `content`) VALUES
('email', '', '', '', '', '', '', '');

-- --------------------------------------------------------

--
-- 表的结构 `fengning_smsconfig`
--

CREATE TABLE IF NOT EXISTS `fengning_smsconfig` (
  `sms` varchar(10) NOT NULL DEFAULT 'sms' COMMENT '标识',
  `appkey` varchar(200) NOT NULL,
  `secretkey` varchar(200) NOT NULL,
  `type` varchar(100) DEFAULT 'normal' COMMENT '短信类型',
  `name` varchar(100) NOT NULL COMMENT '短信签名',
  `code` varchar(100) NOT NULL COMMENT '短信模板ID',
  `content` text NOT NULL COMMENT '短信默认模板',
  KEY `sms` (`sms`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `fengning_smsconfig`
--

INSERT INTO `fengning_smsconfig` (`sms`, `appkey`, `secretkey`, `type`, `name`, `code`, `content`) VALUES
('sms', '', '', '', '', '', '');

-- --------------------------------------------------------

--
-- 表的结构 `fengning_urlconfig`
--

CREATE TABLE IF NOT EXISTS `fengning_urlconfig` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `aliases` varchar(200) NOT NULL COMMENT '想要设置的别名',
  `url` varchar(200) NOT NULL COMMENT '原url结构',
  `desc` text COMMENT '备注',
  `status` int(1) NOT NULL DEFAULT '1' COMMENT '0禁用1使用',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`) USING BTREE,
  KEY `status` (`status`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `fengning_urlconfig`
--

INSERT INTO `fengning_urlconfig` (`id`, `aliases`, `url`, `desc`, `status`, `create_time`, `update_time`) VALUES
(1, 'admin_login', 'admin/common/login', '后台登录地址。', 0, 1517621629, 1517621629);

-- --------------------------------------------------------

--
-- 表的结构 `fengning_webconfig`
--

CREATE TABLE IF NOT EXISTS `fengning_webconfig` (
  `web` varchar(20) NOT NULL COMMENT '网站配置标识',
  `name` varchar(200) NOT NULL COMMENT '网站名称',
  `keywords` text COMMENT '关键词',
  `desc` text COMMENT '描述',
  `is_log` int(1) NOT NULL DEFAULT '1' COMMENT '1开启日志0关闭',
  `file_type` varchar(200) DEFAULT NULL COMMENT '允许上传的类型',
  `file_size` bigint(20) DEFAULT NULL COMMENT '允许上传的最大值',
  `statistics` text COMMENT '统计代码',
  `black_ip` text COMMENT 'ip黑名单',
  `url_suffix` varchar(20) DEFAULT NULL COMMENT 'url伪静态后缀',
  KEY `web` (`web`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `fengning_webconfig`
--

INSERT INTO `fengning_webconfig` (`web`, `name`, `keywords`, `desc`, `is_log`, `file_type`, `file_size`, `statistics`, `black_ip`, `url_suffix`) VALUES
('web', '丰宁BIM协同管理平台', '', '', 1, 'jpg,png,gif,mp4,jpeg,doc,docx,xls,xlsx,pdf', 500, '', '', NULL);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
