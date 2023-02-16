<?php
defined('ABSPATH') || exit;


if (isset($_GET['msg_id']) && !empty($_GET['msg_id'])) {
    $msgId = sanitize_text_field($_GET['msg_id']);
    $msg = get_post($msgId);
    $user_name = get_userdata($msg->post_author) -> user_login;
    ?>
    <div class="message-information">
        <h2 class="message-theme"><span class="re-title">RE: </span> <?php echo esc_html( $msg->post_name );?></h2>
        <p class="from-user"><span class="re-title">From: </span><?php echo  esc_html( $user_name ) ;?></p>
        <p class="from-user"><span
                    class="re-title">Time: </span><?php echo get_the_date('H:i:s') . ' ' . get_the_date('Y-m-d') ?> </p>
        <p class="current-message"><span class="re-title">Message: </span><?php echo $msg->post_content ?></p>
    </div>
    <div class="comments">
        <h2 class="comments-title">Comments</h2>

        <?php
      ?>
        <ul class="commentlist">
            <?php
            //Gather comments for a specific page/post
            $comments = get_comments(array(
                'post_id' => $msgId,
                'status' => 'approve' //Change this to the type of comments to be displayed
            ));

            //Display the list of comments
            wp_list_comments(array(
                'per_page' => 10, //Allow comment pagination
                'reverse_top_level' => false //Show the latest comments at the top of the list
            ), $comments);

            ?>
        </ul>
<?php        $args = array();
        comment_form( $args, $msgId );
        ?>
    </div>
<?php } else {
    $args = array(
        'numberposts'      => -1,
        'post_type'        => 'sensei_message');
    $messages = get_posts($args);
    echo '<h2 class="thread-title">Open threads</h2>';
    echo '<ul>';
    foreach ($messages as $message) {
        $ID = $message->ID;
        echo '<li><a href="/my-account/my-messages/?msg_id=' . $ID . '">' . $message->post_name . '</a></li>';
    }
    echo '</ul>';
}