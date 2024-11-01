<?php
	if ( !defined( 'ABSPATH' ) ) {
		die;
	}
?>

<div class="wptw-page">
	<h1><?php _e( 'Twitter Buttons', 'wp-tweet-walls' ); ?></h1>
	<?php ob_start(); ?>
	
	<p>Easily create, manage and add beautiful customized Twitter buttons to your site in a few simple steps with <a href="<?php echo wptw_pro_link(); ?>">WP Tweet Walls Pro</a>. You can create Tweet buttons, Follow buttons and Direct Message buttons and here are a few examples:</p>
	<div id="wptw-button-examples">
		<a class="twitter-share-button" data-size="large" href="https://twitter.com/intent/tweet">Tweet</a>
		<a class="twitter-follow-button" data-size="large" href="https://twitter.com/SolaPlugins">Follow</a>
		<a href="https://twitter.com/messages/compose?recipient_id=3805104374" data-size="large" class="twitter-dm-button">Message</a>
	</div>
	
	<?php $content = ob_get_clean(); ?>
	<?php echo wptw_pro_upsell($content); ?>
</div>