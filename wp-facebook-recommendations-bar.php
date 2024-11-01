<?php
/*
Plugin Name: WP Facebook Recommendations Bar
Version: 1.0
Description: An easy-to-use plugin which adds the Facebook Recommendations Bar to your website. This is a great way to increase the traffic of your site and makes it easier for your readers and fans to promote your articles on Facebook.
Author: Marvie Pons
Author URI: http://tutskid.com/
Donate URI: http://tutskid.com/
Plugin URI: http://tutskid.com/facebook-recommendations-bar-wordpress-plugin/
  
Copyright 2013  Marvie Pons (email: celebritybanderas@gmail.com)

Released under GPL License.
*/

define('WPFBRC_VERSION', '1.0');


// REQUIRE MINIMUM VERSION OF WORDPRESS:                                               

function wpfbrc_requires_wordpress_version() {
	global $wp_version;
	$plugin = plugin_basename( __FILE__ );
	$plugin_data = get_plugin_data( __FILE__, false );

	if ( version_compare($wp_version, "3.5", "<" ) ) {
		if( is_plugin_active($plugin) ) {
			deactivate_plugins( $plugin );
			wp_die( "'".$plugin_data['Name']."' requires WordPress 3.5 or higher, and has been deactivated! Please upgrade WordPress and try again.<br /><br />Back to <a href='".admin_url()."'>WordPress admin</a>." );
		}
	}
}
add_action( 'admin_init', 'wpfbrc_requires_wordpress_version' );

// Set-up Action and Filter Hooks
register_activation_hook(__FILE__, 'wpfbrc_add_defaults');
register_uninstall_hook(__FILE__, 'wpfbrc_delete_plugin_options');
add_action('admin_init', 'wpfbrc_init' );
add_action('admin_menu', 'wpfbrc_add_options_page');
add_filter( 'plugin_action_links', 'wpfbrc_plugin_action_links', 10, 2 );

// Delete options table entries ONLY when plugin deactivated AND deleted
function wpfbrc_delete_plugin_options() {
	delete_option('wpfbrc_options');
}

// Define default option settings
function wpfbrc_add_defaults() {
	$tmp = get_option('wpfbrc_options');
    if(($tmp['chk_default_options_db']=='1')||(!is_array($tmp))) {
		delete_option('wpfbrc_options'); // so we don't have to reset all the 'off' checkboxes too! (don't think this is needed but leave for now)
		$arr = array(	"app_id" => "",
						"lang" => "",
						"fbml" => "1",
						"home" => "",
						"archive" => "",
						"trigger" => "100",
						"verb" => "like",
						"readtime" => "",
						"num_rec" => "",
						"side" => "right",
						"pwrd_lnk" => ""
		);
		update_option('wpfbrc_options', $arr);
	}
}

// Init plugin options to white list our options
function wpfbrc_init(){
	register_setting( 'wpfbrc_plugin_options', 'wpfbrc_options', 'wpfbrc_validate_options' );
}

// Add menu page
function wpfbrc_add_options_page() {
	add_options_page('WP Facebook Recommendations Bar Options Page', 'FB Recommendations', 'manage_options', 'wpfbrc', 'wpfbrc_render_form');
}

