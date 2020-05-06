-- phpMyAdmin SQL Dump
-- version 4.7.9
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: 2020-04-20 09:08:35
-- 服务器版本： 5.6.44-log
-- PHP Version: 7.3.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mqtt_myshown_com`
--

-- --------------------------------------------------------

--
-- 表的结构 `oauth_access_tokens`
--

CREATE TABLE `oauth_access_tokens` (
  `access_token` varchar(40) NOT NULL,
  `client_id` varchar(80) NOT NULL,
  `user_id` varchar(80) DEFAULT NULL,
  `expires` timestamp NOT NULL,
  `scope` varchar(4000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `oauth_authorization_codes`
--

CREATE TABLE `oauth_authorization_codes` (
  `authorization_code` varchar(40) NOT NULL,
  `client_id` varchar(80) NOT NULL,
  `user_id` varchar(80) DEFAULT NULL,
  `redirect_uri` varchar(2000) DEFAULT NULL,
  `expires` timestamp NOT NULL,
  `scope` varchar(4000) DEFAULT NULL,
  `id_token` varchar(1000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `oauth_clients`
--

CREATE TABLE `oauth_clients` (
  `client_id` varchar(80) NOT NULL,
  `client_secret` varchar(80) DEFAULT NULL,
  `redirect_uri` varchar(2000) DEFAULT NULL,
  `grant_types` varchar(80) DEFAULT NULL,
  `scope` varchar(4000) DEFAULT NULL,
  `user_id` varchar(80) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `oauth_jwt`
--

CREATE TABLE `oauth_jwt` (
  `client_id` varchar(80) NOT NULL,
  `subject` varchar(80) DEFAULT NULL,
  `public_key` varchar(2000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `oauth_refresh_tokens`
--

CREATE TABLE `oauth_refresh_tokens` (
  `refresh_token` varchar(40) NOT NULL,
  `client_id` varchar(80) NOT NULL,
  `user_id` varchar(80) DEFAULT NULL,
  `expires` timestamp NOT NULL,
  `scope` varchar(4000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `oauth_scopes`
--

CREATE TABLE `oauth_scopes` (
  `scope` varchar(80) NOT NULL,
  `is_default` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `oauth_users`
--

CREATE TABLE `oauth_users` (
  `username` varchar(80) NOT NULL DEFAULT '',
  `password` varchar(80) DEFAULT NULL,
  `first_name` varchar(80) DEFAULT NULL,
  `last_name` varchar(80) DEFAULT NULL,
  `email` varchar(80) DEFAULT NULL,
  `email_verified` tinyint(1) DEFAULT NULL,
  `scope` varchar(4000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `tb_device`
--

CREATE TABLE `tb_device` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(10) NOT NULL,
  `online` int(1) NOT NULL DEFAULT '0' COMMENT '在线',
  `type` varchar(10) NOT NULL COMMENT '设备类型',
  `mac` varchar(15) NOT NULL COMMENT 'mac地址',
  `isBind` int(1) NOT NULL COMMENT '设备是否被绑定了',
  `admin_id` int(10) UNSIGNED NOT NULL,
  `attrbute` varchar(255) NOT NULL,
  `create_time` int(15) NOT NULL COMMENT '创建时间',
  `update_time` int(15) NOT NULL COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 触发器 `tb_device` 请修改自己的数据库名字 your_name
--
DELIMITER $$
CREATE TRIGGER `改变设备状态同步` AFTER UPDATE ON `tb_device` FOR EACH ROW UPDATE `your_name`.`tb_rel_device_user` SET `online` =new.online WHERE `tb_rel_device_user`.`device_id` = new.id
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- 表的结构 `tb_rel_device_user`
--

CREATE TABLE `tb_rel_device_user` (
  `id` int(15) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `device_id` int(10) UNSIGNED NOT NULL,
  `type` varchar(10) NOT NULL,
  `uuid` varchar(50) NOT NULL,
  `alias` varchar(10) NOT NULL COMMENT '设备别名',
  `img` varchar(255) NOT NULL DEFAULT 'https://www.xuhonys.cn/icon/light.png' COMMENT '用户自定义的图片地址',
  `online` int(1) NOT NULL COMMENT '是否在线'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `tb_user`
--

CREATE TABLE `tb_user` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(25) NOT NULL,
  `alias` varchar(20) NOT NULL COMMENT '昵称',
  `password` varchar(35) NOT NULL,
  `rank` int(2) UNSIGNED NOT NULL DEFAULT '1',
  `token` varchar(255) DEFAULT NULL,
  `online` int(1) UNSIGNED NOT NULL DEFAULT '0',
  `phone` int(12) NOT NULL,
  `email` varchar(30) DEFAULT NULL,
  `pic` varchar(255) NOT NULL COMMENT '头像地址',
  `isAdmin` int(1) UNSIGNED NOT NULL DEFAULT '0',
  `isActive` int(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否激活',
  `sex` int(1) NOT NULL DEFAULT '3' COMMENT '性别',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  `last_login_ip` varchar(20) NOT NULL,
  `openId` varchar(70) NOT NULL,
  `unionId` varchar(70) NOT NULL,
  `access_token` varchar(255) NOT NULL COMMENT '这个没有调用频率限制，这个授权五分钟有效期',
  `refresh_token` varchar(255) NOT NULL,
  `create_token_time` int(15) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `tb_wc_user_code`
--

CREATE TABLE `tb_wc_user_code` (
  `id` int(10) NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `code` varchar(8) NOT NULL COMMENT '授权码',
  `expire` int(13) NOT NULL COMMENT '过期时间',
  `active` int(1) NOT NULL COMMENT '是否激活'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oauth_access_tokens`
