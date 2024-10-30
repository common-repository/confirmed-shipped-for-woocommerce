<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class confirmedShippedForwoocommerceForm_class {
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
    }

    public function add_settings_page() {
        add_submenu_page(
            'woocommerce', 
            __( 'Shipping Confirmation', 'confirmed-shipped-for-woocommerce' ),
            __( 'Shipping Confirmation', 'confirmed-shipped-for-woocommerce' ),
            'manage_woocommerce',
            'confirmed-shipped-settings',
            array( $this, 'settings_page_html' )
        );
    }

    public function register_settings() {
        register_setting( 
            'confirmed_shipped_settings_group', 
            'confirmed_shipped_email_message', 
            array( $this, 'sanitize_textarea' )
        );
        
        register_setting( 
            'confirmed_shipped_settings_group', 
            'confirmed_shipped_tracking_text', 
            array( $this, 'sanitize_text' )
        );
    
        add_settings_section(
            'confirmed_shipped_settings_section',
            __( 'Email Settings', 'confirmed-shipped-for-woocommerce' ),
            null,
            'confirmed-shipped-settings'
        );
    
        add_settings_field(
            'confirmed_shipped_email_message',
            __( 'Email Message', 'confirmed-shipped-for-woocommerce' ),
            array( $this, 'email_message_html' ),
            'confirmed-shipped-settings',
            'confirmed_shipped_settings_section'
        );
    
        add_settings_field(
            'confirmed_shipped_tracking_text',
            __( 'Tracking Text', 'confirmed-shipped-for-woocommerce' ),
            array( $this, 'tracking_text_html' ),
            'confirmed-shipped-settings',
            'confirmed_shipped_settings_section'
        );
    }
    
    public function sanitize_textarea( $input ) {
        return sanitize_textarea_field( $input );
    }
    
    public function sanitize_text( $input ) {
        return sanitize_text_field( $input );
    }    

    public function settings_page_html() {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Shipping Confirmation Settings', 'confirmed-shipped-for-woocommerce' ); ?></h1>
            <form action="options.php" method="POST">
                <?php
                settings_fields( 'confirmed_shipped_settings_group' );
                do_settings_sections( 'confirmed-shipped-settings' );
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public function email_message_html() {
        $value = get_option( 'confirmed_shipped_email_message', 'Your shipment has been confirmed.' );
        ?>
        <textarea name="confirmed_shipped_email_message" style="width: 100%; height: 100px;"><?php echo esc_textarea( $value ); ?></textarea>
        <?php
    }

    public function tracking_text_html() {
        $value = get_option( 'confirmed_shipped_tracking_text', 'Your tracking code is: {tracking_number}' );
        ?>
        <input type="text" name="confirmed_shipped_tracking_text" value="<?php echo esc_attr( $value ); ?>" style="width: 100%;">
        <?php
    }
}
