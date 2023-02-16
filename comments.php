<?php
/**
 * The template for displaying comments
 *
 * This is the template that displays the area of the page that contains both the current comments
 * and the comment form.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package pigeonpixel
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */

if (post_password_required()) return;

?>
<div id="comments" class="comments-area">
	<div id="respond" class="comment-respond">
		<h3 id="reply-title" class="comment-reply-title"><?php _e('Leave a comment', 'pigeonpixel'); ?></h3>
		
		<form action="#" method="post" id="commentform" class="comment-form">
			<div class="comment-form-field">
				<textarea placeholder="<?php _e('|', 'pigeonpixel'); ?>" name="comment" cols="45" rows="8" required></textarea>
				<div class="comment-form-upload-image">
					<svg class="upload-comment-image" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 544.68 544.68"><g><g><path d="M514.08,88.74H30.6c-16.897,0-30.6,13.703-30.6,30.6v306c0,16.898,13.703,30.6,30.6,30.6h483.48 c16.897,0,30.6-13.701,30.6-30.6v-306C544.68,102.443,530.978,88.74,514.08,88.74z M267.982,416.602H164.524H72.418 c-16.897,0-20.912-9.689-8.96-21.641l85.105-85.105c5.973-5.973,13.807-8.959,21.64-8.959s15.661,2.986,21.64,8.959l24.413,24.414 l60.692,60.691C288.895,406.912,284.886,416.602,267.982,416.602z M103.018,146.88c27.039,0,48.96,21.922,48.96,48.96 s-21.922,48.96-48.96,48.96c-27.038,0-48.96-21.922-48.96-48.96S75.979,146.88,103.018,146.88z M494.122,416.602H300.51 c2.173-5.754,4.713-19.328-10.581-34.621l-60.692-60.693l78.446-78.445c11.952-11.952,31.322-11.952,43.274,0l152.125,152.125 C515.034,406.912,511.02,416.602,494.122,416.602z"/></g></g></svg>
				</div>
			</div>

			<input type="file" name="comment_image" accept=".png, .jpg, .jpeg" multiple>
			
			<div class="submit-row">
				<div class="reply-to">
					<div class="reply-text">
						<span><?php _e('Reply to', 'pigeonpixel'); ?> <b></b></span>
						<span class="text"></span>
					</div>
					<svg class="remove-reply" viewBox="0 0 12 12" xmlns="http://www.w3.org/2000/svg"><path d="M11.6466 9.95266C12.1153 10.4214 12.1153 11.1807 11.6466 11.6494C11.4141 11.8838 11.1066 12 10.7992 12C10.4917 12 10.185 11.8828 9.95097 11.6485L5.99953 7.69909L2.04847 11.6475C1.81411 11.8838 1.50701 12 1.19991 12C0.892805 12 0.586079 11.8838 0.351535 11.6475C-0.117178 11.1788 -0.117178 10.4195 0.351535 9.95078L4.30373 5.99859L0.351535 2.04828C-0.117178 1.57956 -0.117178 0.820248 0.351535 0.351535C0.820248 -0.117178 1.57956 -0.117178 2.04828 0.351535L5.99953 4.3056L9.95172 0.35341C10.4204 -0.115303 11.1798 -0.115303 11.6485 0.35341C12.1172 0.822123 12.1172 1.58144 11.6485 2.05015L7.69627 6.00234L11.6466 9.95266Z" fill="white"/></svg>
				</div>
					
				<input type="submit" class="submit" id="submit" value="Post Comment">
			</div>
			<input type="hidden" name="post_id" value="<?php echo get_queried_object_id(); ?>">
			<input type="hidden" name="parent_id">
			<input type="hidden" name="action" value="save_comment">

			<?php wp_nonce_field('save_comment', 'save_comment_nonce'); ?>

		</form>	
		<div class="preloader">
			<div class="lds-roller"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
		</div>
	</div>

	<?php if (have_comments()) : ?>

		<h2 class="comments-title">
			<?php

				$comment_count = get_comments_number();

				printf(_nx('%s Comment', '%s Comments', $comment_count, 'Comments count', 'pigeonpixel'), '<span>' . $comment_count . '</span>');

			?>
		</h2>

		<span class="block-line"></span>

		<?php the_comments_navigation(); ?>

		<ol class="comment-list">

			<?php wp_list_comments(array(
					'style'      => 'ol',
					'short_ping' => true,
					'callback'	 => 'custom_comment_template',
			)); ?>

		</ol>

		<?php the_comments_navigation(); ?>

		<?php if (!comments_open()) : ?>

			<p class="no-comments"><?php _e('Comments are closed.', 'pigeonpixel'); ?></p>

		<?php endif; ?>

	<?php endif; ?>

</div>
