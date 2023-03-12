<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */

if (!class_exists("DeoPageSetup")) {

	class DeoPageSetup
	{
		public static function getTabs()
		{
			return array(
				array(
					'class_name' => 'AdminDeoProfiles',
					'name' => 'Homepage Builder',
				),
				array(
					'class_name' => 'AdminDeoPositions',
					'name' => 'Position Homepage Manage',
				),
				array(
					'class_name' => 'AdminDeoShortcode',
					'name' => 'ShortCode Manage',
				),
				array(
					'class_name' => 'AdminDeoHome',
					'name' => 'Homepage Builder',
					'id_parent' => -1,
				),
				array(
					'class_name' => 'AdminDeoProducts',
					'name' => 'Products List Builder',
				),
				array(
					'class_name' => 'AdminDeoDetails',
					'name' => 'Details Page Builder',
				),
				array(
					'class_name' => 'AdminDeoOnepagecheckout',
					'name' => 'One Page Checkout Builder',
				),
				array(
					'class_name' => 'AdminDeoOnepagecheckoutConfigure',
					'name' => 'One Page Checkout Configuration',
				),
				array(
					'class_name' => 'AdminDeoHook',
					'name' => 'Position Hook Manage',
				),
				array(
					'class_name' => 'AdminDeoDeveloperConfigure',
					'name' => 'Developer Configure',
				),
				array(
					'class_name' => 'AdminDeoThemeConfigure',
					'name' => 'Theme Configuration',
				),
				array(
					'class_name' => 'AdminDeoImages',
					'name' => 'Image Manage',
					'id_parent' => -1,
				),
				array(
					'class_name' => 'AdminDeoShortcodes',
					'name' => 'Shortcodes Builder',
					'id_parent' => -1,
				),
				array(
					'class_name' => 'AdminDeoMegamenu',
					'name' => 'Megamenu',
				),
				array(
					'class_name' => 'AdminDeoWidgetsMegamenu',
					'name' => 'Widgets Megamenu',
					'id_parent' => -1,
				),
				array(
					'class_name' => 'AdminDeoReviewManager',
					'name' => 'Review Product',
				),
				array(
					'class_name' => 'AdminDeoBlogDashboard',
					'name' => 'Blog',
				),
				array(
					'class_name' => 'AdminDeoBlogs',
					'name' => 'Blog Posts',
				),
				array(
					'class_name' => 'AdminDeoBlogCategories',
					'name' => 'Blog Catgories',
				),
				array(
					'class_name' => 'AdminDeoBlogComments',
					'name' => 'Blog Comment',
				),
				array(
					'class_name' => 'AdminDeoTranslate',
					'name' => 'Tranlsate Theme',
				),
			);
		}

		public static function createTables($reset = 0)
		{
			// if ($reset == 0) {
			//     require_once(_PS_MODULE_DIR_.'deotemplate/libs/DeoDataSample.php');

			//     $sample = new DeoDataSample();
			//     if ($sample->processImport('deotemplate')) {
			//         return true;
			//     }
			// }
			$drop = '';
			if ($reset == 1) {
				$drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'deotemplate_profiles`;';
			}
			//each shop will have one or more profile
			$res = (bool)Db::getInstance()->execute($drop.'
				CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'deotemplate_profiles` (
					`id_deotemplate_profiles` int(11) NOT NULL AUTO_INCREMENT,
					`name` varchar(255),
					`group_box` varchar(255),
					`profile_key` varchar(255),
					`page` varchar(255),
					`params` text,
					`header` int(11) unsigned NOT NULL,
					`content` int(11) unsigned NOT NULL,
					`footer` int(11) unsigned NOT NULL,
					`product` int(11) unsigned NOT NULL,
					`mobile` int(11) unsigned NOT NULL,
					`active` TINYINT(1),
					PRIMARY KEY (`id_deotemplate_profiles`)
				) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
			');
			if ($reset == 1) {
				$drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'deotemplate_profiles_lang`;';
			}
			$res &= Db::getInstance()->execute($drop.'
				CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'deotemplate_profiles_lang` (
					`id_deotemplate_profiles` int(11) NOT NULL AUTO_INCREMENT,
					`id_lang` int(10) unsigned NOT NULL,
					`friendly_url` varchar(255),
					`meta_title` varchar(255),
					`meta_description` varchar(255),
					`meta_keywords` varchar(255),
					PRIMARY KEY (`id_deotemplate_profiles`, `id_lang`)
				) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
			');
			if ($reset == 1) {
				$drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'deotemplate_profiles_shop`;';
			}
			$res &= Db::getInstance()->execute($drop.'
				CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'deotemplate_profiles_shop` (
					`id_deotemplate_profiles` int(11) NOT NULL AUTO_INCREMENT,
					`id_shop` int(10) unsigned NOT NULL,
					`active` TINYINT(1),
					PRIMARY KEY (`id_deotemplate_profiles`, `id_shop`)
				) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
			');
			if ($reset == 1) {
				$drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'deotemplate_products`;';
			}
			//we can create product item for each shop
			$res &= (bool)Db::getInstance()->execute($drop.'
				CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'deotemplate_products` (
					`id_deotemplate_products` int(11) NOT NULL AUTO_INCREMENT,
					`plist_key` varchar(255),
					`name` varchar(255),
					`class` varchar(255),
					`params` text,
					`demo` TINYINT(1),
					`active` TINYINT(1),
					`responsive` varchar(255),
					PRIMARY KEY (`id_deotemplate_products`)
				) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
			');
			if ($reset == 1) {
				$drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'deotemplate_products_shop`;';
			}
			$res &= Db::getInstance()->execute($drop.'
				CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'deotemplate_products_shop` (
					`id_deotemplate_products` int(11) NOT NULL AUTO_INCREMENT,
					`id_shop` int(10) unsigned NOT NULL,
					`active` TINYINT(1),
					PRIMARY KEY (`id_deotemplate_products`, `id_shop`)
				) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
			');
			if ($reset == 1) {
				$drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'deotemplate_details`;';
			}
			$res &= (bool)Db::getInstance()->execute($drop.'
				CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'deotemplate_details` (
					`id_deotemplate_details` int(11) NOT NULL AUTO_INCREMENT,
					`plist_key` varchar(255),
					`name` varchar(255),
					`class_detail` varchar(255),
					`url_img_preview` varchar(255),
					`params` text,
					`fullwidth` TINYINT(1),
					`active` TINYINT(1),
					PRIMARY KEY (`id_deotemplate_details`)
				) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
			');
			if ($reset == 1) {
				$drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'deotemplate_details_shop`;';
			}
			$res &= Db::getInstance()->execute($drop.'
				CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'deotemplate_details_shop` (
				  `id_deotemplate_details` int(11) NOT NULL AUTO_INCREMENT,
				  `id_shop` int(10) unsigned NOT NULL,
				  `active` TINYINT(1),
				  PRIMARY KEY (`id_deotemplate_details`, `id_shop`)
				) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
			');
			if ($reset == 1) {
				$drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'deotemplate_onepagecheckout`;';
			}
			$res &= (bool)Db::getInstance()->execute($drop.'
				CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'deotemplate_onepagecheckout` (
					`id_deotemplate_onepagecheckout` int(11) NOT NULL AUTO_INCREMENT,
					`plist_key` varchar(255),
					`name` varchar(255),
					`class_checkout` varchar(255),
					`params` text,
					`type` TINYINT(1),
					`url_img_preview` varchar(255),
					`fullwidth` TINYINT(1),
					`active` TINYINT(1),
				PRIMARY KEY (`id_deotemplate_onepagecheckout`)
				) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8mb4;
			');
			if ($reset == 1) {
				$drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'deotemplate_onepagecheckout_shop`;';
			}
			$res &= Db::getInstance()->execute($drop.'
				CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'deotemplate_onepagecheckout_shop` (
					`id_deotemplate_onepagecheckout` int(11) NOT NULL AUTO_INCREMENT,
					`id_shop` int(10) unsigned NOT NULL,
					`active` TINYINT(1),
					PRIMARY KEY (`id_deotemplate_onepagecheckout`, `id_shop`)
				) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8mb4;
		
			');
			if ($reset == 1) {
				$drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'deotemplate`;';
			}
			$res &= (bool)Db::getInstance()->execute($drop.'
				CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'deotemplate` (
					`id_deotemplate` int(11) NOT NULL AUTO_INCREMENT,
						`id_deotemplate_positions` int(11) NOT NULL,
						`hook_name` varchar(255),
						`id_deotemplate_shortcode` int(11) NOT NULL,
					PRIMARY KEY (`id_deotemplate`)
				) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
			');

			if ($reset == 1) {
				$drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'deotemplate_shop`;';
			}
			$res &= Db::getInstance()->execute($drop.'
				CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'deotemplate_shop` (
				  `id_deotemplate` int(11) NOT NULL AUTO_INCREMENT,
				  `id_shop` int(10) unsigned NOT NULL,
				  PRIMARY KEY (`id_deotemplate`, `id_shop`)
				) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
			');
			if ($reset == 1) {
				$drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'deotemplate_lang`;';
			}
			$res &= Db::getInstance()->execute($drop.'
				CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'deotemplate_lang` (
				   `id_deotemplate` int(11) NOT NULL AUTO_INCREMENT,
				   `id_lang` int(10) unsigned NOT NULL,
				   `params` MEDIUMTEXT,
				   PRIMARY KEY (`id_deotemplate`, `id_lang`)
				) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
				
			');
			if ($reset == 1) {
				$drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'deotemplate_positions`;';
			}
			$res &= Db::getInstance()->execute($drop.'
				CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'deotemplate_positions` (
					`id_deotemplate_positions` int(11) NOT NULL AUTO_INCREMENT,
					`name` varchar(255) NOT NULL,
					`position` varchar(255) NOT NULL,
					`position_key` varchar(255) NOT NULL,
					`params` text,
					PRIMARY KEY (`id_deotemplate_positions`)
				) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
			');
			if ($reset == 1) {
				$drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'deotemplate_positions_shop`;';
			}
			$res &= Db::getInstance()->execute($drop.'
				CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'deotemplate_positions_shop` (
				  `id_deotemplate_positions` int(11) NOT NULL AUTO_INCREMENT,
				  `id_shop` int(10) unsigned NOT NULL,
				  PRIMARY KEY (`id_deotemplate_positions`, `id_shop`)
				) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
			');
			// create table for deo shortcode
			if ($reset == 1) {
				$drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'deotemplate_shortcode`;';
			}
			$res &= Db::getInstance()->execute($drop.'
				CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'deotemplate_shortcode` (
				  `id_deotemplate_shortcode` int(11) NOT NULL AUTO_INCREMENT,                  
				  `shortcode_key` varchar(255) NOT NULL,
				  `active` TINYINT(1),
				  PRIMARY KEY (`id_deotemplate_shortcode`)
				) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
			');
			// create table for deo shortcode (lang)
			if ($reset == 1) {
				$drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'deotemplate_shortcode_lang`;';
			}
			$res &= Db::getInstance()->execute($drop.'
				CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'deotemplate_shortcode_lang` (
				   `id_deotemplate_shortcode` int(11) unsigned NOT NULL,
				   `id_lang` int(10) unsigned NOT NULL,
				   `shortcode_name` text NOT NULL,
				   PRIMARY KEY (`id_deotemplate_shortcode`, `id_lang`)
				) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
			');
			// create table for deo shortcode (shop)
			if ($reset == 1) {
				$drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'deotemplate_shortcode_shop`;';
			}
			$res &= Db::getInstance()->execute($drop.'
				CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'deotemplate_shortcode_shop` (
					`id_deotemplate_shortcode` int(11) unsigned NOT NULL,
					`id_shop` int(10) unsigned NOT NULL,
					`active` TINYINT(1),
					PRIMARY KEY (`id_deotemplate_shortcode`, `id_shop`)
				) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
			');


			if ($reset == 1) {
				$drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'deoblog_category`;';
			}
			$res &= Db::getInstance()->execute($drop.'
				CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'deoblog_category` (
					`id_deoblog_category` int(11) NOT NULL AUTO_INCREMENT,
					`image` varchar(255) NOT NULL,
					`id_parent` int(11) NOT NULL,
					`level_depth` smallint(6) NOT NULL,
					`active` tinyint(1) NOT NULL,
					`position` int(11) NOT NULL,
					`class_css` varchar(25) DEFAULT NULL,
					`date_add` datetime DEFAULT NULL,
					`date_upd` datetime DEFAULT NULL,
					`template` varchar(200) NOT NULL,
					`randkey` varchar(255) DEFAULT NULL,
					`image_link` varchar(225) DEFAULT NULL,
					`use_image_link` tinyint(1) DEFAULT NULL,
					`rate_image` varchar(25) DEFAULT NULL,
					`is_root` tinyint(1) DEFAULT NULL,
					PRIMARY KEY (`id_deoblog_category`)
				) ENGINE='._MYSQL_ENGINE_.'  DEFAULT CHARSET=utf8;
			');

			if ($reset == 1) {
				$drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'deoblog_category_lang`;';
			}
			$res &= (bool)Db::getInstance()->execute($drop.'
				CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'deoblog_category_lang` (
					`id_deoblog_category` int(11) NOT NULL,
					`id_lang` int(11) NOT NULL,
					`title` varchar(255) DEFAULT NULL,
					`content` text,
					`meta_keywords` varchar(255) NOT NULL,
					`meta_description` varchar(255) NOT NULL,
					`link_rewrite` varchar(250) NOT NULL,
					PRIMARY KEY (`id_deoblog_category`,`id_lang`)
				) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;
			');

			if ($reset == 1) {
				$drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'deoblog_category_shop`;';
			}
			$res &= Db::getInstance()->execute($drop.'
				CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'deoblog_category_shop` (
					`id_deoblog_category` int(11) NOT NULL DEFAULT \'0\',
					`id_shop` int(11) NOT NULL DEFAULT \'0\',
					PRIMARY KEY (`id_deoblog_category`,`id_shop`)
				) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;
			');

			if ($reset == 1) {
				$drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'deoblog_comment`;';
			}
			$res &= Db::getInstance()->execute($drop.'
				CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'deoblog_comment` (
					`id_deoblog_comment` int(11) NOT NULL AUTO_INCREMENT,
					`id_shop` int(11) NOT NULL DEFAULT \'0\',
					`id_deoblog` int(11) unsigned NOT NULL,
					`comment` text NOT NULL,
					`active` tinyint(1) NOT NULL DEFAULT \'0\',
					`date_add` datetime DEFAULT NULL,
					`user` varchar(255) NOT NULL,
					`email` varchar(255) NOT NULL,
					PRIMARY KEY (`id_deoblog_comment`,`id_shop`),
					KEY `FK_blog_comment` (`id_deoblog`)
				) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8; 
			');

			if ($reset == 1) {
				$drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'deoblog`;';
			}
			$res &= (bool)Db::getInstance()->execute($drop.'
				CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'deoblog` (
					`id_deoblog` int(11) NOT NULL AUTO_INCREMENT,
					`id_deoblog_category` int(11) NOT NULL,
					`position` int(11) NOT NULL,
					`date_add` date NOT NULL,
					`active` tinyint(1) NOT NULL,
					`user_id` int(11) NOT NULL,
					`views` int(11) DEFAULT NULL,
					`image` varchar(255) DEFAULT NULL,
					`date_upd` datetime NOT NULL,
					`indexation` int(11) NOT NULL,
					`id_employee` int(11) NOT NULL,
					`author_name` varchar(255) DEFAULT NULL,
					`image_link` varchar(225) DEFAULT NULL,
					`use_image_link` tinyint(1) DEFAULT NULL,
					`rate_image` varchar(25) DEFAULT NULL,
					PRIMARY KEY (`id_deoblog`)
				) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8; '
			);

			if ($reset == 1) {
				$drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'deoblog_lang`;';
			}
			$res &= Db::getInstance()->execute($drop.'
				CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'deoblog_lang` (
					`id_deoblog` int(11) NOT NULL,
					`id_lang` int(11) NOT NULL,
					`meta_description` varchar(255) NOT NULL,
					`meta_title` varchar(250) NOT NULL,
					`link_rewrite` varchar(255) NOT NULL,
					`content` text NOT NULL,
					`description` text NOT NULL,
					`meta_keywords` varchar(225) DEFAULT NULL,
					PRIMARY KEY (`id_deoblog`,`id_lang`)
				) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8; '
			);

			if ($reset == 1) {
				$drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'deoblog_shop`;';
			}
			$res &= (bool)Db::getInstance()->execute($drop.'
				CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'deoblog_shop` (
					`id_deoblog` int(11) NOT NULL DEFAULT \'0\',
					`id_shop` int(11) NOT NULL DEFAULT \'0\',
					PRIMARY KEY (`id_deoblog`,`id_shop`)
				) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;
			');


			// install database for product review
			if ($reset == 1) {
				$drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'deofeature_product_review`;';
			}
			$res = (bool)Db::getInstance()->execute($drop.'
				CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'deofeature_product_review` (
					`id_deofeature_product_review` int(10) unsigned NOT NULL AUTO_INCREMENT,
					`id_product` int(10) unsigned NOT NULL,
					`id_customer` int(10) unsigned NOT NULL,
					`id_guest` int(10) unsigned DEFAULT NULL,
					`title` varchar(64) DEFAULT NULL,
					`content` text NOT NULL,
					`customer_name` varchar(64) DEFAULT NULL,
					`grade` float unsigned NOT NULL,
					`validate` tinyint(1) NOT NULL,
					`deleted` tinyint(1) NOT NULL,
					`date_add` datetime NOT NULL,
					PRIMARY KEY (`id_deofeature_product_review`),
					KEY `id_product` (`id_product`),
					KEY `id_customer` (`id_customer`),
					KEY `id_guest` (`id_guest`)
				) ENGINE='._MYSQL_ENGINE_.'  DEFAULT CHARSET=utf8;
			');

			if ($reset == 1) {
				$drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'deofeature_product_review_criterion`;';
			}
			$res &= (bool)Db::getInstance()->execute($drop.'
				CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'deofeature_product_review_criterion` (
					`id_deofeature_product_review_criterion` int(10) unsigned NOT NULL AUTO_INCREMENT,
					`id_deofeature_product_review_criterion_type` tinyint(1) NOT NULL,
					`active` tinyint(1) NOT NULL,
					PRIMARY KEY (`id_deofeature_product_review_criterion`)
				) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;
			');

			if ($reset == 1) {
				$drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'deofeature_product_review_criterion_product`;';
			}
			$res &= (bool)Db::getInstance()->execute($drop.'
				CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'deofeature_product_review_criterion_product` (
					`id_product` int(10) unsigned NOT NULL,
					`id_deofeature_product_review_criterion` int(10) unsigned NOT NULL,
					PRIMARY KEY (`id_product`,`id_deofeature_product_review_criterion`),
					KEY `id_product_review_criterion` (`id_deofeature_product_review_criterion`)
				) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;
			');

			if ($reset == 1) {
				$drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'deofeature_product_review_criterion_lang`;';
			}
			$res &= (bool)Db::getInstance()->execute($drop.'
				CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'deofeature_product_review_criterion_lang` (
					`id_deofeature_product_review_criterion` int(11) unsigned NOT NULL,
					`id_lang` int(11) unsigned NOT NULL,
					`name` varchar(64) NOT NULL,
					PRIMARY KEY (`id_deofeature_product_review_criterion`,`id_lang`)
				) ENGINE='._MYSQL_ENGINE_.'  DEFAULT CHARSET=utf8;
			');

			if ($reset == 1) {
				$drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'deofeature_product_review_criterion_category`;';
			}
			$res &= (bool)Db::getInstance()->execute($drop.'
				CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'deofeature_product_review_criterion_category` (
					`id_deofeature_product_review_criterion` int(10) unsigned NOT NULL,
					`id_category` int(10) unsigned NOT NULL,
					PRIMARY KEY (`id_deofeature_product_review_criterion`,`id_category`),
					KEY `id_category` (`id_category`)
				) ENGINE='._MYSQL_ENGINE_.'  DEFAULT CHARSET=utf8;
			');

			if ($reset == 1) {
				$drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'deofeature_product_review_grade`;';
			}
			$res &= (bool)Db::getInstance()->execute($drop.'
				CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'deofeature_product_review_grade` (
					`id_deofeature_product_review_grade` int(11) NOT NULL AUTO_INCREMENT,
					`id_deofeature_product_review` int(10) unsigned NOT NULL,
					`id_deofeature_product_review_criterion` int(10) unsigned NOT NULL,
					`grade` int(10) unsigned NOT NULL,
					PRIMARY KEY (`id_deofeature_product_review_grade`),
					KEY `id_product_review_criterion` (`id_deofeature_product_review_criterion`)
				) ENGINE='._MYSQL_ENGINE_.'  DEFAULT CHARSET=utf8;
			');

			if ($reset == 1) {
				$drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'deofeature_product_review_usefulness`;';
			}
			$res &= (bool)Db::getInstance()->execute($drop.'
				CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'deofeature_product_review_usefulness` (
					`id_deofeature_product_review` int(10) unsigned NOT NULL,
					`id_customer` int(10) unsigned NOT NULL,
					`usefulness` tinyint(1) unsigned NOT NULL,
					PRIMARY KEY (`id_deofeature_product_review`,`id_customer`)
				) ENGINE='._MYSQL_ENGINE_.'  DEFAULT CHARSET=utf8;
			');

			if ($reset == 1) {
				$drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'deofeature_product_review_report`;';
			}
			$res &= (bool)Db::getInstance()->execute($drop.'
				CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'deofeature_product_review_report` (
					`id_deofeature_product_review` int(10) unsigned NOT NULL,
					`id_customer` int(10) unsigned NOT NULL,
					PRIMARY KEY (`id_deofeature_product_review`,`id_customer`)
				) ENGINE='._MYSQL_ENGINE_.'  DEFAULT CHARSET=utf8;
			');

			// install database for product compare
			if ($reset == 1) {
				$drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'deofeature_compare`;';
			}
			$res &= (bool)Db::getInstance()->execute($drop.'
				CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'deofeature_compare` (
					`id_compare` int(10) unsigned NOT NULL AUTO_INCREMENT,
					`id_customer` int(10) unsigned NOT NULL,
					PRIMARY KEY (`id_compare`)
				) ENGINE='._MYSQL_ENGINE_.'  DEFAULT CHARSET=utf8;
			');

			if ($reset == 1) {
				$drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'deofeature_compare_product`;';
			}
			$res &= (bool)Db::getInstance()->execute($drop.'
				CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'deofeature_compare_product` (
					`id_compare` int(10) unsigned NOT NULL,
					`id_product` int(10) unsigned NOT NULL,
					`date_add` datetime NOT NULL,
					`date_upd` datetime NOT NULL,
					PRIMARY KEY (`id_compare`,`id_product`)
				) ENGINE='._MYSQL_ENGINE_.'  DEFAULT CHARSET=utf8;
			');

			// install database for wishlist
			if ($reset == 1) {
				$drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'deofeature_wishlist`;';
			}
			$res &= (bool)Db::getInstance()->execute($drop.'
				CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'deofeature_wishlist` (
					`id_wishlist` int(10) unsigned NOT NULL AUTO_INCREMENT,
					`id_customer` int(10) unsigned NOT NULL,
					`token` varchar(64) NOT NULL,
					`name` varchar(64) NOT NULL,
					`counter` int(10) unsigned DEFAULT NULL,
					`id_shop` int(10) unsigned DEFAULT \'1\',
					`id_shop_group` int(10) unsigned DEFAULT \'1\',
					`date_add` datetime NOT NULL,
					`date_upd` datetime NOT NULL,
					`default` int(10) unsigned DEFAULT \'0\',
					PRIMARY KEY (`id_wishlist`)
				) ENGINE='._MYSQL_ENGINE_.'  DEFAULT CHARSET=utf8;
			');

			if ($reset == 1) {
				$drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'deofeature_wishlist_product`;';
			}
			$res &= (bool)Db::getInstance()->execute($drop.'
				CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'deofeature_wishlist_product` (
					`id_wishlist_product` int(10) NOT NULL AUTO_INCREMENT,
					`id_wishlist` int(10) unsigned NOT NULL,
					`id_product` int(10) unsigned NOT NULL,
					`id_product_attribute` int(10) unsigned NOT NULL,
					`quantity` int(10) unsigned NOT NULL,
					`priority` int(10) unsigned NOT NULL,
					PRIMARY KEY (`id_wishlist_product`)
				) ENGINE='._MYSQL_ENGINE_.'  DEFAULT CHARSET=utf8;
			');


			if ($reset == 1) {
				$drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'deomegamenu`;';
			}
			$res = (bool)Db::getInstance()->execute($drop.'
				CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'deomegamenu` (
					`id_deomegamenu` int(11) NOT NULL AUTO_INCREMENT,
					`id_group` int(11) NOT NULL,
					`image` varchar(255) NOT NULL,
					`id_parent` int(11) NOT NULL,
					`sub_with` varchar(255) NOT NULL,
					`is_group` tinyint(1) NOT NULL,
					`width` varchar(255) DEFAULT NULL,
					`submenu_width` varchar(255) DEFAULT NULL,
					`submenu_colum_width` varchar(255) DEFAULT NULL,
					`item` varchar(255) DEFAULT NULL,
					`item_parameter` varchar(255) DEFAULT NULL,
					`colums` varchar(255) DEFAULT NULL,
					`type` varchar(255) NOT NULL,
					`is_content` tinyint(1) NOT NULL,
					`show_title` tinyint(1) NOT NULL,
					`level_depth` smallint(6) NOT NULL,
					`active` tinyint(1) NOT NULL,
					`position` int(11) NOT NULL,
					`submenu_content` text NOT NULL,
					`show_sub` tinyint(1) NOT NULL,
					`target` varchar(25) DEFAULT NULL,
					`privacy` smallint(6) DEFAULT NULL,
					`position_type` varchar(25) DEFAULT NULL,
					`menu_class` varchar(255) DEFAULT NULL,
					`content` text,
					`icon_class` varchar(255) DEFAULT NULL,
					`level` int(11) NOT NULL,
					`left` int(11) NOT NULL,
					`right` int(11) NOT NULL,
					`submenu_catids` text,
					`is_cattree` tinyint(1) DEFAULT \'1\',
					`date_add` datetime DEFAULT NULL,
					`date_upd` datetime DEFAULT NULL,
					`groupBox` varchar(255) DEFAULT \'all\',
					`params_widget` longtext,
					PRIMARY KEY (`id_deomegamenu`)
				) ENGINE='._MYSQL_ENGINE_.'  DEFAULT CHARSET=utf8;
			');

			if ($reset == 1) {
				$drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'deomegamenu_lang`;';
			}
			$res &= (bool)Db::getInstance()->execute($drop.'
				CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'deomegamenu_lang` (
					`id_deomegamenu` int(11) NOT NULL,
					`id_lang` int(11) NOT NULL,
					`title` varchar(255) DEFAULT NULL,
					`text` varchar(255) DEFAULT NULL,
					`url` varchar(255) DEFAULT NULL,
					`description` text,
					`content_text` text,
					`submenu_content_text` text,
					PRIMARY KEY (`id_deomegamenu`,`id_lang`)
				) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;
			');

			if ($reset == 1) {
				$drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'deomegamenu_shop`;';
			}
			$res &= (bool)Db::getInstance()->execute($drop.'
				CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'deomegamenu_shop` (
					`id_deomegamenu` int(11) NOT NULL DEFAULT \'0\',
					`id_shop` int(11) NOT NULL DEFAULT \'0\',
					PRIMARY KEY (`id_deomegamenu`,`id_shop`)
				) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;
			');

			if ($reset == 1) {
				$drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'deomegamenu_widgets`;';
			}
			$res &= (bool)Db::getInstance()->execute($drop.'
				CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'deomegamenu_widgets`(
					`id_deomegamenu_widgets` int(11) NOT NULL AUTO_INCREMENT,
					`name` varchar(250) NOT NULL,
					`type` varchar(250) NOT NULL,
					`params` longtext,
					`id_shop` int(11) unsigned NOT NULL,
					`key_widget` int(11) NOT NULL,
					PRIMARY KEY (`id_deomegamenu_widgets`,`id_shop`)
				) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;
			');

			if ($reset == 1) {
				$drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'deomegamenu_group`;';
			}
			$res &= (bool)Db::getInstance()->execute($drop.'
				CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'deomegamenu_group`(
					`id_deomegamenu_group` int(11) NOT NULL AUTO_INCREMENT,
					`id_shop` int(10) unsigned NOT NULL,
					`hook` varchar(64) DEFAULT NULL,
					`position` int(11) NOT NULL,
					`active` tinyint(1) unsigned NOT NULL DEFAULT \'1\',
					`tab_style` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
					`params` text NOT NULL,
					`active_ap` tinyint(1) DEFAULT NULL,
					`randkey` varchar(255) DEFAULT NULL,
					`form_id` varchar(255) DEFAULT NULL,
					PRIMARY KEY (`id_deomegamenu_group`,`id_shop`)
				) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;
			');

			if ($reset == 1) {
				$drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'deomegamenu_group_lang`;';
			}
			$res &= (bool)Db::getInstance()->execute($drop.'
				CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'deomegamenu_group_lang` (
					`id_deomegamenu_group` int(11) NOT NULL,
					`id_lang` int(11) NOT NULL,
					`title` varchar(255) DEFAULT NULL,
					`title_fo` varchar(255) DEFAULT NULL,
					PRIMARY KEY (`id_deomegamenu_group`,`id_lang`)
				) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;
			');


			return true;
		}

		public static function installSample()
		{
			$theme_name = DeoHelper::getInstallationThemeName();
			if (file_exists(_PS_ALL_THEMES_DIR_.$theme_name.'/samples/deotemplate.xml')) {
				return false;
			}

			// install root blog category
			$rows = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT id_deoblog_category FROM `'._DB_PREFIX_.'deoblog_category`');
			if (count($rows) <= 0) {
				$res = (bool)Db::getInstance()->execute('
					INSERT INTO `'._DB_PREFIX_.'deoblog_category`(`image`,`id_parent`,`is_root`) VALUES(\'\', 0, 1)
				');
				$languages = Language::getLanguages(false);
				foreach ($languages as $lang) {
					$res &= (bool)Db::getInstance()->execute('
						INSERT INTO `'._DB_PREFIX_.'deoblog_category_lang`(`id_deoblog_category`,`id_lang`,`title`) VALUES(1, '.(int)$lang['id_lang'].', \'Root\')
					');
				}

				$context = Context::getContext();
				$res &= (bool)Db::getInstance()->execute('
					INSERT INTO `'._DB_PREFIX_.'deoblog_category_shop`(`id_deoblog_category`,`id_shop`) VALUES( 1, '.(int)($context->shop->id).' )
				');
			}

			// Install sample title review
			$rows = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT id_deofeature_product_review_criterion FROM `'._DB_PREFIX_.'deofeature_product_review_criterion`');
			if (count($rows) <= 0) {
				$res = (bool)Db::getInstance()->execute('
					INSERT INTO `'._DB_PREFIX_.'deofeature_product_review_criterion` VALUES (1, 1, 1)');
				$languages = Language::getLanguages(false);
				foreach ($languages as $lang) {
					$res &= (bool)Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'deofeature_product_review_criterion_lang` VALUES(1, '.(int)$lang['id_lang'].', \'Quality\')');
				}
			}
		}

		public static function installModuleTab()
		{
			$id_parent = Tab::getIdFromClassName('CONFIGURE');

			//create parent tab
			$newtab = new Tab();
			$newtab->class_name = 'AdminDeoTemplate';
			$newtab->id_parent = $id_parent;
			$newtab->module = 'deotemplate';
			foreach (Language::getLanguages(false) as $l) {
				$newtab->name[$l['id_lang']] = Context::getContext()->getTranslator()->trans('Deo Template', array(), 'Modules.Deotemplate.Admin');
			}

			if ($newtab->save()) {

				$id_parent = $newtab->id;
				# insert icon for tab
				Db::getInstance()->execute(' UPDATE `'._DB_PREFIX_.'tab` SET `icon` = "desktop_mac" WHERE `id_tab` = "'.(int)$newtab->id.'"');

				foreach (self::getTabs() as $tab) {
					$newtab = new Tab();
					$newtab->class_name = $tab['class_name'];
					$newtab->id_parent = isset($tab['id_parent']) ? $tab['id_parent'] : $id_parent;
					$newtab->module = 'deotemplate';
					foreach (Language::getLanguages(false) as $l) {
						$newtab->name[$l['id_lang']] = Context::getContext()->getTranslator()->trans($tab['name'], array(), 'Modules.Deotemplate.Admin');
					}
					$newtab->save();
				}
				return true;
			}

			return false;
		}

		public static function getConfigurations(){
			return array(
				// DeoHelper::getConfigName('PRODUCT_MAX_RANDOM') => 2,
				DeoHelper::getConfigName('LOAD_LIBRARY_SWIPER') => 0,
				DeoHelper::getConfigName('LOAD_LIBRARY_OWL_CAROUSEL') => 1,
				DeoHelper::getConfigName('LOAD_LIBRARY_STELLAR') => 1,
				DeoHelper::getConfigName('LOAD_LIBRARY_PANR') => 1,
				DeoHelper::getConfigName('LOAD_LIBRARY_WAYPOINTS') => 0,
				DeoHelper::getConfigName('LOAD_LIBRARY_INSTAFEED') => 1,
				DeoHelper::getConfigName('LOAD_LIBRARY_HTML5_VIDEO') => 0,
				DeoHelper::getConfigName('SAVE_PROFILE_MULTITHREARING') => 1,
				DeoHelper::getConfigName('LOAD_LIBRARY_FULLPAGE') => 0,
				DeoHelper::getConfigName('LOAD_LIBRARY_IMAGE360') => 0,
				DeoHelper::getConfigName('SAVE_PROFILE_SUBMIT') => 1,
				DeoHelper::getConfigName('LOAD_LIBRARY_PRODUCT_ZOOM') => 1,
				DeoHelper::getConfigName('AJAX_CATEGORY_QTY') => 1,
				DeoHelper::getConfigName('AJAX_SECOND_PRODUCT_IMAGE') => 1,
				DeoHelper::getConfigName('AJAX_MULTIPLE_PRODUCT_IMAGE') => 1,
				DeoHelper::getConfigName('AJAX_COUNTDOWN') => 1,
				DeoHelper::getConfigName('SAVE_COOKIE_PROFILE') => 0,
				DeoHelper::getConfigName('LIST_MOBILE_HOOK') => implode(',', DeoSetting::getHook('mobile')),
				DeoHelper::getConfigName('LIST_HEADER_HOOK') => implode(',', DeoSetting::getHook('header')),
				DeoHelper::getConfigName('LIST_CONTENT_HOOK') => implode(',', DeoSetting::getHook('content')),
				DeoHelper::getConfigName('LIST_FOOTER_HOOK') => implode(',', DeoSetting::getHook('footer')),
				DeoHelper::getConfigName('LIST_PRODUCT_HOOK') => implode(',', DeoSetting::getHook('product')),
				DeoHelper::getConfigName('SHORTCODE_WIDGETS_MODULES') => json_encode(array()),
				DeoHelper::getConfigName('SHORTCODE_PRODUCT_LISTS') => json_encode(array()),
				DeoHelper::getConfigName('SHORTCODE_ELEMENTS') => json_encode(array()),
				DeoHelper::getConfigName('COOKIE_GLOBAL_MOBILE_ID') => 0,
				DeoHelper::getConfigName('COOKIE_GLOBAL_HEADER_ID') => 0,
				DeoHelper::getConfigName('COOKIE_GLOBAL_CONTENT_ID') => 0,
				DeoHelper::getConfigName('COOKIE_GLOBAL_FOOTER_ID') => 0,
				DeoHelper::getConfigName('COOKIE_GLOBAL_PRODUCT_ID') => 0,
				DeoHelper::getConfigName('COOKIE_GLOBAL_PROFILE_PARAM') => 0,
				DeoHelper::getConfigName('LOAD_LIBRARY_COOKIE') => 0,
				DeoHelper::getConfigName('DEBUG_MODE') => 0,
				DeoHelper::getConfigName('BLOG_DASHBOARD_FIELDS_VALUE') => '',
				DeoHelper::getConfigName('ENABLE_BLOG') => 1,
				DeoHelper::getConfigName('BLOG_DEFAULT_TEMPLATE') => 'default',
				DeoHelper::getConfigName('BLOG_LINK_REWRITE') => 'blog',
				DeoHelper::getConfigName('BLOG_URL_USE_ID') => 1,
				DeoHelper::getConfigName('BLOG_ITEM_SHOW_DESCRIPTION') => 1,
				DeoHelper::getConfigName('BLOG_ITEM_SHOW_IMAGE') => 1,
				DeoHelper::getConfigName('BLOG_ITEM_SHOW_AUTHOR') => 1,
				DeoHelper::getConfigName('BLOG_ITEM_SHOW_CATEGORY') => 1,
				DeoHelper::getConfigName('BLOG_ITEM_SHOW_CREATED') => 1,
				DeoHelper::getConfigName('BLOG_ITEM_SHOW_VIEWS') => 1,
				DeoHelper::getConfigName('BLOG_ITEM_SHOW_COUNT_COMMENT') => 1,
				DeoHelper::getConfigName('BLOG_ITEM_COMMENT_ENGINE') => 'local',
				DeoHelper::getConfigName('BLOG_ITEM_LIMIT_COMMENTS') => 10,
				DeoHelper::getConfigName('BLOG_ITEM_SHOW_LIST_COMMENT') => 1,
				DeoHelper::getConfigName('BLOG_ITEM_SHOW_FORM_COMMENT') => 1,
				DeoHelper::getConfigName('BLOG_ITEM_FACEBOOK_APP_ID') => '100858303516',
				DeoHelper::getConfigName('BLOG_CATEORY_MENU') => 1,
				DeoHelper::getConfigName('BLOG_SHOW_POPULAR') => 1,
				DeoHelper::getConfigName('BLOG_LIMIT_POPULAR') => 5,
				DeoHelper::getConfigName('BLOG_SHOW_RECENT') => 1,
				DeoHelper::getConfigName('BLOG_LIMIT_RECENT') => 5,
				DeoHelper::getConfigName('BLOG_SHOW_ALL_TAGS') => 1,
				DeoHelper::getConfigName('BLOG_IMAGE_SIZE') => '',
				DeoHelper::getConfigName('BLOG_TEMPLATES') => '',
				DeoHelper::getConfigName('ENABLE_INFINITE_SCROLL') => 0,
				DeoHelper::getConfigName('ENABLE_INFINITE_SCROLL_CATEGORY') => 1,
				DeoHelper::getConfigName('ENABLE_INFINITE_SCROLL_SEARCH') => 1,
				DeoHelper::getConfigName('ENABLE_INFINITE_SCROLL_DEOTEMPLATE-ADVANCEDSEARCH') => 1,
				DeoHelper::getConfigName('ENABLE_INFINITE_SCROLL_BEST-SALES') => 1,
				DeoHelper::getConfigName('ENABLE_INFINITE_SCROLL_NEW-PRODUCTS') => 1,
				DeoHelper::getConfigName('ENABLE_INFINITE_SCROLL_PRICES-DROP') => 1,
				DeoHelper::getConfigName('ENABLE_INFINITE_SCROLL_MANUFACTURER') => 1,
				DeoHelper::getConfigName('ENABLE_INFINITE_SCROLL_SUPPLIER') => 1,
				DeoHelper::getConfigName('INFINITE_SCROLL_PRODUCT_LIST_CSS_SELECTOR') => '#js-product-list .products',
				DeoHelper::getConfigName('INFINITE_SCROLL_ITEM_SELECTOR') => '.ajax_block_product',
				DeoHelper::getConfigName('INFINITE_SCROLL_PAGINATION_SELECTOR') => '.pagination',
				DeoHelper::getConfigName('INFINITE_SCROLL_HIDE_MESSAGE_WHEN_END_PAGE') => 0,
				DeoHelper::getConfigName('INFINITE_SCROLL_DISPLAY_LOAD_MORE_PRODUCT') => 0,
				DeoHelper::getConfigName('INFINITE_SCROLL_NUMBER_PAGE_SHOW_LOAD_MORE_PRODUCT') => 2,
				DeoHelper::getConfigName('INFINITE_SCROLL_FREQUENCY_SHOW_LOAD_MORE_PRODUCT') => 0,
				DeoHelper::getConfigName('INFINITE_SCROLL_JS_SCRIPT_AFTER') => '',
				DeoHelper::getConfigName('INFINITE_SCROLL_JS_SCRIPT_PROCESS_PRODUCTS') => '',
				DeoHelper::getConfigName('PANELTOOL') => 0,
				DeoHelper::getConfigName('SUBCATEGORY') => 0,
				DeoHelper::getConfigName('LAZYLOAD') => 1,
				DeoHelper::getConfigName('LAZY_INTERSECTION_OBSERVER') => 1,
				DeoHelper::getConfigName('STICKEY_MENU') => 0,
				DeoHelper::getConfigName('BACKTOP') => 1,
				DeoHelper::getConfigName('DEFAULT_SKIN') => 'default',
				DeoHelper::getConfigName('PRIMARY_CUSTOM_COLOR_SKIN') => '',
				DeoHelper::getConfigName('SECOND_CUSTOM_COLOR_SKIN') => '',
				DeoHelper::getConfigName('PRIMARY_CUSTOM_FONT') => '',
				DeoHelper::getConfigName('SECOND_CUSTOM_FONT') => '',
				DeoHelper::getConfigName('QUALITY_IMAGE_COMPRESS') => 80,
				DeoHelper::getConfigName('GRID_MODE') => 'grid',
				DeoHelper::getConfigName('BANNER_CATEGORY_PAGE') => 1,
				DeoHelper::getConfigName('ENABLE_GOOGLE_MAP') => 0,
				DeoHelper::getConfigName('API_KEY_GOOGLE_MAP') => '',
				DeoHelper::getConfigName('ENABLE_GOOGLE_MAP_CONTACT_PAGE') => 1,
				DeoHelper::getConfigName('WIDTH_GOOGLE_MAP_CONTACT_PAGE') => '100%',
				DeoHelper::getConfigName('HEIGHT_GOOGLE_MAP_CONTACT_PAGE') => '400px',
				DeoHelper::getConfigName('ZOOM_GOOGLE_MAP_CONTACT_PAGE') => '11',
				DeoHelper::getConfigName('ENABLE_STORE_ON_MAP_CONTACT_PAGE') => 1,
				DeoHelper::getConfigName('SHOW_SELECT_STORE_ON_MAP_CONTACT_PAGE') => 0,
				DeoHelper::getConfigName('LIST_STORE_CONTACT_PAGE') => '',
				DeoHelper::getConfigName('NUMBER_PRODUCT_MODULE') => '',
				DeoHelper::getConfigName('NUMBER_PRODUCT_LARGE_DESKTOP') => '',
				DeoHelper::getConfigName('NUMBER_PRODUCT_DESKTOP') => '',
				DeoHelper::getConfigName('NUMBER_PRODUCT_SMALL_DESKTOP') => '',
				DeoHelper::getConfigName('NUMBER_PRODUCT_TABLET') => '',
				DeoHelper::getConfigName('NUMBER_PRODUCT_SMALL_TABLET') => '',
				DeoHelper::getConfigName('NUMBER_PRODUCT_MOBILE') => '',
				DeoHelper::getConfigName('NUMBER_PRODUCT_SMALL_MOBILE') => '',
				DeoHelper::getConfigName('NUMBER_PRODUCT_LARGE_DESKTOP_SIDEBAR') => '',
				DeoHelper::getConfigName('NUMBER_PRODUCT_DESKTOP_SIDEBAR') => '',
				DeoHelper::getConfigName('NUMBER_PRODUCT_SMALL_DESKTOP_SIDEBAR') => '',
				DeoHelper::getConfigName('NUMBER_PRODUCT_TABLET_SIDEBAR') => '',
				DeoHelper::getConfigName('NUMBER_PRODUCT_SMALL_TABLET_SIDEBAR') => '',
				DeoHelper::getConfigName('NUMBER_PRODUCT_MOBILE_SIDEBAR') => '',
				DeoHelper::getConfigName('NUMBER_PRODUCT_SMALL_MOBILE_SIDEBAR') => '',
				DeoHelper::getConfigName('NUMBER_PRODUCT_LARGE_DESKTOP_BOTH') => '',
				DeoHelper::getConfigName('NUMBER_PRODUCT_DESKTOP_BOTH') => '',
				DeoHelper::getConfigName('NUMBER_PRODUCT_SMALL_DESKTOP_BOTH') => '',
				DeoHelper::getConfigName('NUMBER_PRODUCT_TABLET_BOTH') => '',
				DeoHelper::getConfigName('NUMBER_PRODUCT_SMALL_TABLET_BOTH') => '',
				DeoHelper::getConfigName('NUMBER_PRODUCT_MOBILE_BOTH') => '',
				DeoHelper::getConfigName('NUMBER_PRODUCT_SMALL_MOBILE_BOTH') => '',
				DeoHelper::getConfigName('ENABLE_AJAX_CART') => 1,
				DeoHelper::getConfigName('TYPE_EFFECT_FLYCART') => 1,
				DeoHelper::getConfigName('ENABLE_UPDATE_QUANTITY') => 1,
				DeoHelper::getConfigName('SHOW_COMBINATION') => 1,
				DeoHelper::getConfigName('SHOW_CUSTOMIZATION') => 1,
				DeoHelper::getConfigName('NUMBER_CART_ITEM_DISPLAY') => 3,
				DeoHelper::getConfigName('ENABLE_NOTIFICATION') => 1,
				DeoHelper::getConfigName('ENABLE_PRODUCT_REVIEWS') => 0,
				// DeoHelper::getConfigName('SHOW_PRODUCT_REVIEWS_LIST_PRODUCT') => 1,
				// DeoHelper::getConfigName('SHOW_NUMBER_PRODUCT_REVIEWS_LIST_PRODUCT') => 1,
				// DeoHelper::getConfigName('SHOW_ZERO_PRODUCT_REVIEWS_LIST_PRODUCT') => 1,
				DeoHelper::getConfigName('PRODUCT_REVIEWS_MODERATE') => 1,
				DeoHelper::getConfigName('PRODUCT_REVIEWS_ALLOW_USEFULL_BUTTON') => 1,
				DeoHelper::getConfigName('PRODUCT_REVIEWS_ALLOW_REPORT_BUTTON') => 1,
				DeoHelper::getConfigName('PRODUCT_REVIEWS_ALLOW_GUESTS') => 1,
				DeoHelper::getConfigName('PRODUCT_REVIEWS_MINIMAL_TIME') => 30,
				DeoHelper::getConfigName('ENABLE_PRODUCT_COMPARE') => 1,
				// DeoHelper::getConfigName('SHOW_PRODUCT_COMPARE_LIST_PRODUCT') => 1,
				// DeoHelper::getConfigName('SHOW_PRODUCT_COMPARE_PRODUCT_PAGE') => 1,
				DeoHelper::getConfigName('COMPARATOR_MAX_ITEM') => 3,
				DeoHelper::getConfigName('ENABLE_PRODUCT_WISHLIST') => 1,
				// DeoHelper::getConfigName('SHOW_PRODUCT_WISHLIST_LIST_PRODUCT') => 1,
				// DeoHelper::getConfigName('SHOW_PRODUCT_WISHLIST_PRODUCT_PAGE') => 1,
				DeoHelper::getConfigName('SOCIAL_LOGIN_ENABLE') => 1,
				DeoHelper::getConfigName('SOCIAL_LOGIN_AT_LOGIN_PAGE') => 0,
				DeoHelper::getConfigName('SOCIAL_LOGIN_FACEBOOK_ENABLE') => 1,
				DeoHelper::getConfigName('SOCIAL_LOGIN_FACEBOOK_APPID') => '',
				DeoHelper::getConfigName('SOCIAL_LOGIN_GOOGLE_ENABLE') => 1,
				DeoHelper::getConfigName('SOCIAL_LOGIN_GOOGLE_CLIENTID') => '',
				DeoHelper::getConfigName('SOCIAL_LOGIN_TWITTER_ENABLE') => 1,
				DeoHelper::getConfigName('SOCIAL_LOGIN_TWITTER_APIKEY') => '',
				DeoHelper::getConfigName('SOCIAL_LOGIN_TWITTER_APISECRET') => '',
				DeoHelper::getConfigName('SOCIAL_LOGIN_ENABLE_REDIRECT') => 1,
				DeoHelper::getConfigName('SOCIAL_LOGIN_ENABLE_CHECK_TERMS') => 1,
				DeoHelper::getConfigName('SOCIAL_LOGIN_LINK_TERMS') => '',
				DeoHelper::getConfigName('SOCIAL_LOGIN_ENABLE_CHECK_COOKIE') => 1,
				DeoHelper::getConfigName('SOCIAL_LOGIN_LIFETIME_COOKIE') => '28800',
				DeoHelper::getConfigName('ENABLE_ONEPAGECHECKOUT') => 0,
				DeoHelper::getConfigName('USE_ONEPAGECHECKOUT_MOBILE') => 1,
				DeoHelper::getConfigName('BLOCKS_UPDATE_LOADER') => 1,
				DeoHelper::getConfigName('SHOW_PRODUCT_STOCK_INFO') => 0,
				DeoHelper::getConfigName('CLEAN_CHECKOUT_SESSION_AFTER_CONFIRMATION') => 1,
				DeoHelper::getConfigName('ALLOW_GUEST_CHECKOUT_FOR_REGISTERED') => 0,
				DeoHelper::getConfigName('CREATE_ACCOUNT_CHECKBOX') => 1,
				DeoHelper::getConfigName('SHOW_I_AM_BUSINESS') => 0,
				DeoHelper::getConfigName('BUSINESS_FIELDS') => 'company, dni, vat_number',
				DeoHelper::getConfigName('BUSINESS_DISABLED_FIELDS') => '',
				DeoHelper::getConfigName('SHOW_I_AM_PRIVATE') => 0,
				DeoHelper::getConfigName('PRIVATE_FIELDS') => 'dni',
				DeoHelper::getConfigName('OFFER_SECOND_ADDRESS') => 1,
				DeoHelper::getConfigName('EXPAND_SECOND_ADDRESS') => 0,
				DeoHelper::getConfigName('MARK_REQUIRED_FIELDS') => 1,
				DeoHelper::getConfigName('NEWSLETTER_CHECKED') => 0,
				DeoHelper::getConfigName('SHOW_CALL_PREFIX') => 0,
				DeoHelper::getConfigName('INITIALIZE_ADDRESS') => 0,
				DeoHelper::getConfigName('SHIPPING_REQUIRED_FIELDS') => '',
				DeoHelper::getConfigName('SHOW_SHIPPING_COUNTRY_IN_CARRIERS') => 0,
				DeoHelper::getConfigName('POSTCODE_REMOVE_SPACES') => 0,
				DeoHelper::getConfigName('SHOW_ORDER_MESSAGE') => 0,
				DeoHelper::getConfigName('SEPARATE_PAYMENT') => 0,
				DeoHelper::getConfigName('DEFAULT_PAYMENT_METHOD') => 'local',
				DeoHelper::getConfigName('PAYMENT_REQUIRED_FIELDS') => 1,
				DeoHelper::getConfigName('CUSTOMER_FIELDS') => '',
				DeoHelper::getConfigName('INVOICE_FIELDS') => '',
				DeoHelper::getConfigName('DELIVERY_FIELDS') => '', 
			);
		}

		public static function installConfiguration()
		{
			$res = true;
			foreach (DeoPageSetup::getConfigurations() as $key => $value) {
				$res &= Configuration::updateValue($key, $value);
			}

			return $res;
		}

		public static function deleteTables()
		{
			Db::getInstance()->execute('DROP TABLE IF EXISTS `'.
				_DB_PREFIX_.'deotemplate_profiles`, `'.
				_DB_PREFIX_.'deotemplate_profiles_lang`, `'.
				_DB_PREFIX_.'deotemplate_profiles_shop`, `'.
				_DB_PREFIX_.'deotemplate_products`, `'.
				_DB_PREFIX_.'deotemplate_products_shop`, `'.
				_DB_PREFIX_.'deotemplate`, `'.
				_DB_PREFIX_.'deotemplate_shop`, `'.
				_DB_PREFIX_.'deotemplate_lang`, `'.
				_DB_PREFIX_.'deotemplate_details`, `'.
				_DB_PREFIX_.'deotemplate_details_shop`, `'.
				_DB_PREFIX_.'deotemplate_onepagecheckout`, `'.
				_DB_PREFIX_.'deotemplate_onepagecheckout_shop`, `'.
				_DB_PREFIX_.'deotemplate_positions`, `'.
				_DB_PREFIX_.'deotemplate_positions_shop`, `'.
				_DB_PREFIX_.'deotemplate_shortcode`, `'.
				_DB_PREFIX_.'deotemplate_shortcode_lang`, `'.
				_DB_PREFIX_.'deotemplate_shortcode_shop`, `'.
				_DB_PREFIX_.'deoblog_category`, `'.
				_DB_PREFIX_.'deoblog_category_lang`, `'.
				_DB_PREFIX_.'deoblog_category_shop`, `'.
				_DB_PREFIX_.'deoblog_comment`, `'.
				_DB_PREFIX_.'deoblog`, `'.
				_DB_PREFIX_.'deoblog_lang`, `'.
				_DB_PREFIX_.'deoblog_shop`, `'.
				_DB_PREFIX_.'deofeature_product_review`, `'.
				_DB_PREFIX_.'deofeature_product_review_criterion`, `'.
				_DB_PREFIX_.'deofeature_product_review_criterion_product`, `'.
				_DB_PREFIX_.'deofeature_product_review_criterion_lang`, `'.
				_DB_PREFIX_.'deofeature_product_review_criterion_category`, `'.
				_DB_PREFIX_.'deofeature_product_review_grade`, `'.
				_DB_PREFIX_.'deofeature_product_review_usefulness`, `'.
				_DB_PREFIX_.'deofeature_product_review_report`, `'.
				_DB_PREFIX_.'deofeature_compare`, `'.
				_DB_PREFIX_.'deofeature_compare_product`, `'.
				_DB_PREFIX_.'deofeature_wishlist`, `'.
				_DB_PREFIX_.'deofeature_wishlist_product`, `'.
				_DB_PREFIX_.'deomegamenu`, `'.
				_DB_PREFIX_.'deomegamenu_lang`, `'.
				_DB_PREFIX_.'deomegamenu_shop`, `'.
				_DB_PREFIX_.'deomegamenu_widgets`, `'.
				_DB_PREFIX_.'deomegamenu_group`, `'.
				_DB_PREFIX_.'deomegamenu_group_lang`'.
			'');

			return true;
		}
		
		public static function uninstallModuleTab()
		{
			$id = Tab::getIdFromClassName('AdminDeoTemplate');
			if ($id) {
				$tab = new Tab($id);
				$tab->delete();
			}

			foreach (self::getTabs() as $tab) {
				$id = Tab::getIdFromClassName($tab['class_name']);
				if ($id) {
					$tab = new Tab($id);
					$tab->delete();
				}
			}

			return true;
		}
		
		public static function uninstallConfiguration()
		{
			$res = true;
			foreach (DeoPageSetup::getConfigurations() as $key => $value) {
				$res &= Configuration::deleteByName($key);
			}

			return true;
		}
		
		/**
		 * Remove file index.php in sub folder theme/translations folder when install theme
		 */
		public static function processTranslateTheme()
		{
			$theme_name = DeoHelper::getInstallationThemeName();
			if (file_exists(_PS_ALL_THEMES_DIR_.$theme_name.'/config.xml')) {
				$directories = glob(_PS_ALL_THEMES_DIR_.$theme_name.'/translations/*', GLOB_ONLYDIR);
				if (count($directories) > 0) {
					foreach ($directories as $directories_val) {
						if (file_exists($directories_val.'/index.php')) {
							unlink($directories_val.'/index.php');
						}
					}
				}
			}
		}
		
		/**
		 * Remove file index.php for translate in Quickstart version
		 */
		public static function processTranslateQSTheme()
		{
			# GET ARRAY THEME_NAME
			$arr_theme_name = array();
			$themes = glob(_PS_ROOT_DIR_.'/themes/*/config/theme.yml');
			if (count($themes) > 1) {
				foreach ($themes as $key => $value) {
					$temp_name = basename(Tools::substr($value, 0, -Tools::strlen('/config/theme.yml')));
					if ($temp_name == 'classic') {
						continue;
					} else {
						$arr_theme_name[] = $temp_name;
					}
				}
			}
			
			foreach ($arr_theme_name as $key => $theme_name) {
				// remove index.php in sub folder theme/translations folder when install theme
				
				if (file_exists(_PS_ALL_THEMES_DIR_.$theme_name.'/config.xml')) {
					$directories = glob(_PS_ALL_THEMES_DIR_.$theme_name.'/translations/*', GLOB_ONLYDIR);
					if (count($directories) > 0) {
						foreach ($directories as $directories_val) {
							if (file_exists($directories_val.'/index.php')) {
								unlink($directories_val.'/index.php');
							}
						}
					}
				}
			}
		}
	}

}