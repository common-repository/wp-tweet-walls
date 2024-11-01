<?php
	if ( !defined( 'ABSPATH' ) ) {
		die;
	}
?>

<div class="wptw-page">
	<h1><?php _e( 'Twitter Timelines', 'wp-tweet-walls' ); ?></h1>
	<?php ob_start(); ?>
	
	<p>Add quick and dynmaic Twitter timelines to your website with <a href="<?php echo wptw_pro_link(); ?>">WP Tweet Walls Pro</a> like the example timeline below:</p>

	<div id="wptw-timeline-examples">
		<div id="wptw-timeline-example">
			<?php $timeline = wptw_basic_twitter_get_timeline('https://twitter.com/SolaPlugins', '4'); ?>
			<?php echo $timeline->html; ?>
		</div>
	</div>
	
	<?php $content = ob_get_clean(); ?>
	<?php echo wptw_pro_upsell($content); ?>
</div>