<?php
include 'fbcs-functions.php';

define("FBCS_DONATE_LINK","https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=HB9CMQUTS6LK2&lc=US&item_name=btdogan%20%2d%20free%20software&item_number=facebook%20comments%20sync%20plugin&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted");

add_action('admin_init', 'fbcommentssync_init' );

function fbcommentssync_init(){
	register_setting( 'fbcommentssync_options', 'fbcommentssync' );
	$new_options = array(
		'fbjs' => 'on',
		'posts' => 'on',
		'pages' => 'off',
		'homepage' => 'off',
		'app_ID' => '',
		'moderators' => '',
		'num' => '5',
		'title' => '',
		'titleclass' => '',
		'width' => '100%',
		'btdogan' => 'off',
		'scheme' => 'light',
		'language' => 'en_US'
	);

	// if old options exist, update to array
	foreach( $new_options as $key => $value ) {
		if( $existing = get_option( 'fbcommentssync_' . $key ) ) {
			$new_options[$key] = $existing;
			delete_option( 'fbcommentssync_' . $key );
		}

	}


	add_option( 'fbcommentssync', $new_options );
}

add_action('admin_menu', 'disp_fbcommentssync_opt');
function disp_fbcommentssync_opt() {
	add_menu_page('Facebook Comments Sync Options', 'Facebook Comments Sync', 'manage_options', 'fbcommentssync', 'fbcommentssync_options');
}

function fbcommentssync_admin_notice(){
$options = get_option('fbcommentssync');
if ($options['app_ID']=="") {
	$fbadminurl = get_admin_url()."options-general.php?page=fbcommentssync";
    echo '<div class="error">
       <p>Please enter your Facebook App ID for Facebook Comments to work properly. <a href="'.$fbadminurl.'"><input type="submit" value="Enter App ID" class="button-secondary" /></a></p>
    </div>';
}
}
add_action('admin_notices', 'fbcommentssync_admin_notice');

