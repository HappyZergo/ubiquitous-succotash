<?php
/**
 * My Addresses
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/my-address.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 2.6.0
 */
defined('ABSPATH') || exit;
$customer_id = get_current_user_id();
if ( !wc_ship_to_billing_address_only() && wc_shipping_enabled() ) {
    $get_addresses = apply_filters('woocommerce_my_account_get_addresses', [
        'billing'  => __('Billing address', 'woocommerce'),
        'shipping' => __('Shipping address', 'woocommerce'),
    ],                             $customer_id);
} else {
    $get_addresses = apply_filters('woocommerce_my_account_get_addresses', [
        'billing' => __('Billing address', 'woocommerce'),
    ],                             $customer_id);
}
$oldcol  = 1;
$col     = 1;
$user_id = get_current_user_id();
$user    = get_user_meta($user_id);
?>

    <h1 class="enter-title">BILLING DETAILS</h1>
    <p class="enter-title-header-sub">
        <?php echo apply_filters('woocommerce_my_account_my_address_description', esc_html__('This address will be used on the checkout page by default.', 'woocommerce')); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
    </p>
<?php if ( !wc_ship_to_billing_address_only() && wc_shipping_enabled() ) : ?>
    <div class="u-columns woocommerce-Addresses col2-set addresses">
<?php endif; ?>

<?php
$address         = wc_get_account_formatted_address($name);
$col             = $col * -1;
$oldcol          = $oldcol * -1;
$countries_obj   = new WC_Countries();
$countries_array = $countries_obj->get_countries();
$country         = $countries_array[ $user['billing_country'][0] ];
$calling_code    = WC()->countries->get_country_calling_code($user['billing_country_code'][0]);
?>

    <div class="u-column<?php echo $col < 0 ? 1 : 2; ?> col-<?php echo $oldcol < 0 ? 1 : 2; ?> woocommerce-Address">
        <header class="woocommerce-Address-title title">
            <a href="<?php echo esc_url(wc_get_endpoint_url('edit-address', $name)); ?>" class="edit"><?php echo $address ? esc_html__('Edit', 'woocommerce') : esc_html__('Add', 'woocommerce'); ?></a>
        </header>
        <address class='billing-adress-style'>
            <p><?php echo esc_html($user['first_name'][0] . ' ' . $user['last_name'][0]) ?></p>
            <p><?php echo esc_html($user['billing_company'][0]) ?></p>
            <p><?php echo esc_html($user['billing_eu_vat_number'][0]) ?></p>
            <p><?php echo esc_html($user['billing_address_1'][0]) ?></p>
            <p><?php echo esc_html($user['billing_address_2'][0]) ?></p>
            <p><?php echo esc_html($user['billing_city'][0]) ?></p>
            <p><?php echo esc_html($user['billing_state'][0] . ' ' . $user['billing_postcode'][0]) ?></p>
            <p><?php echo esc_html($country) ?></p>
            <p class="woocommerce-customer-details--phone"><?php echo esc_html($calling_code .
                                                                                $user['billing_phone'][0]); ?></p>
            <p class="woocommerce-customer-details--email"><?php echo esc_html($user['billing_email'][0]); ?></p>
        </address>
        <a class='edit-adress' href="/my-account/edit-address/billing">EDIT</a>
    </div>

<?php if ( !wc_ship_to_billing_address_only() && wc_shipping_enabled() ) : ?>
    </div>
<?php
endif;
