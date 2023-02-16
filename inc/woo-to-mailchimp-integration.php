<?php
require get_template_directory() . '/inc/MailChimp.php';
// add_action('woocommerce_checkout_order_processed', 'get_info');
add_action('woocommerce_checkout_update_order_meta', 'get_info');
// add_action('woocommerce_checkout_process', 'get_info');

function get_info($order_id)
{
    if (isset($_POST['checkout_checkbox'])) {
        global $woocommerce;
        $mailchimp_switcher = get_theme_mod('mailchimp_api_checkbox'); //get customizer checkbox 
        $API = get_theme_mod('mailchimp_api_key'); //get api key from customizer

        if (isset($mailchimp_switcher) && $mailchimp_switcher == true) {
            if (isset($API) && !empty($API)) {
                $MailChimp = new \DrewM\MailChimp\MailChimp($API);
                $order = new WC_Order($order_id);
                $firstname = $order->billing_first_name;
                $lastname = $order->billing_last_name;
                $email = $order->billing_email;

                $lists = $MailChimp->get('lists');
                $list_id = $lists['lists'][0]['id']; //'085b2afbc7';  // get first List Key
                $product_name = array();

                foreach ($order->get_items() as $key => $item) {
                    $product_name[] = $item->get_name(); //get all products from order
                }

                $result = $MailChimp->post("lists/$list_id/members", [ //add user to mailchimp
                    'email_address' => $email,
                    'status'        => 'subscribed',
                    'tags'            => $product_name,
                    'merge_fields'     => array(
                        'FNAME'        => $firstname,
                        'LNAME'        => $lastname
                    ),
                ]);

                if (isset($result['title']) && $result['title'] == "Member Exists") {
                    $result = $MailChimp->put("lists/$list_id/members/$email", [ //update user when he already exist in mailchimp 
                        'email_address' => $email,
                        'status'        => 'subscribed',
                        'tags'          => $product_name,
                        'merge_fields'  => array(
                            'FNAME'     => $firstname,
                            'LNAME'     => $lastname
                        ),
                    ]);
                }
            }
        }
    }
}
