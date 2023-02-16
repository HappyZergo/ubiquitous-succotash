<?php
/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.0
 * @var $checkout WC_Checkout
 */
if ( !defined('ABSPATH') ) {
    exit;
}
global $post;
do_action('woocommerce_before_checkout_form', $checkout);
// If checkout registration is disabled and not logged in, the user cannot checkout.
if ( !$checkout->is_registration_enabled() && $checkout->is_registration_required() && !is_user_logged_in() ) {
    echo esc_html(apply_filters('woocommerce_checkout_must_be_logged_in_message', __('You must be logged in to checkout.', 'woocommerce')));

    return;
}
$geo      = new WC_Geolocation(); // Get WC_Geolocation instance object
$user_ip  = $geo->get_ip_address(); // Get user IP
$user_geo = $geo->geolocate_ip($user_ip); // Get geolocated user data.
$country  = $user_geo['country']; // Get the country code
$loginUrl = home_url('/my-courses/');
$page_ID = $post->ID;
$user_id = get_current_user_id();
$current_user = wp_get_current_user();
$customer_email = $current_user->email;
$args = [
    'post_type' => 'product',
    'posts_per_page' => 12
];
$loop = new WP_Query($args);
if ( $loop->have_posts() ) {
    while ( $loop->have_posts() ) : $loop->the_post();
        $_product = wc_get_product($loop->post->ID);
        if ( wc_customer_bought_product($customer_email, $user_id, $_product->get_id()) ) {
            wp_redirect($loginUrl, 301);
        }
    endwhile;
}
?>
<script>
   window.onpageshow = function(event) {
        if (event.persisted) {
            window.location.reload() 
        }
    };
</script>
<form user_ip_country='<?php echo $country; ?>' name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url(wc_get_checkout_url()); ?>" enctype="multipart/form-data">

    <?php if ( $checkout->get_checkout_fields() ) : ?>

        <?php do_action('woocommerce_checkout_before_customer_details'); ?>

        <div class="col2-set" id="customer_details">

            <div class="col-1">

                <?php do_action('woocommerce_checkout_billing'); ?>

            </div>

            <div class="col-2">

                <?php do_action('woocommerce_checkout_shipping'); ?>

            </div>

        </div>

        <?php do_action('woocommerce_checkout_after_customer_details'); ?>

    <?php endif; ?>



    <?php do_action('woocommerce_checkout_before_order_review_heading'); ?>

    <div class="checkout-total">

        <h3 id="order_review_heading"><?php esc_html_e('Your order', 'woocommerce'); ?></h3>

        <?php do_action('woocommerce_checkout_before_order_review'); ?>

        <div id="order_review" class="woocommerce-checkout-review-order">

            <?php do_action('woocommerce_checkout_order_review'); ?>

        </div>

        <?php do_action('woocommerce_checkout_after_order_review'); ?>

    </div>

</form>

<span id="refund_policy_giperlink" class="refund_policy">30-Day Money-Back Guarantee.</span>

</div>

<?php do_action('woocommerce_after_checkout_form', $checkout); ?>

