<?php

/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package pigeonpixel
 */
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly.
class Theme_Functions
{
    public static $path = null;

    public static $url = null;

    protected static $instance = null;

    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function __construct()
    {
        self::$path = get_template_directory();
        self::$url = get_template_directory_uri();
        add_action('wp_head', [$this, 'pigeonpixel_pingback_header']);
        add_action('wp_footer', [$this, 'gtag_code'], 30);
        add_action('wp_footer', [$this, 'finish_popup'], 30);
        add_action('init', [$this, 'init']);
        add_action('wp_enqueue_scripts', [$this, 'theme_scripts']);
        add_action('admin_head-nav-menus.php', [$this, 'wl_loginout_add_nav_menu_metabox']);
        add_action('woocommerce_save_account_details', [$this, 'my_account_saving_billing_mobile_phone'], 20, 1);
        add_action('woocommerce_save_account_details', [
            $this,
            'custom__woocommerce_save_account_details__redirect'
        ],         PHP_INT_MAX, 1);
        add_action('woocommerce_account_my-messages_endpoint', [$this, 'wl_custom_my_messages_content']);
        add_action('woocommerce_save_account_details', [$this, 'cssigniter_save_billing_address']);
        add_action('woocommerce_customer_save_address', [$this, 'cssigniter_save_billing_address']);
        add_action('template_redirect', [$this, 'template_redirect_fn']);
        add_action('woocommerce_thankyou', [$this, 'update_order_after_successful_payment']);
        add_action('add_meta_boxes', [$this, 'add_meta_boxes']);
        add_action('wp_ajax_lesson_item_complate', [$this, 'lesson_item_complate']);
        add_action('wp_ajax_nopriv_lesson_item_complate', [$this, 'lesson_item_complate']);
        // Save comment
        add_action('wp_ajax_save_comment', [$this, 'save_comment']);
        add_action('wp_ajax_nopriv_save_comment', [$this, 'save_comment']);
        add_filter('tiny_mce_plugins', [$this, 'disable_emojis_tinymce']);
        add_filter('wp_resource_hints', [$this, 'disable_emojis_remove_dns_prefetch'], 10, 2);
        add_filter('body_class', [$this, 'additionall_body_classes']);
        add_filter('show_admin_bar', '__return_false');
        add_filter('sensei_user_quiz_status', [$this, 'manage_notifications'], PHP_INT_MAX, 1);
        add_filter('wp_nav_menu_items', 'do_shortcode');
        add_filter('wp_setup_nav_menu_item', [$this, 'wl_loginout_nav_menu_type_label']);
        add_filter('wp_setup_nav_menu_item', [$this, 'wl_loginout_setup_nav_menu_item']);
        // add_filter('login_redirect', [$this, 'wl_loginout_login_redirect_override'], 11, 3);
        add_filter('woocommerce_login_redirect', [$this, 'login_redirect'], 1, 2);
        add_filter('comment_form_defaults', [$this, 'placeholder_comment_form_field']);
        add_filter('woocommerce_account_menu_items', [$this, 'rename_downloads']);
        add_filter('woocommerce_account_menu_items', [$this, 'affiliate_home_link'], 10, 1);
        add_filter('woocommerce_get_query_vars', [$this, 'wl_custom_my_messages_query_vars'], 0);
        add_filter('comment_post_redirect', [$this, 'redirect_after_comment']);
        add_filter('woocommerce_default_address_fields', [$this, 'custom_default_address_fields'], 20, 1);
        add_filter('woocommerce_lost_password_message', [$this, 'filter_woocommerce_lost_password_message'], 10, 1);
        //add_filter('sensei_send_emails', [ $this, 'custom_wp_mail_from' ]); Sending messages from the student's personal account to the teacher
        add_filter('site_transient_update_plugins', [$this, 'disable_plugin_updates']);
        add_shortcode('username', [$this, 'name_shortcode']);
    }

    /**
     * Adds custom classes to the array of body classes.
     *
     * @param array $classes Classes for the body element.
     *
     * @return array
     */
    public function additionall_body_classes($classes)
    {
        // Adds a class of hfeed to non-singular pages.
        if (!is_singular())
            $classes[] = 'hfeed';
        // Adds a class of no-sidebar when there is no sidebar present.
        if (!is_active_sidebar('sidebar-1'))
            $classes[] = 'no-sidebar';

        return $classes;
    }

