<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! function_exists( 'get_editable_roles' ) ) {
	require_once ABSPATH . 'wp-admin/includes/user.php';
}

$editable_roles = array_reverse( get_editable_roles() );

foreach ( $editable_roles as $role_id => $role_data) {
	$user_roles[$role_id] = translate_user_role( $role_data['name'] );
}

$user_roles = apply_filters( 'xoo_el_admin_user_roles', $user_roles );

$localizeTexts = version_compare( get_option( 'xoo-el-version' ) , '2.5', '<' );

$settings = array(

	/** MAIN **/
	'fake' => array(
		'callback' 		=> 'links',
		'title' 		=> 'Links',
		'id' 			=> 'fake',
		'section_id' 	=> 'gl_main',
		'args' 			=> array(
			'options' 	=> array(
				admin_url('admin.php?page=xoo-el-fields') => 'Manage Fields',
				admin_url( 'nav-menus.php?xoo_el_nav=true' ) => 'Add Links to Menu',
			)
		)
	),


	'm-form-pattern' => array(
		'callback' 		=> 'asset_selector',
		'title' 		=> 'Form Pattern',
		'id' 			=> 'm-form-pattern',
		'section_id' 	=> 'gl_main',
		'default' 		=> 'separate',
		'args' 			=> array(
			'options' => array(
				'separate' 	=> array(
					'title' => 'Separate',
					'asset' => XOO_EL_URL.'/admin/assets/images/pattern-separate.jpg',
					'info' 	=> 'Displays separate login and registration forms side by side'
				),
				'single' 	=> array(
					'title' => 'Single',
					'asset' => XOO_EL_URL.'/admin/assets/images/pattern-single.jpg',
					'info' 	=> 'A single field form where users enter email or username and are auto-directed to login or registration based on input.'
				)
			),
			'custom_attributes' => array(
				'data-multiple' => 'no',
				'data-required' => 'yes'
			)
		),

	),

	'm-nav-pattern' => array(
		'callback' 		=> 'select',
		'title' 		=> 'Navigation Pattern',
		'id' 			=> 'm-nav-pattern',
		'section_id' 	=> 'gl_main',
		'args'			=> array(
			'options' => array(
				'tabs' 		=> 'Header Tabs',
				'links' 	=> 'Footer Links',
				'disable' 	=> 'Disable'
			)
		),
		'default' 		=> 'tabs',
		'desc' 			=> 'Choose a way to switch between login and registration form.'
	),



	'm-en-reg' => array(
		'callback' 		=> 'checkbox',
		'title' 		=> 'Enable Registration',
		'id' 			=> 'm-en-reg',
		'section_id' 	=> 'gl_main',
		'default' 		=> 'yes',
	),

	'm-user-role' => array(
		'callback' 		=> 'select',
		'title' 		=> 'User Role',
		'id' 			=> 'm-user-role',
		'section_id' 	=> 'gl_main',
		'args'			=> array(
			'options' => $user_roles
		),
		'default' 		=> class_exists( 'woocommerce' ) ? 'customer' : 'subscriber',
		'desc' 			=> 'Register users with role.<br> You can also enable "User Role" field from the "Fields" page and allow users to select their role while signing up.'
	),

	'm-auto-login' => array(
		'callback' 		=> 'checkbox',
		'title' 		=> 'Auto Login User on Sign up',
		'id' 			=> 'm-auto-login',
		'section_id' 	=> 'gl_main',
		'default' 		=> 'yes',
	),


	'm-reset-pw' => array(
		'callback' 		=> 'select',
		'title' 		=> 'Reset Password Email',
		'id' 			=> 'm-reset-pw',
		'section_id' 	=> 'gl_main',
		'args'			=> array(
			'options' => array(
				'link' 		=> 'Send Reset Link',
				'code' 		=> 'Send Verification Code',
				'disable' 	=> 'Do not Handle'
			),
			'value_desc' => array(
				'link' 		=> class_exists( 'woocommerce' ) ? 'Reset password email is sent by woocommerce, plugin does not control the email and its content' : 'Sends reset password link to users',
				'code' 		=> "Sends 6 digit code to user email address",
				'disable' 	=> 'Plugin will let your site handle the reset password functionality'
			),
			'toggleSettings' => array( //hide elements if their value is
				'xoo-el-gl-options[m-reset-pw-email]' => array( 'disable', 'link' ),
				'xoo-el-gl-options[m-reset-pw-subject]' => array( 'disable', 'link' )
			),
		),
		'default' 		=> 'link',
		'desc' 			=> 'If you\'re not receiving emails, please check that the email functionality is working on your site. You can use the "WP Mail SMTP" plugin to set up your email.'
	),

	'm-reset-pw-subject' => array(
		'callback' 		=> 'text',
		'title' 		=> 'Reset Password Email Subject',
		'id' 			=> 'm-reset-pw-subject',
		'section_id' 	=> 'gl_main',
		'default' 		=> 'Reset your password for {site_title}',
		'args' 			=> array(
			'custom_attributes' => array(
				'style' => 'max-width: 400px'
			)
		)
	),

	'm-reset-pw-email' => array(
		'callback' 		=> 'wp_editor',
		'title' 		=> 'Reset Password Email Text<br>[ Verification Code ]',
		'id' 			=> 'm-reset-pw-email',
		'section_id' 	=> 'gl_main',
		'args' 			=> array(
			'group' 	=> 'email_content',
			'placeholders' => array(
				'{verify_code}' 	=> 'Verification Code', 	
				'{user_login}' 		=> 'Username',
				'{user_firstname}'	=> 'User\'s Firstname',
				'{site_title}' 		=> 'Site Title',
			),
			'editor_settings' => array(
				'editor_height' => 400,
			)

		),
		'default' 		=> xoo_el_admin_settings()->default_reset_email_text(),
	),



);



