<?php
	if ( !defined( 'ABSPATH' ) ) {
		die;
	}
	$max_walls = wptw_get_max_walls();
	$wall_count = wptw_get_wall_count();
	$tweet_walls = wptw_get_walls();
	$limit_reached = wptw_is_limit_reached();
	$settings = wptw_get_settings();
?>
<div class="wptw-page">
	<h1><?php _e( 'WP Tweet Walls', 'wp-tweet-walls' ); ?></h1>

	<div id="wptw-tweet-walls__container">
		<button id="wptw-create-tweet-wall" class="wptw-button" <?php echo $limit_reached ? 'disabled' : ''; ?>>
			<?php if (!$limit_reached) : ?>
				<?php _e( 'Create New Wall', 'wp-tweet-walls' ); ?>
			<?php else : ?>
				<?php _e( 'Upgrade to Pro to create more walls', 'wp-tweet-walls' ); ?>
			<?php endif; ?>
		</button>
		<div id="wptw-tweet-wall__list">
			<?php foreach ($tweet_walls as $tweet_wall) : ?>
				<?php echo wptw_wall_html($tweet_wall); ?>
			<?php endforeach; ?>
			<?php if (empty($tweet_walls)) : ?>
				<p id="wptw-no-walls-error" class="wptw-error__subtle"><?php _e( 'There are no walls yet. Click on the "Create New Wall" button to create your first wall.', 'wp-tweet-walls' ); ?></p>
			<?php endif; ?>
		</div>
	</div>
	
</div>


<div id="wptw-modal__new-wall" class="wptw-modal">
	<h3 class="wptw-modal-title"><?php _e( 'New Tweet Wall', 'wp-tweet-walls' ); ?></h3>
	<div class="wptw-modal-content">
		<form id="wptw-new-tweet-wall__form">
			<input class="wptw-input-field" placeholder="<?php _e( 'Title', 'wp-tweet-walls' ); ?>" data-ajax-name="title" />
			<textarea class="wptw-input-field wptw-input-field__textarea" placeholder="<?php _e( 'Description', 'wp-tweet-walls' ); ?>" data-ajax-name="description"></textarea>
			<div class="wptw-tweets">
				<div id="wptw-new-wall__tweets">
					
				</div>
				<button id="wptw-new-tweet-wall__add-tweet" class="wptw-button" type="button"><?php _e( 'Add Tweet', 'wp-tweet-walls' ); ?></button>
			</div>
			<div class="wptw-modal-buttons">
				<button class="wptw-button wptw-button__secondary wptw-close-parent-modal" type="button"><?php _e( 'Cancel', 'wp-tweet-walls' ); ?></button>
				<button id="wptw-create-new-tweet__btn" type="submit" class="wptw-button"><?php _e( 'Create Wall', 'wp-tweet-walls' ); ?></button>
			</div>
		</form>
	</div>
</div>

<div id="wptw-modal__edit-wall" class="wptw-modal">
	<h3 class="wptw-modal-title"><?php  _e( 'Update Tweet Wall', 'wp-tweet-walls' ); ?></h3>
	<div class="wptw-modal-content">
		<form id="wptw-update-tweet-wall__form">
			<input type="hidden" data-ajax-name="id" value="" />
			<input class="wptw-input-field" placeholder="<?php _e( 'Title', 'wp-tweet-walls' ); ?>" data-ajax-name="title" />
			<textarea class="wptw-input-field wptw-input-field__textarea" placeholder="<?php _e( 'Description', 'wp-tweet-walls' ); ?>" data-ajax-name="description"></textarea>
			<div class="wptw-tweets">
				<div id="wptw-update-wall__tweets">
					
				</div>
				<button id="wptw-update-tweet-wall__add-tweet" class="wptw-button" type="button"><?php _e( 'Add Tweet', 'wp-tweet-walls' ); ?></button>
			</div>
			<div class="wptw-modal__buttons-left">
				<span class="wptw-shortcode" data-ajax-name="shortcode"></span>
			</div>
			<div class="wptw-modal-buttons">
				<button class="wptw-button wptw-button__secondary wptw-close-parent-modal" type="button"><?php _e( 'Cancel', 'wp-tweet-walls' ); ?></button>
				<button id="wptw-delete-wall__button" type="button" class="wptw-button wptw-button__delete"><?php _e( 'Delete', 'wp-tweet-walls' ); ?></button>
				<button id="wptw-update-wall-tweet__btn" type="submit" class="wptw-button"><?php _e( 'Save Changes', 'wp-tweet-walls' ); ?></button>
			</div>
		</form>
	</div>
	
</div>