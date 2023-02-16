<?php
/**
 * Order Customer Details
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/order/order-details-customer.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 5.6.0
 */
defined('ABSPATH') || exit;
$show_shipping = !wc_ship_to_billing_address_only() && $order->needs_shipping_address();
?>
<section class="woocommerce-customer-details">
    <?php if ( $show_shipping ) : ?>

    <section class="woocommerce-columns woocommerce-columns--2 woocommerce-columns--addresses col2-set addresses">
        <div class="woocommerce-column woocommerce-column--1 woocommerce-column--billing-address col-1">

            <?php endif; ?>

            <h2 class="woocommerce-column__title"><?php esc_html_e('Billing address', 'woocommerce'); ?></h2>
            <?php
            $user_id         = get_current_user_id();
            $user            = get_user_meta($user_id);
            $countries_obj   = new WC_Countries();
            $countries_array = $countries_obj->get_countries();
            $country         = $countries_array[ $user['billing_country'][0] ];
            $args            = [
                'customer_id' => $user_id,
                'limit'       => -1, // to retrieve _all_ orders by this user
            ];
            $orders          = wc_get_orders($args);
            $order           = $orders[0];
            $calling_code    = !empty($order->get_meta('_billing_country_code')) ? WC()->countries->get_country_calling_code($order->get_meta('_billing_country_code')) : WC()->countries->get_country_calling_code($user['billing_country'][0]);
            ?>

            <address>
                <p><?php echo esc_html($user['first_name'][0] . ' ' . $user['last_name'][0]) ?></p>
                <p><?php echo esc_html($user['billing_company'][0]) ?></p>
                <p><?php echo esc_html($user['billing_eu_vat_number'][0]) ?></p>
                <p><?php echo esc_html($user['billing_address_1'][0]) ?></p>
                <p><?php echo esc_html($user['billing_address_2'][0]) ?></p>
                <p><?php echo esc_html($user['billing_city'][0]) ?></p>
                <p><?php echo esc_html($user['billing_state'][0] . ' ' . $user['billing_postcode'][0]) ?></p>
                <p><?php echo esc_html($country) ?></p>
                <p class="woocommerce-customer-details--phone"><?php echo esc_html($calling_code . $user['billing_phone'][0]); ?></p>
                <p class="woocommerce-customer-details--email"><?php echo esc_html($user['billing_email'][0]); ?></p>
            </address>

            <?php if ( $show_shipping ) : ?>

        </div><!-- /.col-1 -->

        <div class="woocommerce-column woocommerce-column--2 woocommerce-column--shipping-address col-2">
            <h2 class="woocommerce-column__title"><?php esc_html_e('Shipping address', 'woocommerce'); ?></h2>
            <address>
                <?php echo wp_kses_post($order->get_formatted_shipping_address(esc_html__('N/A', 'woocommerce'))); ?>
                <?php if ( $order->get_shipping_phone() ) : ?>
                    <p class="woocommerce-customer-details--phone"><?php echo esc_html($order->get_shipping_phone()); ?></p>
                <?php endif; ?>
            </address>
        </div><!-- /.col-2 -->

    </section><!-- /.col2-set -->

<?php endif; ?>

    <?php do_action('woocommerce_order_details_after_customer_details', $order); ?>

</section>
