<?php
/**
 * Template part for displaying page content in page.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package pigeonpixel
 */


?>

<article attr-url = '<?php echo get_template_directory_uri();?>' id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php 
	$post = get_post();

	if($post->post_name !== 'my-account'){?>
		<header class="entry-header">
			<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
		</header><!-- .entry-header -->
	<?php }
	?>
	<?php pigeonpixel_post_thumbnail(); ?>
	<div class="entry-content">
		<?php
		the_content();
		wp_link_pages(
			array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'pigeonpixel' ),
				'after'  => '</div>',
			)
		);
		?>
	</div><!-- .entry-content -->

	<?php if ( get_edit_post_link() ) : ?>
		<footer class="entry-footer">
			<?php
			//edit_post_link(
			//	sprintf(
			//		wp_kses(
			//			/* translators: %s: Name of current post. Only visible to screen readers */
			//			__( 'Edit <span class="screen-reader-text">%s</span>', 'pigeonpixel' ),
			//			array(
			//				'span' => array(
			//					'class' => array(),
			//				),
			//			)
			//		),
			//		wp_kses_post( get_the_title() )
			//	),
			//	'<span class="edit-link">',
			//	'</span>'
			//);
			//?>
		</footer><!-- .entry-footer -->
	<?php endif; ?>
</article><!-- #post-<?php the_ID(); ?> -->
