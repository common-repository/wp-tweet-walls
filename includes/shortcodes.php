<?php

if ( !defined( 'ABSPATH' ) ) {
	die;
}

add_shortcode( 'wp_tweet_wall', 'wptw_tweet_wall_shortcode' );
add_shortcode( 'wptw_tweet_wall', 'wptw_tweet_wall_shortcode' );

function wptw_tweet_wall_shortcode( $atts ){
	$a = shortcode_atts( array(
		'id' => '1',
		'max' => '-1'
	), $atts );

	$wall = wptw_get_wall($a['id']);
	$settings = wptw_get_settings();

	ob_start();

	?>
	<?php if (is_object($wall)) : ?>
		
		<div id="wptw-wall-<?php echo $wall->id; ?>" class="wptw-tweet-wall">
			<?php if ($settings['display_title']) : ?>
				<?php if (!empty($wall->title)) : ?>
					<p class="wptw-wall__title"><?php echo $wall->title; ?></p>
				<?php endif; ?>
				<?php if (!empty($wall->description)) : ?>
					<p class="wptw-wall__description"><?php echo $wall->description; ?></p>
				<?php endif; ?>
			<?php endif; ?>
			
			<div class="wptw-grid">
				<?php
					$columns = 3;
					$tweets = wptw_get_wall_tweets($wall);
					$count = sizeof($wall->tweets);
					$tweets_in_column = ceil($count / $columns);

					for ($i = 0; $i < $columns; $i++) { 
						$offset = $i * $tweets_in_column;
						?> <div class="wptw-grid__column"> <?php
							foreach ($tweets as $key => $tweet) {
								if (!property_exists($tweet, 'html')) {
									continue;
								}
								if ($key >= $offset) {
									if ($offset == 0 && $key < $tweets_in_column) {
										?>
										<div class="wptw-tweet wptw-grid__item">
											<?php echo $tweet->html; ?>
										</div>
									<?php
									} else {
										if ($key < ($tweets_in_column + $offset)) {
											?>
											<div class="wptw-tweet wptw-grid__item">
										<?php echo $tweet->html; ?>
									</div>
											<?php
										}
									}
								}
								
							}
						?> </div> <?php
					}
				?>
			</div>
		</div>
					
	<?php else : ?>
		<p class="wptw-error wptw-error_subtle"><?php _e( 'Wall does not exist', 'wp-tweet-walls' ); ?> </p>
	<?php endif; ?>
	<?php
	$html = ob_get_clean();
	return $html;
}