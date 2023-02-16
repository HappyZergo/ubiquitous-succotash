<?php
/**
 * Remove Has Been Added to Your Cart Message WooCommerce
 */
add_filter('wc_add_to_cart_message_html', '__return_false');
/**
 * Change thank you page title
 */
add_filter('the_title', 'woo_title_order_received', 10, 2);
function woo_title_order_received( $title, $id )
{
    if ( function_exists('is_order_received_page') && is_order_received_page() && get_the_ID() === $id ) {
        $title = "THANK YOU FOR YOUR PURCHASE";
    }

    return $title;
}

/**
 * change add to cart button
 */
function buy_now_submit_form()
{
    ?>
    <script>
        jQuery(document).ready(function () {
            // listen if someone clicks 'Buy Now' button
            jQuery('#buy_now_button').click(function () {
                // set value to 1
                jQuery('#is_buy_now').val('1');
                //submit the form
                jQuery('form.cart').submit();
            });
        });
    </script>
    <?php
}

add_action('woocommerce_after_add_to_cart_form', 'buy_now_submit_form');
add_filter('woocommerce_add_to_cart_redirect', 'redirect_to_checkout');
function redirect_to_checkout( $redirect_url )
{
    if ( isset($_REQUEST['is_buy_now']) && $_REQUEST['is_buy_now'] ) {
        global $woocommerce;
        $redirect_url = wc_get_checkout_url();
    }

    return $redirect_url;
}

/**
 * Limit the Cart to Max One Product
 */
add_filter('woocommerce_add_to_cart_validation', 'bbloomer_only_one_in_cart', 9999, 2);
function bbloomer_only_one_in_cart( $passed, $added_product_id )
{
    wc_empty_cart();

    return $passed;
}

/**
 * Move email_course_details in woocommerce email
 */
$sensei = new Sensei_WC();
add_action('wp_loaded', function ()
{
    remove_action('woocommerce_email_after_order_table', [ 'Sensei_WC', 'email_course_details' ], 10, 1);
});
add_action('woocommerce_email_before_order_table', [ $sensei, 'email_course_details' ], 10, 1);
/**
 *  Move coupon form to bottom
 */
