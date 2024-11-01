jQuery(document).ready(function($){
	
	// Color picker fields
	jQuery('.wptw-color-field').wpColorPicker();

	jQuery('#wptw-new-tweet-wall__form').submit(function(e){
		e.preventDefault();
		var title = $(this).find('[data-ajax-name="title"]');
		var description = $(this).find('[data-ajax-name="description"]');
		var tweets = [];

		jQuery(this).find('[data-ajax-name="tweet"]').each(function(i, el){
			var tweet = jQuery(el).val();
			if (tweet !== '') {
				tweets.push(tweet);
			}
		});

		for (var tweet in tweets) {
			var tweet = tweets[tweet];
			if (wptw_is_valid_twitter_url(tweet)) {

			} else {
				wptw_alert('The Tweet URL "' + tweet + '" is incorrect and needs to be a valid Twitter tweet URL such as "https://twitter.com/bleeper_io/status/1153267452022861824" for example.');
				return;
			}
		}

		var data = {
			action: 'wptw_ajax_create_wall',
			title: title.val(),
			description: description.val(),
			tweets: tweets
		};

		wptw_ajax(data, function(response){
			jQuery('#wptw-tweet-wall__list').append(response.html);
			jQuery('#wptw-no-walls-error').remove();
			wptw_notification('Tweet wall successfully created: [wp_tweet_wall id="' + response.id + '"]', 5000, 'yes');
			wptw_is_limit_reached(function(response){
				if (response.limit_reached) {
					jQuery('body').find('#wptw-create-tweet-wall').text('Upgrade to Pro to create more walls').attr('disabled', 'disabled');
					wptw_upsell_modal();
				}
			});
		});

		title.val('');
		description.val('');

		jQuery('#wptw-new-wall__tweets').html('');
		wptw_add_tweet();
		wptw_close_modal();

	});

	jQuery('body').on('click', '#wptw-new-tweet-wall__add-tweet', function(){
		if (wptw_localized.is_pro || jQuery('#wptw-modal__new-wall').find('[data-ajax-name="tweet"]').length < 3) {
			wptw_add_tweet();
		} else {
			//alert('The Pro Add-on is required to create more than 3 tweets.');
			wptw_notification('The Pro Add-on is required to create more than 3 tweets.');
			wptw_upsell_modal();
		}
	});

	jQuery('body').on('click', '#wptw-update-tweet-wall__add-tweet', function(){
		if (wptw_localized.is_pro || jQuery('#wptw-update-wall__tweets').find('[data-ajax-name="tweet"]').length < 3) {
			wptw_add_tweet('', '#wptw-update-wall__tweets');
		} else {
			//alert('The Pro Add-on is required to create more than 3 tweets.');
			wptw_notification('The Pro Add-on is required to create more than 3 tweets.');
			wptw_upsell_modal();
		}
	});

	jQuery('body').on('click', '#wptw-create-tweet-wall', function(){
		jQuery('#wptw-new-wall__tweets').html('');
		wptw_add_tweet();
		wptw_open_modal('#wptw-modal__new-wall');
	});

	// Close modals when pressing on the background
	jQuery('body').on('click', '#wptw-modal-background', function(){
		wptw_close_modal('.wptw-modal');
	});

	// Close parent modal
	jQuery('body').on('click', '#wptw-modal-background', function(){
		wptw_close_modal('#wptw-modal__new-wall');
	});

	// Remove tweet from list
	jQuery('body').on('click', '.wptw-remove-tweet', function(){
		jQuery(this).closest('.wptw-new-wall__tweet').remove();
	});

	// Edit a wall
	jQuery('body').on('click', '.wptw-twitter-wall__item', function(){
		var id = jQuery(this).data('wall-id');
		var $modal = jQuery('#wptw-modal__edit-wall');
		$modal.find('[data-ajax-name="id"]').val(id);
		$modal.find('#wptw-update-wall__tweets').html('');
		$modal.find('[data-ajax-name="shortcode"]').text('[wptw_tweet_wall id="' + id + '"]');
		
		var $modal = wptw_loader_modal('#wptw-modal__edit-wall');
		wptw_get_wall(id, function(response){
			wptw_remove_loader($modal);
			$modal.find('[data-ajax-name="title"]').val(response.title);
			$modal.find('[data-ajax-name="description"]').val(response.description);
			response.tweets.forEach(function(tweet){
				if (tweet !== '') {
					wptw_add_tweet(tweet, '#wptw-update-wall__tweets');
				}
			});
		});
	});

	// Update the wall
	jQuery('#wptw-update-tweet-wall__form').submit(function(e){
		e.preventDefault();
		var id = $(this).find('[data-ajax-name="id"]');
		var title = $(this).find('[data-ajax-name="title"]');
		var description = $(this).find('[data-ajax-name="description"]');
		var tweets = [];

		jQuery(this).find('[data-ajax-name="tweet"]').each(function(i, el){
			var tweet = jQuery(el).val();
			if (tweet !== '') {
				tweets.push(tweet);
			}
		});

		for (var tweet in tweets) {
			var tweet = tweets[tweet];
			if (wptw_is_valid_twitter_url(tweet)) {

			} else {
				wptw_alert('The Tweet URL "' + tweet + '" is incorrect and needs to be a valid Twitter tweet URL such as "https://twitter.com/bleeper_io/status/1153267452022861824" for example.');
				return;
			}
		}

		var data = {
			action: 'wptw_ajax_update_wall',
			id: id.val(),
			title: title.val(),
			description: description.val(),
			tweets: tweets
		};

		wptw_ajax(data, function(response){
			wptw_notification('Tweet wall successfully updated', 3000, 'yes');
			jQuery('.wptw-twitter-wall__item[data-wall-id="' + data.id + '"]').replaceWith(response.html);
		});

		id.val('');
		title.val('');
		description.val('');

		jQuery('#wptw-new-wall__tweets').html('');
		wptw_add_tweet();
		wptw_close_modal();
	});

	jQuery('body').on('click', '#wptw-delete-wall__button', function(){
		var id = $('#wptw-update-tweet-wall__form').find('[data-ajax-name="id"]').val();
		var data = {
			action: 'wptw_ajax_delete_wall',
			id: id
		};
		if (confirm('Are you sure you want to permanently delete this wall?')) {
			wptw_ajax(data, function(response){
				wptw_notification('Tweet wall successfully deleted', 3000, 'yes');
				wptw_is_limit_reached(function(response){
					if (response.limit_reached) {
						jQuery('body').find('#wptw-create-tweet-wall').text('Upgrade to Pro to create more walls').attr('disabled', 'disabled');
					} else {
						jQuery('body').find('#wptw-create-tweet-wall').text('Create New Wall').removeAttr('disabled');
					}
				});
			});
			wptw_close_modal();
			jQuery('.wptw-twitter-wall__item[data-wall-id="' + id + '"]').remove();
			if (jQuery('.wptw-twitter-wall__item').length <= 0) {
				jQuery('#wptw-tweet-wall__list').append("<p id='wptw-no-walls-error' class='wptw-error__subtle'>There are no walls yet. Click on the \"Create New Wall\" button to create your first wall.</p>");
			}
		}
	});

	// Handling tabs
	jQuery('body').on('click', '[data-tab-title]', function(){
		var tab = jQuery(this).data('tab-title');
		jQuery('body').find('[data-tab]').removeClass('wptw-tab__active');
		jQuery('body').find('[data-tab="' + tab + '"]').addClass('wptw-tab__active');
		jQuery('body').find('[data-tab-title]').removeClass('wptw-nav-item__selected');
		jQuery(this).addClass('wptw-nav-item__selected');
	});

	jQuery('body').on('click', '#wptw-upsell-modal__bg', function(){
		wptw_close_upsell();
	});

	function wptw_get_wall_count() {
		jQuery('body').find('.wptw-twitter-wall__item').length;
	}
	
	function wptw_is_limit_reached(callback) {
		var data = {
			action: 'wptw_ajax_is_limit_reached'
		};
		wptw_ajax(data, function(response){
			callback(response);
		});
	}

	jQuery('body').on('click', '.wptw-close-parent-modal', function(){
		wptw_close_modal();
	});

	jQuery('body').on('click', '[data-wptw-modal]', function(){
		var modal = jQuery(this).data('wptw-modal');
		wptw_open_modal('#' + modal);
	});

	jQuery('body').on('click', '.wptw-close-upsell, #wptw-upsell__buttons .wptw-button', function(){
		wptw_close_upsell();
	});
});