--
ALTER TABLE `oauth_access_tokens`
  ADD PRIMARY KEY (`access_token`);

--
-- Indexes for table `oauth_authorization_codes`
--
ALTER TABLE `oauth_authorization_codes`
  ADD PRIMARY KEY (`authorization_code`);

--
-- Indexes for table `oauth_clients`
--
ALTER TABLE `oauth_clients`
  ADD PRIMARY KEY (`client_id`);

--
-- Indexes for table `oauth_refresh_tokens`
--
ALTER TABLE `oauth_refresh_tokens`
  ADD PRIMARY KEY (`refresh_token`);

--
-- Indexes for table `oauth_scopes`
--
ALTER TABLE `oauth_scopes`
  ADD PRIMARY KEY (`scope`);

--
-- Indexes for table `oauth_users`
--
ALTER TABLE `oauth_users`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `tb_device`
--
ALTER TABLE `tb_device`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_rel_device_user`
--
ALTER TABLE `tb_rel_device_user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `device_id` (`device_id`);

--
-- Indexes for table `tb_user`
--
ALTER TABLE `tb_user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_wc_user_code`
--
ALTER TABLE `tb_wc_user_code`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `code` (`code`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `tb_device`
--
ALTER TABLE `tb_device`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- 使用表AUTO_INCREMENT `tb_rel_device_user`
--
ALTER TABLE `tb_rel_device_user`
  MODIFY `id` int(15) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- 使用表AUTO_INCREMENT `tb_user`
--
ALTER TABLE `tb_user`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- 使用表AUTO_INCREMENT `tb_wc_user_code`
--
ALTER TABLE `tb_wc_user_code`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- 限制导出的表
--

--
-- 限制表 `tb_rel_device_user`
--
ALTER TABLE `tb_rel_device_user`
  ADD CONSTRAINT `rel-BK-DEVID` FOREIGN KEY (`device_id`) REFERENCES `tb_device` (`id`),
  ADD CONSTRAINT `rel-BK-USERID` FOREIGN KEY (`user_id`) REFERENCES `tb_user` (`id`);

--
-- 限制表 `tb_wc_user_code`
--
ALTER TABLE `tb_wc_user_code`
  ADD CONSTRAINT `rel-BK-user` FOREIGN KEY (`user_id`) REFERENCES `tb_user` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