remove_action('woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10);
add_action('woocommerce_after_checkout_form', 'woocommerce_checkout_coupon_form', 5);
/**
 *  Adding a placeholder to checkout input fields
 */
add_filter('woocommerce_checkout_fields', 'override_billing_checkout_fields', 20, 1);
function override_billing_checkout_fields( $fields )
{
    $fields['billing']['billing_phone']['placeholder']      = 'Phone *';
    $fields['billing']['billing_email']['placeholder']      = 'Email address *';
    $fields['billing']['billing_first_name']['placeholder'] = 'First name *';
    $fields['billing']['billing_last_name']['placeholder']  = 'Last name *';
    $fields['billing']['billing_company']['placeholder']    = 'Company name (optional)';
    $fields['billing']['billing_city']['placeholder']       = 'Town / City *';
    $fields['billing']['billing_postcode']['placeholder']   = 'ZIP / Postcode *';
    $fields['billing']['billing_country']['placeholder']    = 'Country *';
    $fields['billing']['billing_state']['placeholder']      = 'State / County / Province';
    $fields['account']['account_username']['placeholder']   = __('Username *', 'woocommerce');
    $fields['account']['account_password']['placeholder']   = __('Password *', 'woocommerce');

    return $fields;
}

/**
 *  Remove default country in checkout
 */
add_filter('default_checkout_billing_country', 'change_default_checkout_country');
add_filter('default_checkout_shipping_country', 'change_default_checkout_country');
function change_default_checkout_country()
{
    $user      = get_current_user_id();
    $user_code = get_user_meta($user, 'billing_country');
    if ( isset($user_code) && !empty($user_code) ) {
        return $user_code[0];
    } else {
        return '';
    }
}

/**
 *  Remove default country in checkout
 */
add_filter('default_checkout_billing_country_code', 'change_default_checkout_country_code');
function change_default_checkout_country_code()
{
    $user      = get_current_user_id();
    $user_code = get_user_meta($user, 'billing_country_code');
    if ( isset($user_code) && !empty($user_code) ) {
        return $user_code[0];
    } else {
        return '';
    }
}


// remove quantity in checkout table
function wc_remove_quantity_field_from_checkout( $return )
{
    if ( is_checkout() )
        return false;
}

add_filter('woocommerce_checkout_cart_item_quantity', 'wc_remove_quantity_field_from_checkout', 10, 2);
// rename VAT to Tax
add_filter('woocommerce_countries_tax_or_vat', function ( $return )
{
    return 'VAT';
},         10, 1);
// remove subtotal row from woocommerce emails
add_filter('woocommerce_get_order_item_totals', 'remove_subtotal_from_orders_total_lines', 100, 1);
function remove_subtotal_from_orders_total_lines( $totals )
{
    unset($totals['cart_subtotal']);

    return $totals;
}
/**
 * Checkout page get county code fjr billin code
 */
add_action('wp_ajax_nopriv_append_country_prefix_in_billing_phone', 'country_prefix_in_billing_phone');
add_action('wp_ajax_append_country_prefix_in_billing_phone', 'country_prefix_in_billing_phone');
function country_prefix_in_billing_phone()
{
    $calling_code = '';
    $country_code = isset($_POST['country_code']) ? $_POST['country_code'] : '';
    if ( $country_code ) {
        $calling_code = WC()->countries->get_country_calling_code($country_code);
        $calling_code = is_array($calling_code) ? $calling_code[0] : $calling_code;
    }
    echo $calling_code;
    wp_die();
}

add_action('woocommerce_review_order_before_submit', 'newsletter_add_checkout_checkbox', 10);
/**
 * Add WooCommerce additional Checkbox checkout field
 */
function newsletter_add_checkout_checkbox()
{
    woocommerce_form_field('checkout_checkbox', [ // CSS ID
        'type'        => 'checkbox',
        'class'       => [ 'form-row mycheckbox' ],
        // CSS Class
        'label_class' => [ 'woocommerce-form__label woocommerce-form__label-for-checkbox checkbox' ],
        'input_class' => [ 'woocommerce-form__input woocommerce-form__input-checkbox input-checkbox' ],
        'required'    => false,
        // Mandatory or Optional
        'label'       => '<span>Subscribe to recieve valuable knowledge about photo manipulation straight in your inbox.</span>',
        // Label and Link
    ]);
}

/**
 * Custom icons for payment
 */
add_filter('woocommerce_gateway_icon', function ( $icon, $id )
{
    $get_page      = get_queried_object();
    $cur_page_name = $get_page->post_name;
    if ( $cur_page_name === 'my-account' ) {
        return;
    } else {
        if ( $id === 'paypal' ) {
            return '<div class="woocommerce_gateway_icon_custom">

                        <img src="' . get_template_directory_uri() . '/images/pay-pal.svg" alt="pay pal" class="sv-wc-payment-gateway-icon wc-braintree-credit-card-payment-gateway-icon">

                    </div>';
        } elseif ( $id === 'braintree_credit_card' ) {
            return '<div class="woocommerce_gateway_icon_custom">

                        <img src="' . get_template_directory_uri() . '/images/credit-cart.svg" alt="credit cart" class="sv-wc-payment-gateway-icon wc-braintree-credit-card-payment-gateway-icon">

                    </div>';
        }
    }
},         10, 2);
/**
 * customyse input for password
 */
function woocommerce_register_form_password_repeat()
{
    ?>
    <p class="form-row form-row-wide">
        <label for="reg_password2"><?php _e('Confirm password', 'woocommerce'); ?>
            <span class="required">*</span></label>
        <input type="password" class="input-text" name="password2" id="reg_password2" value="<?php if ( !empty($_POST['password2']) )
            echo esc_attr($_POST['password2']); ?>"/>
    </p>
    <?php
}

add_action('woocommerce_register_form', 'woocommerce_register_form_password_repeat');
/**
 * Add a second password field to the checkout page in WC 3.x.
 */
add_filter('woocommerce_checkout_fields', 'wc_add_confirm_password_checkout', 10, 1);
function wc_add_confirm_password_checkout( $checkout_fields )
{
    if ( get_option('woocommerce_registration_generate_password') == 'no' ) {
        $checkout_fields['account']['account_password2'] = [
            'type'        => 'password',
            'label'       => __('Confirm password', 'woocommerce'),
            'required'    => true,
            'placeholder' => _x('Re-enter Password', 'placeholder', 'woocommerce')
        ];
    }

    return $checkout_fields;
}

/**
 * Check the password and confirm password fields match before allow checkout to proceed.
 */
add_action('woocommerce_after_checkout_validation', 'wc_check_confirm_password_matches_checkout', 10, 2);
function wc_check_confirm_password_matches_checkout( $posted )
{
    $checkout = WC()->checkout;
    if ( !is_user_logged_in() && ( $checkout->must_create_account || !empty($posted['createaccount']) ) ) {
        if ( strcmp($posted['account_password'], $posted['account_password2']) !== 0 ) {
            wc_add_notice(__('Passwords do not match.', 'woocommerce'), 'error');
        }
    }
}

/**
 * costom hints for password
 */
add_action('wp_enqueue_scripts', 'my_strength_meter_localize_script');
function my_strength_meter_localize_script()
{
    wp_localize_script('password-strength-meter', 'pwsL10n', [
        'short'    => 'VERY WEAK',
        'bad'      => __('WEAK', 'woocommerce'),
        'good'     => __('MEDIUM', 'woocommerce'),
        'strong'   => __('STRONG', 'woocommerce'),
        'mismatch' => __('They are completely different, come on!', 'woocommerce')
    ]);
}

/**
 *  remove symbol from hints
 */
add_filter('woocommerce_get_script_data', 'my_strength_meter_custom_strings', 10, 2);
function my_strength_meter_custom_strings( $data, $handle )
{
    if ( 'wc-password-strength-meter' === $handle ) {
        $data_new = [
            'i18n_password_error' => '',
            'i18n_password_hint'  => ''
        ];
        $data     = array_merge($data, $data_new);
    }

    return $data;
}

/**
 *  create custom selector billing country code billing form
 */
add_filter('woocommerce_billing_fields', 'custom_woocommerce_billing_country_code');
function custom_woocommerce_billing_country_code( $fields )
{
    $wc_countries = new WC_Countries();
    $countries    = $wc_countries->get_countries();
    $codes        = [];
    array_push($codes, 'Country code');
    foreach ( $countries as $key => $country ) {
        $calling_code = WC()->countries->get_country_calling_code($key);
        if ( !empty($calling_code) ) {
            $codes[ $key ] = $country . ' ' . $calling_code;
        }
    }
    array_unshift($codes, '');
    $fields['billing_country_code'] = [
        'label'       => __('Country code', 'woocommerce'), // Add custom field label
        'placeholder' => _x('Your Country code here....', 'placeholder', 'woocommerce'), // Add custom field placeholder
        'type'        => 'select',
        'required'    => true,
        'clear'       => false,
        'class'       => [ 'checkout_country_code' ],
        'options'     => $codes,
    ];

    return $fields;
}

/**
 *  create custom selector billing country code checkout page
 */
add_filter('woocommerce_checkout_fields', 'custom_woocommerce_checkout_billing_country_code');
function custom_woocommerce_checkout_billing_country_code( $fields )
{
    $wc_countries = new WC_Countries();
    $countries    = $wc_countries->get_countries();
    $codes        = [];
    array_push($codes, 'Country code');
    foreach ( $countries as $key => $country ) {
        $calling_code = WC()->countries->get_country_calling_code($key);
        if ( !empty($calling_code) ) {
            $codes[ $key ] = $country . ' ' . $calling_code;
        }
    }
    $fields['billing']['billing_country_code'] = [
        'label'       => __('Country code', 'woocommerce'), // Add custom field label
        'placeholder' => _x('Your Country code here....', 'placeholder', 'woocommerce'), // Add custom field placeholder
        'type'        => 'select',
        'required'    => true,
        'clear'       => false,
        'class'       => [ 'checkout_country_code' ],
        'options'     => $codes
    ];

    return $fields;
}

// BACS payement gateway description: Append custom select field
add_filter('woocommerce_gateway_description', 'gateway_bacs_custom_fields', 20, 2);
function gateway_bacs_custom_fields( $description, $payment_id )
{
    //
    if ( 'bacs' === $payment_id ) {
        ob_start(); // Start buffering
        echo '<div  class="bacs-fields" style="padding:10px 0;">';
        woocommerce_form_field('field_slug', [
            'type'     => 'select',
            'label'    => __("Fill in this field", "woocommerce"),
            'class'    => [ 'form-row-wide' ],
            'required' => false,
            'options'  => [
                ''         => __("Select something", "woocommerce"),
                'choice-1' => __("Choice one", "woocommerce"),
                'choice-2' => __("Choice two", "woocommerce"),
            ],
        ],                     '');
        echo '<div>';
        $description .= ob_get_clean(); // Append buffered content
    }

    return $description;
}

/*

* add phone fields to account details

*/
add_action('woocommerce_edit_account_form', 'add_field_edit_account_form');
function add_field_edit_account_form()
{
    $wc_countries = new WC_Countries();
    $countries    = $wc_countries->get_countries();
    $codes        = [];
    array_push($codes, 'Country code');
    foreach ( $countries as $key => $country ) {
        $calling_code = WC()->countries->get_country_calling_code($key);
        if ( !empty($calling_code) ) {
            $codes[ $key ] = $country . ' ' . $calling_code;
        }
    }
    array_unshift($codes, "");
    woocommerce_form_field('billing_country_code', [
        'label'       => __('Mobile Number', 'woocommerce'),
        // Add custom field label
        'placeholder' => _x('Your Country code here...', 'placeholder', 'woocommerce'),
        // Add custom field placeholder
        'type'        => 'select',
        'required'    => true,
        'clear'       => false,
        'class'       => [
            'checkout_country_code',
            'select2'
        ],
        'options'     => $codes,
    ],                     get_user_meta(get_current_user_id(), 'billing_country_code', true) // get the data
    );
    woocommerce_form_field('billing_phone', [
        'label'       => __('*', 'woocommerce'),
        // Add custom field label
        'placeholder' => _x('Mobile Number', 'placeholder', 'woocommerce'),
        // Add custom field placeholder
        'type'        => 'tel',
        'required'    => true,
        'clear'       => false,
    ],                     get_user_meta(get_current_user_id(), 'billing_phone', true) // get the data
    );
}

/**
 * Step 2. Save field value
 */
add_action('woocommerce_save_account_details', 'save_account_details');
function save_account_details( $user_id )
{
    update_user_meta($user_id, 'billing_country_code', sanitize_text_field($_POST['billing_country_code']));
}

/**
 * Step 3. Make it required
 */
add_filter('woocommerce_save_account_details_required_fields', 'make_field_required');
function make_field_required( $required_fields )
{
    $required_fields['billing_country_code'] = 'Country you want to visit the most';

    return $required_fields;
}

function change_woocommerce_field_markup( $field, $key, $args, $value )
{
    global $wp;
    home_url($wp->request);
    $url_seach = stristr(home_url($wp->request), 'checkout');
    if ( !empty($url_seach) ) {
        if ( $key === 'billing_address_2' ) {
            $args['placeholder'] = 'Address Line 2 (optional)';
            $field               = '<p class="form-row ' . esc_attr(implode(' ', $args['class'])) . '" id="' . esc_attr($args['id']) . '_field" data-priority="10">
                <label class="">' . $args['label'] . '</label>
                <span class="woocommerce-input-wrapper">
                <input type="' . esc_attr($args['type']) . '" class="input-text" name="' . esc_attr($args['id']) . '" id="' . esc_attr($args['id']) . '" placeholder="' . esc_html($args['placeholder']) . '" value="' . esc_html($value) . '" autocomplete="' . $args['autocomplete'] . '">
                </span>
                </p>';
        }
        if ( $key === 'billing_state' || $key === 'billing_address_1' || $key === 'billing_email' || $key === 'account_username' || $key === 'billing_phone' || $key === 'billing_address_1' || $key === 'billing_first_name' || $key === 'billing_last_name' || $key === 'billing_city' || $key === 'billing_postcode' ) {
            $custom_attr = '';
            if ( $key === 'billing_address_1' ) {
                $args['label'] = 'Address Line 1';
            }
            if ( $key === 'billing_postcode' ) {
                $args['label'] = 'ZIP / Postcode';
            }
            if ( $key === 'account_username' ) {
                $args['label'] = 'Username';
            }
            if ( $key === 'billing_phone' ) {
                $args['label'] = 'Phone number';
            }
            if ( $key === 'billing_state' ) {
                $args['label'] = 'State / County / Province';
                $value         = '';
            }
            if ( $key === 'billing_first_name' || $key === 'billing_last_name' ) {
                $custom_attr = 'maxlength="10"';
            }
            $xlength = strlen($custom_attr);
            $field   = '<p class="form-row ' . esc_attr(implode(' ', $args['class'])) . ' input" id="' . esc_attr($args['id']) . '_field" data-priority="10">
                <span class="woocommerce-input-wrapper">
                <input type="' . esc_attr($args['type']) . " " . $custom_attr . '" class="input-text" name="' . esc_attr($args['id']) . '" id="' . esc_attr($args['id']) . '" placeholder=" " value="' . esc_html($value) . '" autocomplete="' . $args['autocomplete'] . '">
                <label style="display: block" class="placeholder">' . $args['label'] . '<abbr class="required" title="required"> *</abbr></label>
                </span>
                </p>';
        }
        return $field;
    } else {
        return $field;
    }
}

add_filter('woocommerce_form_field_text', 'change_woocommerce_field_markup', 10, 4);
add_filter('woocommerce_form_field_email', 'change_woocommerce_field_markup', 10, 4);
add_filter('woocommerce_form_field_tel', 'change_woocommerce_field_markup', 10, 4);
add_filter('woocommerce_form_field_select', 'change_woocommerce_field_markup', 10, 4);
add_filter('woocommerce_form_field_country', 'change_woocommerce_field_markup', 10, 4);
add_filter('woocommerce_form_field_state', 'change_woocommerce_field_markup', 10, 4);
/**
 *  custom placeholder for input password
 */
add_filter('woocommerce_form_field_password', 'filter_woocommerce_form_field_password', 10, 4);
function filter_woocommerce_form_field_password( $field, $key, $args, $value )
{
    if ( $key === 'account_password2' ) {
        $args['placeholder'] = 'Re-enter Password';
    }
    if ( $key === 'account_password' ) {
        $args['placeholder'] = '*********';
    }
    $field = '<p class="' . esc_attr(implode(' ', $args['class'])) . ' input" id="' . esc_attr($args['id']) . '_field" data-priority="">
    <span class="woocommerce-input-wrapper password-input">
        <input type="' . esc_attr($args['type']) . '" class="input-text" name="' . $args['id'] . '" id="' . esc_attr($args['id']) . '" placeholder="' . esc_attr($args['placeholder']) . '" value="" autocomplete="' . esc_attr($args['autocomplete']) . '">
        <span class="show-password-input">
        </span>
        </span>
    </p>';

    return $field;
}

/**
 *  create custom billing field city
 */
add_filter('woocommerce_billing_fields', 'custom_woocommerce_billing_fields');
function custom_woocommerce_billing_fields( $fields )
{
    $fields['billing_city'] = [
        'label'       => __('Town / City', 'woocommerce'),
        'placeholder' => _x('Town / City', 'placeholder', 'woocommerce'),
        'required'    => true,
        'clear'       => true,
        'type'        => 'text',
        'class'       => [],
    ];

    return $fields;
}

/**
 *  Get a formatted billing address for the order
 */
function filter_woocommerce_order_get_formatted_billing_address( $address, $raw_address, $order )
{
    $user_id   = wp_get_current_user()->ID;
    $user_meta = get_user_meta($user_id);
    unset($raw_address['company']);
    $countries_obj   = new WC_Countries();
    $countries_array = $countries_obj->get_countries();
    $country         = $countries_array[ $raw_address['country'] ];
    $name            = $raw_address['first_name'] . ' ' . $raw_address['last_name'];
    $address         = '<p>' . $name . '</p><p>' . $raw_address['address_1'] . '</p><p>' . $raw_address['address_2'] . '</p><p>' . $user_meta['billing_state'][0] . ' ' . $raw_address['postcode'] . '</p><p>' . $country . '</p>';

    return $address;
}

add_filter('woocommerce_order_get_formatted_billing_address', 'filter_woocommerce_order_get_formatted_billing_address', 10, 3);
/**
 * change text button checkout
 */
add_filter('gettext', 'ld_custom_paypal_button_text', 20, 3);
function ld_custom_paypal_button_text( $change_text, $text, $domain )
{
    switch ( $change_text ) {
        case 'Proceed to PayPal':
            $change_text = __('PLACE ORDER', 'woocommerce');
            break;
    }

    return $change_text;
}

/**
 * Filters the error messages displayed above the login form.
 */
add_filter('login_errors', 'login_error_message');
function login_error_message( $error )
{
    //check if that's the error you are looking for
    $pos = strpos($error, 'The username');
    if ( 0 < $pos ) {
        //its the right error so you can overwrite it
        $error = "";
    }

    return $error;
}

/**
 * Fires after a user login has failed.
 */
add_action('wp_login_failed', "loginizer_login_failed_custom");
//add_action('woocommerce_login_failed', 'loginizer_login_failed_custom', 10001);
function loginizer_login_failed_custom( $username, $is_2fa = '' )
{
    global $wpdb, $loginizer, $lz_cannot_login;
    // Some plugins are changing the value for username as null so we need to handle it before using it for the INSERT OR UPDATE query
    if ( empty($username) || is_null($username) ) {
        $username = '';
    }
    $fail_type = 'Login';
    if ( !empty($is_2fa) ) {
        $fail_type = '2FA';
    }
    if ( empty($lz_cannot_login) && empty($loginizer['ip_is_whitelisted']) && empty($loginizer['no_loginizer_logs']) ) {
        $url       = @addslashes(( !empty($_SERVER['HTTPS']) ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
        $url       = esc_url($url);
        $sel_query = $wpdb->prepare("SELECT * FROM `" . $wpdb->prefix . "loginizer_logs` WHERE `ip` = %s", $loginizer['current_ip']);
        $result    = lz_selectquery($sel_query);
        if ( !empty($result) ) {
            $lockout      = floor(( ( $result['count'] + 1 ) / $loginizer['max_retries'] ));
            $update_data  = [
                'username' => $username,
                'time'     => time(),
                'count'    => $result['count'] + 1,
                'lockout'  => $lockout,
                'url'      => $url
            ];
            $where_data   = [ 'ip' => $loginizer['current_ip'] ];
            $format       = [ '%s', '%d', '%d', '%d', '%s' ];
            $where_format = [ '%s' ];
            $wpdb->update($wpdb->prefix . 'loginizer_logs', $update_data, $where_data, $format, $where_format);
            // Do we need to email admin ?
            if ( !empty($loginizer['notify_email']) && $lockout >= $loginizer['notify_email'] ) {
                $lockout_time = $loginizer['lockout_time'];
                if ( $lockout >= $loginizer['max_lockouts'] ) {
                    // extended lockout is in hours so we have to convert to minute
                    $lockout_time = $loginizer['lockouts_extend'];
                }
                $sitename        = lz_is_multisite() ? get_site_option('site_name') : get_option('blogname');
                $mail            = [];
                $mail['to']      = $loginizer['notify_email_address'];
                $mail['subject'] = 'Failed ' . $fail_type . ' Attempts from IP ' . $loginizer['current_ip'] . ' (' . $sitename . ')';
                $mail['message'] = 'Hi,



' . ( $result['count'] + 1 ) . ' failed ' . strtolower($fail_type) . ' attempts and ' . $lockout . ' lockout(s) from IP ' . $loginizer['current_ip'] . ' on your site :

' . home_url() . '



Last ' . $fail_type . ' Attempt : ' . date('d/M/Y H:i:s P', time()) . '

Last User Attempt : ' . $username . '

IP has been blocked until : ' . date('d/M/Y H:i:s P', time() + $lockout_time) . '



Regards,

Loginizer';
                @wp_mail($mail['to'], $mail['subject'], $mail['message']);
            }
        } else {
            $result          = [];
            $result['count'] = 0;
            $insert_data     = [
                'username' => $username,
                'time'     => time(),
                'count'    => 1,
                'ip'       => $loginizer['current_ip'],
                'lockout'  => 0,
                'url'      => $url
            ];
            $format          = [ '%s', '%d', '%d', '%s', '%d', '%s' ];
            $wpdb->insert($wpdb->prefix . 'loginizer_logs', $insert_data, $format);
        }
        // We need to add one as this is a failed attempt as well
        $result['count']           = $result['count'] + 1;
        $loginizer['retries_left'] = ( $loginizer['max_retries'] - ( $result['count'] % $loginizer['max_retries'] ) );
        $loginizer['retries_left'] = $loginizer['retries_left'] == 'Incorrect login details. ' . $loginizer['max_retries'] ? 0 : 'Incorrect login details. ' . $result['count'] % $loginizer['max_retries'];
    }
}

/**
 * add cart icon
 */
add_filter('wc_stripe_cc_icon_template_args', function ( $args )
{
    $args['cards'] = [];
    $args['icons'] = [];
    $url_image     = get_template_directory_uri() . '/images/new-cart.svg';
    array_push($args['cards'], 'credit cart');
    array_push($args['icons'], $url_image);

    return $args;
});
/**
 * define the wpo_wcpdf_document_is_allowed callback
 */
add_filter('wpo_wcpdf_document_is_allowed', 'wpo_wcpdf_allow_invoice_if_customer_has_vat_number', 10, 2);
function wpo_wcpdf_allow_invoice_if_customer_has_vat_number( $allowed, $document )
{
    if ( $document->type == 'invoice' ) {
        if ( $order = $document->order ) {
            $allowed = ( !empty($order->get_meta('billing_eu_vat_number')) ) ? true : false;
        }
    }

    return $allowed;
}

/**
 * change symbol for invoice
 */
add_filter('woocommerce_currency_symbol', 'change_currency_symbol', 10, 2);
function change_currency_symbol( $symbols, $currency )
{
    if ( 'USD' === $currency ) {
        return '$ ';
    }
    if ( 'EUR' === $currency ) {
        return '&#8364; ';
    }

    return $symbols;
}

/**
 * events in chekout page form
 */
add_action('woocommerce_checkout_update_order_review', function ( $data )
{
    global $WOOCS;
    if ( is_string($data) ) {
        parse_str($data, $data);
    }
    $_currency = $WOOCS->get_currency_by_country($data['billing_country']);
    $WOOCS->storage->set_val('woocs_user_country', $data['billing_country']);
    if ( !empty($_currency) ) {
        $WOOCS->set_currency($_currency);
    }
},         9999);
// Start session on init hook wp.
add_action('init', 'wpse16119876_init_session');
function wpse16119876_init_session()
{
    if ( !session_id() ) {
        session_start();
    }
}
//get tax rate woocommerce
function get_tax_rate()
{
    $post_data = isset($_POST['post_data']) ? $_POST['post_data'] : '';
    parse_str($post_data, $output);
    $countries_obj = new WC_Countries();
    $countries     = $countries_obj->__get('countries');
    $continents    = $countries_obj->get_continents();
    $country       = isset($output['billing_country']) ? $output['billing_country'] : false;
    $all_tax_rates = [];
    $tax_classes   = WC_Tax::get_tax_classes(); // Retrieve all tax classes.
    if ( !in_array('', $tax_classes) ) { // Make sure "Standard rate" (empty class name) is present.
        array_unshift($tax_classes, '');
    }
    foreach ( $tax_classes as $tax_class ) { // For each tax class, get all rates.
        $taxes         = WC_Tax::get_rates_for_tax_class($tax_class);
        $all_tax_rates = array_merge($all_tax_rates, $taxes);
    }
    $curr_tax = '';
    foreach ( $all_tax_rates as $key => $item ) {
        $get_name_code = $item->tax_rate_country;
        if ( $get_name_code === WC()->customer->get_shipping_country() ) {
            $curr_tax = number_format($item->tax_rate, 0);
        };
    }
    $curr_tax = $curr_tax == '' ? 0 : $curr_tax;

    return $curr_tax;
}
//create func filter default checkout billing country by WC_geolocation
function filter_default_checkout_billing_country( $default )
{
    // If the user already exists, don't override country
    if ( WC()->customer->get_is_paying_customer() ) {
        return $default;
    } elseif ( class_exists('WC_Geolocation') ) {
        // Get location country
        $location = WC_Geolocation::geolocate_ip();
        if ( isset($location['country']) ) {
            return $location['country'];
        } else {
            $default = null;
        }
    } else {
        $default = null;
    }

    return $default;
}

add_filter('default_checkout_billing_country', 'filter_default_checkout_billing_country', 10, 1);
function njengah_change_email_tax_label( $label )
{
    $label = '';

    return $label;
}

add_filter('woocommerce_countries_ex_tax_or_vat', 'njengah_change_email_tax_label');
/**
 * popp up for custom lending page
 */
add_action('wp_ajax_nopriv_get_post_by_url', 'get_post_by_url');
add_action('wp_ajax_get_post_by_url', 'get_post_by_url');
function get_post_by_url()
{
    $url     = sanitize_text_field($_POST['url']);
    $id_post = url_to_postid($url);
    ?>

    <div class="privacy_policy-bg popp_landing">

        <div class="privacy_policy-content">

            <span class="close-privacy_policy"><i class='icon-close-modal'></i></span>

            <?php
            $args     = [
                'p'         => $id_post, // ID of a page, post, or custom type
                'post_type' => 'any'
            ];
            $my_posts = new WP_Query($args);
            if ( $my_posts->have_posts() ) {
                while ( $my_posts->have_posts() ) {
                    $my_posts->the_post(); ?>

                    <h3> <?php the_title(); ?></h3>

                    <?php the_content(); ?>

                <?php }
                wp_reset_postdata();
            }
            ?>

        </div>

    </div>

    <?php
    wp_die();
}

/* Describe what the code snippet does so you can remember later on */
add_action('wp_footer', 'wp_footer_custom');
function wp_footer_custom()
{
    $args   = [
        'customer_id' => get_current_user_id(),
        'limit'       => -1, // to retrieve _all_ orders by this user
    ];
    $orders = wc_get_orders($args);
    if ( is_user_logged_in() ) { ?>

        <style>

           .header-menu-custom-class .elementor-nav-menu > li:first-child{
              display:none;
           }

        </style>

    <?php }
    ?>

    <div class="refund_policy-bg background">

        <div class="refund_policy-content popup_content">

            <span class="close-refund_policy"><i class='icon-close-modal'></i></span>

            <?php
            $args     = [
                'p'         => 4406,
                'post_type' => 'any'
            ];
            $my_posts = new WP_Query($args);
            if ( $my_posts->have_posts() ) {
                while ( $my_posts->have_posts() ) {
                    $my_posts->the_post(); ?>

                    <?php the_content(); ?>

                <?php }
                wp_reset_postdata();
            }
            ?>

        </div>

    </div>

    <div class="privacy_policy-bg background">

        <div class="privacy_policy-content popup_content">

            <span class="close-privacy_policy"><i class='icon-close-modal'></i></span>

            <?php
            $args     = [
                'p'         => 4304,
                'post_type' => 'any'
            ];
            $my_posts = new WP_Query($args);
            if ( $my_posts->have_posts() ) {
                while ( $my_posts->have_posts() ) {
                    $my_posts->the_post(); ?>

                    <?php the_content(); ?>

                <?php }
                wp_reset_postdata();
            }
            ?>

        </div>

    </div>

    <div class="woocommerce-terms-and-conditions-bg-custom background">

        <div class="woocommerce-terms-and-conditions-custom popup_content" style="overflow: auto;">

            <span class="close-term-cond"><i class='icon-close-modal'></i></span>

            <?php
            $args     = [
                'p'         => 1787,
                'post_type' => 'any'
            ];
            $my_posts = new WP_Query($args);
            if ( $my_posts->have_posts() ) {
                while ( $my_posts->have_posts() ) {
                    $my_posts->the_post(); ?>

                    <?php the_content(); ?>

                <?php }
                wp_reset_postdata();
            }
            ?>

        </div>

    </div>

    <?php
}

;



