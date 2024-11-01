jQuery(document).ready(function($){
	
	function wptw_twitter_request(query, callback) {
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
});