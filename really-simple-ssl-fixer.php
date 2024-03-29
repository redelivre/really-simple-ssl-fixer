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

class RSSSLF {
	public function __construct() {
		add_action('init', array($this, 'init'), 11);
	}
	public function sslinit() {
		//add_filter('template_directory_uri', array($this,'fix_template_directory_uri'), 10, 3);
		add_filter('theme_root_uri', array($this, 'fix_theme_root_uri'), 10, 3);
		add_filter('includes_url', array($this, 'fix_uri'), 10, 1);
		add_filter('wp_calculate_image_srcset', array($this, 'wp_calculate_image_srcset'), 10, 1);
		add_filter('et_builder_custom_fonts', array($this, 'et_builder_custom_fonts'), 9, 1 );
	}
	public function init() {
		add_filter('get_user_option_use_ssl', array($this, 'get_user_option_use_ssl') );
		add_filter('login_redirect', array($this, 'login_redirect'), 99 );
		if (!is_admin() && is_ssl() && defined('rsssl_plugin') && RSSSL()->rsssl_front_end->autoreplace_insecure_links) {
			$this->sslinit();
		}
		
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
	public function wp_calculate_image_srcset($sources) {
		$sources['url'] = $this->fix_uri($sources['url']);
		return $sources;
	}
	public function get_user_option_use_ssl($ssl) {
		if( isset($_SERVER['HTTPS']) && 'on' == $_SERVER['HTTPS'] ) { //force ssl when enabled
			return true;
		}
		return $ssl;
	}
	public function login_redirect($url) {
		if( isset($_SERVER['HTTPS']) && 'on' == $_SERVER['HTTPS'] ) { //force ssl when enabled
			$url = str_replace("http://", "https://", $url);
		}
		return $url;
	}
	
	public function et_builder_custom_fonts($all_custom_fonts) {
		if(is_array($all_custom_fonts)) {
			foreach ($all_custom_fonts as $font_name => $font_array) {
				if(array_key_exists('font_url', $font_array) && is_array($font_array['font_url'])) {
					foreach ($font_array['font_url'] as $ext => $url) {
						$all_custom_fonts[$font_name]['font_url'][$ext] = str_replace("http://", "https://", $url);
					}
				}
			}
		}
		return $all_custom_fonts;
	}
	
}
new RSSSLF();
