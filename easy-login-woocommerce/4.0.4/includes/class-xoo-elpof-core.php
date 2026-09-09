<?php

use XooEL\Framework\Xoo_Exception;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


if( !class_exists('Xoo_ELPOF_Core') ){

	class Xoo_ELPOF_Core{

		private static $_instance = null;

		public $aff;

		public $settings = array();

		public $profileFields;

		public $defaultRegisterFields;

		public $avatar_field_id;

		public static function get_instance(){

			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}


		public function __construct(){

			$this->defaultRegisterFields = array(
				'xoo_el_reg_username' 	=> 'user_login',
				'xoo_el_reg_email' 		=> 'user_email',
				'xoo_el_reg_pass' 		=> 'user_pass',
				'xoo_el_reg_fname' 		=> 'first_name',
				'xoo_el_reg_lname' 		=> 'last_name',
			);

			$this->settings = (array) get_option( 'xoo-elpof-options' );

			$this->hooks();
		}



		public function hooks(){

			add_shortcode( 'xoo_el_profile', array( $this, 'profile_shortcode' ) );
			add_action( 'wp_ajax_xoo_elpof_update_profile', array( $this, 'ajax_update_profile' ) );
			add_filter( 'wc_get_template', array( $this, 'override_woocommerce_myaccount_form' ), 99999, 5 );
			add_filter( 'xoo_el_register_fields', array( $this, 'modify_register_fields' ) );
			add_filter( 'get_avatar_url', array( $this, 'profile_field_set_avatar_url' ), 999, 3 );
		}


		public function profile_field_set_avatar_url( $url, $id_or_email, $args ){

			if( !xoo_el_helper()->is_customfields_active() || ( isset( $this->avatar_field_id ) && !$this->avatar_field_id ) ) return $url;

			$user = false;

			if ( is_numeric($id_or_email) ) {
				$user = get_user_by('id', $id_or_email);
			} elseif (is_object($id_or_email) && !empty($id_or_email->user_id)) {
				$user = get_user_by('id', $id_or_email->user_id);
			} elseif (is_string($id_or_email)) {
				$user = get_user_by('email', $id_or_email);
			}

			if ($user) {

				if( !isset( $this->avatar_field_id ) ){

					$this->avatar_field_id = null;

					$fields = $this->get_profile_fields();

					foreach ( $fields as $field_id => $field_data ) {
						if( isset( $field_data['settings']['avatar_merge'] ) && $field_data['settings']['avatar_merge'] === 'yes' ){
							$this->avatar_field_id = $field_id;
							break;
						}
					}
				}


				if( $this->avatar_field_id ){

					$custom_avatar = get_user_meta( $user->ID, $this->avatar_field_id, true );

					if( isset( $custom_avatar[0] ) && $custom_avatar[0] ){
						$url = wp_get_attachment_image_url($custom_avatar[0]);
					}

				}

			}

			return $url;

		}


		public function modify_register_fields( $fields ){

			foreach ( $fields as $field_id => $field_data ) {
				
				if( isset( $field_data['settings']['register_enable'] ) && $field_data['settings']['register_enable'] !== 'yes' ){
					unset( $fields[ $field_id ] );
					continue;
				}

				if( isset( $field_data['settings']['label_display'] ) && $field_data['settings']['label_display'] !== 'yes' ){
					$fields[ $field_id ][ 'settings' ]['label'] = '';
				}

			}

		

			return $fields;
		}




		public function profile_shortcode($atts){

			if( !is_user_logged_in() ) return;

			$atts = shortcode_atts( array(

			), $atts, 'xoo_el_profile' );

			return xoo_el_helper()->get_template( 'xoo-el-profile.php', $atts, XOO_EL_PATH.'/templates/', true );

		}

		public function get_profile_fields(){

			if( !isset( $this->profileFields ) ){

				$fields = xoo_el()->aff->fields->get_fields_data();

				foreach ( $fields as $field_id => $field_data ) {

					$settings = $field_data['settings'];

					$profileDisplay = isset( $settings['profile_display'] ) ? $settings['profile_display'] : null;

					if( isset( $settings['elType'] ) || $profileDisplay === 'disable' || $settings['active'] !== 'yes' ) continue; //if the field is not register type or is not allowed to be shown or is not active, skip

					$fieldArgs 	= xoo_el()->aff->fields->get_field_html_args( $field_id );
					$value 		= $this->get_field_value( $field_id );

					if( $profileDisplay === 'only_show' || ($field_id === 'xoo_el_reg_userrole' && $value === 'administrator' ) ){
						$fieldArgs['cont_class'][] = 'xoo-elpof-disable';
						$fieldArgs['options']['administrator'] = __( 'Administrator', 'elw-addon-profile' );
					}

					$fieldArgs['value'] = $value;

					if( $field_data['input_type'] === 'password' ){
						$fieldArgs['value'] = '';
						$fieldArgs['validation'] = false;
					}

					if( isset( $settings['label_display'] ) && $settings['label_display'] === 'no' ){
						$fieldArgs['label'] = '';
					}

					$field_data['profile_args'] = $fieldArgs; 

					$this->profileFields[ $field_id ] = $field_data;

				}
			}

			return apply_filters( 'xoo_elpf_profile_fields', $this->profileFields );

		}

		public function profile_fields_html(){
			foreach ( $this->get_profile_fields() as $field_id => $field_data ) {
				xoo_el()->aff->fields->get_field_html( $field_id, $field_data['profile_args'] );
			}
		}


		public function get_user_role(){
			$user = wp_get_current_user();
			return reset($user->roles);
		}


		public function get_field_value( $field_id ){

			$fields = $this->defaultRegisterFields;

			$user = wp_get_current_user();

			if( isset( $fields[ $field_id ] ) ){

				if( is_callable( $fields[ $field_id ] ) ){
					$value = $fields[ $field_id ]();
				}
				else{
					$value = $user->{$fields[ $field_id ]};
				}
				
			}
			else if( $field_id === 'xoo_el_reg_userrole' ) {
				$value = $this->get_user_role();
			}
			else{
				$value = get_user_meta( $user->ID, $field_id, true );
			}

			return $value;

		}

		public function ajax_update_profile(){

			if( !isset( $_POST['xoo_elpof_nonce_field'] ) || !wp_verify_nonce( $_POST['xoo_elpof_nonce_field'], 'xoo_elpof_profile_update' ) ) {
				wp_die('Nonce verification failed!');
			}

			try {

				$user = wp_get_current_user();

				if( !$user ) return;

				$registrationFields = xoo_el()->aff->fields->get_fields_data();
				$profileFields 		= $this->get_profile_fields();

				$submittedData 		= $_POST;

				$fileFields 		= array();

				$refreshPage 		= false;

				foreach ( $profileFields as $field_id => $field_data ) {

					//Remove fields which are not allowed to edit
					if( isset( $field_data['settings']['profile_display'] ) && $field_data['settings']['profile_display'] !== 'show_edit' ){
						unset( $profileFields[ $field_id ] );
					}
					else{
						if( $field_data['input_type'] === 'file' ){
							$fileFields[] = $field_id;
						}
					}
				}



				$doNotValidateIDs 	 	= array_keys( array_diff_key( $registrationFields , $profileFields ) );

				$passwordField 			= isset( $profileFields['xoo_el_reg_pass'] ) ? $_POST['xoo_el_reg_pass'] : false;
				$confirmPasswordField 	= isset( $profileFields['xoo_el_reg_pass_again'] ) ? $_POST['xoo_el_reg_pass_again'] : false;
				
				//If password field exist
				if( $passwordField !== false ){

					//If password field was updated
					if( $passwordField || $confirmPasswordField ){

						//If confirm password field exists
						if( $confirmPasswordField !== false && $passwordField !== $confirmPasswordField ){
							throw new Xoo_Exception( __( "Passwords don't match", 'elw-addon-profile' ) );
						}

					}
					else{
						//Passwords remain same
						$doNotValidateIDs[] = 'xoo_el_reg_pass';
						$doNotValidateIDs[] = 'xoo_el_reg_pass_again';
					}

					

				}

				//Passing already uploaded files to the submitted data as input file does not have a value attribute
				foreach ( $fileFields as $field_id ) {
					$userFileFieldValue = get_user_meta( $user->ID, $field_id, true );
					if( !is_array( $userFileFieldValue ) ){
						$userFileFieldValue = empty( $userFileFieldValue ) ? array() : explode(',', $userFileFieldValue);
					}
					$submittedData[ $field_id.'_attachments' ] = $userFileFieldValue;
				}

				$fieldValues = xoo_el()->aff->fields->validate_submitted_field_values( $submittedData, $doNotValidateIDs );

				if( is_wp_error( $fieldValues ) ){
					$message = '';
					if( count( $fieldValues->get_error_messages() ) > 1 ){
						foreach ( $fieldValues->get_error_messages() as $error_message ) {
							$message .= '<p>'.$error_message.'</p>';
						}
					}
					else{
						$message = $fieldValues;
					}

					throw new Xoo_Exception( $message );
				}

				$fieldValues 		= apply_filters( 'xoo_elpof_profile_update_field_values', $fieldValues, $user ); //allow other plugins to filter field values

				$validation_error 	= apply_filters( 'xoo_elpof_profile_update_errors', new \WP_Error(), $fieldValues, $profileFields );

				if ( $validation_error->get_error_code() ) {
					throw new Xoo_Exception( $validation_error );
				}

				if( isset( $fieldValues['xoo_el_reg_pass'] ) && isset( $fieldValues['xoo_el_reg_pass_again'] ) && $fieldValues['xoo_el_reg_pass'] !== $fieldValues['xoo_el_reg_pass_again'] ){
					throw new Xoo_Exception( __("Passwords don't match","elw-addon-profile") );
				}

				do_action( 'xoo_elpof_before_profile_update', $user, $fieldValues );


				//Updating core user data
				$updateUserData = array();
				foreach ( $this->defaultRegisterFields as $field_id => $user_key ) {
					if( isset( $fieldValues[ $field_id ] ) ){
						$updateUserData[$user_key] = sanitize_text_field( $fieldValues[ $field_id ] );
					}
				}

				//Updating user role
				if( isset( $profileFields['xoo_el_reg_userrole'] ) ){

					$userRoleSelected = sanitize_text_field( $fieldValues['xoo_el_reg_userrole'] );

					if( $userRoleSelected !== $this->get_user_role() ){ // user role changed

						if( !isset( $profileFields[ 'xoo_el_reg_userrole' ]['settings']['select_list'][ $userRoleSelected ] ) ){
							throw new Xoo_Exception( 'Error selecting user role, please try again' );
						}

						$updateUserData['role'] = $userRoleSelected;

						update_user_meta( $user->ID, 'userRoleSelected', $userRoleSelected );

						unset($profileFields['xoo_el_reg_userrole']);

						$refreshPage = true;

					}
				}

				$fieldsHavingFiles = array();

				foreach ( $fileFields as $field_id ) {
					if( isset( $fieldValues[ $field_id ] ) ){
						$fieldsHavingFiles[ $field_id ] = $fieldValues[ $field_id ];
					}
				}


				if( !empty( $fieldsHavingFiles ) ){
					$uploadedAttachmentIDS = xoo_el_helper()->upload_files_as_attachment( $fieldsHavingFiles );
					if( is_wp_error( $uploadedAttachmentIDS ) ){
						throw new Xoo_Exception( $uploadedAttachmentIDS );
					}
				}


				if( !empty( $updateUserData ) ){
					$updateUserData['ID'] = $user->ID;
					$updated = wp_update_user($updateUserData);
					if( is_wp_error( $updated ) ){
						throw new Xoo_Exception( $updated );
					}
				}

				//Updating other extra fields
				$otherExtraFields = array_diff_key( array_diff_key( $profileFields, $fieldsHavingFiles), $this->defaultRegisterFields );

				foreach ( $otherExtraFields as $field_id => $field_data ) {
					if( isset( $fieldValues[$field_id] ) ){
						update_user_meta( $user->ID, $field_id, $fieldValues[$field_id] );
					}
				}

				//Saving field with files
				foreach ( $fileFields as $field_id ) {

					$fieldSavedAttachments 			= isset( $submittedData[ $field_id.'_attachments' ] ) ? $submittedData[ $field_id.'_attachments' ] : array();
					$fieldModifedSavedAttachments 	= isset( $fieldValues[ $field_id.'_attachments' ] ) ? array_map( 'sanitize_text_field', $fieldValues[ $field_id.'_attachments' ] ) : array();
					$fieldUploadedAttachmentIDS 	= isset( $uploadedAttachmentIDS[ $field_id ] ) ? array_map( 'sanitize_text_field', $uploadedAttachmentIDS[ $field_id ] ) : array();

					update_user_meta( $user->ID, $field_id, array_merge( $fieldUploadedAttachmentIDS, $fieldModifedSavedAttachments ) );

					$fieldDeletedAttachments 		= array_diff( $fieldSavedAttachments , $fieldModifedSavedAttachments );

					foreach ( $fieldDeletedAttachments as $deleteAttachmentID ) {
						wp_delete_attachment( $deleteAttachmentID, true );
					}

				}


				//refresh if files exist
				if( !empty( $uploadedAttachmentIDS ) ){
					$refreshPage = true;
				}


				do_action( 'xoo_elpof_update_success', $user );

				wp_send_json( array(
					'error' 		=> 0,
					'notice' 		=> xoo_el_add_notice('success', xoo_el_helper()->get_general_option('txt-profile-update')),
					'refreshPage' 	=> $refreshPage 
				) );

				exit;

				
			} catch (Xoo_Exception $e) {

				$message = apply_filters( 'xoo_elpof_update_errors', $e->getMessage(), $e );

				wp_send_json( array(
					'error' 		=> 1,
					'error_code' 	=> $e->getWpErrorCode(),
					'notice' 		=> xoo_el_add_notice('error', $message, $e->getWpErrorCode()),
				) );

				exit;
			}

			

		}


		public function override_woocommerce_myaccount_form($located, $template_name, $args, $template_path, $default_path ){

			if( $template_name === 'myaccount/form-edit-account.php' && xoo_el_helper()->get_general_option('m-editaccount-replace') === "yes" ){
				$located = xoo_el_helper()->locate_template( 'xoo-el-wc-edit-account-form.php', XOO_EL_PATH.'/templates/' );
			}

			return $located;
		}



	}

	function xoo_elpof_core(){
		return Xoo_ELPOF_Core::get_instance();
	}
	xoo_elpof_core();

}