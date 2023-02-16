<?php
/**
 * Order details
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/order/order-details.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.6.0
 */
defined('ABSPATH') || exit;
$order = wc_get_order($order_id); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
if ( !$order ) {
    return;
}
$order_items           = $order->get_items(apply_filters('woocommerce_purchase_order_item_types', 'line_item'));
$show_purchase_note    = $order->has_status(apply_filters('woocommerce_purchase_note_order_statuses', [
    'completed',
    'processing'
]));
$show_customer_details = is_user_logged_in() && $order->get_user_id() === get_current_user_id();
$downloads             = $order->get_downloadable_items();
$show_downloads        = $order->has_downloadable_item() && $order->is_download_permitted();
if ( $show_downloads ) {
    wc_get_template('order/order-downloads.php', [
        'downloads'  => $downloads,
        'show_title' => true,
    ]);
}
$item_totals = $order->get_order_item_totals();
?>
    <section class="woocommerce-order-details">
        <?php
        do_action('woocommerce_order_details_before_order_table', $order); ?>

        <h2 class="woocommerce-order-details__title"><?php esc_html_e('Order details', 'woocommerce'); ?></h2>

        <table class="woocommerce-table woocommerce-table--order-details shop_table order_details">

            <thead>
            <tr>
                <th class="woocommerce-table__product-name product-name">Item</th>
                <th class="woocommerce-table__product-table product-total"><?php esc_html_e('Total', 'woocommerce'); ?></th>
            </tr>
            </thead>

            <tbody>

            <?php
            do_action('woocommerce_order_details_before_order_table_items', $order);
            foreach ( $order_items as $item_id => $item ) {
                $product = $item->get_product();
                wc_get_template('order/order-details-item.php', [
                    'order'              => $order,
                    'item_id'            => $item_id,
                    'item'               => $item,
                    'show_purchase_note' => $show_purchase_note,
                    'purchase_note'      => $product ? $product->get_purchase_note() : '',
                    'product'            => $product,
                ]);
            }
            do_action('woocommerce_order_details_after_order_table_items', $order);
            ?>
            </tbody>

            <tfoot>
            <?php

            foreach ( $order->get_order_item_totals() as $key => $total ) {
                $perc_tax = 0;

                if( 'tax' == $key ){
                    foreach($order->get_items('tax') as $item_id => $item ) {
                        $tax_rate_id    = $item->get_rate_id(); // Tax rate ID
                        $tax_percent    = WC_Tax::get_rate_percent( $tax_rate_id ); // Tax percentage
                    }

                    if($order->get_total_tax() == 0){
                        $perc_tax = '0%';
                    }
                    else{
                        $perc_tax = $tax_percent;
                    }

                    $total['label'] = 'VAT';
                }
                if ( 'order_total' !== $key ):
                    ?>
                    <tr>
                        <th scope="row"><?php echo esc_html($total['label']); ?> <?php echo !empty($perc_tax) ? '(' . $perc_tax . ')' : '';?>:</th>
                        <td><?php echo ( 'payment_method' === $key ) ? $total['value'] : wp_kses_post($total['value']); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                            ?></td>
                    </tr>
                <?php
                endif;
            }
            ?>
            <?php if ( $order->get_customer_note() ) : ?>
                <tr>
                    <th><?php esc_html_e('Note:', 'woocommerce'); ?></th>
                    <td><?php echo wp_kses_post(nl2br(wptexturize($order->get_customer_note()))); ?></td>
                </tr>
            <?php endif; ?>
            </tfoot>
        </table>
        <?php
        $product_attr = $item->get_data();
        //$product_id = $item->get_product_id();
        $product_id = '';
        foreach ( $order->get_items() as $item_id => $item ) {
            $product_id = $item->get_product_id();
        }
        $courses      = get_posts([
                                      'numberposts'      => -1,
                                      'post_type'        => 'course',
                                      'suppress_filters' => true,
                                  ]);
        foreach ( $courses as $course ) :
            $course_product_id = (int) get_post_meta($course->ID, '_course_woocommerce_product', true);
            if ( $course_product_id === $product_id ) :
                $course_url = get_permalink($course->ID);
            endif;
        endforeach;
        if ( isset($course_url) && !empty($course_url) ) :?>
            <p class="order-again">
                <a href="<?php echo esc_url($course_url); ?>" class="button">GO TO COURSE</a>
            </p>
        <?php endif; ?>

        <?php do_action('woocommerce_order_details_after_order_table', $order); ?>
    </section>

<?php
/**
 * Action hook fired after the order details.
 *
 * @param WC_Order $order Order data.
 *
 * @since 4.4.0
 */
do_action('woocommerce_after_order_details', $order);
if ( $show_customer_details ) {
    wc_get_template('order/order-details-customer.php', [ 'order' => $order ]);
}
