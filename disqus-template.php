<?php  do_action('wiloke_comment_type_before_render_disqus_tmp', $template, $post); ?>
<div id="respond" class="pi-comments-area comment-respond">
  <div id="disqus_thread"></div>
  <noscript><?php printf( (__('Please enable JavaScript to view the <a href="%s">comments powered by Disqus.</a>', 'wiloke')), 'http://disqus.com/?ref_noscript'); ?></noscript>
</div>
<?php do_action('wiloke_comment_type_after_render_disqus_tmp', $template, $post); ?>