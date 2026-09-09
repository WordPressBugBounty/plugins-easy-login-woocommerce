<?php
/**
 * Contains Header HTML for switching tabs.
 *
 * This template can be overridden by copying it to yourtheme/templates/easy-login-woocommerce/global/xoo-el-header.php.
 *
 * HOWEVER, on occasion we will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen.
 * @see     https://docs.xootix.com/easy-login-woocommerce/
 * @version 3.2.0
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

//return if single form pattern
if( $args['navstyle'] !== 'tabs' ) return;

$loginTabText 		= xoo_el_helper()->parsePlaceHolders(  xoo_el_helper()->get_general_option( 'txt-tab-login' ), array( '{icon}' => '<span class="xoo-el-icon-user"></span>' ) );
$registerTabText 	= xoo_el_helper()->parsePlaceHolders(  xoo_el_helper()->get_general_option( 'txt-tab-reg' ), array( '{icon}' => '<span class="xoo-el-icon-user-plus"></span>' ) );

?>

<div class="xoo-el-header">
	<ul class="xoo-el-tabs">
		
        <?php if( in_array( 'login', $args['tabs'] ) && $loginTabText  ): ?>
		  <li data-tab="login" class="xoo-el-login-tgr" style="order: <?php echo (int) array_search('login', $args['tabs'] ) ?> "><?php echo wp_kses_post( $loginTabText  ) ?></li>
        <?php endif; ?>

		<?php if( in_array( 'register', $args['tabs'] ) && xoo_el_helper()->get_general_option( 'txt-tab-reg' ) ):?> 
			<li data-tab="register" class="xoo-el-reg-tgr" style="order: <?php echo (int) array_search('register', $args['tabs'] ) ?>"><?php echo wp_kses_post( $registerTabText  ) ?></li>
		<?php endif; ?>

	</ul>
</div>