    /**
     * Add a pingback url auto-discovery header for single posts, pages, or attachments.
     *
     * @return void
     */
    public function pigeonpixel_pingback_header()
    {
        if ('production' === wp_get_environment_type()) {
?>
<!-- Meta Pixel Code -->
<script>
! function(f, b, e, v, n, t, s) {
    if (f.fbq) return;
    n = f.fbq = function() {
        n.callMethod ?
            n.callMethod.apply(n, arguments) : n.queue.push(arguments)
    };
    if (!f._fbq) f._fbq = n;
    n.push = n;
    n.loaded = !0;
    n.version = '2.0';
    n.queue = [];
    t = b.createElement(e);
    t.async = !0;
    t.src = v;
    s = b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t, s)
}(window, document, 'script',
    'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '247460283482887');
fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
        src="https://www.facebook.com/tr?id=247460283482887&ev=PageView&noscript=1" /></noscript>
<!-- End Meta Pixel Code -->
<?php
        }
        if (is_singular() && pings_open()) {
            printf('<link rel="pingback" href="%s">', esc_url(get_bloginfo('pingback_url')));
        }
        // Hide menu item from unlogged users
        $current_user_id = get_current_user_id();
        $all_ids = get_posts([
            'post_type' => 'product',
            'numberposts' => -1,
            'post_status' => 'publish',
            'fields' => 'ids',
        ]);
        $products = [];
        foreach ($all_ids as $key => $id) {
            if (wc_customer_bought_product('', $current_user_id, $id)) {
                $products[$key] = $id;
            }
        }
        if (empty($products)) { ?>

<style>
.logged-in-menu_curse {
    display: none !important;
}
</style>

<?php }
        if (!is_user_logged_in()) { ?>

<style>
.logged-in-menu {
    display: none !important;
}
</style>

<?php }
        // Styling for coupon block when user is logged in
        if (is_user_logged_in()) { ?>

<style>
@media (max-width:992px) {
    .woocommerce-page.woocommerce-checkout .woocommerce form.checkout_coupon {
        top: 709px;
    }
}

@media (max-width:453px) {
    .woocommerce-page.woocommerce-checkout .woocommerce form.checkout_coupon {
        top: 697px;
    }
}

@media (max-width:322px) {
    .woocommerce-page.woocommerce-checkout .woocommerce form.checkout_coupon {
        top: 736px;
    }
}
</style>

<?php }
        if (strpos(get_permalink(), '/my-courses') !== false) { ?>

<style>
div[data-elementor-type="wp-page"] .elementor-widget,
div[data-elementor-type="wp-page"] .elementor-widget-wrap,
div[data-elementor-type="wp-page"] .elementor-column,
div[data-elementor-type="wp-page"] .elementor-section .elementor-container {
    position: static;
}

.elementor-section {
    overflow: hidden;
}

.sensei #my-courses .sensei-message.alert {
    padding: 12px 0;
    max-width: 110vw;
    left: 0;
    top: 0px;
    text-align: center;
    width: 100vw;
    display: block;
    margin: 0;
}

.sensei #my-courses div.sensei-message.alert:before {
    left: calc(50% - 200px);
}

@media all and (max-width:440px) {
    .sensei #my-courses .sensei-message.alert {
        font-size: 12px
    }

    .sensei #my-courses div.sensei-message.alert:before {
        left: calc(50% - 151px);
    }
}
</style>

