<?php
/**
 * The Template for displaying all single lessons.
 *
 * Override this template by copying it to yourtheme/sensei/single-lesson.php
 *
 * @author      Automattic
 * @package     Sensei
 * @category    Templates
 * @version     3.6.0
 */

if (!defined('ABSPATH')) exit;

global $post;

get_sensei_header();

if (have_posts()) the_post();

if ('top' === apply_filters('sensei_video_position', 'top', $post->ID)) {
	do_action('sensei_lesson_video', $post->ID);
}

echo '<div class="menuSet">';

	do_action('sensei_single_lesson_content_inside_before', $post->ID);

	do_action('sensei_single_lesson_content_inside_after', $post->ID);

	if($post->post_content !== ''){
		printf('<div class="lesson-content">%s</div>', $post->post_content);
	}
	do_action( 'sensei_pagination' );

echo '</div>';

do_action( 'sensei_after_main_content' );

do_action( 'sensei_sidebar' );

get_footer();