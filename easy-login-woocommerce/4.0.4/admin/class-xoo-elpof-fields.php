<?php

use XooEL\Framework\Xoo_Exception;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


if( !class_exists('Xoo_ELPOF_Fields') ){

	class Xoo_ELPOF_Fields{

		protected static $_instance = null;

		public $defaultRegisterFields;

		public static function get_instance(){
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		public function __construct(){
			$this->hooks();	
		}

		public function hooks(){
			add_filter( 'xoo_aff_easy-login-woocommerce_default_field_settings', array( $this, 'modify_default_field_settings' ) );
			add_filter( 'xoo_aff_easy-login-woocommerce_field_setting_options', array( $this, 'add_custom_settings_option' ) );
			add_action( 'xoo_aff_easy-login-woocommerce_add_predefined_fields', array( $this, 'handle_fields_before_adding' ), 99 );
		}

		public function add_custom_settings_option( $setting_options ){

			$setting_options['profile_display'] = array(
				'type' 		=> 'select',
				'id'		=> 'profile_display',
				'section' 	=> 'basic',	
				'title' 	=> 'Profile Visibility',
				'width'		=> 'half',
				'value'		=> 'show_edit',
				'options' 	=> array(
					'show_edit' => 'Show and allow edit',
					'only_show' => 'Only show and disable edit',
					'disable' 	=> 'Do not show (Disable)'
				),
				'info' 		=> 'Field behavior on profile page.',
				'priority' 	=> 15
			);


			$setting_options['register_enable'] = array(
				'type' 		=> 'checkbox',
				'id'		=> 'register_enable',
				'section' 	=> 'basic',	
				'title' 	=> 'Show on registration form',
				'width'		=> 'half',
				'value'		=> 'yes',
				'info' 		=> 'Display this field on the registration form.',
				'priority' 	=> 16
			);

			if( xoo_el_helper()->is_customfields_active() ){
				$setting_options['avatar_merge'] = array(
					'type' 		=> 'checkbox',
					'id'		=> 'avatar_merge',
					'section' 	=> 'basic',	
					'title' 	=> 'Replace Wordpress Avatar',
					'width'		=> 'half',
					'value'		=> 'no',
					'priority' 	=> 17
				);
			}
			
			$setting_options['label_display'] = array(
				'type' 		=> 'select',
				'id'		=> 'label_display',
				'section' 	=> 'display',	
				'title' 	=> 'Show Label',
				'width'		=> 'half',
				'value'		=> 'on_profile',
				'options' 	=> array(
					'no' 			=> 'No',
					'yes' 			=> 'Yes, everywhere',
					'on_profile' 	=> 'Only on profile'
				),
				'priority' 	=> 25
			);
			return $setting_options;
		}


		//Add option to show field on woocommerce my account page
		public function modify_default_field_settings( $settings ){

			foreach ( $settings as $setting_id => $setting_options ) {
				$settings[ $setting_id ][] = 'profile_display';
				$settings[ $setting_id ][] = 'register_enable';
				$settings[ $setting_id ][] = 'label_display';
			}

			if( xoo_el_helper()->is_customfields_active() ){
				$settings['xoo_aff_profile_photo'][] = 'avatar_merge';
			}


			return $settings;

		}

		public function handle_fields_before_adding( $fieldsObj ){

			$this->predefined_field_profilepicture();

			//adding profile display to predefined fields with the value
			$fields = array(
				'xoo_el_reg_username' 	=> array(
					'profile_display' 	=> 'only_show',
					'register_enable' => 'yes',
				),
				'xoo_el_reg_email' 		=> array(
					'profile_display' => 'only_show',
					'register_enable' => 'yes',
				),
				'xoo_el_reg_pass' 		=> array(
					'profile_display' => 'show_edit',
					'register_enable' => 'yes',
				),
				'xoo_el_reg_pass_again' => array(
					'profile_display' => 'show_edit',
					'register_enable' => 'yes',
				),
				'xoo_el_reg_fname' 		=> array(
					'profile_display' => 'show_edit',
					'register_enable' => 'yes',
				),
				'xoo_el_reg_lname' 		=> array(
					'profile_display' => 'show_edit',
					'register_enable' => 'yes',
				),
				'xoo_el_reg_terms' 		=> array(
					'profile_display' => 'disable',
					'register_enable' => 'yes',
				),
				'xoo_el_reg_userrole' 	=> array(
					'profile_display' => 'disable',
					'register_enable' => 'yes',
				),
			);

			foreach ( $fields as $field_id => $setting_data ) {

				$setting_data['label_display'] = 'on_profile';
				
				foreach ($setting_data as $key => $value) {
					$setting_values[ $key ] = array( 'value' => $value );
				}


				$fieldsObj->create_field_settings( $field_id, $setting_values );
			}

			unset( $fieldsObj->settings['xoo_el_reg_username']['profile_display']['options']['show_edit'] );

			if( function_exists('xoo_ml') ){
				unset( $fieldsObj->settings['xoo-ml-reg-phone-cc']['register_enable'], $fieldsObj->settings['xoo-ml-reg-phone']['register_enable'] );
			}

		}

		public function predefined_field_profilepicture(){

			if( !xoo_el_helper()->is_customfields_active() ) return;

			$fields = xoo_el()->aff->fields;

			$field_type_id = $field_id = 'xoo_el_pof_picture';

			$fields->add_type(
				$field_type_id,
				'file',
				'Profile Picture',
				array(
					'is_selectable' => 'no',
					'can_delete'	=> 'no',
					'icon' 			=> 'fas fa-user-circle'
				)
			);

			$setting_options = $fields->settings['xoo_aff_profile_photo'];

			$my_settings = array(
				'unique_id' => array(
					'disabled' => 'disabled'
				),
				'avatar_merge' 	=> array(
					'value' => 'yes'
				),
				'active' 	=> array(
					'value' => 'no'
				),
			);
			
			$setting_options = array_merge(
				$setting_options,
				$my_settings
			);

			$fields->create_field_settings(
				$field_type_id,
				$setting_options
			);

			$fields->add_field(
				$field_id,
				$field_type_id,
				array(
					'unique_id' => $field_id,
				),
				array(
					'group' 	=> 'register',
					'priority' 	=> 5,
				)		
			);
		}

	}

	function xoo_elpof_fields(){
		return Xoo_ELPOF_Fields::get_instance();
	}
	xoo_elpof_fields();

}