// Render the Plugin options form
function wpfbrc_render_form() {
	?>
	<div class="wrap">
	
	<!-- Display Plugin Icon, Header, and Description -->
	
	<div class="icon32" id="icon-options-general"><br></div>
	<h2>WP Facebook Recommendations Bar Plugin</h2>

		<!-- Beginning of the Plugin Options Form -->
		<form method="post" action="options.php">
			<?php settings_fields('wpfbrc_plugin_options'); ?>
			<?php $options = get_option('wpfbrc_options'); ?>
			
<div id="poststuff">
	<div id="post-body" class="metabox-holder columns-2">
		<div id="post-body-content">

			<div class="postbox">
				<div class="inside">

			<!-- Table Structure Containing Form Controls -->
<h2>Main Settings</h2>
			<table class="form-table">

			<!-- Textbox Control -->
	
				<tr valign="top">
					<th scope="row">Facebook App ID</th>
					<td>
						<input type="text" size="57" name="wpfbrc_options[app_id]" value="<?php echo $options['app_id']; ?>" /><br />Enter a valid Facebook app ID.<br /><span style="color:#666666;margin-left:2px;">If you don't have one, you can <a href="https://developers.facebook.com/apps" target="_blank">Create New Facebook App here</a>.</span>
					</td>
				</tr>
				
				<tr>
					<th scope="row">Language</th>
					<td>
						<input type="text" size="57" name="wpfbrc_options[lang]" value="<?php echo $options['lang']; ?>" /><span style="color:#666666;margin-left:2px;"><br />Default is <strong>en_US</strong>. Complete listing of all the locales supported by Facebook are <a href="http://www.facebook.com/translations/FacebookLocales.xml" target="_blank">here</a>.</span>
					</td>
				</tr>
				
				<!-- Checkbox Button -->
				<tr style="border-top:#dddddd 1px solid;">
					<th scope="row">Enable FBML</th>
					<td>
						<!-- First checkbox button -->
						<label><input name="wpfbrc_options[fbml]" type="checkbox" value="1" <?php if (isset($options['fbml'])) { checked('1', $options['fbml']); } ?> /> Only disable if you already have XFBML enabled elsewhere.</label>
						<br /><span style="color:#666666;margin-left:2px;">If left unchecked, then the main settings will not be loaded.</span><br /><span style="color:#666666;margin-left:2px;">If unsure, leave this box checked.</span>
					</td>
				</tr>
			
			</table>

<h2>Display Settings</h2>
			<table class="form-table">			
				
				<!-- Textbox Control -->
				
				<tr valign="top">
					<th scope="row">Trigger</th>
					<td>
						<input type="text" size="57" name="wpfbrc_options[trigger]" value="<?php echo $options['trigger']; ?>" /><span style="color:#666666;margin-left:2px;"><br />This option defines the percentage of the page scrolled by the visitor, before the bar will appear. 50 would be to the mid point of the page.</span>
					</td>
				</tr>
				
				<tr>
					<th scope="row">Read Time</th>
					<td>
						<input type="text" size="57" name="wpfbrc_options[readtime]" value="<?php echo $options['readtime']; ?>" /><span style="color:#666666;margin-left:2px;"><br />The number of seconds the plugin will wait before it expands. The minimum is 10 seconds. Default is set to 30 seconds.</span>
					</td>
				</tr>
				
				<tr>
					<th scope="row">Number of Recommendations</th>
					<td>
						<input type="text" size="57" name="wpfbrc_options[num_rec]" value="<?php echo $options['num_rec']; ?>" /><span style="color:#666666;margin-left:2px;"><br />Default value is 2 and the maximum value is 5.</span>
					</td>
				</tr>
				
				<!-- Select Drop-Down Control -->
				<tr>
					<th scope="row">Verb to Display</th>
					<td>
						<select name='wpfbrc_options[verb]'>
							<option value='like' <?php selected('like', $options['verb']); ?>>Like</option>
							<option value='recommend' <?php selected('recommend', $options['verb']); ?>>Recommend</option>
						</select>
						<span style="color:#666666;margin-left:2px;">Like or Recommend?</span>
					</td>
				</tr>
				
				<tr>
					<th scope="row">Which Side to Appear</th>
					<td>
						<select name='wpfbrc_options[side]'>
							<option value='right' <?php selected('right', $options['side']); ?>>Right</option>
							<option value='left' <?php selected('left', $options['side']); ?>>Left</option>
						</select>
						<span style="color:#666666;margin-left:2px;">The side of the window where the plugin will display.</span>
					</td>
				</tr>
				
				<!-- Checkbox Button -->
				<tr>
					<th scope="row">Enable on Homepage</th>
					<td>
						<!-- First checkbox button -->
						<label><input name="wpfbrc_options[home]" type="checkbox" value="1" <?php if (isset($options['home'])) { checked('1', $options['home']); } ?> /> Activate Recommendations Bar on Homepage</label>
											
					</td>
				</tr>
				
				<tr>
					<th scope="row">Enable on Archive Pages</th>
					<td>
						<!-- Second checkbox button -->
						<label><input name="wpfbrc_options[archive]" type="checkbox" value="1" <?php if (isset($options['archive'])) { checked('1', $options['archive']); } ?> /> Activate Recommendations Bar on Archive Pages</label>
											
					</td>
				</tr>

			</table>				

<h2>Miscellaneous</h2>
			<table class="form-table">

				<!-- Checkbox Button -->
				<tr valign="top">
					<th scope="row">Show Your Support</th>
					<td>
						<!-- First checkbox button -->
						<label><input name="wpfbrc_options[pwrd_lnk]" type="checkbox" value="1" <?php if (isset($options['pwrd_lnk'])) { checked('1', $options['pwrd_lnk']); } ?> /> Support this free plug-in with a small powered by link at your page footer. Thank you!</label>										
					</td>
				</tr>		
			
				<tr><td colspan="2"><div style="margin-top:10px;"></div></td></tr>
				<tr valign="top" style="border-top:#dddddd 1px solid;">
					<th scope="row">Database Options</th>
					<td>
						<label><input name="wpfbrc_options[chk_default_options_db]" type="checkbox" value="1" <?php if (isset($options['chk_default_options_db'])) { checked('1', $options['chk_default_options_db']); } ?> /> Restore defaults upon plugin deactivation/reactivation</label>
						<br /><span style="color:#666666;margin-left:2px;">Only check this if you want to reset plugin settings upon Plugin reactivation</span>
					</td>
				</tr>
			</table>
		<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
		</form>
		
				</div><!-- .inside -->
			</div><!-- .postbox -->
		
		
	

	</div> <!-- #post-body-content -->
	
			<div id="postbox-container-1" class="postbox-container">
				<div id="side-sortables" class="meta-box-sortables">
				
					<div id="about" class="postbox">
						<h3 class="hndle"><span>About the Plugin:</span></h3>
						<div class="inside">
							<p>You are using version <?php echo WPFBRC_VERSION; ?> of<br /> <a href="http://tutskid.com/facebook-recommendations-bar-wordpress-plugin/" target="_blank" style="color:#72a1c6;"><strong>WP Facebook Recommendations Bar</strong>.</a><br /><br />
							WordPress Plugin from <a href="http://tutskid.com/" title="TutsKid.com" target="_blank">TutsKid</a>.</p>
							<p>More Plugins by Marvie Pons:
							<ul>
								<li><a href="http://wordpress.org/extend/plugins/wp-facebook-like-send-open-graph-meta/">WP Facebook Like Send & Open Graph Meta</a></li>
								<li><a href="http://wordpress.org/extend/plugins/yet-another-social-plugin/">Yet Another Social Plugin</a></li>
								<li><a href="http://wordpress.org/extend/plugins/pinterest-verify-meta-tag/">Pinterest Verify Meta Tag</a></li>
								<li><a href="http://wordpress.org/extend/plugins/wp-nofollow-more-links/">WP Nofollow More Links</a></li>
								<li><a href="http://wordpress.org/extend/plugins/rel-nofollow-categories/">Rel Nofollow Categories</a></li>
							</ul>
							</p>
						</div><!-- .inside -->
					</div><!-- #about.postbox -->

					<div id="donate" class="postbox">
						<div class="inside">
						<h3 class="hndle"><span>Enjoy the plugin?</span></h3>	
							<p>If you have found this plugin at all useful, why not consider <a href="http://wordpress.org/extend/plugins/wp-facebook-recommendations-bar/" target="_blank">giving it a good rating on WordPress.org</a>, <a href="http://twitter.com/?status=Facebook Recommendations Bar Plugin for WordPress - check it out! http://wp.me/p2uqdU-tn" target="_blank">Tweet about it</a> and buying me a cup of coffee. Thank you!<br />
							<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
							<input type="hidden" name="cmd" value="_s-xclick">
							<input type="hidden" name="hosted_button_id" value="E5NA5DLGYWGZQ">
							<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
							<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
							</form></p>
						</div><!-- .inside -->
					</div><!-- #donate.postbox -->
					
					<div id="news" class="postbox">
						<h3 class="hndle"><span>Latest Blog Posts from TutsKid</span></h3>
						<div class="inside">
							<?php echo tutskid_news(); ?>
							<span><a href="http://tutskid.com/feed/" title="Subscribe with RSS" target="_blank"><img style="border:0px #ccc solid;" src="<?php echo plugins_url(); ?>/wp-facebook-recommendations-bar/images/rss-icon.png" /></a></span>
							<span><a href="http://www.facebook.com/triptrippertips" title="Become a fan on Facebook" target="_blank"><img style="border:0px #ccc solid;" src="<?php echo plugins_url(); ?>/wp-facebook-recommendations-bar/images/facebook-icon.png" /></a></span>
							<span><a href="http://twitter.com/Triptrippertips" title="Follow us on Twitter" target="_blank"><img style="border:0px #ccc solid;" src="<?php echo plugins_url(); ?>/wp-facebook-recommendations-bar/images/twitter-icon.png" /></a></span>
						</div><!-- .inside -->
					</div><!-- #news.postbox -->

				</div><!-- #side-sortables.meta-box-sortables -->
			</div><!-- .postbox-container -->
	
	</div> <!-- #post-body -->
</div> <!-- #poststuff -->


	
</div>
	<?php	
}

