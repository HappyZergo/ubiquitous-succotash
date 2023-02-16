<?php
/**
 * Payment methods
 *
 * Shows customer payment methods on the account page.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/payment-methods.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 2.6.0
 */
global $wp;
$current_slug = add_query_arg( array(), $wp->request );
defined( 'ABSPATH' ) || exit;

$saved_methods = wc_get_customer_saved_methods_list( get_current_user_id() );
$has_methods   = (bool) $saved_methods;
$types         = wc_get_account_payment_methods_types();

do_action( 'woocommerce_before_account_payment_methods', $has_methods ); 

$custom_array = array();
$custom_array['stripe_cc'] = array();
foreach($saved_methods['stripe_cc'] as $get_methods){
	$custom_array['stripe_cc'];
	$custom_sub_array = array(); 
	foreach($get_methods as $key=>$item){
		$custom_sub_array[$key] =  $item;
	}
	$custom_sub_array['details'] = '';
	array_push($custom_array['stripe_cc'], $custom_sub_array);
}

?>

<h1 class='enter-title'>PAYMENT METHODS</h1>
<?php if ( $has_methods ) : 

$customn_column = array(
	"title"=>"Title",
	'details'=>'Details',
	"expires"=>"Expires",
	'default'=> 'Default?',
	'actions' => '',
	'Actions' => 'Actions',
);
?>
	
	<table class="woocommerce-MyAccount-paymentMethods shop_table shop_table_responsive account-payment-methods-table">
		<thead>
			<tr>
				<?php 
				foreach ( $customn_column as $column_id => $column_name ) :
				?>
					<th class="woocommerce-PaymentMethod woocommerce-PaymentMethod--<?php echo esc_attr( $column_id ); ?> payment-method-<?php echo esc_attr( $column_id ); ?>">
					<span class="nobr"><?php echo esc_attr( $column_name ); ?></span></th>
				<?php endforeach; ?>
			</tr>
		</thead>
		<?php 
		$count = 0;
		foreach ( $custom_array as $type => $methods ) : // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited ?>
			
			<?php foreach ( $methods as $method ) : 
			?>
				<tr date-index="<?php echo $count; $count++;?>" class="payment-method<?php echo ! empty( $method['is_default'] ) ? ' default-payment-method' : ''; ?>">
					<?php foreach ( $customn_column as $column_id => $column_name ) : 
						?>
						<td class="woocommerce-PaymentMethod woocommerce-PaymentMethod--<?php echo esc_attr( $column_id ); ?> payment-method-<?php echo esc_attr( $column_id ); ?>" data-title="<?php echo esc_attr( $column_name ); ?>">	
							<?php
							do_action( 'woocommerce_account_payment_methods_column_' . $column_id, $method );
							
						if ( 'default' === $column_id ) {
							if( !$method['is_default']){
								if( $column_name === 'Default?'){
									$action = $method['actions']['default'];
									echo '<a class="do_default" href="' . esc_url( $action['url'] ) . '">' . esc_html( $action['name'] ) . '</a>';
								}
							}
							else{
								echo '<span class="payment-Dвefault">Default</span';
							}
						}
							if ( 'details' === $column_id ) {
								$current_image;
								switch($method['method']['brand']){
									case 'Visa':
										$current_image = '/images/visa.svg';
										break;
									case 'MasterCard':
										$current_image = '/images/mastercard.svg';
										break;
									default:
										$current_image = '/images/new-cart.svg';
								}
								echo '<span><img src="'.get_template_directory_uri(). $current_image . '">**********' . $method['method']['last4'] . '</span';
							} 
							 elseif ( 'title' === $column_id ) {
								if ( ! empty( $method['method']['brand'] ) ){
									echo esc_html( wc_get_credit_card_type_label( $method['method']['brand'] ) );
								}
							} elseif ( 'expires' === $column_id ) {
								echo esc_html( $method['expires'] );
							} elseif ( 'actions' === $column_id ) {
								foreach ( $method['actions'] as $key => $action ) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
									if( $key !== 'default'){
										echo '<a href="' . esc_url( $action['url'] ) . '" class="button ' . sanitize_html_class( $key ) . '">' . esc_html( $action['name'] ) . '</a>';
									}
								}
							} 
							?>
						</td>
					<?php endforeach; ?>
				</tr>
		<?php endforeach; ?>
		<?php endforeach; ?>
	</table>

<?php else : ?>
	<p class="payment_methods-error"><?php esc_html_e( 'You don’t have any saved payment methods.', 'woocommerce' ); ?></p>
<?php endif; ?>

<?php do_action( 'woocommerce_after_account_payment_methods', $has_methods ); ?>



<?php if ( WC()->payment_gateways->get_available_payment_gateways() ) :?>
	<a class="button" href="<?php echo esc_url( wc_get_endpoint_url( 'add-payment-method' ) ); ?>"><?php esc_html_e( 'ADD PAYMENT METHOD', 'woocommerce' ); ?></a>
<?php endif; ?>
