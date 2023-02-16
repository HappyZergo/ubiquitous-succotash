<?php if ( !defined('ABSPATH') )
    exit; // Exit if accessed directly
?>
<?php do_action('wpo_wcpdf_before_document', $this->get_type(), $this->order);
//$all_tax_rates = [];
//
//$tax_classes   = WC_Tax::get_tax_classes(); // Retrieve all tax classes.
//
//if ( !in_array('', $tax_classes) ) { // Make sure "Standard rate" (empty class name) is present.
//
//    array_unshift($tax_classes, '');
//
//}
//
//foreach ( $tax_classes as $tax_class ) { // For each tax class, get all rates.
//
//    $taxes         = WC_Tax::get_rates_for_tax_class($tax_class);
//
//    $all_tax_rates = array_merge($all_tax_rates, $taxes);
//
//}
//
//$order                    = $this->order;
//
//$get_current_country_oder = WC()->countries->countries[ $order->get_billing_country() ];
//
//$get_countries_array      = WC()->countries->countries;
//
//foreach ( $get_countries_array as $key => $item ) {
//
//    if ( $item === $get_current_country_oder ) {
//
//        $get_current_country_oder = $key;
//
//    }
//
//}
//
//
//
//foreach ( $all_tax_rates as $key => $item ) {
//
//    $get_name_code = $item->tax_rate_country;
//
//    if ( $get_name_code === $get_current_country_oder ) {
//
//        $curr_tax = number_format($item->tax_rate, 2);
//
//    };
//
//    $curr_tax_pircent = number_format('1' . $curr_tax);
//
//}
//
$currency_code = $order->get_currency();
//
$currency_symbol = get_woocommerce_currency_symbol($currency_code);
//
//$countries_obj = new WC_Countries();
//$countries     = $countries_obj->__get('countries');
//$continents    = $countries_obj->get_continents();
//$user_continents;
//foreach($continents['EU']['countries'] as $continents_key => $continents_item){
//
//    if($get_current_country_oder === $continents_item){
//        $user_continents = 'EU';
//    }
//}
?>

<table class="head container" style="margin: 20px 20px 0;">
    <tr>
        <td class="header">
            <?php
            if ( $this->has_header_logo() ) {
                $this->header_logo();
            } else {
                ;
                echo $this->get_title();
            }
            ?>

        </td>
        <td class="shop-info">
            <table style="text-align: left;">
                <h3><?php _e('FOUR YAWNS LIMITED', 'woocoomerce'); ?></h3>
                <?php do_action('wpo_wcpdf_before_shop_name', $this->get_type(), $this->order); ?>
                <?php do_action('wpo_wcpdf_after_shop_name', $this->get_type(), $this->order); ?>
                <?php do_action('wpo_wcpdf_before_shop_address', $this->get_type(), $this->order); ?>
                <div class="shop-address"><?php $this->shop_address(); ?></div>
                <?php do_action('wpo_wcpdf_after_shop_address', $this->get_type(), $this->order); ?>
                </td>
            </table>
        </td>
    </tr>
</table>

<?php do_action('wpo_wcpdf_before_document_label', $this->get_type(), $this->order); ?>
<?php do_action('wpo_wcpdf_after_document_label', $this->get_type(), $this->order); ?>

<table class="order-data-addresses" style="margin: 20px 20px 0;">
    <tr>
        <td class="address billing-address">
            <h3><?php _e('To:', 'woocoomerce'); ?></h3>
            <p>
                <?php
                $billing_company = $order->get_billing_company();
                echo 'Company name: ' . $billing_company;
                ?>
            </p>
            <p>
                <?php
                $vat_numnber = $order->get_meta('billing_eu_vat_number');
                echo ' VAT No. ' . $vat_numnber;
                ?>
            </p>
            <div class='billing-address_address-style'>
                <p><?php echo $order->get_billing_address_1() ?></p>
                <p><?php echo $order->get_billing_address_2() ?></p>
                <p><?php echo 'OR ' . $order->get_billing_postcode() ?></p>
                <p>
                    <?php
                    $countries_obj = new WC_Countries();
                    $countries_array = $countries_obj->get_countries();
                    echo $countries_array[ $order->billing_country ];
                    ?>
                </p>
            </div>
        </td>
        <td class="billing-address">
            <?php
            $order_data = $order->get_data();
            ?>
            Invoice Number: <?php $this->invoice_number(); ?><br>
            <?php echo 'Order Number: ' . $order->get_order_number(); ?><br>
            Date: <?php $this->invoice_date(); ?><br>
        </td>
    </tr>
</table>

<?php do_action('wpo_wcpdf_before_order_details', $this->get_type(), $this->order); ?>
<h1 style="text-align: center;" class="document-type-label">
    <?php if ( $this->has_header_logo() )
        echo $this->get_title(); ?>
</h1>

