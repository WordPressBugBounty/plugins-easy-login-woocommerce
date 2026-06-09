<?php

$settings = array(

	/* Form Style */
	array(
		'callback' 		=> 'upload',
		'title' 		=> 'Header Image',
		'id' 			=> 'sy-head-img',
		'section_id' 	=> 'sy_form',
		'default' 		=> '',
	),


	array(
		'callback' 		=> 'checkbox',
		'title' 		=> 'New Button Layout',
		'id' 			=> 'sy-btn-newlayout',
		'section_id' 	=> 'sy_form',
		'args' 			=> array(
			'toggleSettings' => array(
				'xoo-el-sy-options[sy-btns-theme]' 		=> array( 'yes' ),
				'xoo-el-sy-options[sy-btn-bgcolor]' 	=> array( 'yes' ),
				'xoo-el-sy-options[sy-btn-txtcolor]' 	=> array( 'yes' ),
				'xoo-el-sy-options[sy-btn-border]' 		=> array( 'yes' ),
				'xoo-el-sy-options[sy-btn-height]' 		=> array( 'yes' ),
				'xoo-el-sy-options[sy-btn-main]' 		=> array('unchecked')
			)
		),
		'default' => 'yes'
		
	),


	array(
		'callback' 		=> 'select',
		'title' 		=> 'Button Design',
		'id' 			=> 'sy-btns-theme',
		'section_id' 	=> 'sy_form',
		'args' 			=> array(
			'options' 	=> array(
				'theme'		=> 'Use theme button design & colors',
				'custom' 	=> 'Custom',
			),
		),
		'default' 	=> 'custom',
		'desc' 		=> 'Below color options will be ineffective if set to theme design.'
	),

	array(
		'callback' 		=> 'button',
		'title' 		=> 'Button',
		'id' 			=> 'sy-btn-main',
		'section_id' 	=> 'sy_form',
		'default' 		=> array(
			'width'         => 100,
			'width_unit' 	=> '%',
			'height'        => 45,
			'bgColor'       => '#27374d',
			'txtColor'      => '#dde6ed',
			'border' => array(
				'color'     => '#dde6ed',
			),

			'hover' => array(
				'bgColor'       => '#dde6ed',
				'txtColor'      => '#27374d',
				'border' => array(
					'color'     => '#27374d',
				),
			),
		)
		
	),


	array(
		'callback' 		=> 'color',
		'title' 		=> 'Button Background Color',
		'id' 			=> 'sy-btn-bgcolor',
		'section_id' 	=> 'sy_form',
		'default' 		=> '#27374d',
	),


	array(
		'callback' 		=> 'color',
		'title' 		=> 'Button Text Color',
		'id' 			=> 'sy-btn-txtcolor',
		'section_id' 	=> 'sy_form',
		'default' 		=> '#ffffff',
	),

	array(
		'callback' 		=> 'text',
		'title' 		=> 'Button Border',
		'id' 			=> 'sy-btn-border',
		'section_id' 	=> 'sy_form',
		'default' 		=> '2px solid #000000',
		'desc' 			=> 'Default: 2px solid #000000'
	),


	array(
		'callback' 		=> 'number',
		'title' 		=> 'Button Height',
		'id' 			=> 'sy-btn-height',
		'section_id' 	=> 'sy_form',
		'default' 		=> '40',
		'desc' 			=> 'size in px'
	),


	

	/* Main Style */
	array(
		'callback' 		=> 'asset_selector',
		'title' 		=> 'Popup Style',
		'id' 			=> 'sy-popup-style',
		'section_id' 	=> 'sy_popup',
		'default' 		=> 'popup',
		'args' 			=> array(
			'options' => array(
				'popup' 	=> array(
					'title' => 'Popup',
					'asset' => XOO_EL_URL.'/admin/assets/images/popup.jpg',
				),
				'slider' 	=> array(
					'title' => 'Slider',
					'asset' => XOO_EL_URL.'/admin/assets/images/slider.jpg',
				)
			),
			'custom_attributes' => array(
				'data-multiple' => 'no',
				'data-required' => 'yes'
			)
		),

	),

	array(
		'callback' 		=> 'select',
		'title' 		=> 'Popup Position',
		'id' 			=> 'sy-popup-pos',
		'section_id' 	=> 'sy_popup',
		'args'			=> array(
			'options' => array(
				'top' 		=> 'Top',
				'middle' 	=> 'Middle'
			)
		),
		'default' 		=> 'middle'
	),


	array(
		'callback' 		=> 'number',
		'title' 		=> 'Popup Width',
		'id' 			=> 'sy-popup-width',
		'section_id' 	=> 'sy_popup',
		'default' 		=> 850,
		'desc' 			=> 'size in px'
	),

	array(
		'callback' 		=> 'select',
		'title' 		=> 'Popup Height',
		'id' 			=> 'sy-popup-height-type',
		'section_id' 	=> 'sy_popup',
		'args'			=> array(
			'options' => array(
				'custom' 	=> 'Custom',
				'auto' 		=> 'Auto Adjust'
			)
		),
		'default' 		=> 'custom',
	),

	array(
		'callback' 		=> 'number',
		'title' 		=> 'Custom Popup Height',
		'id' 			=> 'sy-popup-height',
		'section_id' 	=> 'sy_popup',
		'default' 		=> 650,
		'desc' 			=> 'size in px'
	),


	array(
		'callback' 		=> 'text',
		'title' 		=> 'Popup Padding',
		'id' 			=> 'sy-popup-padding',
		'section_id' 	=> 'sy_popup',
		'default' 		=> '40px 30px',
		'desc' 			=> '↨ ⟷ ( Default: 30px 30px )'
	),


	array(
		'callback' 		=> 'color',
		'title' 		=> 'Background Color',
		'id' 			=> 'sy-popup-bgcolor',
		'section_id' 	=> 'sy_popup',
		'default' 		=> '#ffffff',
	),


	array(
		'callback' 		=> 'color',
		'title' 		=> 'Text Color',
		'id' 			=> 'sy-popup-txtcolor',
		'section_id' 	=> 'sy_popup',
		'default' 		=> '#000000',
	),

	array(
		'callback' 		=> 'upload',
		'title' 		=> 'Sidebar Image',
		'id' 			=> 'sy-sidebar-img',
		'section_id' 	=> 'sy_popup',
		'default' 		=> XOO_EL_URL.'/assets/images/login.jpg',
	),


	array(
		'callback' 		=> 'select',
		'title' 		=> 'Sidebar Position',
		'id' 			=> 'sy-sidebar-pos',
		'section_id' 	=> 'sy_popup',
		'args'			=> array(
			'options' => array(
				'left' 		=> 'Left',
				'right' 	=> 'Right'
			)
		),
		'default' 		=> 'left'
	),


	array(
		'callback' 		=> 'number',
		'title' 		=> 'Sidebar width',
		'id' 			=> 'sy-sidebar-width',
		'section_id' 	=> 'sy_popup',
		'default' 		=> 43,
		'desc' 			=> 'Width in percentage'
	),


	array(
		'callback' 		=> 'color',
		'title' 		=> 'Overlay Color',
		'id' 			=> 'sy-overlay-color',
		'section_id' 	=> 'sy_popup',
		'default' 		=> '#000000',
	),


	array(
		'callback' 		=> 'text',
		'title' 		=> 'Overlay opacity',
		'id' 			=> 'sy-overlay-opac',
		'section_id' 	=> 'sy_popup',
		'default' 		=> 0.7,
		'desc' 			=> 'Put value <= 1 in points'
	),


	/* Header Tab */
		array(
		'callback' 		=> 'color',
		'title' 		=> 'Tab Background Color',
		'id' 			=> 'sy-tab-bgcolor',
		'section_id' 	=> 'sy_tab',
		'default' 		=> '#dde6ed',
	),

	array(
		'callback' 		=> 'color',
		'title' 		=> 'Tab Text Color',
		'id' 			=> 'sy-tab-txtcolor',
		'section_id' 	=> 'sy_tab',
		'default' 		=> '#27374d',
	),

		array(
		'callback' 		=> 'color',
		'title' 		=> 'Active Tab Background Color',
		'id' 			=> 'sy-taba-bgcolor',
		'section_id' 	=> 'sy_tab',
		'default' 		=> '#27374d',
	),


	array(
		'callback' 		=> 'color',
		'title' 		=> 'Active Tab Text Color',
		'id' 			=> 'sy-taba-txtcolor',
		'section_id' 	=> 'sy_tab',
		'default' 		=> '#dde6ed',
	),

	array(
		'callback' 		=> 'number',
		'title' 		=> 'Tab Text Font Size',
		'id' 			=> 'sy-tab-fsize',
		'section_id' 	=> 'sy_tab',
		'default' 		=> '16',
		'desc' 			=> 'size in px'
	),

	array(
		'callback' 		=> 'text',
		'title' 		=> 'Tab Padding',
		'id' 			=> 'sy-tab-padding',
		'section_id' 	=> 'sy_tab',
		'default' 		=> '12px 20px',
		'desc' 			=> '↨ ⟷ ( Default: 12px 20px )'
	),

);

return apply_filters( 'xoo_el_admin_settings', $settings, 'style' );