$popup = array(

	'popup-forms' => array(
		'callback' 		=> 'checkbox_list',
		'title' 		=> 'Forms',
		'id' 			=> 'popup-forms',
		'section_id' 	=> 'gl_popup',
		'args' 			=> array(
			'options' 	=> array(
				'login' 		=> 'Login',
				'register' 		=> 'Register',
			),
		),
		'default' 	=> array(
			'login', 'register',
		)
	),


	'popup-force' => array(
		'callback' 		=> 'checkbox',
		'title' 		=> 'Prevent closing',
		'id' 			=> 'popup-force',
		'section_id' 	=> 'gl_popup',
		'default' 		=> 'no',
		'desc' 			=> 'Once popup is opened, this option will prevent user from closing it. Useful when you want to hide your website page content for guest users. You can also set "overlay opacity to 1" from style tab to completely blackout the background.'
	),



	'ao-enable' => array(
		'callback' 		=> 'checkbox',
		'title' 		=> 'Auto open Popup',
		'id' 			=> 'ao-enable',
		'section_id' 	=> 'gl_ao',
		'default' 		=> 'yes',
	),


	'ao-once' => array(
		'callback' 		=> 'checkbox',
		'title' 		=> 'Open on first visit only',
		'id' 			=> 'ao-once',
		'section_id' 	=> 'gl_ao',
		'default' 		=> 'no',
	),



	'ao-default-form' => array(
		'callback' 		=> 'select',
		'title' 		=> 'Default Tab',
		'id' 			=> 'ao-default-form',
		'section_id' 	=> 'gl_ao',
		'args' 			=> array(
			'options' 		=> array(
				'login' 	=> 'Login',
				'register' 	=> 'Register',
			),
		),
		'default' 		=> 'login',
	),



	'ao-pages' => array(
		'callback' 		=> 'textarea',
		'title' 		=> 'On Pages',
		'id' 			=> 'ao-pages',
		'section_id' 	=> 'gl_ao',
		'default' 		=> '',
		'desc' 			=> 'Use post type/page id/slug separated by comma. For eg: 19,contact-us,shop .Leave empty for every page.'
	),

	'ao-delay' => array(
		'callback' 		=> 'number',
		'title' 		=> 'Delay',
		'id' 			=> 'ao-delay',
		'section_id' 	=> 'gl_ao',
		'default' 		=> 500,
		'desc' 			=> 'Trigger popup after seconds. 1000 = 1 second'
	),


	'm-red-login' => array(
		'callback' 		=> 'text',
		'title' 		=> 'Login Redirect',
		'id' 			=> 'm-red-login',
		'section_id' 	=> 'gl_red',
		'default' 		=> '',
		'desc' 			=> 'Leave empty to redirect on the same page.'
	),

	'm-red-register' => array(
		'callback' 		=> 'text',
		'title' 		=> 'Register Redirect',
		'id' 			=> 'm-red-register',
		'section_id' 	=> 'gl_red',
		'default' 		=> '',
		'desc' 			=> 'Leave empty to redirect on the same page.'
	),

	'm-red-logout' => array(
		'callback' 		=> 'text',
		'title' 		=> 'Logout Redirect',
		'id' 			=> 'm-red-logout',
		'section_id' 	=> 'gl_red',
		'default' 		=> '',
		'desc' 			=> 'Leave empty to redirect on the same page.'
	),


	'm-ep-success' => array(
		'callback' 		=> 'checkbox',
		'title' 		=> 'Success Endpoint',
		'id' 			=> 'm-ep-success',
		'section_id' 	=> 'gl_red',
		'default' 		=> 'yes',
		'desc' 			=> 'Adds (login="success" & register="success") in URL bar on login & register. Clears cache on login/register if you have cache plugin enabled'
	),

);

