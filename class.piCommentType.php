<?php
/*
Plugin Name: Wiloke - Comment Type
Plugin URI: http://blog.wiloke.com
Author: wiloke
Author URI: http://wiloke.com
Version: 1.0.3
Description: Wiloke Comment Type is a free comment platform plugin, which allows you to replace default WordPress comment with Facebook Comment or Disqus Comment.
*/


if ( !class_exists('piCommentType') )
{
	class piCommentType
	{
		static $aCommentSettings = array();

		public function __construct()
		{
			$this->pi_get_settings();
			add_action( 'admin_menu', array($this, 'pi_comment_settings_menu') );
			add_action( 'admin_enqueue_scripts', array($this, 'pi_comment_type_scripts') );
			add_action( 'plugin_action_links', array($this, 'add_setting_link'), 10, 2 );
		}

		/**
		 * Add setting link in plugin row action
		 * @since 1.0
		 */
		public function add_setting_link($actions, $file)
		{
			if( false !== strpos($file, 'wiloke-comment-type') )
			{
				$actions['settings'] = '<a href="'.admin_url('options-general.php?page=wiloke-comments').'">'.esc_html__('Settings', 'wiloke').'</a>';
			}
			return $actions;
		}

		public function pi_comment_type_scripts($hook)
		{
			if ( $hook == 'settings_page_wiloke-comments' )
			{
				wp_enqueue_script('pi_comment_main', plugin_dir_url(__FILE__) . 'source/main.js', array('jquery'), '1.0', true);
			}
		}

		public function pi_get_settings()
		{
			$aDef = array('comment_type'=>'default', 'fb_app_id'=>'', 'fb_app_secret'=>'', 'fb_number_of_posts'=>10, 'fb_post_order'=>'time', 'fb_color_scheme'=>'light', 'disqus_shortname'=>'');

			self::$aCommentSettings =  get_option('pi_comment_settings');

			self::$aCommentSettings = wp_parse_args(self::$aCommentSettings, $aDef);
		}

		public function pi_comment_settings_menu()
		{
			add_options_page(esc_html__('Wiloke Comments', 'wiloke'), esc_html__('Wiloke Comments', 'wiloke'), 'edit_theme_options', 'wiloke-comments', array($this, 'pi_comments_type'));
		}

		public function pi_comments_type()
		{
			
			if ( isset( $_POST['pi_nonce_field'] )  && wp_verify_nonce( $_POST['pi_nonce_field'], 'pi_nonce_action' )  )
			{
				unset($_POST['pi_nonce_field']);
				foreach ($_POST as $key => $value) {
					$_POST[$key] = sanitize_text_field($value);
				}

				update_option('pi_comment_settings', $_POST);

				self::$aCommentSettings = $_POST;
			}

			?>
			<form method="POST" action="">
				<?php wp_nonce_field('pi_nonce_action', 'pi_nonce_field'); ?>
				<table class="form-table">
					<tbody>
						<tr>
							<th><?php _e('Comment Type', 'wiloke'); ?></th>
							<td>
								<select name="comment_type">
									<option value="default" <?php selected(self::$aCommentSettings['comment_type'], 'default'); ?>><?php _e('Default', 'wiloke'); ?></option>
									<option value="facebook" <?php selected(self::$aCommentSettings['comment_type'], 'facebook'); ?>><?php _e('Facebook', 'wiloke'); ?></option>
									<option value="disqus" <?php selected(self::$aCommentSettings['comment_type'], 'disqus'); ?>><?php _e('Disqus', 'wiloke'); ?></option>
								</select>
							</td>
						</tr>
					</tbody>
				</table>

				<table id="facebook-comment" class="form-table pi-comment-settings">
					<tbody>
						<tr>
							<th><?php _e('App ID', 'wiloke'); ?></th>
							<td>
								<input type="text" name="fb_app_id" value="<?php echo esc_attr(self::$aCommentSettings['fb_app_id']); ?>">
								<code><?php _e('Enter in your Facebook App ID.', 'wiloke'); ?></code>
							</td>
						</tr>
						<tr>
							<th><?php _e('App Secret', 'wiloke'); ?></th>
							<td>
								<input type="text" name="fb_app_secret" value="<?php echo esc_attr(self::$aCommentSettings['fb_app_secret']); ?>">
								<code><?php _e('Enter in your Facebook App Secret.', 'wiloke'); ?></code>
							</td>
						</tr>
						<tr>
							<th><?php _e('Number Posts', 'wiloke'); ?></th>
							<td>
								<input type="number" min="5" max="500" step="5" name="fb_number_of_posts" value="<?php echo esc_attr(self::$aCommentSettings['fb_number_of_posts']); ?>">
								<code><?php _e('Select the amount of posts per page to display. Valid inputs are between 5 and 500 in increments of 5.', 'wiloke'); ?></code>
							</td>
						</tr>
						<tr>
							<th><?php _e('Post Order', 'wiloke'); ?></th>
							<td>
								<select name="fb_post_order">
									<option value="time" <?php selected(self::$aCommentSettings['fb_post_order'], 'time'); ?>><?php _e('Time', 'wiloke'); ?></option>
									<option value="revert_time" <?php selected(self::$aCommentSettings['fb_post_order'], 'revert_time'); ?>><?php _e('Revert Time', 'wiloke'); ?></option>
									<option value="social" <?php selected(self::$aCommentSettings['fb_post_order'], 'social'); ?>><?php _e('Social', 'wiloke'); ?></option>
								</select>
							</td>
						</tr>
						<tr>
							<th><?php _e('Color Scheme', 'wiloke'); ?></th>
							<td>
								<select name="fb_color_scheme">
									<option value="light" <?php selected(self::$aCommentSettings['fb_color_scheme'], 'light'); ?>><?php _e('Light', 'wiloke'); ?></option>
									<option value="dark" <?php selected(self::$aCommentSettings['fb_color_scheme'], 'dark'); ?>><?php _e('Dark', 'wiloke'); ?></option>
								</select>
							</td>
						</tr>
						<tr>
							<th><?php _e('How to get Facebook App ID and App Secret ?') ?></th>
							<td>
								<?php printf( (__('Firstly, go to the <a href="%s">Facebook Developers</a> portal. Once there, select Apps > Add a New App from the navbar. Next, select Website as your platform and follow the quickstart guide. Once you have completed this process, you will be able to acquire your App ID and App Secret from the application Dashboard.', 'wiloke')), 'https://developers.facebook.com/' );?>
							</td>
						</tr>
					</tbody>
				</table>

				<table id="disqus-comment" class="form-table pi-comment-settings">
					<tr>
						<th><?php _e('Enter Shortname', 'wiloke'); ?></th>
						<td>
							<input name="disqus_shortname" type="text" value="<?php echo esc_attr(self::$aCommentSettings['disqus_shortname']); ?>" />
							<p><?php _e('Your Disqus <strong>shortname</strong> is a unique identifier assigned to your Disqus site. All the comments posted to a site are referenced with the shortname. Go to this <a href="http://disqus.com/register" target="_blank">link</a> to register disqus.', 'wiloke'); ?></p>
						</td>
					</tr>
				</table>

				<table class="form-table">
					<tr>
						<td><button class="button button-primary" type="submit"><?php _e('Save', 'wiloke'); ?></button></td>
					</tr>
				</table>
			</form>
			<?php
		}

		/*Comment Settings*/

	}

	new piCommentType;


	if ( piCommentType::$aCommentSettings )
	{

	    if ( piCommentType::$aCommentSettings['comment_type'] =='facebook' )
	    {
	        add_action( 'wp_head', 'pi_print_facebook_data' );
	        // add_action( 'pi_after_body', 'pi_print_facebook_sdk' );
	        add_action( 'wp_footer', 'pi_print_facebook_sdk' );
	        add_filter( 'comments_template', 'pi_facebook_comments_template' );
	        add_filter( 'pi_comments_nummber', 'pi_facebook_comments_number' );
	    }elseif (piCommentType::$aCommentSettings['comment_type'] =='disqus')
	    {
		  	add_action( 'wp_footer', 'pi_print_disqus_js' );
		  	add_action( 'wp_footer', 'pi_disqus_comments_count' );
		  	add_filter( 'comments_template', 'pi_disqus_comments_template' );
	    }
	}
	
	/*Facebook*/
	function pi_print_facebook_data()
	{
		if ( is_singular() && comments_open() )
		{
	    	echo '<meta property="fb:app_id" content="' . esc_attr(piCommentType::$aCommentSettings['fb_app_id']) . '">';
		}
	}

	function pi_print_facebook_sdk()
	{
	    ?>
	    <div id="fb-root"></div>

	    <script>
	      window.fbAsyncInit = function() {
	        FB.init({
	          appId   : "<?php echo esc_js(piCommentType::$aCommentSettings['fb_app_id']); ?>",
	          xfbml   : true,
	          version : 'v2.1'
	        });
	      };

	      (function(d, s, id){
	         var js, fjs = d.getElementsByTagName(s)[0];
	         if (d.getElementById(id)) {return;}
	         js = d.createElement(s); js.id = id;
	         js.src = "//connect.facebook.net/en_US/sdk.js";
	         fjs.parentNode.insertBefore(js, fjs);
	       }(document, 'script', 'facebook-jssdk'));
	    </script>
	    <?php
	}

	function pi_facebook_comments_template($template)
	{
	   	global $post;
		return plugin_dir_path(__FILE__) . 'facebook-template.php';
	}


	function pi_facebook_comments_number() 
	{

	  $url     = 'https://graph.facebook.com/v2.1/?fields=share{comment_count}&id=' . urlencode( get_the_permalink() );
	  $request = wp_remote_get( $url );
	  $data    = json_decode( $request['body'], true );
	  $number  = $data['share']['comment_count'];

	  return $number;

	}

	/*Disqus*/
	function pi_print_disqus_js() 
	{

	  if ( is_singular() && comments_open() ) : 
	  ?>
	  	<script id="pi-disqus-comments-embed-js" type="text/javascript">
		    var disqus_shortname = "<?php echo esc_js(piCommentType::$aCommentSettings['disqus_shortname']); ?>";
		    (function() {
		      var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
		      dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
		      (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
		    })();
	  	</script>
	  <?php 
	  endif;
	}

	function pi_disqus_comments_count() 
	{
		?>
	  	<script id="pi-disqus-comments-count-js" type="text/javascript">
			var disqus_shortname = "<?php echo esc_js(piCommentType::$aCommentSettings['disqus_shortname']); ?>";
			(function () {
			  var s = document.createElement('script'); s.async = true;
			  s.type = 'text/javascript';
			  s.src = '//' + disqus_shortname + '.disqus.com/count.js';
			  (document.getElementsByTagName('HEAD')[0] || document.getElementsByTagName('BODY')[0]).appendChild(s);
			}());
	  	</script>

		<?php 
	}

	function pi_disqus_comments_template($template)
	{
		global $post;
		return plugin_dir_path(__FILE__) . 'disqus-template.php';
	}

}