// ADMIN PAGE
function fbcommentssync_options() {
?>
    <link href="<?php echo plugins_url( 'admin.css' , __FILE__ ); ?>" rel="stylesheet" type="text/css">
    <div class="options">
        <div class="options_header">
            <h1>Facebook Comments Sync</h1>
        </div>

        <div class="options">
            <div class="options_left">
				<div class="inside">
		<form id="import_form" action="" method="post"></form>
		<form method="post" action="options.php" id="options">
			<?php settings_fields('fbcommentssync_options');
				$options = get_option('fbcommentssync');
				if (!isset($options['fbjs'])) {$options['fbjs'] = "";}
				if (!isset($options['btdogan'])) {$options['btdogan'] = "";}
				if (!isset($options['posts'])) {$options['posts'] = "";}
				if (!isset($options['pages'])) {$options['pages'] = "";}
				if (!isset($options['homepage'])) {$options['homepage'] = "";}
				if (!isset($options['count'])) {$options['count'] = "";}
				if ($options['app_ID']=="") { ?>
			<div class="error">
			<h3 class="title">Facebook App ID is required!</h3>
			<table class="form-table">
				<tr valign="top">
				<th scope="row"><label>Facebook App ID:</label></th>
				<td><input id="app_ID" type="text" name="fbcommentssync[app_ID]" value="<?php echo $options['app_ID']; ?>" size="20" /><strong><a href="https://developers.facebook.com/apps" style="text-decoration:none; padding-left: 10px;" target="_blank">(Set up your Facebook App ID)</a></strong>
				<br></td>
				</tr>
			</table>
			</div>
<?php } ?>

			<h3 class="title">Setup Settings</h3>
			<table class="form-table">
			<?php if ($options['app_ID']!="") { ?>
				<tr valign="top"><th scope="row"><label>Facebook App ID:</label></th>
					<td><input id="app_ID" type="text" name="fbcommentssync[app_ID]" value="<?php echo $options['app_ID']; ?>" size="20"/><strong><a href="https://developers.facebook.com/apps<?php if ($options['app_ID'] != "") { echo "/".$options['app_ID']."/summary"; } ?>" style="text-decoration:none; padding-left: 10px;" target="_blank">(App Setttings)</a></strong></td>
				</tr>
<?php } ?>
				<tr valign="top"><th scope="row"><label>Moderators:</label></th>
					<td><input id="moderators" type="text" name="fbcommentssync[moderators]" value="<?php echo $options['moderators']; ?>" size="20" /><strong><a href="https://developers.facebook.com/tools/comments<?php if ($options['app_ID'] != "") { echo "?id=".$options['app_ID']."&view=queue"; } ?>" style="text-decoration:none; padding-left:10px;" target="_blank">Comment Moderation</a></strong><br><small>By default, all admins to the App ID can moderate comments. To add moderators, enter each Facebook Profile ID by a comma <strong>without spaces</strong>. To find your Facebook User ID, click <a href="https://developers.facebook.com/tools/explorer/?method=GET&path=me" target="blank">here</a> where you will see your own. To view someone else's, replace "me" with their username in the input provided</small></td>
				</tr>
			</table>

			<h3 class="title">Facebook Sync</h3>
			<table class="form-table">
				<tr valign="top">
					<th scope="row">
						<input form="import_form" type="submit" name="" class="button-primary" value="Sync Comments"/>
					</th>
					<td id="synctext"><small>To sync previously entered Facebook Comments with your database, use this button.<br/>By default, comments are added(removed) to your database when they are entered(deleted).</small></td>
				</tr>
			</table>

<script>
    jQuery(document).ready(function($) {
        $('#import_form').submit(function() {
		$('#synctext').html('<small><img src="<?php echo admin_url('/images/wpspin_light.gif'); ?>" class="waiting" /> Comments are syncing...</small>');
		$.ajax({
			type: 'POST',
			url: ajaxurl,
			data: {'action': 'fbcs_import'},
			success:function(){
			$('#synctext').html('<small>Yayy!!! Comments are successfully synced</small>');
			},
			error:function(exception){alert('Unexpected error occurred. Please refresh the page and try again. If you see this error message again after 5 mins, please contact the plugin author (@btdogan) for debug');console.log(exception);}
			});
            return false;
        });
    });
</script>

			<h3 class="title">Main Settings</h3>
			<table class="form-table">

				<tr valign="top"><th scope="row"><label for="fbjs">FBML</label></th>
					<td><input id="fbjs" name="fbcommentssync[fbjs]" type="checkbox" value="on" <?php checked('on', $options['fbjs']); ?> /> <small>If you already have XFBML enabled, disable this.</small></td>
				</tr>
				<tr valign="top"><th scope="row"><label for="btdogan">Promote</label></th>
					<td><input id="credit" name="fbcommentssync[btdogan]" type="checkbox" value="on" <?php checked('on', $options['btdogan']); ?> /><small>This changes Facebook's default credit text with "Facebook Comments Sync".</small></td>
				</tr>
			</table>

			<h3 class="title">Display Settings</h3>
			<table class="form-table">
				<tr valign="top"><th scope="row"><label for="posts">Posts</label></th>
					<td><input id="posts" name="fbcommentssync[posts]" type="checkbox" value="on" <?php checked('on', $options['posts']); ?> /> <small>Note: To disable comment box for a specific post, please use the meta box at the bottom of the page that you edit your post.</small></td>
				</tr>
				<tr valign="top"><th scope="row"><label for="pages">Pages</label></th>
					<td><input id="pages" name="fbcommentssync[pages]" type="checkbox" value="on" <?php checked('on', $options['pages']); ?> /> <small>Note: To disable comment box for a specific page, please use the meta box at the bottom of the page that you edit your page.</small></td>
				</tr>
				<tr valign="top"><th scope="row"><label for="homepage">Homepage</label></th>
					<td><input id="home" name="fbcommentssync[homepage]" type="checkbox" value="on" <?php checked('on', $options['homepage']); ?> /></td>
				</tr>
				<tr valign="top"><th scope="row"><label for="language">Language</label></th>
					<td>
						<select name="fbcommentssync[language]">
							<option value="af_ZA" <?php selected( $options['language'], 'af_ZA' ); ?>>Afrikaans</option>
							<option value="ar_AR" <?php selected( $options['language'], 'ar_AR' ); ?>>Arabic</option>
							<option value="az_AZ" <?php selected( $options['language'], 'az_AZ' ); ?>>Azerbaijani</option>
							<option value="be_BY" <?php selected( $options['language'], 'be_BY' ); ?>>Belarusian</option>
							<option value="bg_BG" <?php selected( $options['language'], 'bg_BG' ); ?>>Bulgarian</option>
							<option value="bn_IN" <?php selected( $options['language'], 'bn_IN' ); ?>>Bengali</option>
							<option value="bs_BA" <?php selected( $options['language'], 'bs_BA' ); ?>>Bosnian</option>
							<option value="ca_ES" <?php selected( $options['language'], 'ca_ES' ); ?>>Catalan</option>
							<option value="cs_CZ" <?php selected( $options['language'], 'cs_CZ' ); ?>>Czech</option>
							<option value="cy_GB" <?php selected( $options['language'], 'cy_GB' ); ?>>Welsh</option>
							<option value="da_DK" <?php selected( $options['language'], 'da_DK' ); ?>>Danish</option>
							<option value="de_DE" <?php selected( $options['language'], 'de_DE' ); ?>>German</option>
							<option value="el_GR" <?php selected( $options['language'], 'el_GR' ); ?>>Greek</option>
							<option value="en_GB" <?php selected( $options['language'], 'en_GB' ); ?>>English (UK)</option>
							<option value="en_PI" <?php selected( $options['language'], 'en_PI' ); ?>>English (Pirate)</option>
							<option value="en_UD" <?php selected( $options['language'], 'en_UD' ); ?>>English (Upside Down)</option>
							<option value="en_US" <?php selected( $options['language'], 'en_US' ); ?>>English (US)</option>
							<option value="eo_EO" <?php selected( $options['language'], 'eo_EO' ); ?>>Esperanto</option>
							<option value="es_ES" <?php selected( $options['language'], 'es_ES' ); ?>>Spanish (Spain)</option>
							<option value="es_LA" <?php selected( $options['language'], 'es_LA' ); ?>>Spanish</option>
							<option value="et_EE" <?php selected( $options['language'], 'et_EE' ); ?>>Estonian</option>
							<option value="eu_ES" <?php selected( $options['language'], 'eu_ES' ); ?>>Basque</option>
							<option value="fa_IR" <?php selected( $options['language'], 'fa_IR' ); ?>>Persian</option>
							<option value="fb_LT" <?php selected( $options['language'], 'fb_LT' ); ?>>Leet Speak</option>
							<option value="fi_FI" <?php selected( $options['language'], 'fi_FI' ); ?>>Finnish</option>
							<option value="fo_FO" <?php selected( $options['language'], 'fo_FO' ); ?>>Faroese</option>
							<option value="fr_CA" <?php selected( $options['language'], 'fr_CA' ); ?>>French (Canada)</option>
							<option value="fr_FR" <?php selected( $options['language'], 'fr_FR' ); ?>>French (France)</option>
							<option value="fy_NL" <?php selected( $options['language'], 'fy_NL' ); ?>>Frisian</option>
							<option value="ga_IE" <?php selected( $options['language'], 'ga_IE' ); ?>>Irish</option>
							<option value="gl_ES" <?php selected( $options['language'], 'gl_ES' ); ?>>Galician</option>
							<option value="he_IL" <?php selected( $options['language'], 'he_IL' ); ?>>Hebrew</option>
							<option value="hi_IN" <?php selected( $options['language'], 'hi_IN' ); ?>>Hindi</option>
							<option value="hr_HR" <?php selected( $options['language'], 'hr_HR' ); ?>>Croatian</option>
							<option value="hu_HU" <?php selected( $options['language'], 'hu_HU' ); ?>>Hungarian</option>
							<option value="hy_AM" <?php selected( $options['language'], 'hy_AM' ); ?>>Armenian</option>
							<option value="id_ID" <?php selected( $options['language'], 'id_ID' ); ?>>Indonesian</option>
							<option value="is_IS" <?php selected( $options['language'], 'is_IS' ); ?>>Icelandic</option>
							<option value="it_IT" <?php selected( $options['language'], 'it_IT' ); ?>>Italian</option>
							<option value="ja_JP" <?php selected( $options['language'], 'ja_JP' ); ?>>Japanese</option>
							<option value="ka_GE" <?php selected( $options['language'], 'ka_GE' ); ?>>Georgian</option>
							<option value="km_KH" <?php selected( $options['language'], 'km_KH' ); ?>>Khmer</option>
							<option value="ko_KR" <?php selected( $options['language'], 'ko_KR' ); ?>>Korean</option>
							<option value="ku_TR" <?php selected( $options['language'], 'ku_TR' ); ?>>Kurdish</option>
							<option value="la_VA" <?php selected( $options['language'], 'la_VA' ); ?>>Latin</option>
							<option value="lt_LT" <?php selected( $options['language'], 'lt_LT' ); ?>>Lithuanian</option>
							<option value="lv_LV" <?php selected( $options['language'], 'lv_LV' ); ?>>Latvian</option>
							<option value="mk_MK" <?php selected( $options['language'], 'mk_MK' ); ?>>Macedonian</option>
							<option value="ml_IN" <?php selected( $options['language'], 'ml_IN' ); ?>>Malayalam</option>
							<option value="ms_MY" <?php selected( $options['language'], 'ms_MY' ); ?>>Malay</option>
							<option value="nb_NO" <?php selected( $options['language'], 'nb_NO' ); ?>>Norwegian (bokmal)</option>
							<option value="ne_NP" <?php selected( $options['language'], 'ne_NP' ); ?>>Nepali</option>
							<option value="nl_NL" <?php selected( $options['language'], 'nl_NL' ); ?>>Dutch</option>
							<option value="nn_NO" <?php selected( $options['language'], 'nn_NO' ); ?>>Norwegian (nynorsk)</option>
							<option value="pa_IN" <?php selected( $options['language'], 'pa_IN' ); ?>>Punjabi</option>
							<option value="pl_PL" <?php selected( $options['language'], 'pl_PL' ); ?>>Polish</option>
							<option value="ps_AF" <?php selected( $options['language'], 'ps_AF' ); ?>>Pashto</option>
							<option value="pt_BR" <?php selected( $options['language'], 'pt_BR' ); ?>>Portuguese (Brazil)</option>
							<option value="pt_PT" <?php selected( $options['language'], 'pt_PT' ); ?>>Portuguese (Portugal)</option>
							<option value="ro_RO" <?php selected( $options['language'], 'ro_RO' ); ?>>Romanian</option>
							<option value="ru_RU" <?php selected( $options['language'], 'ru_RU' ); ?>>Russian</option>
							<option value="sk_SK" <?php selected( $options['language'], 'sk_SK' ); ?>>Slovak</option>
							<option value="sl_SI" <?php selected( $options['language'], 'sl_SI' ); ?>>Slovenian</option>
							<option value="sq_AL" <?php selected( $options['language'], 'sq_AL' ); ?>>Albanian</option>
							<option value="sr_RS" <?php selected( $options['language'], 'sr_RS' ); ?>>Serbian</option>
							<option value="sv_SE" <?php selected( $options['language'], 'sv_SE' ); ?>>Swedish</option>
							<option value="sw_KE" <?php selected( $options['language'], 'sw_KE' ); ?>>Swahili</option>
							<option value="ta_IN" <?php selected( $options['language'], 'ta_IN' ); ?>>Tamil</option>
							<option value="te_IN" <?php selected( $options['language'], 'te_IN' ); ?>>Telugu</option>
							<option value="th_TH" <?php selected( $options['language'], 'th_TH' ); ?>>Thai</option>
							<option value="tl_PH" <?php selected( $options['language'], 'tl_PH' ); ?>>Filipino</option>
							<option value="tr_TR" <?php selected( $options['language'], 'tr_TR' ); ?>>Turkish</option>
							<option value="uk_UA" <?php selected( $options['language'], 'uk_UA' ); ?>>Ukrainian</option>
							<option value="vi_VN" <?php selected( $options['language'], 'vi_VN' ); ?>>Vietnamese</option>
							<option value="zh_CN" <?php selected( $options['language'], 'zh_CN' ); ?>>Simplified Chinese (China)</option>
							<option value="zh_HK" <?php selected( $options['language'], 'zh_HK' ); ?>>Traditional Chinese (Hong Kong)</option>
							<option value="zh_TW" <?php selected( $options['language'], 'zh_TW' ); ?>>Traditional Chinese (Taiwan)</option>
						</select>
					</td>
				</tr>
				<tr valign="top"><th scope="row"><label for="scheme">Colour Scheme</label></th>
					<td>
						<select name="fbcommentssync[scheme]">
							  <option value="light"<?php if ($options['scheme'] == 'light') { echo ' selected="selected"'; } ?>>Light</option>
							  <option value="dark"<?php if ($options['scheme'] == 'dark') { echo ' selected="selected"'; } ?>>Dark</option>
						</select>
					</td>
				</tr>
				<tr valign="top"><th scope="row"><label for="num">Number of Comments</label></th>
					<td><input id="num" type="text" name="fbcommentssync[num]" value="<?php echo $options['num']; ?>" /> <small>default is <strong>5</strong></small></td>
				</tr>
				<tr valign="top"><th scope="row"><label for="width">Width</label></th>
					<td><input id="width" type="text" name="fbcommentssync[width]" value="<?php echo $options['width']; ?>" /> <small>default is <strong>100%</strong>. Keep at this to ensure the comment box is responsive</small></td>
				</tr>
				<tr valign="top"><th scope="row"><label for="title">Title</label></th>
					<td><input id="title" type="text" name="fbcommentssync[title]" value="<?php echo $options['title']; ?>" /> with a CSS class of <input type="text" name="fbcommentssync[titleclass]" value="<?php echo $options['titleclass']; ?>" /></td>
				</tr>
			</table>

			<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
		</form>

					<h3 class="title">Shortcodes</h3>
					<table class="form-table">
						<tr valign="top">
							<th scope="row">Injection</th>
							<td><p>For posts/pages (default settings):</p>
								<p><code>[fbcommentssync]</code></p>
								<p>For PHP (default settings):</p>
								<p><code>&lt;?php echo do_shortcode('[fbcommentssync]'); ?&gt;</code></p>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">Variables</th>
							<td><p>To manually adding comment box into any place in your website or override the default settings, you can use shortcodes.</p>
								<ul>
									<li><strong>url:</strong> url of the comments (leave blank for current URL)</li>
									<li><strong>width:</strong>  width of the comment box (px/%)</li>
									<li><strong>title:</strong> title of the comments box (you can leave blank)</li>
									<li><strong>num:</strong> number of comments (number)</li>
									<li><strong>scheme:</strong> colour scheme (light/dark)</li>
								</ul>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								Examples
							</th>
							<td><p>For posts/pages (override):</p>
								<p><code>[fbcommentssync url="http://btdogan.com" width="100%" num="5"]</code></p>
								<p>For PHP (override):</p>
								<p><code>&lt;?php echo do_shortcode('[fbcommentssync url="http://btdogan.com" width="100%" num="5"]'); ?&gt;</code></p>
						</tr>
					</table>

			</div></div>
            <div class="options_right">
                 <div class="donation">
					<a href="<?php echo FBCS_DONATE_LINK; ?>" target="_blank"><img class="paypal" src="<?php echo plugins_url( 'images/paypal.gif' , __FILE__ ); ?>" width="150" height="50"></a>
					<p>If you like this plugin, please help me building more open source projects with your donation. Every cent counts, I appreciate your kindness.</p>
                </div>

                <div class="box">
                    <h3>About the Author</h3>
                    <h2>Burak T. DOGAN</h2>
                    <p class="atitle">Engineer(M.Sc.)/Developer</p><p>Looking for job!<br/>Available for Hire!<br/><br/><a href="http://btdogan.com">btdogan.com</a></p>
					<a href="https://twitter.com/btdogan" class="twitter-follow-button">Follow @btdogan</a>
				</div>

                <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>

            </div>
        </div>
    </div>



<?php
}

