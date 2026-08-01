<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}


use XooEL\Framework\Xoo_Exception;

class Xoo_El_Code_Form{

    public $form_id;

    public $limits;

    public $input;

    public $object;

    public $code;

    public $parentFormSelector = '';

    public $codeFormArgs = array();

    public $successMessage;

    public static $glSettings;

    public function __construct() {

        if( !self::$glSettings ){
            self::$glSettings = xoo_el_helper()->get_general_option();
        }

        $this->limits =  array(
            'resend_limit'      => 7,
            'incorrect_limit'   => 7,
            'resend_wait_time'  => 90,
            'ban_time'          => 600
        );

        $this->codeFormArgs = wp_parse_args( $this->codeFormArgs, array(
            'return'                => false,
            'digits'                => 6,
            'code_form_id'          => $this->form_id ,
            'verify_btn'            => __( 'Verify', 'easy-login-woocommerce' ),
            'resend'                => true,
            'resend_txt'            => __( 'Not received your code? Resend code', 'easy-login-woocommerce' ),
            'parentFormSelector'    => $this->parentFormSelector,
            'allow_change'          => true
        ) );

        $this->successMessage = __( 'Thank you for verifying.', 'easy-login-woocommerce' );

        $this->hooks();
    }


    public function hooks(){

        // phpcs:ignore WordPress.Security.NonceVerification.Missing
        if( isset( $_POST['xoo_el_code_ajax'] ) && $_POST['xoo_el_code_ajax'] === $this->form_id ){
            add_action( 'wp_ajax_xoo_el_code_form_submit', array( $this, 'verify_code' ) );
            add_action( 'wp_ajax_nopriv_xoo_el_code_form_submit', array( $this, 'verify_code' ) );
        }
    }


    public function get_form_id() {
        return $this->form_id;
    }


    public function get_transient_name(){
        $token = $this->get_verification_token();

        if ( ! $token ) {
            return '';
        }

        // The browser receives only the random token. Store and look up the
        // verification state by its keyed hash so a leaked transient name is
        // not itself a usable bearer credential.
        return 'xoo_el_verification_' . sanitize_key( $this->form_id ) . '_' . hash_hmac( 'sha256', $token, wp_salt( 'auth' ) );
    }


    public function get_verification_cookie_name(){
        return 'xoo_el_verification_' . sanitize_key( $this->form_id );
    }


    public function get_verification_token(){
        $cookie_name = $this->get_verification_cookie_name();

        if ( empty( $_COOKIE[ $cookie_name ] ) ) {
            return '';
        }

        // Tokens created below are URL-safe base64 strings. Reject anything
        // else rather than using arbitrary client input in a transient key.
        $token = sanitize_text_field( wp_unslash( $_COOKIE[ $cookie_name ] ) );
        return preg_match( '/^[A-Za-z0-9_-]{43}$/', $token ) ? $token : '';
    }


    public function create_verification_token(){
        $token = rtrim( strtr( base64_encode( random_bytes( 32 ) ), '+/', '-_' ), '=' );
        $cookie_name = $this->get_verification_cookie_name();
        $expires = time() + DAY_IN_SECONDS;
        $path = COOKIEPATH ? COOKIEPATH : '/';

        if ( PHP_VERSION_ID >= 70300 ) {
            setcookie( $cookie_name, $token, array(
                'expires'  => $expires,
                'path'     => $path,
                'domain'   => COOKIE_DOMAIN,
                'secure'   => is_ssl(),
                'httponly' => true,
                'samesite' => 'Lax',
            ) );
        } else {
            setcookie( $cookie_name, $token, $expires, $path, COOKIE_DOMAIN, is_ssl(), true );
        }

        // Make it available during this request too; PHP does not populate
        // $_COOKIE until the browser makes its next request.
        $_COOKIE[ $cookie_name ] = $token;

        return $token;
    }


    public function ensure_verification_token(){
        return $this->get_verification_token() ?: $this->create_verification_token();
    }


    public function get_destination_limit_transient_name( $destination ){
        $destination = strtolower( trim( (string) $destination ) );
        return 'xoo_el_verification_limit_' . sanitize_key( $this->form_id ) . '_' . hash_hmac( 'sha256', $destination, wp_salt( 'auth' ) );
    }


    public function get_destination_limit_data( $destination ){
        return get_transient( $this->get_destination_limit_transient_name( $destination ) );
    }


