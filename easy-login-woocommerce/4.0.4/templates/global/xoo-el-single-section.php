<?php
/**
 * A single form which navigates to login/register
 *
 * This template can be overridden by copying it to yourtheme/templates/easy-login-woocommerce/global/xoo-el-single-section.php
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


?>


<?php do_action( 'xoo_el_single_before_fields', $args ); ?>

<div class="xoo-el-sing-fields">

	<?php xoo_el_fields()->get_fields_html('single'); ?>

	<?php do_action( 'xoo_el_single_add_fields', $args ); ?>

</div>

<input type="hidden" name="_xoo_el_form" value="single">

<input type="hidden" name="_xoo_el_referral" value="">

<button type="submit" class="button btn xoo-el-action-btn xoo-el-single-btn"><?php esc_html_e( xoo_el_helper()->get_general_option( 'txt-btn-single' ) ) ?></button>