<?php }
    }

    /**NOTE FINISH POPP
     * Add Google Analytics script
     *
     * @return void
     */
    public function finish_popup()
    {
        global $post;
        if ($post->post_type === 'lesson') {
            $feedpack_status = get_user_meta(get_current_user_id(), 'feedpack_complate');
            $feedpack_status = !empty($feedpack_status[0]) ? $feedpack_status[0] : '';
            if ('complate' !== $feedpack_status) {
            ?>
<div class='popp-finish'>
    <div class="popp-finish-form-wrapper">
        <h3><?php _e('CONGRATULATIONS ON COMPLETING THE COURSE!', 'pigeonpixel') ?></h3>
        <p><?php _e('If you found this course helpful, please leave a review. We’d love for you to share your experience with other artists like yourself.', 'pigeonpixel') ?>
        </p>
        <?php echo do_shortcode('[ratemypost id="1590"]'); ?>
        <form action="#">
            <span class="close-form-popp"><i class='icon-close-modal'></i></span>
            <label for="title"><?php _e('Write a short title for your review', 'pigeonpixel') ?></label>
            <input name='title' type="text">
            <label for="feedback"><?php _e('Write your review'); ?></label>
            <textarea rows="6" cols="50" name="feedback" id="feedback" class="form-control" rows="3"
                required="required"></textarea>
            <input class="form-sunmit-popp" type="submit" placeholder="send" value="SUBMIT REVIEW">
        </form>
    </div>
</div>
<?php }
        }
    }

    /**
     * Add Google Analytics script
     *
     * @return void
     */
    public function gtag_code()
    {

        if ('production' === wp_get_environment_type()) {
            ?>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-101934910-18"></script>
<script>
window.dataLayer = window.dataLayer || [];

function gtag() {
    dataLayer.push(arguments);
}
gtag('js', new Date());
gtag('config', 'UA-101934910-18');
</script>
<?php
        }
    }

    /**
     * Init
     *
     * @return void
     */
    public function init()
    {
        // Disable emojis
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('admin_print_scripts', 'print_emoji_detection_script');
        remove_action('wp_print_styles', 'print_emoji_styles');
        remove_action('admin_print_styles', 'print_emoji_styles');
        remove_filter('the_content_feed', 'wp_staticize_emoji');
        remove_filter('comment_text_rss', 'wp_staticize_emoji');
        // remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
        // Register new endpoint slug to use for My Account page
        add_rewrite_endpoint('my-messages', EP_ROOT | EP_PAGES);
    }

    /**
     * Filter function used to remove the tinymce emoji plugin.
     *
     * @param array $plugins
     *
     * @return array
     */
    public function disable_emojis_tinymce($plugins)
    {
        if (is_array($plugins))
            return array_diff($plugins, ['wpemoji']);

        return [];
    }

    /**
     * Remove emoji CDN hostname from DNS prefetching hints.
     *
     * @param array  $urls          URLs to print for resource hints.
     * @param string $relation_type The relation type the URLs are printed for.
     *
     * @return array
     */
    public function disable_emojis_remove_dns_prefetch($urls, $relation_type)
    {
        if ('dns-prefetch' == $relation_type) {
            $emoji_svg_url_bit = 'https://s.w.org/images/core/emoji/';
            foreach ($urls as $key => $url) {
                if (strpos($url, $emoji_svg_url_bit) !== false) {
                    unset($urls[$key]);
                }
            }
        }

        return $urls;
    }

    /**
     * Enqueue theme styles
     *
     * @return void
     */
    public function theme_scripts()
    {
        wp_deregister_style('dashicons');
        wp_dequeue_style('wp-block-library');
        wp_dequeue_style('wp-block-library-theme');
        wp_dequeue_style('wc-block-style');
        wp_enqueue_style('woocommerce_css', self::$url . '/css/woocommerce.css');
        wp_enqueue_style('sensei_css', self::$url . '/css/sensei.css');
        wp_enqueue_style('style-form-logo-courses', self::$url . '/sensei/user/style-form.css');
        if (is_singular('lesson') || is_singular('quiz')) {
            wp_enqueue_style('lesson', self::$url . '/css/lesson.css');
        }
        wp_enqueue_script('password-strength-mete-custom', self::$url . '/js/payment-str.js', false, false, true);
        wp_enqueue_script('player', self::$url . '/js/playaer.js', false, false, true);
        wp_enqueue_script('woocommerce_js', self::$url . '/js/woocommerce.js');
        wp_enqueue_script('main_js', self::$url . '/js/main.js', false, false, true);
        wp_localize_script('main_js', 'theme_data', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'labels' => [
                'comment_added' => __('Comment successfully added', 'pigeonpixel'),
                'invalid_filetype' => __('Invalid file type', 'pigeonpixel'),
                'submit' => __('Submit', 'pigeonpixel'),
                'reset_quiz' => __('RETAKE QUIZ', 'pigeonpixel'),
                'reset_lesson' => __('Retake lesson', 'pigeonpixel'),
                'images_limit' => __('You can upload only 3 images', 'pigeonpixel'),
                'mark_as_complete' => __('Mark as complete', 'pigeonpixel'),
                'images_limit' => __('You can upload only 3 images', 'pigeonpixel'),
            ],
        ]);
        /// only for staging site / delete on prod
        wp_enqueue_style('wpdiscuss-style', 'https://www.staging2.pigeonpixel.com/wp-content/plugins/wpdiscuz/themes/default/wpdiscuz-frontend-css.min.css');
        /// only for staging site / delete on prod
    }

    /**
     * Manage Sensei notifications
     *
     * @param array $data
     *
     * @return array
     */
    public function manage_notifications($data)
    {
        if (isset($data['message']) && 'You have not taken this lesson\'s quiz yet' == $data['message'])
            return [];
        if (isset($data['message']) && false !== strpos($data['message'], 'Congratulations! You have completed this lesson.')) {
            $data['message'] = __('You have completed this lesson.', 'pigeonpixel');
        }

        return $data;
    }



    /**
     * Adding a shortcode to display username
     *
     * @return string
     */
    public function name_shortcode()
    {
        if (is_user_logged_in()) {
            global $current_user;
            //get_currentuserinfo();
            $name = $current_user->user_firstname;
            $lenght = strlen($name);
            if ($lenght > 15) {
                return mb_strimwidth($current_user->user_firstname, 0, 15) . '...';
            } else {
                return $current_user->user_firstname . '<b class="dropdown-arrow-custom"></b>';
            }
        }

        return '';
    }

    /**
     * Add a metabox in admin menu page
     *
     * @return void
     */
    public function wl_loginout_add_nav_menu_metabox()
    {
        add_meta_box('wl_loginout', __('Login/Logout', 'wl_loginout'), [
            $this,
            'wl_loginout_nav_menu_metabox'
        ],           'nav-menus', 'side', 'default');
    }

    /**
     * Log in/out metaboxes
     *
     * @param $object
     *
     * @return void
     */
    public function wl_loginout_nav_menu_metabox($object)
    {
        global $nav_menu_selected_id;
        $elems = [
            '#wl_loginout#' => __('Login', 'wl_loginout') . '|' . __('Logout', 'wl_loginout')
        ];
        $elems_obj = [];
        foreach ($elems as $value => $title) {
            $elems_obj[$title] = new wl_loginoutLogItems();
            $elems_obj[$title]->object_id = esc_attr($value);
            $elems_obj[$title]->title = esc_attr($title);
            $elems_obj[$title]->url = esc_attr($value);
        }
        $walker = new Walker_Nav_Menu_Checklist([]);
        ?>

<div id="login-links" class="loginlinksdiv">

    <div id="tabs-panel-login-links-all" class="tabs-panel tabs-panel-view-all tabs-panel-active">

        <ul id="login-linkschecklist" class="list:login-links categorychecklist form-no-clear">

            <?php echo walk_nav_menu_tree(array_map('wp_setup_nav_menu_item', $elems_obj), 0, (object) ['walker' => $walker]); ?>

        </ul>

    </div>

    <p class="button-controls">

        <span class="add-to-menu">

            <input type="submit" <?php disabled($nav_menu_selected_id, 0); ?>
                class="button-secondary submit-add-to-menu right"
                value="<?php esc_attr_e('Add to Menu', 'wl_loginout'); ?>" name="add-login-links-menu-item"
                id="submit-login-links" />



            <span class="spinner"></span>

        </span>

    </p>

</div>

<?php
    }

    /**
     * Modify the "type_label"
     *
     * @param object $menu_item
     *
     * @return object
     */
    public function wl_loginout_nav_menu_type_label($menu_item)
    {
        $elems = ['#wl_loginout#'];
        if (isset($menu_item->object, $menu_item->url) && 'custom' == $menu_item->object && in_array($menu_item->url, $elems)) {
            $menu_item->type_label = __('Dynamic Link', 'wl_loginout');
        }

        return $menu_item;
    }

    /**
     * Used to return the correct title for the double login/logout menu item
     *
     * @param string $title
     *
     * @return string
     */
    public function wl_loginout_loginout_title($title)
    {
        $titles = explode('|', $title);
        if (!is_user_logged_in()) {
            return esc_html(isset($titles[0]) ? $titles[0] : __('Login', 'wl_loginout'));
        } else {
            return esc_html(isset($titles[1]) ? $titles[1] : __('Logout', 'wl_loginout'));
        }
    }

    /**
     * The main code, this replace the #keyword# by the correct links with nonce ect
     *
     * @param object $item
     *
     * @return void
     */
    public function wl_loginout_setup_nav_menu_item($item)
    {
        global $pagenow;
        global $wp;
        if ($pagenow != 'nav-menus.php' && !defined('DOING_AJAX') && isset($item->url) && strstr($item->url, '#wl_loginout') != '') {
            $login_page_url = get_option('wl_loginout_login_page_url', '/my-account?redirect_to=' . home_url($wp->request) . '');
            $logout_redirect_url = get_option('wl_loginout_logout_redirect_url', home_url($wp->request));
            $item->url = (is_user_logged_in()) ? wp_logout_url($logout_redirect_url) : $login_page_url;
            $item->title = $this->wl_loginout_loginout_title($item->title);
        }

        return $item;
    }

    /**
     * If the login failed, or if the user is an Admin - let's not override the login redirect
     *
     * @param string  $redirect_to
     * @param array   $request
     * @param WP_User $user
     *
     * @return array
     */
    // public function wl_loginout_login_redirect_override($redirect_to, $request, $user)
    // {
    //     if (!is_a($user, 'WP_User') || user_can($user, 'manage_options')) {
    //         return $redirect_to;
    //     }

    //     return $request;
    // }
    function login_redirect($redirect_to, $user) {

        if (wc_customer_bought_product('', $user->ID, 1618)) {
            $redirect_to = home_url('/my-courses');
            return $redirect_to;
        }
        else {
            $redirect_to = home_url();
            return $redirect_to;
        }
       
    }

    /**
     * Comment Form Placeholder Comment Field
     *
     * @param array $fields
     *
     * @return array
     */
    public function placeholder_comment_form_field($fields)
    {
        $replace_comment = __('Comment', 'pigeonpixel');
        $fields['comment_field'] = '<span class="comment-name">Comment</span><p class="comment-form-comment"><label for="comment"></label><textarea  placeholder="|" id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea></p>';

        return $fields;
    }

    /**
     * Manage nav menunu
     *
     * @param array $menu_links
     *
     * @return array
     */
    public function rename_downloads($menu_links)
    {
        $menu_links['orders'] = 'My Orders';
        $menu_links['my-messages'] = 'My messages';
        $menu_links['edit-address'] = 'Billing Details';
        unset($menu_links['dashboard']); // Remove Dashboard
        unset($menu_links['downloads']); // Disable Downloads

        return $menu_links;
    }

    /**
     * Save the mobile phone value to user data
     *
     * @param int $user_id
     *
     * @return void
     */
    public function my_account_saving_billing_mobile_phone($user_id)
    {
        if (isset($_POST['billing_mobile_phone']) && !empty($_POST['billing_mobile_phone']))
            update_user_meta($user_id, 'billing_mobile_phone', sanitize_text_field($_POST['billing_mobile_phone']));
    }

    /**
     * Redirect submit
     *
     * @param int $user_id
     *
     * @return void
     */
    public function custom__woocommerce_save_account_details__redirect($user_id)
    {
        wp_safe_redirect(wc_get_endpoint_url('edit-account'));
        exit();
    }

    /**
     * Affiliate home link
     *
     * @param array $menu_links
     *
     * @return array
     */
    public function affiliate_home_link($menu_links)
    {
        $new_nav = [
            'edit-account' => $menu_links['edit-account'],
            'my-messages' => 'My messages',
            'orders' => $menu_links['orders'],
            'payment-methods' => $menu_links['payment-methods'],
            'edit-address' => $menu_links['edit-address'],
            'customer-logout' => $menu_links['customer-logout'],
        ];

        return $new_nav;
    }

    /**
     * Add query vars
     *
     * @param array $new_nav
     *
     * @return array
     */
    public function wl_custom_my_messages_query_vars($new_nav)
    {
        $new_nav['my-messages'] = 'my-messages';

        return $new_nav;
    }

    /**
     * Add content to the new endpoint
     *
     * @return void
     */
    public function wl_custom_my_messages_content()
    {
        get_template_part('woocommerce/myaccount/my-messages');
    }

    /**
     * Redirect after ave comment
     *
     * @param string $location
     *
     * @return string
     */
    public function redirect_after_comment($location)
    {
        if (false !== strpos($_SERVER['HTTP_REFERER'], 'my-account/my-messages')) {
            return $_SERVER["HTTP_REFERER"];
        }

        return $location;
    }

    /**
     * Account Edit Adresses: Remove and reorder addresses fields
     *
     * @param array $fields
     *
     * @return array
     */
    public function custom_default_address_fields($fields)
    {
        // Only on account pages
        if (!is_account_page())
            return $fields;
        $priority = 0;
        $new_fields = [];
        $sorted_fields = [
            'first_name',
            'last_name',
            'billing_email',
            'company',
            'billing_eu_vat_number',
            'country',
            'address_1',
            'address_2',
            'state',
            'postcode'
        ];
        foreach ($sorted_fields as $key_field) {
            $field_current = !empty($fields[$key_field]) ? $fields[$key_field] : [];
            $priority += 20; // keep space for email and phone fields
            $new_fields[$key_field] = $field_current;
            $new_fields[$key_field]['priority'] = $priority;
        }

        return $new_fields;
    }

    /**
     * Update user meta
     *
     * @param int $user_id
     *
     * @return void
     */
    public function cssigniter_save_billing_address($user_id)
    {
        if (isset($_POST['country_cod'])) {
            update_user_meta($user_id, 'country_cod', sanitize_text_field($_POST['country_cod']));
        }
        if (isset($_POST['billing_phone'])) {
            update_user_meta($user_id, 'billing_phone', sanitize_text_field($_POST['billing_phone']));
        }
    }

    /**
     * Define the woocommerce_lost_password_message callback
     *
     * @param string $var
     *
     * @return string
     */
    public function filter_woocommerce_lost_password_message($var)
    {
        $var = "<div class='header-forgot-pass'>
                <h2 class='enter-title'>FORGOT PASSWORD?</h2>
                <p>Enter your username or email address.<br>We’ll send you a link by email to reset your password.</p>";

        return $var;
    }

    /**
     * Send mail
     *
     * @return void
     */
    /*
    The function of sending messages from the student's personal account to the teacher
    public function custom_wp_mail_from()
        {
            global $current_user;
            //get_currentuserinfo();
            $mess          = sanitize_text_field($_POST['contact_message']);
            $post_id       = sanitize_text_field($_POST['post_id']);
            $teacher_nonce = sanitize_text_field($_POST['sensei_message_teacher_nonce']);
            $author_id     = get_post_field('post_author', $post_id);
            $get_author    = get_user_meta($author_id);
            $email         = $current_user->user_email;
            $to            = $get_author['billing_email'][0];
            $subject       = "Our student " . $current_user->display_name . " has sent you a private message regarding the course";
            $headers       = 'From: ' . $email . "\r\n" . 'Reply-To: ' . $email . "\r\n";
            $sent          = wp_mail($to, $subject, strip_tags($mess), $headers);
        }*/
    /**
     * Template redirect
     *
     * @return void
     */
    public function template_redirect_fn()
    {
        global $post;
        global $current_user;
        global $wp;
        $course = get_posts([
            'post_type' => 'course',
            'numberposts' => -1,
            'post_status' => 'publish',
        ]);
        $user_id = get_current_user_id(); // The current user ID
        $homeUrl = home_url();
        $login = is_user_logged_in();
        if ($login === false) {
            if ($post->post_type === 'course' || $post->post_type === 'lesson' || $post->post_name === 'my-courses') {
                wp_redirect('/my-account/', 302);
            }
        } else {
            $getCurrentUrl = home_url($wp->request);
            $args = [
                'customer_id' => $user_id,
                'limit' => 1, // to retrieve _all_ orders by this user
            ];
            $orders = wc_get_orders($args);
            if (strripos($getCurrentUrl, 'order-received') > 0) {
                if ('paypal' === $orders[0]->payment_method || 'braintree_credit_card' === $orders[0]->payment_method) {
                    $orders[0]->update_status('completed');
                }
            }
            if ($post->post_type === 'checkout') {
                wp_redirect($homeUrl, 302);
            }
            if ($post->post_name === 'my-courses') {
                wp_redirect(get_post_permalink($course[0]->ID), 302);
            }
        }
        if ($post->post_name === 'cart') {
            wp_redirect(home_url(), 301);
        }
        if ($post->post_type === 'course') {
            echo '<div class="bg_course_preload" style="
                display: flex;
                align-items: center;
                justify-content: center;
                position: fixed;
                top: 0;
                bottom: 0;
                left: 0;
                right: 0;
                background-color: white;
                color: #333333;
                z-index: 9999;
                text-transform: capitalize;
                font-weight: 700;">LOADING</div>'; ?>

<script>
document.addEventListener("DOMContentLoaded", function(e) {

    let complate = document.getElementsByClassName('completed');

    let all_lesson_count = document.getElementsByClassName('wp-block-sensei-lms-course-outline-lesson');


    if (complate.length > 0) {

        let el = complate[complate.length - 1];

        let url = el.nextElementSibling;


        if (url === null) {

            let wrappParent = el.closest(".wp-block-sensei-lms-course-outline-module");

            let nextWrapp = wrappParent.nextElementSibling;


            if (nextWrapp === null) {

                window.location.href = el.getAttribute('href');

            }


            let get_item = nextWrapp.getElementsByClassName('completed');

            if (get_item.length > 0) {
                url = get_item[get_item.length - 1].getAttribute("href");

            } else {
                get_item = nextWrapp.getElementsByClassName('wp-block-sensei-lms-course-outline-lesson');
                url = get_item[0].getAttribute("href");
            }
        } else {
            url = url.getAttribute("href");
        }
        window.location.href = url;
    } else {
        let no_complate = document.getElementsByClassName('wp-block-sensei-lms-course-outline-lesson');
        let url = no_complate[0].getAttribute("href");
        window.location.href = url;
    }
});
</script>

<?php
        }
    }

    /**
     * Update order after successfull payment
     *
     * @param int $order_id
     *
     * @return void
     */
    public function update_order_after_successful_payment($order_id)
    {
        if (!$order_id)
            return;
        $order = wc_get_order($order_id);
        if ('paypal' === $order->get_payment_method() || 'braintree_credit_card' === $order->get_payment_method()) {
            $order->update_status('completed');
        }
    }

    /**
     * Update lesson status
     *
     * @return void
     */
    public function lesson_item_complate()
    {
        $user = wp_get_current_user();
        $user_id = $user->ID;
        $id_less = sanitize_text_field($_POST['id']);
        $status = Sensei_Utils::update_lesson_status($user_id, $id_less, 'complete');
    }

    /**
     * Save comment
     *
     * @return void
     */
    public function save_comment()
    {
        check_ajax_referer('save_comment', 'save_comment_nonce');
        $data = sanitize_post($_POST, 'db');
        $user = wp_get_current_user();
        $filetypes = ['image/jpeg', 'image/png'];
        $post = get_post($data['post_id']);
        $comment_content = sanitize_text_field($data['comment']);
        $images = [];
        $comment_args = [
            'comment_post_ID'       => $data['post_id'],
            'comment_content'       => $comment_content,
            'comment_parent'        => $data['parent_id'],
            'user_id'               => $user->ID,
            'comment_author'        => $user->user_login,
            'comment_author_email'  => $user->user_email,
            'comment_author_url'    => $user->user_url,
            'comment_approved'      => 0,
        ];
        if (!empty($_FILES)) {
            foreach ($_FILES as $key => $file) {
                if (false !== strpos($key, 'comment_images') && false == $_FILES[$key]['error'] && in_array($_FILES[$key]['type'], $filetypes)) {
                    $image_id = media_handle_upload($key, 0);
                    if (!is_wp_error($image_id)) {
                        $images[] = wp_get_attachment_image_url($image_id);
                    }
                }
            }
        }
        if (!empty($images)) {
            $comment_args['comment_meta'] = ['comment_images' => $images];
        }
        $comment = wp_insert_comment($comment_args);

        $message       = '<p>New comment on your lesson "<a href="' . get_the_permalink($data['post_id']) . '">' . $post->post_name . '</a>"</p>';
        $message      .= '<p>                Author: ' . $user->user_login . '</p>';
        $message      .= '<p>               Email: ' . $user->user_email . '</p>';
        $message      .= '<p>                Comment: ' . $comment_content . '</p>';
        $author_id     = $post->post_author;
        $get_author    = get_user_meta($author_id);
        $email         = $current_user->user_email;
        $to            = $get_author['billing_email'][0];
        $subject       = "New comment on your lesson " . $post->post_name . " ";
        $headers       = 'From: ' . $email . "\r\n" . 'Reply-To: ' . $email . "\r\n";
        $sent          = wp_mail($to, $subject, $message, $headers);




        wp_send_json($comment);
    }

    /**
     * Show comment image in admin panel on edit page
     *
     * @param object $comment
     *
     * @return void
     */
    public function comment_images_metabox($comment)
    {
        $images = get_comment_meta($comment->comment_ID, 'comment_images', true);
        if (!empty($images)) {
            foreach ($images as $url) {
                printf('<img src="%s" alt="%s" style="margin: 10px;">', $url, __('Comment Image', 'pigeonpixel'));
            }
        }
    }

    /**
     * Disable plugin updates
     *
     * @param object $value
     *
     * @return object
     */
    public function disable_plugin_updates($value)
    {
        $pluginsNotUpdatable = [
            'sensei-lms/sensei-lms.php',
            'sensei-course-progress/sensei-course-progress.php'
        ];
        if (isset($value) && is_object($value)) {
            foreach ($pluginsNotUpdatable as $plugin) {
                if (isset($value->response[$plugin])) {
                    unset($value->response[$plugin]);
                }
            }
        }

        return $value;
    }
}

