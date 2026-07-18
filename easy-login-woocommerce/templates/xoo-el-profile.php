<?php
/**
 * Profile Page
 *
 * This template can be overridden by copying it to yourtheme/templates/easy-login-woocommerce/xoo-el-profile.php.
 *
 * HOWEVER, on occasion we will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen.
 * @see     https://docs.xootix.com/easy-login-woocommerce
 * @version 3.2.6
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<div class="xoo-elpof-container">

	<div class="xoo-elpof-head"><?php echo wp_kses_post( xoo_el_helper()->get_general_option('txt-profile-head') ) ?></div>

	<div class="xoo-ml-notice xoo-el-notice"></div>

	<form class="xoo-elpof-profile-update">

		<div class="xoo-elpof-notices"></div>


		<div class="xoo-elpof-form-fields">
			<?php xoo_elpof_core()->profile_fields_html(); ?>
		</div>

		<?php wp_nonce_field( 'xoo_elpof_profile_update', 'xoo_elpof_nonce_field' ); ?>
		
		<div class="xoo-elpof-buttons">
			<button type="submit" class="xoo-elpof-submit button btn xoo-elpof-btn xoo-el-action-btn"><?php echo wp_kses_post( xoo_el_helper()->get_general_option('txt-btn-profile') ) ?></button>
		</div>

	</form>

</div>