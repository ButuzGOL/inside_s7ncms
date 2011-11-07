CREATE TABLE `config` (
  `id` int(11) NOT NULL auto_increment,
  `context` varchar(200) NOT NULL,
  `key` varchar(200) NOT NULL,
  `value` varchar(200) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `config` VALUES (1, 's7nm', 'site_title', 'My Website');
INSERT INTO `config` VALUES (2, 's7nm','theme','default');
INSERT INTO `config` VALUES (3, 's7nm','admintheme','default');

CREATE TABLE `modules` (
  `id` int(10) NOT NULL auto_increment,
  `name` varchar(200) default NULL,
  `version` float default 0,
  `status` varchar(200) default 'on',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `pages` (
  `id` int(11) NOT NULL auto_increment,
  `lvl` int(11) NOT NULL,
  `lft` int(11) default NULL,
  `rgt` int(11) default NULL,
  `scope` int(11) default 1,
  `title` varchar(200) default NULL,
  `type` varchar(200) default NULL,
  `target` varchar(200) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `page_contents` (
  `id` int(11) NOT NULL auto_increment,
  `page_id` int(11) NOT NULL,
  `language` varchar(250) default NULL,
  `date` int(20) default NULL,
  `title` varchar(200) NOT NULL,
  `content` longtext NOT NULL,
  `uri` varchar(200) NOT NULL,
  `modified` int(20) default NULL,
  PRIMARY KEY  (`id`),
  KEY `uri` (`uri`),
  KEY `language` (`language`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `roles` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(32) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `uniq_name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `roles` VALUES (1, 'login', 'Login privileges, granted after account confirmation');
INSERT INTO `roles` VALUES (2, 'admin', 'Administrative user, has access to everything.');

CREATE TABLE `roles_users` (
  `user_id` int(10) unsigned NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`user_id`,`role_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `users` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `email` varchar(200) NOT NULL default '',
  `username` varchar(200) NOT NULL default '',
  `password` char(50) NOT NULL,
  `logins` int(10) unsigned NOT NULL default '0',
  `homepage` varchar(200) default NULL,
  `registered_on` int(20) default NULL,
  `last_login` int(20) unsigned default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `uniq_username` (`username`),
  UNIQUE KEY `uniq_email` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