// Sanitize and validate input
function wpfbrc_validate_options($input) {
	if ( ! isset( $input['home'] ) )
		$input['home'] = null;
	$input['home'] = ( $input['home'] == 1 ? 1 : 0 );
	
	if ( ! isset( $input['archive'] ) )
		$input['archive'] = null;
	$input['archive'] = ( $input['archive'] == 1 ? 1 : 0 );
	
	if ( ! isset( $input['fbml'] ) )
		$input['fbml'] = null;
	$input['fbml'] = ( $input['fbml'] == 1 ? 1 : 0 );
	
	if ( ! isset( $input['pwrd_lnk'] ) )
		$input['pwrd_lnk'] = null;
	$input['pwrd_lnk'] = ( $input['pwrd_lnk'] == 1 ? 1 : 0 );
	
	// strip html from textboxes
	$input['readtime'] =  wp_filter_nohtml_kses($input['readtime']); // Sanitize textbox input (strip html tags, and escape characters)
	
	$input['num_rec'] =  wp_filter_nohtml_kses($input['num_rec']); // Sanitize textbox input (strip html tags, and escape characters)
	
	$input['trigger'] =  wp_filter_nohtml_kses($input['trigger']); // Sanitize textbox input (strip html tags, and escape characters)
	
	$input['lang'] =  wp_filter_nohtml_kses($input['lang']); // Sanitize textbox input (strip html tags, and escape characters)
	
	$input['app_id'] =  wp_filter_nohtml_kses($input['app_id']); // Sanitize textbox input (strip html tags, and escape characters)
	
	return $input;
}