$settings = array_merge( $settings, $popup );

if( class_exists( 'woocommerce' ) ){
	$settings['m-en-myaccount'] = array(
		'callback' 		=> 'checkbox',
		'title' 		=> 'Replace myaccount form',
		'id' 			=> 'm-en-myaccount',
		'section_id' 	=> 'gl_wc',
		'default' 		=> 'yes',
		'desc' 			=> 'If checked , this will replace woocommerce myaccount page form.'
	);

	$settings['m-myacc-sc'] = array(
		'callback' 		=> 'textarea',
		'title' 		=> 'My account page form shortcode',
		'id' 			=> 'm-myacc-sc',
		'section_id' 	=> 'gl_wc',
		'default' 		=> '[xoo_el_inline_form active="login"]',
		'desc' 			=> 'My account page form shortcode. See shortcodes tab for shortcode details',
		'args' 			=> array(
			'rows' => 2,
			'cols' => 60,
			'custom_attributes' => array(
				'spellcheck' => 'false',
			)
		)
	);

	$settings['m-myacclpw-sc'] = array(
		'callback' 		=> 'textarea',
		'title' 		=> 'Lost Password page form shortcode',
		'id' 			=> 'm-myacclpw-sc',
		'section_id' 	=> 'gl_wc',
		'default' 		=> '[xoo_el_inline_form active="lostpw"]',
		'desc' 			=> 'Lost Password page form shortcode. See shortcodes tab for shortcode details',
		'args' 			=> array(
			'rows' => 2,
			'cols' => 60,
			'custom_attributes' => array(
				'spellcheck' => 'false',
			)
		)
	);

	$settings['m-en-chkout'] = array(
		'callback' 		=> 'checkbox',
		'title' 		=> 'Replace Checkout page login',
		'id' 			=> 'm-en-chkout',
		'section_id' 	=> 'gl_wc',
		'default' 		=> 'yes',
		'desc' 			=> 'This will replace checkout page login form, make sure to enable "Login during checkout" from woocommerce settings'
	);

	$settings['m-chkout-sc'] = array(
		'callback' 		=> 'textarea',
		'title' 		=> 'Checkout page form shortcode',
		'id' 			=> 'm-chkout-sc',
		'section_id' 	=> 'gl_wc',
		'default' 		=> '[xoo_el_inline_form active="login" login_redirect="same" register_redirect="same"]',
		'desc' 			=> 'Checkout page form shortcode. See shortcodes tab for shortcode details',
		'args' 			=> array(
			'rows' => 2,
			'cols' => 60,
			'custom_attributes' => array(
				'spellcheck' => 'false',
			)
		)
	);

	$settings['m-editaccount-replace'] = array(
		'callback' 		=> 'checkbox',
		'title' 		=> 'Replace woocommerce my-account edit form',
		'id' 			=> 'm-editaccount-replace',
		'section_id' 	=> 'gl_wc',
		'default' 		=> 'yes',
		'args' 			=> array(
			'toggleSettings' => array(
				'xoo-el-gl-options[m-editaccount-sc]' => array( 'unchecked' )
			)
		),
		'desc' 			=> '<a class="xoo-icon-link" href="'.wc_get_account_endpoint_url( 'edit-account' ).'" target="__blank"></a>Woocommerce Edit account details page. See shortcodes tab for shortcode details'
	);

	$settings['m-editaccount-sc'] = array(
		'callback' 		=> 'textarea',
		'title' 		=> 'My-account edit form shortcode',
		'id' 			=> 'm-editaccount-sc',
		'section_id' 	=> 'gl_wc',
		'default' 		=> '[xoo_el_profile]',
		'desc' 			=> '<a class="xoo-icon-link" href="'.wc_get_account_endpoint_url( 'edit-account' ).'" target="__blank"></a>Woocommerce Edit account details page shortcode. See shortcodes tab for shortcode details',
		'args' 			=> array(
			'rows' => 2,
			'cols' => 60,
			'custom_attributes' => array(
				'spellcheck' => 'false',
			)
		)
	);
}



