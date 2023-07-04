
CREATE TABLE IF NOT EXISTS `admins` (
`id` int(11) NOT NULL,
  `login_id` varchar(100) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `login_account` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `authority` varchar(50) NOT NULL,
  `last_login` datetime NOT NULL,
  `delete_flag` tinyint(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `admins` (`id`, `login_id`, `user_name`, `login_account`, `password`, `email`, `authority`, `last_login`, `delete_flag`, `created`, `modified`) VALUES
(1, '1000001', '管理者', 'adminuser1', '9f490bf18dae9d13ba5aa784009c6c894a67e399', 'cieliacms@cielia.com', 'MASTER', '2018-07-26 22:49:04', 0, '2015-08-21 00:00:00', '2015-08-21 00:00:00');

CREATE TABLE IF NOT EXISTS `admins_deleted` (
`id` int(11) NOT NULL,
  `login_id` varchar(100) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `authority` varchar(50) NOT NULL,
  `created` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `clients` (
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `clients` (`setting_key`, `setting_value`) VALUES
('account', 'torikago'),
('email', ''),
('name', 'CIELIA'),
('site_url', 'http://cielia.com/');

CREATE TABLE IF NOT EXISTS `rooms_category` (
`category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `sort_num` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

INSERT INTO `rooms_category` (`category_id`, `category_name`, `sort_num`, `created`, `modified`) VALUES
(1, '分譲中', 0, '2018-07-13 17:27:15', '2018-07-13 17:27:15'),
(2, '分譲済み', 0, '2018-07-13 17:27:24', '2018-07-13 17:27:24'),
(3, '次期分譲', 0, '2018-07-13 17:27:40', '2018-07-13 17:27:40'),
(4, '非分譲', 0, '2018-07-13 17:27:53', '2018-07-13 17:27:53'),
(5, '共有、その他', 0, '2018-07-13 17:28:03', '2018-07-13 17:28:03');

CREATE TABLE IF NOT EXISTS `rooms_data` (
`id` int(11) NOT NULL,
`edit_id` varchar(100) NOT NULL,
`title` varchar(255) NOT NULL,
`category` int(11) NOT NULL,
`content` longtext NOT NULL,
`distribution_start_date` datetime NOT NULL,
`distribution_end_date` datetime NOT NULL,
`type_str` varchar(255) NOT NULL COMMENT 'タイプ名',
`link` varchar(255) NOT NULL,
`link_window` tinyint(1) NOT NULL DEFAULT '0',
`status_str` varchar(255) NOT NULL COMMENT 'ステータス',
`price` varchar(50) NOT NULL,
`pricetype` tinyint(2) NOT NULL DEFAULT '1',
`breadth` float DEFAULT '0',
`ldk` text,
`orientation` text,
`free_tag` text,
`base_image` text,
`menu1_image` text,
`menu2_image` text,
`menu3_image` text,
`menu4_image` text,
`sort_num` int(11) NOT NULL,
`output_flag` tinyint(4) NOT NULL DEFAULT '0',
`status` varchar(20) NOT NULL,
`parent_data_id` int(11) NOT NULL,
`user_id` varchar(100) NOT NULL,
`last_edit_user_id` varchar(100) NOT NULL,
`history` text NOT NULL,
`created` datetime NOT NULL,
`modified` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `rooms_media` (
`id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `upload_filename` varchar(255) NOT NULL,
  `type` varchar(20) NOT NULL,
  `description` varchar(255) NOT NULL,
  `created` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `rooms_metadata` (
`meta_id` int(11) NOT NULL,
  `data_id` int(11) NOT NULL,
  `meta_key` varchar(50) NOT NULL,
  `meta_value` text NOT NULL,
  `meta_type` varchar(20) NOT NULL,
  `block_number` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `search_tag` (
`id` int(11) NOT NULL,
`tag_key` text NOT NULL,
`tag_value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='検索用のタグを格納するためのもの';

CREATE TABLE IF NOT EXISTS `sequence` (
  `id` bigint(20) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


ALTER TABLE `admins`
 ADD PRIMARY KEY (`id`);

ALTER TABLE `admins_deleted`
 ADD PRIMARY KEY (`id`);

ALTER TABLE `clients`
 ADD PRIMARY KEY (`setting_key`);

ALTER TABLE `rooms_category`
 ADD PRIMARY KEY (`category_id`);

ALTER TABLE `rooms_data`
 ADD PRIMARY KEY (`id`);

ALTER TABLE `rooms_media`
 ADD PRIMARY KEY (`id`);

ALTER TABLE `rooms_metadata`
 ADD PRIMARY KEY (`meta_id`);

ALTER TABLE `search_tag`
ADD PRIMARY KEY (`id`);


ALTER TABLE `admins`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
ALTER TABLE `admins_deleted`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `rooms_category`
MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
ALTER TABLE `rooms_data`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `rooms_media`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `rooms_metadata`
MODIFY `meta_id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `search_tag`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