function wptw_upsell_modal() {
	var html = '<div id="wptw-upsell-modal__bg"></div><div id="wptw-upsell-modal" class="wptw-modal wptw-modal-active"><div id="wptw-pro-modal__left" class="wptw-gradient"><div id="wptw-upsell__content-left"><i class="dashicons dashicons-twitter"></i>WP Tweet Walls Pro</div></div><div id="wptw-pro-modal__right"><div id="wptw-upsell__content"><span class="wptw-close-upsell dashicons dashicons-no"></span><h4 id="wptw-upsell-title">WP Tweet Walls Pro</h4>Get WP Tweet Walls Pro now and get even more features including unlimited walls and unlimited tweets, viewing images and videos in your tweets, the ability to create Twitter timelines and Twitter buttons, customized to your site.</div><div id="wptw-upsell__buttons"><a class="wptw-button wptw-button__outline wptw-button__rounded" href="http://solaplugins.com/plugins/wp-tweet-walls/?utm_source=plugin&utm_medium=link&utm_campaign=tweet_wall_upgrade" target="_BLANK">Get the Pro version now</a></div></div></div>';
	jQuery('body').append(html);
}

function wptw_close_upsell() {
	jQuery('body').find('#wptw-upsell-modal__bg').remove();
	jQuery('body').find('#wptw-upsell-modal').remove();
}

