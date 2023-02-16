<?php
get_header();
echo "<div id='content'>";
while ( have_posts() ) :
    the_post();

    get_template_part( 'template-parts/content', 'page-finish' );

    // If comments are open or we have at least one comment, load up the comment template.
    if ( comments_open() || get_comments_number() ) :
        comments_template();
    endif;

endwhile;
echo "</div>" // End of the loop.
?>
<?php
get_footer();