Theme_Functions::instance();

class wl_loginoutLogItems
{
    public $db_id = 0;

    public $object = 'wl_loginoutlog';

    public $object_id;

    public $menu_item_parent = 0;

    public $type = 'custom';

    public $title;

    public $url;

    public $target = '';

    public $attr_title = '';

    public $classes = [];

    public $xfn = '';
}

// Our custom post type function
add_action('init', 'create_posttype');
function create_posttype()
{
    register_post_type(
        'feedback', // CPT Options
        [
            'labels' => [
                'name' => __('feedback'),
                'singular_name' => __('feedback')
            ],
            'public' => true,
            'has_archive' => true,
            'rewrite' => ['slug' => 'feedback'],
            'show_in_rest' => true,
        ]
    );
}
/* -------------------------- //NOTE FEEDBACK AJAX -------------------------- */
add_action('wp_ajax_feedback_mess', 'feedback_mess');
add_action('wp_ajax_nopriv_feedback_mess', 'feedback_mess');
function feedback_mess()
{

    $author_id = get_current_user_id();
    $user_meta = get_user_meta($author_id);
    $title = sanitize_text_field($_POST['title']);
    $feedback = sanitize_text_field($_POST['feedback']);
    $stars = sanitize_text_field($_POST['stars']);

    // Create post object
    $feedback_post = [
        'post_title' => $user_meta['first_name'][0] . ' ' . $user_meta['last_name'][0] . ' ' . $user_meta['nickname'][0] . ' - ' . wp_strip_all_tags($title),
        'post_content' => $feedback,
        'post_status' => 'publish',
        'post_author' => $author_id,
        'post_type' => 'feedback',
        'meta_input'     => ['meta_key' => 'meta_value'],
    ];
    // Insert the post into the database
    $post_id = wp_insert_post($feedback_post);
    update_user_meta($author_id, 'feedpack_complate', 'complate');
    update_post_meta($post_id, 'feedback_stars', $stars);
    wp_send_json($user_meta);
}