    public function set_destination_limit_data( $destination, $data = array() ){
        $existing = $this->get_destination_limit_data( $destination );
        $data = wp_parse_args( $data, is_array( $existing ) ? $existing : array() );

        if ( empty( $data['created'] ) ) {
            $data['created'] = time();
        }

        set_transient( $this->get_destination_limit_transient_name( $destination ), $data, DAY_IN_SECONDS );
    }


    public function consume_verification(){
        $transient_name = $this->get_transient_name();

        if ( $transient_name ) {
            delete_transient( $transient_name );
        }

        $cookie_name = $this->get_verification_cookie_name();
        $path = COOKIEPATH ? COOKIEPATH : '/';
        setcookie( $cookie_name, ' ', time() - YEAR_IN_SECONDS, $path, COOKIE_DOMAIN, is_ssl(), true );
        unset( $_COOKIE[ $cookie_name ] );
    }


    public function hash_code( $code ){
        return hash_hmac( 'sha256', (string) $code, wp_salt( 'nonce' ) );
    }


    public function get_user_data( $subkey = '' ){

        $transient_name = $this->get_transient_name();
        $user_data = $transient_name ? get_transient( $transient_name ) : array();

        if( $subkey ){
            return isset( $user_data[$subkey] ) ? $user_data[$subkey] : null;
        }

        return $user_data;

    }



    public function set_user_data( $data = array() ){

        $this->ensure_verification_token();

        $existing_data          = $this->get_user_data();

        $data                   = wp_parse_args( $data, $existing_data );

        $data['last_updated']   = time();

        set_transient( $this->get_transient_name(), $data, DAY_IN_SECONDS );

    }


    public function request_code( $email ){

    }

  

    public function ok_to_send_code( $sendTo = '' ){

        $user_data = $this->get_destination_limit_data( $sendTo );

        if( !is_array( $user_data ) || empty( $user_data ) ) return;

        $limits = $this->limits;

        $time_passed = time() - (int) $user_data['created'];

        if( isset( $user_data['sent_times'] ) && $user_data['sent_times'] >= $limits['resend_limit'] ){
            $unban_time_left = $limits['ban_time'] - $time_passed;
            if(  $unban_time_left < 0  ){
                $this->set_destination_limit_data( $sendTo, array( 'sent_times' => 0, 'created' => time() ) );
            }
            else{
                return new \WP_Error( 'limit-reached', sprintf( __( 'Code Limit reached. Please try again in %s.', 'easy-login-woocommerce' ), self::getTimeDuration( $unban_time_left) ) );
            }
        }


        //For resend
       /* if( $limits['resend_wait_time'] > $time_passed ){
            $unban_time_left = $limits['resend_wait_time'] - $time_passed;
            return new \WP_Error( 'resend-wait', sprintf( __( 'Please wait %s for a new code.', 'easy-login-woocommerce' ), self::getTimeDuration( $unban_time_left) ) );
        }*/


        $incorrect_tries_limit_reached = $this->incorrect_tries_limit_reached( $sendTo );

        if( is_wp_error(  $incorrect_tries_limit_reached ) ){

            $unban_time_left = $limits['ban_time'] - $time_passed;

            if( $unban_time_left < 0 ){
                $this->set_destination_limit_data( $sendTo, array( 'incorrect' => 0, 'created' => time() ) );
            }
            else{
                return $incorrect_tries_limit_reached;
            }
        }


    }


    public function incorrect_tries_limit_reached( $destination = '' ){

        $user_data = $destination ? $this->get_destination_limit_data( $destination ) : $this->get_user_data();

        if( isset( $user_data['incorrect'] ) && $user_data['incorrect'] >= $this->limits['incorrect_limit'] ){
            return new \WP_Error( 'tries-exceeded', __( 'Number of tries exceeded, Please try again in few minutes', 'easy-login-woocommerce' ) );
        }

        return false;

    }


    public function code_expired(){

        $user_data = $this->get_user_data();

        if( isset( $user_data['expiry'] ) && strtotime('now') > (int) $user_data['expiry'] ){
            return new \WP_Error( 'code-expired', __( 'Code Expired', 'easy-login-woocommerce' ) );
        }

        return false;
    }


    public static function getTimeDuration( $time ){
        return $time > 60 ? round($time/60). ' minutes' : $time. ' seconds';
    }