add_action('wp_ajax_fbcs_import', 'fbcsf_runfbcs');
add_action('wp_ajax_nopriv_fbcs_ajaxCA', 'fbcsf_runajaxCA');
add_action('wp_ajax_fbcs_ajaxCA', 'fbcsf_runajaxCA');
add_action('wp_ajax_nopriv_fbcs_ajaxCR', 'fbcsf_runajaxCR');
add_action('wp_ajax_fbcs_ajaxCR', 'fbcsf_runajaxCR');

function fbcs_add_custom_box() {
    $post_types = get_post_types( '', 'names' );
    $options = get_option('fbcommentssync');
    if (!isset($options['posts'])) {$options['posts'] = "";}
	if (!isset($options['pages'])) {$options['pages'] = "";}
    foreach ( $post_types as $post_type ) {
        if ( "post" == $post_type ) {
        	if ($options['posts']=='on') {
	            add_meta_box(
	                'fbcs_sectionid',
	                __( 'Facebook Comments Sync', 'fbcs_singlemeta' ),
	                'fbcs_metabox',
	                $post_type,
	                'advanced',
	                'core'
	                );
	        }
        } elseif ( "page" == $post_type) {
        	if ($options['pages']=='on') {
	            add_meta_box(
	                'fbcs_sectionid',
	                __( 'Facebook Comments Sync', 'fbcs_singlemeta' ),
	                'fbcs_metabox',
	                $post_type,
	                'advanced',
	                'core'
	                );
       		}
        } else {
        	if ($options['posts']=='on') {
	            add_meta_box(
	                'fbcs_sectionid',
	                __( 'Facebook Comments Sync', 'fbcs_singlemeta' ),
	                'fbcs_metabox',
	                $post_type,
	                'advanced',
	                'high'
	                );
        	}
        }
    }
}
add_action( 'add_meta_boxes', 'fbcs_add_custom_box' );

