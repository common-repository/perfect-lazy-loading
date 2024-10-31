<?php
/**
 * Plugin Name:       Perfect Lazy Loading
 * Plugin URI:        https://www.loremipsum.at/
 * Description:       A simple lazy loading solution for images
 * Version:           1.0.2
 * Author:            Lorem Ipsum web.solutions GmbH
 * Author URI:        https://www.loremipsum.at/
 * License:           GPL v3
 * Text Domain:       pll
 * Domain Path:       /languages/
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

load_plugin_textdomain( 'admin-page-framework', false, basename( dirname( __FILE__ ) ) . '/lib/apf/languages' );
load_plugin_textdomain( 'pll', false, basename( dirname( __FILE__ ) ) . '/languages' );

define( 'PERFECT_LAZY_LOADING_PLUGIN_VERSION', '1.0.2' );
define( 'PERFECT_LAZY_LOADING_PLUGIN_FILE', __FILE__);
define( 'PERFECT_LAZY_LOADING_PLUGIN_TITLE', 'Perfect Lazy Loading');

require_once plugin_dir_path( __FILE__ ) . 'frontend/class-pll-frontend.php';
new Perfect_Lazy_Loading_Frontend();

if (is_admin()) {
    require_once plugin_dir_path( __FILE__ ) . 'admin/class-pll-admin.php';
    new Perfect_Lazy_Loading_Admin();
}