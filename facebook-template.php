<?php do_action('wiloke_comment_type_before_render_fb_tmp', $template, $post); ?>
<div id="respond" class="pi-comments-area comment-respond">
  <div class="fb-comments" data-href="<?php the_permalink(); ?>" data-numposts="<?php echo esc_attr(piCommentType::$aCommentSettings['fb_number_of_posts']); ?>" data-colorscheme="<?php echo esc_attr(piCommentType::$aCommentSettings['fb_color_scheme']); ?>" data-order-by="<?php echo esc_attr(piCommentType::$aCommentSettings['fb_post_order']); ?>" data-width="100%" data-mobile="false"></div>
</div>
<?php  do_action('wiloke_comment_type_after_render_fb_tmp', $template, $post); ?>