/* ------------------------- //NOTE FEEDBACK COLUMNS ------------------------ */
add_filter('manage_' . 'feedback' . '_posts_columns', 'add_views_column', 4);
function add_views_column($columns)
{
    $num = 2;

    $new_columns = array(
        'views' => 'Stars  &#11088;',
    );

    return array_slice($columns, 0, $num) + $new_columns + array_slice($columns, $num);
}

add_action('manage_' . 'feedback' . '_posts_custom_column', 'fill_views_column', 5, 2);
function fill_views_column($colname, $post_id)
{
    if ($colname === 'views') {
        echo (get_post_meta($post_id, 'feedback_stars', true)) ? get_post_meta($post_id, 'feedback_stars', true) . " &#11088;" : '-';
    }
}
//
add_filter('sensei_certificates_custom_font', 'certificates_custom_font', 10, 1);
function certificates_custom_font($font)
{
    // Font family and file name MUST be supplied
    $font = [
        'family' => 'CeraProBlack',
        'file' => 'CeraProBlack.ttf',
    ];

    return $font;
}

// Custom condition
add_action('elementor/theme/register_conditions', "condition_header_menu");
function condition_header_menu($conditions_manager)
{
    class Header_Template_Main extends ElementorPro\Modules\ThemeBuilder\Conditions\Condition_Base
    {
        public static function get_type()
        {
            return 'singular';
        }

        public static function get_priority()
        {
            return 30;
        }

        public function get_name()
        {
            return 'header_menu';
        }

        public function get_label()
        {
            return __('Header Menu');
        }

        public function check($args)
        {
            if (is_checkout()) {
                if (isset($_SERVER["HTTP_REFERER"])) {
                    return true;
                } else {
                    return false;
                }
            }
        }

        protected function _register_controls()
        {
            $this->add_control('header_menu', [
                'section' => 'settings',
                'label' => __('Header Menu'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'header_menu' => 'header_menu',
                    'publisher' => 'Publisher',
                    'review' => 'Review',
                ],
            ]);
        }
    }

    $conditions_manager->get_condition('singular')->register_sub_condition(new Header_Template_Main());

    class Header_Template_Custom extends ElementorPro\Modules\ThemeBuilder\Conditions\Condition_Base
    {
        public static function get_type()
        {
            return 'singular';
        }

        public static function get_priority()
        {
            return 30;
        }

        public function get_name()
        {
            return 'header_log_custom';
        }

        public function get_label()
        {
            return __('Header Logo');
        }

        public function check($args)
        {
            if (is_checkout()) {
                $get_server = isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : '';
                $url = parse_url($get_server);
                $args = [
                    'post_type' => 'e-landing-page',
                    'numberposts' => -1
                ];
                $my_posts = get_posts($args);
                if (empty($get_server)) {
                    return true;
                } elseif (isset($my_posts)) {
                    foreach ($my_posts as $item) {
                        if (isset($url['path'])) {
                            if ($item->post_name === str_replace('/', '', $url['path'])) {
                                return true;
                            }
                        }
                    }
                } else {
                    return false;
                }
            }
        }

        protected function _register_controls()
        {
            $this->add_control('header_log_custom', [
                'section' => 'settings',
                'label' => __('custom condition page'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'header_log_custom' => 'header_log_custom',
                    'publisher' => 'Publisher',
                    'review' => 'Review',
                ],
            ]);
        }
    }

    $conditions_manager->get_condition('singular')->register_sub_condition(new Header_Template_Custom());
}

