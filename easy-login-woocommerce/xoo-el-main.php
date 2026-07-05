<?php
/**
* Plugin Name: Login & Register Forms – Popup | Slider | Inline | WooCommerce
* Plugin URI: http://xootix.com/easy-login-for-woocommerce
* Author: XootiX
* Version: 3.2.2
* Text Domain: easy-login-woocommerce
* Domain Path: /languages
* Author URI: http://xootix.com
* Description: Allow users to login/signup using interactive popup design.
* Tags: woocommerce login, login popup, ajax login, otp login, registration
*/



//Exit if accessed directly
if( !defined( 'ABSPATH' ) ){
	return;
}


define( 'XOO_EL', true);
define( 'XOO_EL_PLUGIN_FILE', __FILE__ );
define( "XOO_EL_PATH", plugin_dir_path( XOO_EL_PLUGIN_FILE ) ); // Plugin path
define( "XOO_EL_URL", untrailingslashit( plugins_url( '/', XOO_EL_PLUGIN_FILE ) ) ); // plugin url
define( "XOO_EL_PLUGIN_BASENAME", plugin_basename( XOO_EL_PLUGIN_FILE ) );
define( "XOO_EL_VERSION", "3.2.2" ); //Plugin version

require_once XOO_EL_PATH.'/includes/xoo-framework/xoo-framework.php';


if ( ! class_exists( 'Xoo_El_Core' ) ) {
	require_once 'includes/class-xoo-el-core.php';
}

if( !function_exists( 'xoo_el' ) ){
	function xoo_el(){
		
		do_action('xoo_el_before_plugin_activation');

		return Xoo_El_Core::get_instance();
		
	}
}
add_action( 'plugins_loaded', 'xoo_el', 8 );

?>