function fbcs_save_postdata( $post_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ){
        return;
    }
    if ( !isset( $_POST['fbcs_noncename'] ) ) {
        return;
    }
    if ( isset( $_POST['fbcs_noncename'] ) && !wp_verify_nonce( $_POST['fbcs_noncename'], plugin_basename( __FILE__ ) ) ){
        return;
    }
    if ( 'page' == $_POST['post_type'] ){
        if ( !current_user_can( 'edit_page', $post_id ) ){
            return;
        }
    } else {

        if ( !current_user_can( 'edit_post', $post_id ) ){
            return;
        }
    }

	$_disable_fbcs_data = sanitize_text_field( $_POST['_disable_fbcs'] );
    add_post_meta($post_id, '_disable_fbcs', $_disable_fbcs_data, true) or
    update_post_meta($post_id, '_disable_fbcs', $_disable_fbcs_data);

}

add_action( 'save_post', 'fbcs_save_postdata' );

function fbcs_metabox() {
  wp_nonce_field( plugin_basename( __FILE__ ), 'fbcs_noncename' );
  $_disable_fbcs = get_post_meta( get_the_ID(), $key = '_disable_fbcs', $single = true );
?>
    <input id="_disable_fbcs" name="_disable_fbcs" type="checkbox" value="on" <?php checked('on', $_disable_fbcs); ?> /> <label for="_disable_fbcs">Don't use Facebook Comments</label>
<?php
//}
}
?>