    public function sendEmailCode( $email ){

        $this->ensure_verification_token();

        $ok_to_send_code = $this->ok_to_send_code( $email );

        if( is_wp_error( $ok_to_send_code ) ){
            return $ok_to_send_code;
        }

        $this->code = $this->generate_code_digits();

        $this->triggerCodeEmail();
       
        $limit_data = $this->get_destination_limit_data( $email );
        $this->set_destination_limit_data( $email, array(
            'sent_times' => isset( $limit_data['sent_times'] ) ? (int) $limit_data['sent_times'] + 1 : 1,
            'incorrect'  => isset( $limit_data['incorrect'] ) ? (int) $limit_data['incorrect'] : 0,
        ) );

        $this->set_user_data(
            array(
                'code_hash'      => $this->hash_code( $this->code ),
                'verified'      => false,
                'sendTo'        => $email,
                'expiry'        => time() + 10 * MINUTE_IN_SECONDS,
            )
        );

    }


    public function resend_code(){

        $sendTo = $this->get_user_data('sendTo');

        if( !$sendTo ){
            return new \Wp_Error( 'no-phone', __( "Phone Number not found", 'mobile-login-woocommerce' ) );
        }
        return $this->sendEmailCode( $sendTo );
    }



    public function get_code_email_content(){

    }


    public function generate_code_digits(){

        $digits = 6;

        $pattern = 'number';

        switch ($pattern) {

            case 'alphabet':
                $code = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, $digits);
                break;

            case 'numb_alpha':
                $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                $code = '';
                for ($i = 0; $i < $digits; $i++) {
                    $code .= $characters[random_int(0, strlen($characters) - 1)];
                }
                break;

            default:
                $code = random_int( pow( 10, $digits - 1 ) , pow( 10, $digits ) - 1 );
                break;
        } 

        return $code;
    }


    public function get_code_form( $args = array() ){
        return xoo_el_helper()->get_template( 'xoo-el-code-form.php', wp_parse_args( $args, $this->codeFormArgs ), '', $this->codeFormArgs['return'] );
    }


    public function onSuccess(){

    }

    public function verify_code(){

        try {
            
            $user_data = $this->get_user_data();
            $destination = isset( $user_data['sendTo'] ) ? $user_data['sendTo'] : '';
            $incorrect_limit_reached = $this->incorrect_tries_limit_reached( $destination );

            //Check for incorrect limit
            if( is_wp_error( $incorrect_limit_reached )  ){
                throw new Xoo_Exception( $incorrect_limit_reached );
            }

            // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated
            // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
            $submitted_code = isset( $_POST['code'] ) ? wp_unslash( $_POST['code'] ) : '';

            if( !isset( $user_data['code_hash'] ) || !hash_equals( $user_data['code_hash'], $this->hash_code( $submitted_code ) ) ){

                if ( $destination ) {
                    $limit_data = $this->get_destination_limit_data( $destination );
                    $this->set_destination_limit_data( $destination, array(
                        'incorrect' => isset( $limit_data['incorrect'] ) ? (int) $limit_data['incorrect'] + 1 : 1,
                    ) );
                }

                throw new Xoo_Exception( __( 'Invalid Code', 'easy-login-woocommerce' ) );
            }

            $code_expired = $this->code_expired();

            if( is_wp_error( $code_expired ) ){
                throw new Xoo_Exception( $code_expired );
            }

            $this->set_user_data( array(
                'verified'          => true,
				'verified_until'    => time() + 10 * MINUTE_IN_SECONDS,
                'expiry'            => '',
            ) );

            $updated_user_data = $this->get_user_data();

            //Hook functions on Code verification
            do_action( 'xoo_el_code_validation_success', $this->form_id, $updated_user_data );

            $this->onSuccess();

            $notice = apply_filters( 'xoo_el_code_validation_success_notice', $this->successMessage, $this->form_id, $updated_user_data );

            wp_send_json(array(
                'error'     => 0,
                'notice'    => xoo_el_add_notice( 'success', $notice )
            ));

        } catch ( Xoo_Exception $e ) {

            $notice = apply_filters( 'xoo_el_code_verify_errors', $e->getMessage() );

            wp_send_json(array(
                'error'     => 1,
                'notice'    => xoo_el_add_notice( 'error', $notice )
            ));

        }
        
    }


    public function mask_email($email){

        $email = preg_replace('/\B[^@.]/', '*', $email);

        return $email;

    }


    public function is_user_verified(){
        $user_data = $this->get_user_data();

        return ! empty( $user_data['verified'] )
            && ! empty( $user_data['verified_until'] )
            && time() <= (int) $user_data['verified_until'];
    }

}
