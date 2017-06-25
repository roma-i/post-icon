<?php 
/*

Plugin Name: Post Icon
Plugin URI:  https://github.com/roma-i/post-icon.git
Description: Plugin for adding icons to posts titles
Version:     1.0
Author:      Roman I
Text Domain: post-icon

*/


//Adding item to menu
add_action('admin_menu', 'pic_admin_submenu');

function pic_admin_submenu() {
	add_submenu_page(
		'options-general.php',
		'Custon Post Title Icon',
		'Post Icon',
		'manage_options',
		'post-icon-submenu-page',
		'pic_submenu_page_callback' 
	);
}

//Register settings
add_action( 'admin_init', 'pic_plugin_settings' );
function pic_plugin_settings() {
	register_setting( 'pic-plugin-settings-group', 'pic_posts_id', 'pic_sanitize_settings' );
	register_setting( 'pic-plugin-settings-group', 'pic_icon_class', 'pic_sanitize_settings' );
	register_setting( 'pic-plugin-settings-group', 'pic_active_checkbox', 'pic_sanitize_settings' );
	register_setting( 'pic-plugin-settings-group', 'pic_position', 'pic_sanitize_settings' );
}

//Settings sanitize
function pic_sanitize_settings( $input ) {
	$input['pic_posts_id'] = sanitize_text_field( $input['pic_posts_id'] );
	$input['pic_icon_class'] = sanitize_text_field( $input['pic_icon_class'] );
	$input['pic_active_checkbox'] = ( $input['pic_active_checkbox'] == 'on' ) ? 'on' : '';
	$input['pic_position'] = sanitize_option( 'pic_position', 'left' );
	return $input;
}

//Generate plugin settings page in WP Admin Panel
function pic_submenu_page_callback() { ?>
	<div class="wrap">
		<h2><?php esc_html_e('Add custom icon to post titles', 'post-icon'); ?></h2>
	</div>
	<div class="wrap">
		<form method="post" action="options.php">
		    <?php settings_fields( 'pic-plugin-settings-group' ); ?>
		    <?php do_settings_sections( 'pic-plugin-settings-group' ); ?>
		    <table class="form-table">
		    	
		        <tr valign="top">
		        	<th scope="row"><?php esc_html_e('Posts ID', 'post-icon'); ?></th>
		        	<td><input type="text" name="pic_posts_id" value="<?php echo esc_attr( get_option('pic_posts_id') ); ?>" /></td>
		        </tr>

		        <tr valign="top">
			        <th scope="row"><?php esc_html_e('Icon Class', 'post-icon'); ?></th>
			        <td><input type="text" name="pic_icon_class" value="<?php echo esc_attr( get_option('pic_icon_class') ); ?>" /> <?php esc_html_e('Please, use FontAwesome icon classes (for example: fa fa-globe).', 'post-icon'); ?></td>
		        </tr>

		        <tr valign="top">
			        <th scope="row"><?php esc_html_e('Active', 'post-icon'); ?></th>
			        <td><input type="checkbox" name="pic_active_checkbox" value="1" <?php checked('1', get_option('active_checkbox')); ?>/></td>
		        </tr>

		        <tr valign="top">
			        <th scope="row"><?php esc_html_e('Icon position', 'post-icon'); ?></th>
			        <td>
				        <select name="pic_position">
						  <option value="left" <?php selected( get_option('pic_position'), 'left' ); ?>><?php esc_html_e('Left', 'post-icon'); ?></option>
						  <option value="right" <?php selected( get_option('pic_position'), 'right' ); ?>><?php esc_html_e('Right', 'post-icon'); ?></option>
						</select>
					</td>
		        </tr>

		    </table>
		    
		    <?php submit_button(); ?>

		</form>
	</div>

<?php }

//Adding Icon to Post Title
function pic_add_to_title($title,$id){
  	$selected_id = explode(",",get_option('pic_posts_id'));
  	if (is_array($selected_id) && in_array($id, $selected_id) && get_option('pic_active_checkbox')) {
	  	$pic_icon_position = get_option('pic_position');
	  	$pic_icon = ' <i class="'.get_option('pic_icon_class').'"></i> ';
	  	if ($pic_icon_position == 'left') {
	  		$title = $pic_icon.$title;
	  	} else {
	  		$title = $title.$pic_icon;
	  	}
  	}
  	return $title;
}
add_filter( 'the_title', 'pic_add_to_title',10,2);

//Include FontAwesome library
function pic_plugin_icon_style(){
    wp_enqueue_style( 'font-awesome', plugins_url('/assets/css/font-awesome.min.css', __FILE__), 'all');
}
add_action('wp_enqueue_scripts', 'pic_plugin_icon_style');