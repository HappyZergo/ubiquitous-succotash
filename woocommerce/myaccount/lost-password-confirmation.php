<?php
/**
 * Lost password confirmation text.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/lost-password-confirmation.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.9.0
 */

defined( 'ABSPATH' ) || exit;

wc_print_notice( esc_html__( 'Password reset email sent.', 'woocommerce' ) );
?>

<?php do_action( 'woocommerce_before_lost_password_confirmation_message' ); ?>
<div class='wrapper-form-reset-password-link-sent'>
<h2 class="enter-title"> <?php echo esc_html( 'HELP IS ON THE WAY!', 'woocommerce' ) ?></h2>
<p><?php echo esc_html( apply_filters( 'woocommerce_lost_password_confirmation_message', esc_html__( 'We’ve sent you a email to reset your password to the address connected with your account.', 'woocommerce' ) ) ); ?></p>
<p><?php echo esc_html( apply_filters( 'woocommerce_lost_password_confirmation_message', esc_html__( 'If you don’t get the email, check your spam mail folder.', 'woocommerce' ) ) ); ?></p>
<p><?php echo esc_html( apply_filters( 'woocommerce_lost_password_confirmation_message', esc_html__( 'Please give it a few minutes before trying a new reset.', 'woocommerce' ) ) ); ?></p>
</div>
<?php do_action( 'woocommerce_after_lost_password_confirmation_message' ); ?>
