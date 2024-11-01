<?php
	if ( !defined( 'ABSPATH' ) ) {
		die;
	}
?>

<div id="wptw-welcome-page" class="wptw-page">
	<div class="wptw-columns">
		<div id="wptw-welcome-column__left" class="wptw-column wptw-column-4">

			<div class="wptw-welcome-logo">
				<i class="wptw-logo-icon dashicons dashicons-twitter"></i>
			</div>
		</div>
		<div id="wptw-welcome-column__right" class="wptw-column wptw-column-8">
			<h4 id="wptw-welcome-title"><?php _e( 'Welcome to WP Tweet Walls', 'wp-tweet-walls' ); ?></h4>
			<?php _e( 'WP Tweet Walls allows you to easily setup beautiful tweet walls on your site in a few simple steps.', 'wp-tweet-walls' ); ?>
			<div class="wptw-steps">
				<ul>
					<li class="wptw-step"><span>1.</span><?php _e( 'Click on the "Get Started" button below to go to the "WP Tweet Walls" page.', 'wp-tweet-walls' ); ?></li>
					<li class="wptw-step"><span>2.</span><?php _e( 'Click on the "Add New Wall" button to create a new tweet wall.', 'wp-tweet-walls' ); ?></li>
					<li class="wptw-step"><span>3.</span><?php _e( 'Enter a name, description and the tweet links for your wall - these can be updated at any time..', 'wp-tweet-walls' ); ?></li>
					<li class="wptw-step"><span>4.</span>  <?php printf( __( 'Edit the page you want to add your wall to and add the following shortcode %s with the "id" being the ID of your wall.', 'wp-tweet-walls' ), '<code class="wptw-shortcode">[wptw_tweet_wall id="1"]</code>' ); ?></li>
					<li class="wptw-step"><span>5.</span><?php _e( 'Thats it! Your tweet wall will now be displayed on your page.', 'wp-tweet-walls' ); ?></li>
					<li class="wptw-step"><span>6.</span><?php _e( 'Get the Pro version to get unlimited tweet walls, unlimited tweets, timelines, Twitter buttons and even more features.', 'wp-tweet-walls' ); ?></li>
				</ul>
			</div>
			<div id="wptw-welcome-buttons">
				<form action="" method="POST">
					<button name="wptw-submit-first-time" class="wptw-button"><?php _e( 'Get Started!', 'wp-tweet-walls' ); ?></button>
				</form>
				
			</div>
		</div>
	</div>
</div>