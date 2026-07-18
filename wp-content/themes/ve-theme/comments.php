<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if ( post_password_required() ) {
  return;
}

do_action( 'generate_before_comments' );
?>
<div id="comments" class="bc-comments">

  <?php
  do_action( 'generate_inside_comments' );

  if ( have_comments() ) :
    $comments_number = get_comments_number();
    $comments_title = apply_filters(
      'generate_comment_form_title',
      sprintf(
        esc_html(
          _nx(
            '%1$s thought on &ldquo;%2$s&rdquo;',
            '%1$s thoughts on &ldquo;%2$s&rdquo;',
            $comments_number,
            'comments title',
            'generatepress'
          )
        ),
        number_format_i18n( $comments_number ),
        get_the_title()
      )
    );

    echo apply_filters(
      'generate_comments_title_output',
      sprintf(
        '<h2 class="bc-comments-title">%s</h2>',
        esc_html( $comments_title )
      ),
      $comments_title,
      $comments_number
    );

    do_action( 'generate_below_comments_title' );

    if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) :
      ?>
      <nav id="comment-nav-above" class="bc-comment-navigation" role="navigation">
        <h2 class="screen-reader-text"><?php esc_html_e( 'Comment navigation', 'generatepress' ); ?></h2>
        <div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', 'generatepress' ) ); ?></div>
        <div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', 'generatepress' ) ); ?></div>
      </nav>
    <?php endif; ?>

    <ol class="bc-comment-list">
      <?php
      wp_list_comments(
        array(
          'callback' => 'generate_comment',
        )
      );
      ?>
    </ol>

    <?php
    if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) :
      ?>
      <nav id="comment-nav-below" class="bc-comment-navigation" role="navigation">
        <h2 class="screen-reader-text"><?php esc_html_e( 'Comment navigation', 'generatepress' ); ?></h2>
        <div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', 'generatepress' ) ); ?></div>
        <div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', 'generatepress' ) ); ?></div>
      </nav>
      <?php
    endif;

  endif;

  if ( ! comments_open() && '0' != get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
    ?>
    <p class="bc-no-comments"><?php esc_html_e( 'Comments are closed.', 'generatepress' ); ?></p>
    <?php
  endif;

  comment_form();
  ?>

</div>