function wptw_add_tweet(value, container) {
	if (typeof value == 'undefined') {
		value = '';
	}
	if (typeof container == 'undefined') {
		container = '#wptw-new-wall__tweets';
	}
	var tweet_html = '<div class="wptw-new-wall__tweet"><span class="wptw-tweet-input__icon dashicons dashicons-twitter"></span><input type="text" placeholder="Tweet Link" data-ajax-name="tweet" value="' + value + '"/><i class="wptw-remove-tweet dashicons dashicons-no"></i></div>';
	jQuery(container).append(tweet_html);
}

function wptw_get_wall(id, callback) {
	var data = {
		id: id,
		action: 'wptw_ajax_get_wall'
	};
	wptw_ajax(data, function(response){
		callback(response);
	});
}

function wptw_ajax(data, callback) {
	data.nonce = wptw_localized.nonce;
	jQuery.ajax({
		url: wptw_localized.ajaxurl,
		type: 'post',
		dataType: 'json',
		data: data,
		error: function(response, status, error) {
		},
		success: function(response) {
			callback(response);
		}
	});
}

function wptw_twitter_request(query, callback) {
	data.nonce = wptw_localized.nonce;
	jQuery.ajax({
		url: 'https://publish.twitter.com/oembed?' + query,
		type: 'post',
		dataType: 'jsonp',
		data: data,
		error: function(response, status, error) {
		},
		success: function(response) {
			callback(response);
		}
	});
}

function wptw_get_tweet(url, callback) {
	wptw_twitter_request('url=' + url, function(response){
		callback(response);
	});
}

function wptw_open_modal(selector) {
	jQuery(selector).addClass('wptw-modal-active');
	jQuery('body').find('#wptw-modal-background').remove();
	jQuery('body').prepend('<div id="wptw-modal-background"></div>');
}

function wptw_close_modal(selector) {
	if (typeof selector == 'undefined') {
		selector = '.wptw-modal';
	}
	jQuery(selector).removeClass('wptw-modal-active');
	jQuery('body').find('#wptw-modal-background').remove();
}

function wptw_loader_modal(selector) {
	var modal = jQuery(selector);
	var content = modal.find('.wptw-modal-content');
	content.hide();
	content.after('<div class="wptw-loader-container"><div class="wptw-loader"><div></div><div></div><div></div><div></div></div></div>');
	wptw_open_modal(selector);
	return modal;
}

function wptw_remove_loader($modal) {
	$modal.find('.wptw-loader-container').remove();
	$modal.find('.wptw-modal-content').show();
}

function wptw_notification(message = '', time = 3000, icon = '') {
	if (icon !== '') {
		message = '<i class="wptw-notification-icon dashicons dashicons-yes"></i>' + message;
	}
	jQuery('body').find('.wptw-notification').remove();
	jQuery('body').append('<div class="wptw-notification">' + message + '</div>');
	setTimeout(function(){
		jQuery('body').find('.wptw-notification').remove();
	}, time);
}

function wptw_is_valid_twitter_url(url) {
	if (url.indexOf('twitter.com/') !== -1){
		return true;
	} else {
		return false;
	}
}

function wptw_alert(msg) {
	//alert(msg);
	jQuery('body').append('<div class="wptw-error-modal wptw-modal wptw-modal-active">' + msg + '<button class="wptw-close-error wptw-button">OK</button></div>');
	var error = jQuery('body').find('.wptw-error-modal').last();
	var button = error.find('.wptw-close-error');
	button.on('click', function(){
		error.remove();
	});
}