<table class="order-details" style="margin: 20px 20px 0; width:unset;">
    <thead>
    <tr>
        <th class="product"><?php _e('Product', 'woocommerce-pdf-invoices-packing-slips'); ?></th>
        <th class="quantity"><?php _e('Quantity', 'woocommerce-pdf-invoices-packing-slips'); ?></th>
        <th class="price"><?php _e('Price', 'woocommerce-pdf-invoices-packing-slips'); ?></th>
        <th class="vat-invoice-percent"><?php _e('VAT%', 'woocommerce-pdf-invoices-packing-slips'); ?></th>
        <th class="vat-invoice"> <?php _e('VAT', 'woocommerce-pdf-invoices-packing-slips'); ?></th>
        <th class="price-subtotal"><?php _e('Subtotal', 'woocommerce-pdf-invoices-packing-slips'); ?></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ( $this->get_order_items() as $item_id => $item ) :
        $order_total = is_callable([ $order, 'get_total' ]) ? $order->get_total() : $order->order_total;
        // Get order subtotal
        // Get the correct number format (2 decimals)
        $order_subtotal = ( 'EU' === $user_continents ) ? number_format($order_total, 2) : $order->get_subtotal();
        ?>
        <tr class="<?php echo apply_filters('wpo_wcpdf_item_row_class', 'item-' . $item_id, $this->get_type(), $this->order, $item_id); ?>">
            <td class="product">
                <?php $description_label = __('Description', 'woocommerce-pdf-invoices-packing-slips'); // registering alternate label translation
                ?>
                <span class="item-name"><?php echo $item['name']; ?></span>
                <?php do_action('wpo_wcpdf_before_item_meta', $this->get_type(), $item, $this->order); ?>
                <span class="item-meta"><?php echo $item['meta']; ?></span>
                <dl class="meta">
                    <?php $description_label = __('SKU', 'woocommerce-pdf-invoices-packing-slips'); // registering alternate label translation
                    ?>
                    <?php if ( !empty($item['sku']) ) : ?>
                        <dt class="sku"><?php _e('SKU:', 'woocommerce-pdf-invoices-packing-slips'); ?></dt>
                        <dd class="sku"><?php echo $item['sku']; ?></dd><?php endif; ?>
                    <?php if ( !empty($item['weight']) ) : ?>
                        <dt class="weight"><?php _e('Weight:', 'woocommerce-pdf-invoices-packing-slips'); ?></dt>
                        <dd class="weight"><?php echo $item['weight']; ?><?php echo get_option('woocommerce_weight_unit'); ?></dd><?php endif; ?>
                </dl>
                <?php do_action('wpo_wcpdf_after_item_meta', $this->get_type(), $item, $this->order); ?>
            </td>
            <td class="quantity"><?php echo $item['quantity']; ?></td>
            <td class="price">
                <?php
                echo !empty($order->get_subtotal()) ? $currency_symbol . $order->get_subtotal() : $currency_symbol . 0;
                ?>
            </td>
            <td class="price">
                <?php 
                if('MT' == $order->get_billing_country()){
                    $calculated_tax_rates    = round(floatval($item['calculated_tax_rates'])); // Tax rate ID
                    echo $calculated_tax_rates.'%';
                }
                ?>
            
            </td>
            <td class="price">
                <?php 
                if('MT' == $order->get_billing_country()){
                    $line_subtotal_tax    = $item['line_subtotal_tax']; // Tax rate ID
                    echo $line_subtotal_tax;
                }
                ?>
            </td>
            <td class="price">
                <?php
                echo !empty($order->get_subtotal()) ? $currency_symbol . $order->get_subtotal() : $currency_symbol . 0;
                ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
    <tfoot>
    <tr class="no-borders">
        <td class="no-borders" colspan="3">
        </td>
        <td class="no-borders" colspan="3">
            <table class="totals">
                <tfoot>
                <tr>
                    <th class="description">Subtotal</th>
                    <td class="price">
                        <span class="totals-price">
                        <?php
                        echo !empty($order->get_subtotal()) ? $currency_symbol . $order->get_subtotal() : $currency_symbol . 0;
                        ?>   
                        </span>
                    </td>
                </tr>
                <tr>
                    <th class="description">Discount</th>
                    <td class="price">
                        <span class="totals-price"><?php echo !empty($order->get_total_discount()) ? '- ' . $currency_symbol . $order->get_total_discount() : 0; ?></span>
                    </td>
                </tr>
                <tr>
                <tr>
                    <th class="description">VAT
                    <td class="price"><span class="totals-price">
                    <?php 
                    if('MT' == $order->get_billing_country()){
                        $single_line_tax    = $item['single_line_tax']; // Tax rate ID
                        echo $single_line_tax;
                    }
                    ?>
                    </span></td>
                </tr>
                <?php if ( $curr_tax == null ): ?>
                    <tr class="free-tr">
                        <th class="description"> free block</th>
                        <td class="price"><span class="totals-price"> free block </span></td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <th class="description">Total</th>
                    <td class="price">
                        <span class="totals-price"> <?php echo $currency_symbol . ' ' . $order->get_total(); ?></span>
                    </td>
                </tr>
                </tfoot>
            </table>
        </td>
    </tr>
    </tfoot>
</table>
<div class="bottom-spacer"></div>
<?php do_action('wpo_wcpdf_after_order_details', $this->get_type(), $this->order); ?>

<?php if ( $this->get_footer() ) : ?>
    <div id="footer">
        <!-- hook available: wpo_wcpdf_before_footer -->
        <?php $this->footer(); ?>
        <!-- hook available: wpo_wcpdf_after_footer -->
    </div><!-- #letter-footer -->
<?php endif; ?>



<?php do_action('wpo_wcpdf_after_document', $this->get_type(), $this->order); ?>

