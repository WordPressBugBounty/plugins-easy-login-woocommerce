<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$tabs = array(

	'shortcodes' => array(
		'title'			=> 'Shortcodes',
		'id' 			=> 'shortcodes',
		'option_key' 	=> '',
		'args' 			=> array(
			'priority' => 5
		),
		'icon' 			=> 'xoo-icon-code',
	),


	'general' => array(
		'title'			=> 'General',
		'id' 			=> 'general',
		'option_key' 	=> 'xoo-el-gl-options',
		'args' 			=> array(
			'priority' => 10
		),
		'icon' 			=> 'xoo-icon-setting',

	),

	'style' => array(
		'title'			=> 'Style',
		'id' 			=> 'style',
		'option_key' 	=> 'xoo-el-sy-options',
		'args' 			=> array(
			'priority' => 20
		),
		'icon' 			=> 'xoo-icon-brush',
	),

	'addon' => array(
		'title'			=> 'Add-ons',
		'id' 			=> 'addon',
		'option_key' 	=> '',
		'args' 			=> array(
			'priority' => 50
		),
		'icon' 			=> 'xoo-icon-crown',
	),


	'advanced' => array(
		'title'			=> 'Advanced',
		'id' 			=> 'advanced',
		'option_key' 	=> 'xoo-el-av-options',
		'args' 			=> array(
			'priority' => 60
		),
		'icon' 			=> 'xoo-icon-tune',
	),
);

return apply_filters( 'xoo_el_admin_settings_tabs', $tabs );