add_action('wp_ajax_nopriv_get_user_certificate', 'get_user_certificate');
add_action('wp_ajax_get_user_certificate', 'get_user_certificate');
function get_user_certificate()
{
    $user_id = get_current_user_id();
    $certificate_url = '';
    $args = [
        'post_type' => 'certificate',
        'author' => $user_id,
        'meta_key' => 'course_id',
        'meta_value' => 1590,
    ];
    $query = new WP_Query($args);
    if ($query->have_posts()) {
        $count = 0;
        while ($query->have_posts()) {
            $query->the_post();
            $certificate_url = get_permalink();
            $count++;
        }
    }
    if (!empty($certificate_url)) {
        update_user_meta($user_id, 'certificate_url', $certificate_url);
    }
    $certificate_url_res = !empty($certificate_url) ? $certificate_url : get_user_meta($user_id, 'certificate_url');
    wp_send_json($certificate_url_res);
    exit();
}


/**
 * Show user nickname in comments layout
 */
add_filter( 'get_comment_author', 'wpse_use_user_real_name', 10, 3 ) ;

//use registered commenter first and/or last names if available
function wpse_use_user_real_name( $author, $comment_id, $comment ) {
    $nickname = '' ;
    //returns 0 for unregistered commenters
    $user_id = $comment->user_id ;
    if ( $user_id ) {
        $user_object = get_userdata( $user_id ) ;
        $nickname = $user_object->nickname ;
    }
    if ( $nickname ) {
        $author = trim( $nickname ) ;
    }

    return $author ;
}