// Display a Settings link on the main Plugins page
function wpfbrc_plugin_action_links( $links, $file ) {

	if ( $file == plugin_basename( __FILE__ ) ) {
		$wpfbrc_links = '<a href="'.get_admin_url().'options-general.php?page=wpfbrc">'.__('Settings').'</a>';
		// make the 'Settings' link appear first
		array_unshift( $links, $wpfbrc_links );
	}

	return $links;
}

// ------------------------------------------------------------------------------
// OUR PLUGIN FUNCTIONS:
// ------------------------------------------------------------------------------
$options = get_option('wpfbrc_options');
	if ($options['app_id']!= '')
add_filter ('the_content', 'wpfbrc_recbar');
	else
add_filter ('the_content', 'wpfbrc_appid');
add_action('wp_footer', 'wpfbrc_footer');

function wpfbrc_recbar($content) { 
	$options = get_option('wpfbrc_options');
	
	$trigger = $options['trigger'];
	if($trigger == '') { $trigger = '100'; }
	
	$action = $options['verb'];
	if($action == '') { $action = 'like'; }
	
	$read_time = $options['readtime'];
	if($read_time == '') { $read_time = '30'; }
	
	$num_rec = $options['num_rec'];
	if($num_rec == '') { $num_rec = '2'; }
	
	$side = $options['side'];
	if($side == '') { $side = 'right'; }
	
	$get_locale = $options['lang'];
	if($get_locale == '') { $get_locale = 'en_US'; }
	
	$app_id = $options['app_id'];
	
	$permalink = get_permalink();

	$recbar = '
	<!-- WP Facebook Recommendations Bar plugin v'.WPFBRC_VERSION.' : http://tutskid.com/facebook-recommendations-bar-wordpress-plugin/ -->
	<div id="fb-root"></div><script src="http://connect.facebook.net/'.$get_locale.'/all.js#xfbml=1&appId='.$app_id.'"></script><fb:recommendations-bar data-href="'.$permalink.'" data-trigger="'.$trigger.'%" data-read-time="'.$read_time.'" data-action="'.$action.'" data-side="'.$side.'" data-num-recommendations="'.$num_rec.'"></fb:recommendations-bar>';
	
	$recbarwofbml = '
	<!-- WP Facebook Recommendations Bar plugin v'.WPFBRC_VERSION.' (XFBML disabled): http://tutskid.com/facebook-recommendations-bar-wordpress-plugin/ -->
	<fb:recommendations-bar data-href="'.$permalink.'" data-trigger="'.$trigger.'%" data-read-time="'.$read_time.'" data-action="'.$action.'" data-side="'.$side.'" data-num-recommendations="'.$num_rec.'"></fb:recommendations-bar>';

	if (is_singular() || (is_home() || is_front_page()) && $options['home'] == '1' || is_archive() && $options['archive'] == '1') {
		if ( isset($options['fbml']) && $options['fbml']=="" ) {
		$content .= $recbarwofbml;
			} else {
		$content .= $recbar;
		}
	}
    return $content;
}

