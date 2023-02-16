<?php

class Elementor
{

    /**
     * Fields constructor.
     */
    function __construct()
    {
        add_action('elementor/elements/categories_registered', __CLASS__ . '::add_widget_categories');
        add_action('elementor/widgets/widgets_registered', __CLASS__ . '::register_widgets');
    }

    /**
     * @param $instance
     *
     * @return void
     */
    public static function register_widgets( $instance )
    {
        spl_autoload_register(__CLASS__ . '::autoload');

        $instance->register_widget_type(new Elementor_Styles_Legal_Docs_Widget());

    }

    public static function add_widget_categories( $elements_manager )
    {

        $elements_manager->add_category('custom-widgets', [
            'title' => __('Custom widgets', 'pigeonpixel'),
            'icon'  => 'fa fa-bicycle',
        ]);

    }

    /**
     * Autoload function
     *
     * @return void
     */
    public static function autoload()
    {
        $dir   = get_template_directory() . '/elementor/widgets/class-*.php';
        $paths = glob($dir);

        if ( is_array($paths) && count($paths) > 0 ) {
            foreach ( $paths as $file ) {
                if ( file_exists($file) ) {
                    include_once $file;
                }
            }
        }
    }

    /**
     * @var null
     */
    protected static $instance = null;

    /**
     * Return an instance of this class.
     *
     * @return    object    A single instance of this class.
     * @since     1.0.0
     *
     */
    public static function instance()
    {

        // If the single instance hasn't been set, set it now.
        if ( null == self::$instance ) {
            self::$instance = new self;
        }

        return self::$instance;
    }
}

Elementor::instance();
