<?php
	if ( !defined( 'ABSPATH' ) ) {
		die;
	}

	if (isset($_POST['wptw-submit__save-settings'])) {

		$data = [];
		$data['theme'] = isset($_POST['wptw-setting__theme']) ? sanitize_text_field( $_POST['wptw-setting__theme'] ) : 'light';
		$data['show_conversation'] = isset($_POST['wptw-setting__conversation']) ? true : false;
		$data['display_title'] = isset($_POST['wptw-setting__display-title']) ? true : false;
		$data['show_media'] = isset($_POST['wptw-setting__show-media']) ? true : false;
		$data['link_color'] = isset($_POST['wptw-setting__link-color']) ? sanitize_text_field( $_POST['wptw-setting__link-color'] ) : '';
		
		wptw_update_settings($data);
	}

	$settings = wptw_get_settings();
	$settings_pages = wptw_get_settings_pages();
?>

<div id="wptw-settings-page" class="wptw-page">
	<div class="wptw-columns">
		<div id="wptw-settings-column__left" class="wptw-column wptw-column-4 wptw-gradient">
			<div class="wptw-nav wptw-nav__vertical">
				<p class="wptw-nav__title"><?php _e( 'Settings', 'wp-tweet-walls' ); ?></p>
				<ul class="wptw-nav__list">
					<li class="wptw-nav__item wptw-nav-item__selected" data-tab-title="main-settings"><?php _e( 'Tweet Wall Settings', 'wp-tweet-walls' ); ?></li>
				
					<?php foreach ($settings_pages as $page) : ?>
						<li class="wptw-nav__item" data-tab-title="<?php echo $page['slug']; ?>"><?php echo $page['title']; ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
		<div id="wptw-settings-column__right" class="wptw-column wptw-column-8">
			<form action="" method="POST" class="wptw-form">

				<div data-tab="main-settings" class="wptw-tab wptw-tab__active">
					<p class="wptw-tab-title"><?php _e( 'Tweet Wall Settings', 'wp-tweet-walls' ); ?></p>
					<div class="wptw-form-field">
						<label class="wptw-label"><?php _e( 'Tweet Theme', 'wp-tweet-walls' ); ?></label>
						<select name="wptw-setting__theme" class="wptw-select">
							<option value="light" <?php echo $settings['theme'] == 'light' ? 'selected' : ''; ?>><?php _e( 'Light', 'wp-tweet-walls' ); ?></option>
							<option value="dark" <?php echo $settings['theme'] == 'dark' ? 'selected' : ''; ?>><?php _e( 'Dark', 'wp-tweet-walls' ); ?></option>
						</select>
					</div>

					<div class="wptw-form-field">
						<label class="wptw-label"><?php _e( 'Link Color', 'wp-tweet-walls' ); ?></label>
						<input type="text" name="wptw-setting__link-color" value="<?php echo $settings['link_color']; ?>" class="wptw-color-field" />
					</div>

					<div class="wptw-form-field">
						<label class="wptw-label"><?php _e( 'Display Wall Title', 'wp-tweet-walls' ); ?></label>
						<input type="checkbox" name="wptw-setting__display-title" value="" <?php echo $settings['display_title'] ? 'checked' : ''; ?> class="wptw-checkbox" /><?php _e( 'Display the wall title and description', 'wp-tweet-walls' ); ?>
					</div>

					<div class="wptw-form-field">
						<label class="wptw-label"><?php _e( 'Show Conversations', 'wp-tweet-walls' ); ?></label>
						<input type="checkbox" name="wptw-setting__conversation" value="" <?php echo $settings['show_conversation'] ? 'checked' : ''; ?> class="wptw-checkbox" /><?php _e( 'Show previous conversation in tweet card', 'wp-tweet-walls' ); ?>
					</div>

					<div class="wptw-form-field <?php echo !wptw_is_pro() ? 'wptw-pro-required' : ''; ?>">
						<label class="wptw-label"><?php _e( 'Show Media', 'wp-tweet-walls' ); ?> <?php echo !wptw_is_pro() ? _e( '(Only available in the Pro version)', 'wp-tweet-walls' ) : ''; ?></label>
						<input type="checkbox" name="wptw-setting__show-media" value="" <?php echo $settings['show_media'] ? 'checked' : ''; ?> class="wptw-checkbox" <?php echo !wptw_is_pro() ? 'disabled' : ''; ?> /><?php _e( 'Show images and videos in tweet cards', 'wp-tweet-walls' ); ?>
					</div>
				</div>

				<?php foreach ($settings_pages as $page) : ?>
					<div data-tab="<?php echo $page['slug']; ?>" class="wptw-tab">
						<p class="wptw-tab-title"><?php echo $page['title']; ?></p>
						<div><?php echo $page['content']; ?></div>
					</div>
				<?php endforeach; ?>

				<div id="wptw-settings-buttons">
						<button name="wptw-submit__save-settings" class="wptw-button"><?php _e( 'Save', 'wp-tweet-walls' ); ?></button>
				</div>

			</form>
		</div>
	</div>
</div>