<?php
global $wp_query, $post;
$comment_id = $wp_query->query['replytocom'];
$comment = get_comment($comment_id);
?><!DOCTYPE htmL>
<html>
  <head>
    <?php wp_head();?>

    <script type="text/javascript">
      <!--
      // redirect to comment-page and scroll to comment
      window.location = "<?php echo get_permalink($comment->comment_post_ID) . '#comment-' . $comment_id; ?>";
      //–>
    </script>
  </head>

  <body <?php body_class(); ?>>
    <div id="page">
      <?php do_action( 'before' ); ?>

      <article id="comment-<?php comment_ID(); ?>" class="h-comment h-as-comment h-entry">
        <div class="e-content p-summary p-name"><?php comment_text(); ?></div>
        <footer class="entry-meta">
          <address class="p-author h-card">
            <?php echo get_avatar( $comment, 50 ); ?>
            <?php printf( '<cite class="p-name">%s</cite>', get_comment_author_link() ); ?>
          </address><!-- .comment-author .vcard -->
          <a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>"><time datetime="<?php comment_time( 'c' ); ?>" class="dt-published dt-updated published updated">
            <?php
            /* translators: 1: date, 2: time */
            printf( __( '%1$s at %2$s', 'webmention' ), get_comment_date(), get_comment_time() ); ?>
          </time></a>
        </footer>
      </article>
    </div>
  </body>
</html>
