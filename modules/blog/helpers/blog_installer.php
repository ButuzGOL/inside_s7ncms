<?php defined('SYSPATH') OR die('No direct access allowed.');

class blog_installer {

	public static function install()
	{
		$db = Database::instance();

		$version = (int) module::version('blog');

		// blog module is not installed yet
		if ($version === 0)
		{
			// TODO use dbforge
			$db->query("
				CREATE TABLE IF NOT EXISTS `blog_posts` (
					`id` bigint(20) unsigned NOT NULL auto_increment,
					`user_id` bigint(20) NOT NULL default '0',
					`date` int(20) default NULL,
					`content` longtext NOT NULL,
					`title` varchar(200) NOT NULL,
					`status` varchar(20) NOT NULL default 'published',
					`comment_status` varchar(20) NOT NULL default 'open',
					`uri` varchar(200) NOT NULL default '',
					`modified` int(20) default NULL,
					`comment_count` bigint(20) NOT NULL default '0',
					`tags` text,
					PRIMARY KEY  (`id`),
					KEY `uri` (`uri`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;
			");

			$db->query("
				CREATE TABLE IF NOT EXISTS `blog_comments` (
					`id` bigint(20) unsigned NOT NULL auto_increment,
					`blog_post_id` int(11) NOT NULL default '0',
					`author` varchar(200) NOT NULL,
					`email` varchar(100) default NULL,
					`url` varchar(200) default NULL,
					`ip` varchar(100) NOT NULL default '0.0.0.0',
					`date` int(20) default NULL,
					`content` text,
					`approved` varchar(20) NOT NULL default '1',
					`user_id` bigint(20) NOT NULL default '0',
					PRIMARY KEY  (`id`),
					KEY `blog_posts_id` (`blog_post_id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;
			");

			config::set('blog.items_per_page', 5);
			config::set('blog.enable_captcha', 'yes');
			config::set('blog.comment_status', 'open');
            config::set('blog.enable_tagcloud', 'yes');
            
            config::set('blog.tags_minsize', 80);
            config::set('blog.tags_maxsize', 180);
            
			module::version('blog', 1);
		}
	}

	public static function uninstall()
	{
		$db = Database::instance();

		$db->query("DROP TABLE IF EXISTS `blog_posts`;");
	    $db->query("DROP TABLE IF EXISTS `blog_comments`;");

	    module::delete("blog");

	    $db->delete('config', array('context' => 'blog'));
	}

}
