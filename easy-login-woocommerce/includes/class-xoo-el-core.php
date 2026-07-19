<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Xoo_El_Core{

	private static $_instance = null;

	public $aff, $db_version;
	public $updatedFrom = false;

	public static function get_instance(){

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}


	public function __construct(){

		$this->db_version = get_option('xoo-el-version');

		if( defined( 'XOO_ML_VERSION' ) && version_compare( XOO_ML_VERSION , '1.3', '<=' ) ){
			add_action( 'admin_notices', array( $this, 'otp_login_update_notice' ) );
			return;
		}
		
		$this->includes();
		$this->hooks();
	}



	public function includes(){

		require_once XOO_EL_PATH.'/includes/class-xoo-el-helper.php';

		//Field framework
		require_once XOO_EL_PATH.'/xoo-form-fields-fw/xoo-aff.php';

		$this->aff = \XooEL\Aff\xoo_aff_fire( 'easy-login-woocommerce', 'xoo-el-fields', xoo_el_helper() ); // start framework

		require_once XOO_EL_PATH.'includes/class-xoo-el-func.php';
		
		require_once XOO_EL_PATH.'includes/xoo-el-functions.php';

		require_once XOO_EL_PATH.'includes/class-xoo-el-fields.php';

		require_once XOO_EL_PATH.'includes/verification/class-xoo-el-code-forms.php';

		if( xoo_el_helper()->is_request('frontend')){

			require_once XOO_EL_PATH.'includes/class-xoo-el-frontend.php';
			require_once XOO_EL_PATH.'includes/class-xoo-el-form-handler.php';
			

		}
		

		if( xoo_el_helper()->is_request('admin') || version_compare( $this->db_version, XOO_EL_VERSION, '<' ) ){
			require_once XOO_EL_PATH.'admin/class-xoo-el-aff-fields.php';
			require_once XOO_EL_PATH.'admin/class-xoo-el-admin-settings.php';
		}

	
		if ( xoo_el_helper()->is_request('admin')) {
			require_once XOO_EL_PATH.'admin/class-xoo-el-menu-settings.php';
		}


		//Let profile builder add-on handle it.
		if( !defined( 'XOO_ELPOF_VERSION' ) && !(isset( $_GET['action'], $_GET['plugin'] ) && $_GET['action'] === 'activate'  && $_GET['plugin'] === 'easy-login-addon-profile/xoo-elpof-main.php' ) ){

			if( xoo_el_helper()->is_request('admin') || version_compare( $this->db_version, XOO_EL_VERSION, '<' ) ){
				require_once XOO_EL_PATH.'admin/class-xoo-elpof-fields.php';
			}


			if( xoo_el_helper()->is_request('admin') ){
				require_once XOO_EL_PATH.'admin/class-xoo-el-user-profile.php';
			}

			require_once XOO_EL_PATH.'/includes/class-xoo-elpof-core.php';
		}
	}


	public function hooks(){
		add_action( 'init', array( $this, 'on_install' ), 0 );
		add_action( 'admin_notices', array( $this, 'show_outdated_template_notice' ) );
		add_action( 'admin_head', array( $this, 'inline_styling' ) );
		add_filter( 'xoo_aff_enable_autocompadr', array( $this,'enable_autocompadr' ), 10, 2 );
	}



	/**
	* On install
	*/
	public function on_install(){

		$version_option = 'xoo-el-version';
		$db_version 	= get_option( $version_option );

		//If first time install
		if( $db_version === false ){
			add_action( 'admin_notices', array( $this, 'admin_notice_on_install' ) );
		}

		if( $db_version ){

			$glOptions = (array) xoo_el_helper()->get_general_option();
			$syOptions = (array) xoo_el_helper()->get_style_option();
			$avOptions = (array) xoo_el_helper()->get_advanced_option();

			if( version_compare( $db_version, '2.3', '<') ){
				//Map old values to new option
				$oldValues = (array) include XOO_EL_PATH.'/admin/views/oldtonew.php';
				foreach ( $oldValues as $keyData ) {
					$oldKeyValue = (array) get_option( $keyData['oldkey'] );
					$newKeyValue = (array) get_option( $keyData['newkey'] );

					if( $oldKeyValue === false ) continue;
					foreach ( $keyData['values'] as $oldsubkey => $newsubkey ) {
						if( !isset( $oldKeyValue[ $oldsubkey ] ) ) continue;
						$newKeyValue[ $newsubkey ] = $oldKeyValue[ $oldsubkey ];
					}
					update_option( $keyData['newkey'], $newKeyValue );
				}
			}


			if( version_compare( $db_version, '2.6', '<') ){

				$syOptions['sy-head-img'] = '';
				$syOptions['sy-tab-padding'] = '12px 20px';
				$syOptions['sy-tab-fsize'] = '16';
				$syOptions['sy-popup-height-type'] = 'custom';

				$glOptions['m-nav-pattern'] = 'tabs';
				$glOptions['m-form-pattern'] = 'separate';

			}

			if( version_compare( $db_version, '2.7', '<') ){

				$avOptions['m-error-log'] = 'no';
				
			}

			if( version_compare( $db_version, '2.7.4', '<') ){

				if( isset( $glOptions['m-myacc-sc'] ) ){
					$glOptions['m-chkout-sc'] = $glOptions['m-myacc-sc'];
				}
	
			}

			if( version_compare( $db_version, '2.9.2', '<')  ){
				update_option('xoo_tracking_consent_easy-login-woocommerce', 'no' );
			}

			if( version_compare( $db_version, '2.9.3', '<')  ){
				update_option( 'xoo-el-settings-init', 'yes' );
			}

			if( version_compare( $db_version, '2.9.4', '<') ){

				$glOptions['m-myacclpw-sc'] = '';

			}


			if( version_compare( $db_version, '3.0.0', '<') ){
				$glOptions['m-reset-pw'] 			= $glOptions['m-reset-pw'] === "yes" ? 'link' : 'disable';
				$glOptions['m-reset-pw-subject'] 	= 'Reset your password for {site_title}';
				$glOptions['m-reset-pw-email'] 		= xoo_el_admin_settings()->default_reset_email_text();
			}


			if( version_compare( $db_version, '3.2.0', '<') ){
				$glOptions['txt-login-form'] 			= '';
				$glOptions['txt-register-form'] 		= '';
				ob_start();
				?>
				<p style="margin: 0 0 16px 0;">
					<span class="xoo-el-sing-head" style="font-size: 32px;"><?php echo wp_kses_post( $glOptions['txt-sing-head'] ) ?></span><br />
					<span class="xoo-el-sing-subtxt" style="font-size: 16px;"><?php echo wp_kses_post( $glOptions['txt-sing-subtxt'] ) ?></span>
				</p>
				<?php

				$glOptions['txt-single-form'] 			= ob_get_clean();
				$syOptions['sy-btn-newlayout'] 			= 'no'; 
				update_option( 'xoo-el-old-btn-layout', 'yes' );
			}


			if( version_compare( $db_version, '3.2.2', '<')  ){


				//Create theme from older button settings
				if( isset( $syOptions['sy-btn-main'] ) && !empty( $syOptions['sy-btn-main'] ) && ( !isset( $syOptions['sy-btn-newlayout'] ) || $syOptions['sy-btn-newlayout'] === "yes" ) ){

					$button_settings = xoo_el_helper()->get_button_values( $syOptions['sy-btn-main'] );

					$default_theme1 = array_merge(
						$button_settings,
						array(
							'theme_id' => 'theme_default1',
							'title'    => 'Default Theme #1',
						)
					);

					$syOptions['sy-btnthemes'] = array(
						'theme_default1' => $default_theme1,
					);

					$syOptions['sy-btntheme-action'] = 'theme_default1';

					if( function_exists( 'xoo_ml' ) ){
						$syOptions['sy-btntheme-toggle'] = 'theme_default1';
					}
				
					
				}

			}

			if( version_compare( $db_version, '3.2.8', '<') ){

				$glOptions['m-editaccount-replace'] 	= 'no';
				$glOptions['m-editaccount-sc'] 			= '[xoo_el_profile]';
				$glOptions['txt-btn-profile'] 			= 'Update';
				$glOptions['txt-profile-update'] 		= 'Your profile updated successfully.';
				$glOptions['txt-profile-head'] 			= xoo_el_admin_settings()->default_profile_head_text();
				$syOptions['sy-btntheme-profupdate'] 	= 'theme_default1';


				$fields = $this->aff->fields->get_fields_data();

				foreach ( $fields as $field_id => $field_data ) {

					if( !isset( $field_data['group'] ) || $field_data['group'] !== 'register' ) continue;

					$fields[$field_id]['settings']['register_enable'] 	= isset( $fields[$field_id]['settings']['register_disable'] ) && $fields[$field_id]['settings']['register_disable'] === "yes" ? "no" : "yes" ;
					$fields[$field_id]['settings']['label_display'] 	= 'yes';
					
				}

				$this->aff->fields->update_field_option( $fields );

			}

			if( version_compare( $db_version, '3.2.9', '<') ){
				update_option( 'xoo_tracking_consent_easy-login-woocommerce', 'no' );
			}

			if( version_compare( $db_version, '4.0.0', '<') ){
				$syOptions['sy-profile-width'] = 700;
			}


			update_option( 'xoo-el-gl-options', $glOptions );
			update_option( 'xoo-el-sy-options', $syOptions );
			update_option( 'xoo-el-av-options', $avOptions );

		}
		

		if( version_compare( $db_version, XOO_EL_VERSION, '<') ){


			/* Including OTP Login fields file - Fix this later*/
			if( defined('XOO_ML_PATH') ){
				if( file_exists( XOO_ML_PATH.'admin/class-xoo-ml-el-fields.php' ) ){
					require_once XOO_ML_PATH.'admin/class-xoo-ml-el-fields.php';
				}
				else if( file_exists( XOO_ML_PATH.'admin/includes/class-xoo-ml-el-fields.php' ) ){
					require_once XOO_ML_PATH.'admin/includes/class-xoo-ml-el-fields.php';
				}
				
			}

			xoo_el()->aff->fields->set_defaults();
			
			xoo_el_helper()->admin->auto_generate_settings();

			//Update to current version
			update_option( $version_option, XOO_EL_VERSION);

			xoo_el_helper()->update_theme_templates_data(); //get theme template data

			$this->updatedFrom = $db_version;
		}
	}


	public function otp_login_update_notice(){
		?>
		<div class="notice is-dismissible notice-warning" style="padding: 10px; font-weight: 600; font-size: 16px; line-height: 2">This version of login/signup popup is not compatible with the current version of OTP Login plugin. <br>Please update the OTP login plugin.</div>
		<?php
	}


	public function admin_notice_on_install(){
		?>
		<div class="notice notice-success is-dismissible xoo-el-admin-notice">
			<p>Start by adding Login/Registration links to your <a href="<?php echo esc_url( admin_url( 'nav-menus.php?xoo_el_nav=true' ) ); ?>">menu</a>.</p>
			<p>Check <a href="<?php echo esc_url( admin_url( 'admin.php?page=easy-login-woocommerce-settings' ) ); ?>">Settings & Shortcodes</a></p>
		</div>
		<?php
	}




	public function show_outdated_template_notice(){

		if( !xoo_el_helper()->admin->is_settings_page() ) return;

		$themeTemplatesData = xoo_el_helper()->get_theme_templates_data();
		if( empty( $themeTemplatesData ) || $themeTemplatesData['has_outdated'] !== 'yes' ) return;
		?>
		<div class="notice notice-success is-dismissible xoo-el-admin-notice">
		<p><?php printf( 'You have <a href="%1$s">outdated templates</a> in your theme which are no longer supported. Please see "info" tab for more info.', esc_url( admin_url( 'admin.php?page=easy-login-woocommerce-settings' ) ) ); ?></p>
		</div>
		<?php
	}


	public function inline_styling(){
		?>
		<style type="text/css">
			.notice.xoo-el-admin-notice p {
			    font-size: 16px;
			}
	
		</style>
		<?php
	}


	public function enable_autocompadr( $allow, $aff ){
		if( $aff->plugin_slug === 'easy-login-woocommerce' ) return false;
		return $allow;
	}
	


}