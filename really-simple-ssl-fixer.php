<?php
/**
 * Plugin Name: Really Simple SSL Fixer
 * Plugin URI: https://github.com/redelivre/really-simple-ssl-fixer
 * Description: Fix for some problems on multi sites with really-simple-ssl
 * Version: 0.0.1
 * Text Domain: really-simple-ssl-fixer
 * Domain Path: /languages
 * Author: Rede Livre
 * Author URI: https://redelivre.org.br
 * License: GPL3
 */

defined('ABSPATH') or die("you do not have access to this page!");

if(defined('rsssl_plugin')) {
	class RSSSLF {
		public function __construct() {
			if (!is_admin() && is_ssl() && RSSSL()->rsssl_front_end->autoreplace_insecure_links) {
				add_action('init', array($this, 'init'), 11);
			}
		}
		public function init() {
			//add_filter('template_directory_uri', array($this,'fix_template_directory_uri'), 10, 3);
			add_filter('theme_root_uri', array($this, 'fix_theme_root_uri'), 10, 3);
			add_filter('includes_url', array($this, 'fix_uri'), 10, 1);
		}
		public function fix_uri($uri) {
			return preg_replace('/^https?\:/i', '//', $uri); // replace "http://" or "https://" by "//"
		}
		public function fix_template_directory_uri($template_dir_uri, $template, $theme_root_uri) {
			return $this->fix_uri($template_dir_uri);
		}
		public function fix_theme_root_uri($theme_root_uri, $siteurl, $stylesheet_or_template) {
			return $this->fix_uri($theme_root_uri);
		}
	}
}