<?php
/**
 * Edit address form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-edit-address.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;

$page_title = ( 'billing' === $load_address ) ? __( 'Billing address', 'woocommerce' ) : __( 'Shipping address', 'woocommerce' );
do_action( 'woocommerce_before_edit_account_address_form' ); ?>
<?php if ( ! $load_address ) : ?>
	<?php wc_get_template( 'myaccount/my-address.php' ); ?>
<?php else : ?>

	<form class='custom-style-eddit-detail' method="post">
		<h3>EDIT BILLING DETAILS</h3><?php // @codingStandardsIgnoreLine ?>
		<div class="woocommerce-address-fields">
			<?php do_action( "woocommerce_before_edit_address_form_{$load_address}" ); ?>
			<div class="woocommerce-address-fields__field-wrapper">
				<?php
					$tax_chekout = WC()->checkout->get_value( 'billing_eu_vat_number');

					if($tax_chekout !== null){
						$address['billing_eu_vat_number']['value'] = $tax_chekout;
					}
                    else{
                        $address['billing_eu_vat_number']['value'] = $address['billing_eu_vat_number']['value'];
                    }
					//unset($address['billing_eu_vat_number']);
					$address['billing_last_name']["label"] = 'Surname';
					$address['billing_company']["placeholder"] = 'Company Name';
					$address['billing_country']["label"] = 'Country';
					$address['billing_country']['required'] = true;
					$address['billing_state']["label"] = 'State / County / Province';
					$address['billing_country_code']["label"] = 'Mobile Number';
					$address['billing_phone']["label"] = ' ';
					$address['billing_address_1']["label"] = 'Address Line 1'; 
					$address['billing_address_2']["label"] = 'Address Line 2'; 
					$address['billing_address_2']['label_class'][0] = '';
					$address['billing_postcode']["label"] = 'ZIP / Postcode'; 
					$address['billing_city']["label"] = 'Town / City  <abbr class="required" title="required">*</abbr>';

					woocommerce_form_field( 'billing_first_name', $address['billing_first_name'], wc_get_post_data_by_key( 'billing_first_name', $address['billing_first_name']['value'] ) );
					woocommerce_form_field( 'billing_last_name', $address['billing_last_name'], wc_get_post_data_by_key( 'billing_last_name', $address['billing_last_name']['value'] ) );
					woocommerce_form_field( 'billing_email', $address['billing_email'], wc_get_post_data_by_key( 'billing_email', $address['billing_email']['value'] ) );
					woocommerce_form_field( 'billing_country_code', $address['billing_country_code'], wc_get_post_data_by_key( 'billing_country_code', $address['billing_country_code']['value'] ) );
					woocommerce_form_field( 'billing_phone', $address['billing_phone'], wc_get_post_data_by_key( 'billing_phone', $address['billing_phone']['value'] ) );
					woocommerce_form_field( 'billing_company', $address['billing_company'], wc_get_post_data_by_key( 'billing_company', $address['billing_company']['value'] ) );
					woocommerce_form_field( 'billing_eu_vat_number', 1, wc_get_post_data_by_key( 'billing_eu_vat_number', $address['billing_eu_vat_number']['value'] ) );
					woocommerce_form_field( 'billing_country', $address['billing_country'], wc_get_post_data_by_key( 'billing_country', $address['billing_country']['value'] ) );
					woocommerce_form_field( 'billing_address_1', $address['billing_address_1'], wc_get_post_data_by_key( 'billing_address_1', $address['billing_address_1']['value'] ) );
					woocommerce_form_field( 'billing_address_2', $address['billing_address_2'], wc_get_post_data_by_key( 'billing_address_2', $address['billing_address_2']['value'] ) );
					woocommerce_form_field( 'billing_city', $address['billing_city'], wc_get_post_data_by_key( 'billing_city', $address['billing_city']['value'] ) );		
					woocommerce_form_field( 'billing_state', $address['billing_state'], wc_get_post_data_by_key( 'billing_state', $address['billing_state']['value'] ) );
					woocommerce_form_field( 'billing_postcode', $address['billing_postcode'], wc_get_post_data_by_key( 'billing_postcode', $address['billing_postcode']['value'] ) );		
				?>
			</div>
			<?php do_action( "woocommerce_after_edit_address_form_{$load_address}" ); ?>

			<p>
				<button type="submit" class="button" name="save_address" value="<?php esc_attr_e( 'Save address', 'woocommerce' ); ?>"><?php esc_html_e( 'SAVE CHANGES', 'woocommerce' ); ?></button>
				<?php wp_nonce_field( 'woocommerce-edit_address', 'woocommerce-edit-address-nonce' ); ?>
				<input type="hidden" name="action" value="edit_address" />
			</p>
		</div>
	</form>

<?php endif; ?>

<?php do_action( 'woocommerce_after_edit_account_address_form' ); ?>
