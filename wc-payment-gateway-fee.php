<?php
/*
Plugin Name: WooCommerce Payment Gateway Fee
Description: Adds a fee when specific payment gateways are selected (0.5 KD per 10 KD)
Version: 1.3
Author: Talal Al-Ashab
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class WC_Payment_Gateway_Fee {
    public function __construct() {
        // Add settings page
        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_init', array($this, 'register_settings'));
        
        // Add fee when payment gateway is selected
        add_action('woocommerce_cart_calculate_fees', array($this, 'add_gateway_fee'));
        
        // Update totals when payment method changes (AJAX)
        add_action('wp_footer', array($this, 'add_checkout_script'));
    }

    public function add_settings_page() {
        add_submenu_page(
            'woocommerce',
            'Payment Gateway Fee',
            'Gateway Fee',
            'manage_options',
            'gateway-fee-settings',
            array($this, 'settings_page_html')
        );
    }

    public function register_settings() {
        register_setting('gateway-fee-settings', 'wc_gateway_fee_gateways');
        register_setting('gateway-fee-settings', 'wc_gateway_fee_amount', array(
            'default' => 0.5,
            'sanitize_callback' => 'floatval'
        ));
        register_setting('gateway-fee-settings', 'wc_gateway_fee_label', array(
            'default' => 'Payment Gateway Fee',
            'sanitize_callback' => 'sanitize_text_field'
        ));
    }

    public function settings_page_html() {
        // Get available payment gateways
        $available_gateways = WC()->payment_gateways->payment_gateways();
        $selected_gateways = get_option('wc_gateway_fee_gateways', array());
        $fee_amount = get_option('wc_gateway_fee_amount', 0.5);
        $fee_label = get_option('wc_gateway_fee_label', 'Payment Gateway Fee');
        ?>
        <div class="wrap">
            <h1>Payment Gateway Fee Settings</h1>
            <form method="post" action="options.php">
                <?php settings_fields('gateway-fee-settings'); ?>
                <table class="form-table">
                    <tr>
                        <th scope="row">Fee Label</th>
                        <td>
                            <input type="text" 
                                   name="wc_gateway_fee_label" 
                                   value="<?php echo esc_attr($fee_label); ?>" 
                                   class="regular-text">
                            <p class="description">The label that will appear for the fee at checkout</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Fee Amount (KD)</th>
                        <td>
                            <input type="number" 
                                   name="wc_gateway_fee_amount" 
                                   value="<?php echo esc_attr($fee_amount); ?>" 
                                   step="0.1" 
                                   min="0">
                            <p class="description">Amount in KD to charge per 10 KD in cart</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Select Payment Gateways</th>
                        <td>
                            <?php foreach ($available_gateways as $gateway): ?>
                                <label>
                                    <input type="checkbox" 
                                           name="wc_gateway_fee_gateways[]" 
                                           value="<?php echo esc_attr($gateway->id); ?>"
                                           <?php checked(in_array($gateway->id, $selected_gateways)); ?>>
                                    <?php echo esc_html($gateway->title); ?>
                                </label><br>
                            <?php endforeach; ?>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    public function add_gateway_fee($cart) {
        if (is_admin() && !defined('DOING_AJAX')) {
            return;
        }

        // Get selected payment method
        $payment_method = WC()->session->get('chosen_payment_method');
        
        // Get selected gateways from options
        $selected_gateways = get_option('wc_gateway_fee_gateways', array());

        // Check if current payment method should have fee
        if (!in_array($payment_method, $selected_gateways)) {
            return;
        }

        // Get fee amount from settings
        $fee_per_10kd = get_option('wc_gateway_fee_amount', 0.5);
        
        // Get fee label from settings
        $fee_label = get_option('wc_gateway_fee_label', 'Payment Gateway Fee');
        
        // Calculate fee (0.5 KD per 10 KD)
        $cart_total = $cart->cart_contents_total;
        
        // Calculate number of complete 10 KD blocks
        $blocks = floor($cart_total / 10);
        
        // Calculate fee based on complete blocks
        $fee = $blocks * $fee_per_10kd;

        if ($fee > 0) {
            $cart->add_fee(__($fee_label, 'woocommerce'), $fee);
        }
    }

    public function add_checkout_script() {
        if (!is_checkout()) {
            return;
        }
        ?>
        <script type="text/javascript">
            jQuery(function($) {
                $('form.checkout').on('change', 'input[name="payment_method"]', function() {
                    $(document.body).trigger('update_checkout');
                });
            });
        </script>
        <?php
    }
}

// Initialize the plugin
add_action('plugins_loaded', function() {
    new WC_Payment_Gateway_Fee();
});