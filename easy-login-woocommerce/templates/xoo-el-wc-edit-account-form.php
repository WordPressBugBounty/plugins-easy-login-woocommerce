<?php
/**
 * Woocommerce edit-account form is replaced by this template
 *
 * This template can be overridden by copying it to yourtheme/templates/xoo-el-wc-edit-account-form.php.
 * @version 99.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$shortcode = xoo_el_helper()->get_general_option('m-editaccount-sc');

?>

<?php do_action( 'woocommerce_before_edit_account_form' ); ?>

<?php echo do_shortcode( apply_filters( 'xoo_el_editaccount_shortcode', $shortcode ) ); ?>
		
<?php do_action( 'woocommerce_after_edit_account_form' ); ?>