/**
 * Custom comments layout
 *
 * @param object $comment
 * @param array  $args
 * @param string $depth
 *
 * @return void
 */
function custom_comment_template($comment, $args, $depth)
{
    $tag = ('div' === $args['style']) ? 'div' : 'li';
    $commenter = wp_get_current_commenter();
    $show_pending_links = !empty($commenter['comment_author']);
    $moderation_note = __('Your comment is awaiting moderation.');
    $comment_id = get_comment_ID();
    $images = get_comment_meta($comment_id, 'comment_images', true); ?>

<<?php echo $tag; ?> id="comment-<?php echo $comment_id; ?>"
    <?php comment_class(!empty($args['has_children']) ? 'parent' : '', $comment); ?>>

    <article id="div-comment-<?php echo $comment_id; ?>" class="comment-body">

        <footer class="comment-meta">

            <div class="comment-author vcard">

                <?php if (0 != $args['avatar_size']) {
                        echo get_avatar($comment, $args['avatar_size']);
                    } ?>

                <?php
                    $comment_author = get_comment_author_link($comment);
                    if ('0' == $comment->comment_approved && !$show_pending_links) {
                        $comment_author = get_comment_author($comment);
                    }
                    printf(__('%s <span class="says">says:</span>'), sprintf('<b class="fn">%s</b>', $comment_author));
                    ?>

            </div>

            <div class="comment-metadata">

                <?php
                    printf('<a href="%s"><time datetime="%s">%s</time></a>', esc_url(get_comment_link($comment, $args)), get_comment_time('c'), sprintf(__('%1$s at %2$s'), get_comment_date('', $comment), get_comment_time()));
                    edit_comment_link(__('Edit'), ' <span class="edit-link">', '</span>');
                    ?>

            </div>

            <?php if ('0' == $comment->comment_approved) : ?>

            <em class="comment-awaiting-moderation"><?php echo $moderation_note; ?></em>

            <?php endif; ?>

        </footer>

        <div class="comment-content">

            <?php comment_text(); ?>

            <?php if (false && !empty($images)) : ?>

            <?php foreach ($images as $url) : ?>
            <div class="comment-image">
                <img src="<?php echo $url; ?>" alt="<?php _e('Comment Image', 'pigeonpixel'); ?>">
            </div>
            <?php endforeach; ?>

            <?php endif; ?>

        </div>

        <?php if ('1' == $comment->comment_approved || $show_pending_links) {
                comment_reply_link(array_merge($args, [
                    'add_below' => 'div-comment',
                    'depth' => $depth,
                    'max_depth' => $args['max_depth'],
                    'before' => '<div class="reply">',
                    'after' => '</div>',
                ]));
            } ?>

    </article>

    <?php
}


add_filter("wpdiscuz_author_title", function ($label, $comment) {
    global $post;
    $user_meta = get_userdata($comment->user_id);
    $user_roles = $user_meta->roles;
    if (in_array('administrator', $user_roles)) {
        $label = __("Author", "wpdiscuz");
    }
    return $label;
}, 10, 2);

/**
 * Write logs to debug.log
 *
 * @return void
 */
function write_log($log)
{
    if (true === WP_DEBUG) {
        if (is_array($log) || is_object($log)) {
            error_log(print_r($log, true));
        } else {
            error_log($log);
        }
    }
}


function disable_decimals_in_home_and_landing($decimals)
{
    $uri = $_SERVER['HTTP_REFERER'];
    $home_uri = home_url('/');
    if (($uri === $home_uri) ||
        (strpos($_SERVER['REQUEST_URI'], 'three-pillars') !== false) ||
        (strpos($uri, 'three-pillars') !== false)
    ) {
        return 0;
    } else {
        return $decimals;
    }
}
add_filter('wc_get_price_decimals', 'disable_decimals_in_home_and_landing', 1, 1);