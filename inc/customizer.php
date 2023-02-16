<?php
/**
 * pigeonpixel Theme Customizer
 *
 * @package pigeonpixel
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function pigeonpixel_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';

	if ( isset( $wp_customize->selective_refresh ) ) {
		$wp_customize->selective_refresh->add_partial(
			'blogname',
			array(
				'selector'        => '.site-title a',
				'render_callback' => 'pigeonpixel_customize_partial_blogname',
			)
		);
		$wp_customize->selective_refresh->add_partial(
			'blogdescription',
			array(
				'selector'        => '.site-description',
				'render_callback' => 'pigeonpixel_customize_partial_blogdescription',
			)
		);
	}
}
add_action( 'customize_register', 'pigeonpixel_customize_register' );

/**
 * Render the site title for the selective refresh partial.
 *
 * @return void
 */
function pigeonpixel_customize_partial_blogname() {
	bloginfo( 'name' );
}

/**
 * Render the site tagline for the selective refresh partial.
 *
 * @return void
 */
function pigeonpixel_customize_partial_blogdescription() {
	bloginfo( 'description' );
}

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function pigeonpixel_customize_preview_js() {
	wp_enqueue_script( 'pigeonpixel-customizer', get_template_directory_uri() . '/js/customizer.js', array( 'customize-preview' ), '20151215', true );
}
add_action( 'customize_preview_init', 'pigeonpixel_customize_preview_js' );

function wl_customize_register( $wp_customize ) {
 
	$true_transport = 'postMessage'; 
	$wp_customize->add_section('mailchimp_api', array(
		'title'     	=> 'MailChimp API key',
		'priority'  	=> 999, 
		'description' 	=> '',
	));

    $wp_customize->add_setting( 'mailchimp_api_checkbox', array(
        'default'    =>  '',
		'transport'  =>  $true_transport,
    ) );

    $wp_customize->add_control( 'mailchimp_api_checkbox', array(
        'label'      => 'Add users to MailChimp',
        'section'    => 'mailchimp_api',
        // 'settings'   => 'mailchimp_api_checkbox',
        'type'       => 'checkbox',
        'std'        => '1'
    ) );

	$wp_customize->add_setting('mailchimp_api_key', array(
		'default'    =>  '',
		'transport'  =>  $true_transport,
	));
	$wp_customize->add_control('mailchimp_api_key', array(
		'section'   => 'mailchimp_api',
		'label'     => 'MailChimp API key',
		'type'      => 'text',
	));
	
}
add_action( 'customize_register', 'wl_customize_register' );