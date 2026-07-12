<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<?php

$sections = array(

	/* General TAB Sections */
	array(
		'title' => 'Main',
		'id' 	=> 'gl_main',
		'tab' 	=> 'general',
		'icon' 	=> 'xoo-icon-home'
	),


	array(
		'title' => 'Popup',
		'id' 	=> 'gl_popup',
		'tab' 	=> 'general',
		'icon' 	=> 'xoo-icon-popup'
	),


	array(
		'title' => 'Auto Open Popup',
		'id' 	=> 'gl_ao',
		'tab' 	=> 'general',
		'icon' 	=> 'xoo-icon-auto'
	),


	array(
		'title' => 'WooCommerce Settings',
		'id' 	=> 'gl_wc',
		'tab' 	=> 'general',
		'icon' 	=> 'xoo-icon-woo'
	),



	array(
		'title' => 'Redirects',
		'id' 	=> 'gl_red',
		'tab' 	=> 'general',
		'desc' 	=> 'There are other ways to handle redirections, please check info tab.',
		'icon' 	=> 'xoo-icon-redirect'
	),

	array(
		'title' => 'Texts',
		'id' 	=> 'gl_texts',
		'tab' 	=> 'general',
		'desc' 	=> 'Leave text empty to remove element',
		'icon' 	=> 'xoo-icon-page'
	),


	/* Style TAB Sections */
	array(
		'title' => 'Button Themes',
		'id' 	=> 'sy_button_theme_creator',
		'tab' 	=> 'style',
		'icon' 	=> 'xoo-icon-tune',
		'desc' 	=> 'Create and manage reusable button styles for side cart.'
	),

	array(
		'title' => 'Pop-up',
		'id' 	=> 'sy_popup',
		'tab' 	=> 'style',
		'icon' 	=> 'xoo-icon-popup'
	),


	array(
		'title' => 'Form',
		'id' 	=> 'sy_form',
		'tab' 	=> 'style',
		'icon' 	=> 'xoo-icon-page'
	),


	array(
		'title' => 'Header Tab Settings',
		'id' 	=> 'sy_tab',
		'tab' 	=> 'style',
		'icon' 	=> 'xoo-icon-header'
	),




	/* Custom CSS TAB Sections */
	array(
		'title' => 'Main',
		'id' 	=> 'av_main',
		'tab' 	=> 'advanced',
		'icon' 	=> 'xoo-icon-home'
	),
);

return apply_filters( 'xoo_el_admin_settings_sections', $sections );