$texts = array(

	'txt-tab-login' => array(
		'callback' 		=> 'text',
		'title' 		=> 'Login Tab text',
		'id' 			=> 'txt-tab-login',
		'section_id' 	=> 'gl_texts',
		'default' 		=> sprintf( '{icon} %s', $localizeTexts ? __( 'Login', 'easy-login-woocommerce' ) : 'Login' ),
		'desc' 			=> 'Placeholder: {icon}'
	),

	'txt-tab-reg' => array(
		'callback' 		=> 'text',
		'title' 		=> 'Register Tab text',
		'id' 			=> 'txt-tab-reg',
		'section_id' 	=> 'gl_texts',
		'default' 		=> sprintf( '{icon} %s', $localizeTexts ? __( 'Sign up', 'easy-login-woocommerce' ) : 'Sign up' ),
		'desc' 			=> 'Placeholder: {icon}'
	),

	'txt-btn-login' => array(
		'callback' 		=> 'text',
		'title' 		=> 'Login Button text',
		'id' 			=> 'txt-btn-login',
		'section_id' 	=> 'gl_texts',
		'default' 		=> $localizeTexts ? __( 'Sign in', 'easy-login-woocommerce' ) : 'Sign in',
	),

	'txt-btn-reg' => array(
		'callback' 		=> 'text',
		'title' 		=> 'Register Button text',
		'id' 			=> 'txt-btn-reg',
		'section_id' 	=> 'gl_texts',
		'default' 		=> $localizeTexts ? __( 'Sign Up', 'easy-login-woocommerce' ) : 'Sign Up',
	),

	'txt-btn-respw' => array(
		'callback' 		=> 'text',
		'title' 		=> 'Reset password Button text',
		'id' 			=> 'txt-btn-respw',
		'section_id' 	=> 'gl_texts',
		'default' 		=> $localizeTexts ? __( 'Email Reset Link', 'easy-login-woocommerce' ) : 'Email Reset Link',
	),

	'txt-btn-single' => array(
		'callback' 		=> 'text',
		'title' 		=> 'Single Field Form Button text',
		'id' 			=> 'txt-btn-single',
		'section_id' 	=> 'gl_texts',
		'default' 		=> 'Continue',
	),

	'txt-btn-profile' => array(
		'callback' 		=> 'text',
		'title' 		=> 'Profile update button text',
		'id' 			=> 'txt-btn-profile',
		'section_id' 	=> 'gl_texts',
		'default' 		=> 'Update',
	),

	'txt-profile-update' => array(
		'callback' 		=> 'text',
		'title' 		=> 'Profile update success text',
		'id' 			=> 'txt-profile-update',
		'section_id' 	=> 'gl_texts',
		'default' 		=> 'Your profile updated successfully.',
	),

	'txt-login-form' => array(
		'callback' 		=> 'wp_editor',
		'title' 		=> 'Login Form Text',
		'id' 			=> 'txt-login-form',
		'section_id' 	=> 'gl_texts',
		'args' 			=> array(
			'editor_settings' => array(
				'editor_height' => 400,
			)
		),
		'default' 		=> xoo_el_admin_settings()->default_login_form_text(),
	),

	'txt-register-form' => array(
		'callback' 		=> 'wp_editor',
		'title' 		=> 'Register Form Text',
		'id' 			=> 'txt-register-form',
		'section_id' 	=> 'gl_texts',
		'args' 			=> array(
			'editor_settings' => array(
				'editor_height' => 400,
			)
		),
		'default' 		=> xoo_el_admin_settings()->default_register_form_text(),
	),

	'txt-single-form' => array(
		'callback' 		=> 'wp_editor',
		'title' 		=> 'Single Field Form Text',
		'id' 			=> 'txt-single-form',
		'section_id' 	=> 'gl_texts',
		'args' 			=> array(
			'editor_settings' => array(
				'editor_height' => 400,
			)
		),
		'default' 		=> xoo_el_admin_settings()->default_single_form_text(),
	),

	'txt-profile-head' => array(
		'callback' 		=> 'wp_editor',
		'title' 		=> 'Update Profile Form Heading',
		'id' 			=> 'txt-profile-head',
		'section_id' 	=> 'gl_texts',
		'args' 			=> array(
			'editor_settings' => array(
				'editor_height' => 400,
			)
		),
		'default' 		=> xoo_el_admin_settings()->default_profile_head_text(),
	),

);

$settings = array_merge( $settings, $texts );

if( defined( 'XOO_ELPOF_VERSION' ) ){

	$profile_keys = array( 'txt-profile-head', 'm-editaccount-sc', 'm-editaccount-replace', 'txt-profile-update', 'txt-btn-profile' );

	foreach ( $profile_keys as $profile_key ) {
		unset( $settings[ $profile_key ] );
	}
}

return apply_filters( 'xoo_el_admin_settings', $settings, 'general' );