<?php

if ( !defined( 'ABSPATH' ) ) {
	die;
}

// Creates necessary plugin database tables
function wptw_create_tables() {
	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();
	$table_name = WPTW_WALLS_TABLE;
	$sql = "CREATE TABLE $table_name (
	  id mediumint(9) NOT NULL AUTO_INCREMENT,
	  title text NOT NULL,
	  description text NOT NULL,
	  tweets text NOT NULL,
	  UNIQUE KEY id (id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
}

// Returns all table rows as array
function wptw_db_get_results($table_name) {
	global $wpdb;
	$query = "SELECT * FROM $table_name;";
	$results = $wpdb->get_results($query);
	return $results;
}

// Creates DB entry
function wptw_db_create($table_name, $args) {
	global $wpdb;
	$insert = $wpdb->insert($table_name, $args);
	return $wpdb->insert_id;
}

// Updates database row based on ID
function wptw_db_update($table_name, $id, $args) {
	global $wpdb;
	$where = [
		'id' => $id
	];
	$wall = $wpdb->update($table_name, $args, $where);
	return $id;
}

// Deletes item from DB with given ID
function wptw_db_delete($table_name, $id) {
	global $wpdb;
	$where = [
		'id' => $id
	];
	$wpdb->delete($table_name, $where);
}

// Gets result from DB via ID
function wptw_db_get_by_id($table_name, $id) {
	global $wpdb;
	$query = "SELECT * FROM $table_name WHERE id = '$id';";
	$results = $wpdb->get_row($query);
	return $results;
}

// Create a wall
function wptw_create_wall($title = '', $description = '', $tweets = []) {
	global $wpdb;
	$table_name = WPTW_WALLS_TABLE;
	$args = [
		'title' => $title,
		'description' => $description,
		'tweets' => serialize($tweets)
	];
	$wall = $wpdb->insert($table_name, $args);
	return $wpdb->insert_id;
}

// Update a wall
function wptw_update_wall($id = '', $title = '', $description = '', $tweets = []) {
	global $wpdb;
	$table_name = WPTW_WALLS_TABLE;
	$args = [
		'title' => $title,
		'description' => $description,
		'tweets' => serialize($tweets)
	];
	$where = [
		'id' => $id
	];
	$wall = $wpdb->update($table_name, $args, $where);
	return $id;
}

// Delete a wall
function wptw_delete_wall($id) {
	global $wpdb;
	$table_name = WPTW_WALLS_TABLE;
	$where = [
		'id' => $id
	];
	$wpdb->delete($table_name, $where);
}

// Get wall by ID
function wptw_get_wall($id) {
	global $wpdb;
	$table_name = WPTW_WALLS_TABLE;
	$query = "SELECT * FROM $table_name WHERE id = '$id';";
	$wall = $wpdb->get_row($query);
	
	if (is_object($wall)) {
		$wall->tweets = (array) maybe_unserialize( $wall->tweets );
	}

	return $wall;
}

// Get all tweet walls
function wptw_get_walls() {
	global $wpdb;
	$table_name = WPTW_WALLS_TABLE;
	$query = "SELECT * FROM $table_name;";
	$walls = $wpdb->get_results($query);
	return $walls;
}

// Get tweet wall count
function wptw_get_wall_count() {
	global $wpdb;
	$table_name = WPTW_WALLS_TABLE;
	$query = "SELECT COUNT(*) FROM $table_name;";
	return $wpdb->get_var($query);
}

// Get tweets from a specific wall
function wptw_get_wall_tweets($wall) {
	$tweets = $wall->tweets;
	$wall_tweets = [];

	foreach ($tweets as $tweet) {
		$tweet = wptw_twitter_get_tweet($tweet);
		if (!is_null($tweet)) {
			$wall_tweets[] = $tweet;
		}
	}

	return $wall_tweets;
}

// Get tweet wall item HTML
function wptw_wall_html($wall) {
	$title = !empty($wall->title) ? $wall->title : __( 'Untitled Wall', 'wp-tweet-walls' );
	$tweets = maybe_unserialize( $wall->tweets );
	$count = !empty($tweets) ? sizeof($tweets) : 0;
	?>
		<div class="wptw-twitter-wall__item" data-wall-id="<?php echo $wall->id; ?>">
			<span class="wptw-wall__item-id"><?php echo $wall->id; ?></span>
			<span class="wptw-wall__item-content">
				<span class="wptw-wall__item-title"><?php echo $title; ?></span>
				<?php if (!empty($wall->description)) : ?>
					<span class="wptw-wall__item-description"><?php echo $wall->description; ?></span>
				<?php endif; ?>
			</span>
			<span class="wptw-wall__item-tweet-count"><span class="dashicons dashicons-twitter"></span><?php echo $count; ?></span>
		</div>
	<?php
	$html = ob_get_clean();
	return $html;
}

// Get tweet data from the Twitter API for a specific URL
function wptw_twitter_get_tweet($url) {
	$settings = wptw_get_settings();
	$base = apply_filters( 'wptw_twitter_base_url', 'https://publish.twitter.com/oembed?cards=hidden&hide_media=1&url=' );
	$url = $base . $url;

	if ($settings['theme'] == 'dark') {
		$url .= '&theme=dark';
	}

	if (!$settings['show_media']) {
		$url .= '&cards=hidden&hide_media=1';
	}

	if (!empty($settings['link_color'])) {
		$color = urlencode($settings['link_color']);
		$url .= '&link_color=' . $color;
	}

	$url .= '&omit_script=true';

	$args = array(
	    'timeout' => 10
	); 
	$result = wptw_get_url_content($url);
	
	return $result;
}

// Gets the content of a request and decodes it
function wptw_get_url_content($url) {
	$result = null;
	$args = array(
	    'timeout' => 10
	);
	$response = wp_remote_get( $url, $args );

	if (!is_wp_error($response)) {
		$body = $response['body'];
		$result = json_decode( $body );
	}

	return $result;
}

// Checks whether the pro version is installed
function wptw_is_pro() {
	if (function_exists('wptw_pro_init')) {
		return true;
	} else {
		return false;
	}
}

// Checks if it is the first time the user is using the plugin
function wptw_is_first_time() {
	if (get_option( 'wptw_installed' )) {
		return false;
	} else {
		return true;
	}
}

// Get maxiumum allowed tweet walls
function wptw_get_max_walls() {
	$max = apply_filters( 'wptw_max_walls', 3 );
	return $max;
}

// Checks whether the tweet wall limit is reached
function wptw_is_limit_reached() {
	if (wptw_is_pro()) {
		return false;
	}
	$max_walls = wptw_get_max_walls();
	$wall_count = wptw_get_wall_count();
	$is_limit_reached = $wall_count < $max_walls ? false : true;
	return $is_limit_reached;
}

function wptw_get_settings() {
	$defaults = [
		'theme' 			=> 'light',
		'columns' 			=> 3,
		'show_conversation' => true,
		'display_title' 	=> true,
		'show_media'		=> true,
		'border_color'		=> '',
		'link_color'		=> ''
	];
	$defaults = apply_filters( 'wptw_default_settings', $defaults );
	$settings = maybe_unserialize( get_option( 'wptw_settings', [] ) );
	$settings = wp_parse_args( $settings, $defaults );
	return $settings;
}

function wptw_update_settings($args) {
	$defaults = wptw_get_settings();
	$settings = wp_parse_args( $args, $defaults );
	$settings = apply_filters( 'wptw_save_settings', $settings );
	update_option( 'wptw_settings', serialize($settings) );
}

function wptw_add_subpage($page) {
	add_filter( 'wptw_subpages', function( $subpages ) {
		$subpages[] = $page;
		return $subpages;
	});
}

function wptw_modal($title = '', $content = '', $id = '', $classes = '') {
	ob_start();
	?>

	<div id="<?php echo $id; ?>" class="wptw-modal <?php echo $classes; ?>">
		<h3 class="wptw-modal-title"><?php echo $title; ?></h3>
		<div class="wptw-modal-content">
			<?php echo $content; ?>
		</div>
	</div>

	<?php
	$html = ob_get_clean();
	echo $html;
}

function wptw_get_settings_pages() {
	$pages = [];
	$pages = apply_filters( 'wptw_settings_pages', $pages );
	return $pages;
}

function wptw_get_pages() {
	$pages = [
		'wp_tweet_walls',
		'wp_tweet_walls_timelines',
		'wp_tweet_walls_buttons',
		'wp_tweet_walls_settings'
	];
	return $pages;
}

function wptw_hide_unrelated_notices() {

		global $wp_filter;

		$wptw_pages = wptw_get_pages();

		// Quit if it is not on our pages
		if ( empty( $_REQUEST['page'] ) || in_array($_REQUEST['page'], $wptw_pages) === false ) {
			return;
		}

		if ( ! empty( $wp_filter['user_admin_notices']->callbacks ) && is_array( $wp_filter['user_admin_notices']->callbacks ) ) {
			foreach ( $wp_filter['user_admin_notices']->callbacks as $priority => $hooks ) {
				
				foreach ( $hooks as $name => $arr ) {
					if ( is_object( $arr['function'] ) && $arr['function'] instanceof \Closure ) {
						unset( $wp_filter['user_admin_notices']->callbacks[ $priority ][ $name ] );
						continue;
					}
					if ( ! empty( $arr['function'][0] ) && is_object( $arr['function'][0] ) && strpos( strtolower( get_class( $arr['function'][0] ) ), 'wptw_admin_notice' ) !== false ) {
						continue;
					}
					if ( ! empty( $name ) && strpos( strtolower( $name ), 'wptw_admin_notice' ) === false ) {
						unset( $wp_filter['user_admin_notices']->callbacks[ $priority ][ $name ] );
					}
				}
			}
		}

		if ( ! empty( $wp_filter['admin_notices']->callbacks ) && is_array( $wp_filter['admin_notices']->callbacks ) ) {
			foreach ( $wp_filter['admin_notices']->callbacks as $priority => $hooks ) {
				foreach ( $hooks as $name => $arr ) {
					if ( is_object( $arr['function'] ) && $arr['function'] instanceof \Closure ) {
						unset( $wp_filter['admin_notices']->callbacks[ $priority ][ $name ] );
						continue;
					}
					if ( ! empty( $arr['function'][0] ) && is_object( $arr['function'][0] ) && strpos( strtolower( get_class( $arr['function'][0] ) ), 'wptw_admin_notice' ) !== false ) {
						continue;
					}
					if ( ! empty( $name ) && strpos( strtolower( $name ), 'wptw_admin_notice' ) === false ) {
						unset( $wp_filter['admin_notices']->callbacks[ $priority ][ $name ] );
					}
				}
			}
		}

		if ( ! empty( $wp_filter['all_admin_notices']->callbacks ) && is_array( $wp_filter['all_admin_notices']->callbacks ) ) {
			foreach ( $wp_filter['all_admin_notices']->callbacks as $priority => $hooks ) {
				foreach ( $hooks as $name => $arr ) {
					if ( is_object( $arr['function'] ) && $arr['function'] instanceof \Closure ) {
						unset( $wp_filter['all_admin_notices']->callbacks[ $priority ][ $name ] );
						continue;
					}
					if ( ! empty( $arr['function'][0] ) && is_object( $arr['function'][0] ) && strpos( strtolower( get_class( $arr['function'][0] ) ), 'wptw_admin_notice' ) !== false ) {
						continue;
					}
					if ( ! empty( $name ) && strpos( strtolower( $name ), 'wptw_admin_notice' ) === false ) {
						unset( $wp_filter['all_admin_notices']->callbacks[ $priority ][ $name ] );
					}
				}
			}
		}
	}


function wptw_basic_pro_timelines_page() {
    if (wptw_is_first_time()) {
            include(WPTW_PLUGIN_PATH . 'pages/welcome.php');
    } else {
            include(WPTW_PLUGIN_PATH . 'pages/timelines.php');
    }
}

function wptw_basic_pro_buttons_page() {
    if (wptw_is_first_time()) {
            include(WPTW_PLUGIN_PATH . 'pages/welcome.php');
    } else {
            include(WPTW_PLUGIN_PATH . 'pages/buttons.php');
    }
}

function wptw_pro_upsell($content = '') {
	ob_start();
	?>

	<div class="wptw-panel">
		<h5 class="wptw-panel__title"><?php _e( 'WP Tweet Walls Pro', 'wp-tweet-walls' ); ?></h5>
		<div class="wptw-panel__content">
			<div class="wptw-panel__text">
				<?php if (empty($content)) : ?>
					<?php _e( 'Get WP Tweet Walls Pro now and get even more features including unlimited walls and unlimited tweets, viewing images and videos in your tweets, the ability to create Twitter timelines and Twitter buttons, customized to your site.', 'wp-tweet-walls' ); ?>
				<?php else: ?>
					<?php echo $content; ?>
				<?php endif; ?>
				
					
			</div>
		</div>
		<div class="wptw-float-right">
			<a class="wptw-button wptw-button__outline" href="http://solaplugins.com/plugins/wp-tweet-walls/?utm_source=plugin&utm_medium=link&utm_campaign=tweet_wall_upgrade"><?php _e( 'Get the Pro version now', 'wp-tweet-walls' ); ?></a>
		</div>
	</div>

	<?php
	$html = ob_get_clean();
	return $html;
}

function wptw_basic_twitter_get_timeline($url, $limit = '-1') {

	$url = 'https://publish.twitter.com/oembed?url=' . $url;

	if ($limit !== '-1') {
		$url .= '&limit=' . $limit;
	}

	$url .= '&omit_script=true';

	$content = wptw_get_url_content($url);
	return $content;
}

function wptw_pro_link() {
	return 'http://solaplugins.com/plugins/wp-tweet-walls/?utm_source=plugin&utm_medium=link&utm_campaign=tweet_wall_upgrade';
}