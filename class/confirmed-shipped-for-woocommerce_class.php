<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once (dirname(__FILE__).'/confirmed-shipped-for-woocommerce-form_class.php');

class confirmedShippedForwoocommerce_class {
    
    public function __construct () {
        new confirmedShippedForwoocommerceForm_class();

        add_filter('wc_order_statuses', [$this, 'add_state_shipped_wc']);
        add_action('init', [$this, 'register_state_shipped_wc']);
        add_action( 'woocommerce_order_status_changed', [$this, 'send_email_shipping_confirmed'], 10, 3 );
        add_action( 'save_post_shop_order', [$this, 'save_tracking_in_order_detail'] );
        add_action( 'woocommerce_admin_order_data_after_order_details', [$this,'add_tracking_form_in_order_detail'] );

    }

    public function add_state_shipped_wc($order_statuses) {
        $new_statuses = array();
        
        foreach ($order_statuses as $key => $status) {
            $new_statuses[$key] = $status;
            if ('wc-processing' === $key) {
                $new_statuses['wc-order-shipped'] = _x('Order Shipped', 'Order status', 'confirmed-shipped-for-woocommerce');
            }
        }
        return $new_statuses;
    }

    public function register_state_shipped_wc() {
        register_post_status('wc-order-shipped', array(
            'label'                     => _x('Order Shipped', 'Order status', 'confirmed-shipped-for-woocommerce'),
            'public'                    => true,
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            // translators: %s represents the number of shipped orders
            'label_count'               => _n_noop(
                'Shipped Order <span class="count">(%s)</span>', 
                'Shipped Orders <span class="count">(%s)</span>', 
                'confirmed-shipped-for-woocommerce'
            ),
        ));        
    }

    public function send_email_shipping_confirmed( $order_id, $old_status, $new_status ) {
        if ( 'order-shipped' === $new_status ) {
            $order = wc_get_order( $order_id );
            $mailer = WC()->mailer();
            $mails = $mailer->get_emails();
            
            $tracking_number = get_post_meta( $order_id, '_tracking_number', true );
    
            $email_message = get_option( 'confirmed_shipped_email_message', 'Your shipment has been confirmed.' );
            $tracking_text = get_option( 'confirmed_shipped_tracking_text', 'Your tracking code is: {tracking_number}' );
    
            if ( ! empty( $tracking_number ) ) {
                $tracking_text = str_replace( '{tracking_number}', $tracking_number, $tracking_text );
            } else {
                $tracking_text = '';
            }
    
            $final_message = $email_message . "\n" . $tracking_text;
    
            if ( ! empty( $mails['WC_Email_Customer_Note'] ) ) {
                $email_note = $mails['WC_Email_Customer_Note'];
    
                $order->add_order_note( $final_message, true );
            }
        }
    }
    
    public function save_tracking_in_order_detail( $post_id ) {
        if ( ! isset( $_POST['tracking_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['tracking_nonce'] ) ), 'save_tracking_number' ) ) {
            return;
        }
    
        if ( isset( $_POST['tracking_number'] ) && 'shop_order' === get_post_type( $post_id ) ) {
            $tracking_number = sanitize_text_field( wp_unslash( $_POST['tracking_number'] ) );
            update_post_meta( $post_id, '_tracking_number', $tracking_number );
        }
    }    
               
    public function add_tracking_form_in_order_detail( $order ) {
        $tracking_number = get_post_meta( $order->get_id(), '_tracking_number', true );
        wp_nonce_field( 'save_tracking_number', 'tracking_nonce' );
        echo '<p class="form-field form-field-wide wc-customer-user inited inited_media_selector">
                <label for="tracking_number">' . esc_html__( 'Tracking code:', 'confirmed-shipped-for-woocommerce' ) . '</label> 
                <input type="text" name="tracking_number" value="' . esc_attr( $tracking_number ) . '" style="width:100%;">
              </p>';
    }
    
}