function wpfbrc_appid($content) {
	$options = get_option('wpfbrc_options');
	if (is_singular() || (is_home() || is_front_page()) && $options['home'] == '1' || is_archive() && $options['archive'] == '1') {
	$content .= '
	<!-- WP Facebook Recommendations Bar plugin v'.WPFBRC_VERSION.' NEEDS an app ID to work, please visit the plugin settings page! -->';
	}
	return $content;
}

function wpfbrc_footer() {
	$options = get_option('wpfbrc_options');

if ( isset($options['pwrd_lnk']) && ($options['pwrd_lnk']!="") ) { 

		print('<p style="text-align:center;font-size:x-small;color:#666;"><a style="font-weight:normal;color:#666" href="http://tutskid.com/facebook-recommendations-bar-wordpress-plugin/" title="Facebook Recommendations Bar plugin for WordPress" target="_blank">Facebook Recommendations Bar plugin for WordPress</a> powered by <a style="font-weight:normal;color:#666" href="http://tutskid.com/" title="TutsKid | WordPress Tutorials, Themes, Plugins, and more!" target="_blank">TutsKid.com</a>.</p>');
	}
}
function wpfbrc_warning() {
	$options = get_option('wpfbrc_options');
	 if ($options['app_id']== '') {
		if (isset($_GET['page']) &&  $_GET['page'] == 'wpfbrc') {
	 ob_start(); ?>
			<div id='wpfbrc-warning' class='error'>
				<p><strong>WP Facebook Recommendations Bar</strong> is almost ready. You must enter your <strong>Facebook App ID</strong> for it to work.</p>
			</div>
	<?php echo ob_get_clean();
		}
	}
}

add_action('admin_notices', 'wpfbrc_warning');

function tutskid_news() {
	include_once( ABSPATH . WPINC . '/feed.php' );
	$rss = fetch_feed( 'http://feeds.feedburner.com/triptrippertips' );
		if ( ! is_wp_error( $rss ) ) {
			$maxitems = $rss->get_item_quantity( 5 );
			$rss_items = $rss->get_items( 0, $maxitems );
		}
		
echo '<ul>';
    if ( $maxitems == 0 ) {
    echo '<li>';
		echo '<p>The feed is either empty or unavailable.</p>';
	echo '</li>';
    } else {
        foreach ( $rss_items as $item ) {
         echo '<li>';
         echo '<a href="'.esc_url( $item->get_permalink() ).'">'.esc_html( $item->get_title() ).'</a>';
         echo '</li>';
		}
	}
echo '</ul>';
}