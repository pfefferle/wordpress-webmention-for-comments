<?php
/*
 Plugin Name: WebMention for comments
 Plugin URI: https://github.com/pfefferle/wordpress-webmention-for-comments
 Description: Webmention support for WordPress comments
 Author: pfefferle
 Author URI: http://notizblog.org/
 Version: 1.0.0
*/

// check if class already exists
if (!class_exists("WebMentionForCommentsPlugin")) :

// initialize plugin
add_action('init', array( 'WebMentionForCommentsPlugin', 'init' ));


class WebMentionForCommentsPlugin {
  /**
   * Initialize the plugin, registering WordPress hooks.
   */
  public static function init() {
    add_action('comment_post', array('WebMentionForCommentsPlugin', 'comment_post'));
    add_filter('template_include', array('WebMentionForCommentsPlugin', 'template_include'));
    add_filter('query_vars', array('WebMentionForCommentsPlugin', 'query_var'));

    // enable distributed and threaded comments
    add_filter('webmention_comment_parent', array('WebMentionForCommentsPlugin', 'set_comment_parent'), 10, 2);
  }

  /**
   * send webmentions on new comments
   *
   * @param int $id the post id
   * @param obj $comment the comment object
   */
  public static function comment_post($id) {
    $comment = get_comment($id);

    // check parent comment
    if ($comment->comment_parent) {
      // get parent comment...
      $parent = get_comment($comment->comment_parent);
      // ...and gernerate target url
      $target = $parent->comment_author_url;

      if ($target) {
        $source = add_query_arg( 'replytocom', $comment->comment_ID, get_permalink($comment->comment_post_ID) );

        do_action("send_webmention", $source, $target);
        //send_webmention($source, $target);
      }
    }
  }

  /**
   * adds some query vars
   *
   * @param array $vars
   * @return array
   */
  public static function query_var($vars) {
    $vars[] = 'replytocom';

    return $vars;
  }

  /**
   * replace the template for all URLs with a "replytocom" query-param
   *
   * @param string $template the template url
   * @return string
   */
  public static function template_include( $template ) {
    global $wp_query;

    // replace template
    if (isset($wp_query->query['replytocom'])) {
      return apply_filters("webmention_comment_template", dirname(__FILE__)."/templates/comment.php");
    }

    return $template;
  }

  /**
   * set "parent id" if URL has a "replytocom" param
   *
   * @param int $id the id of the parent post
   * @param string $target the target url
   *
   * @return int
   */
  public static function set_comment_parent($id, $target) {
    // check if there is a parent comment
    if ( $query = parse_url($target, PHP_URL_QUERY) ) {
      parse_str($query);
      if (isset($replytocom) && get_comment($replytocom)) {
        return $replytocom;
      }
    }

    return $id;
  }
}

endif;
