$(function() {

	window.Pilot.Common = {

		loadAccountInfo: function() {

			var usernames = [];

			$('[data-account-info]').each(function(e) {
				usernames.push( $(this).data('account-info') );
		    });

		    var unique_usernames = $.unique(usernames);

			$.each(unique_usernames, function(i, username) {

				var $that = $('[data-account-info="' + username + '"]');

				$.ajax({
	                url: BASE_URL + '/account/' + username + '/info',
	                type: 'GET',
	                cache: true,
	                success: function(response) {
			        	$that.find('[data-avatar]').css('background-image', 'url(' + response['avatar'] + ')');
			        	$that.find('[data-followers]').text(response['followed_by_count']);
			        	$that.find('[data-following]').text(response['following_count']);
			        	$that.find('[data-posts]').text(response['posts_count']);
	                }
	            });

		    });

		}
	}

	$('[data-toggle="card-collapse"]').on('click', function(e) {
		let $card = $(this).closest('div.card');
		$card.toggleClass('card-collapsed');
		e.preventDefault();
		return false;
	});

	$('input.dm-date-time-picker[type=text]').flatpickr({
        locale: document.documentElement.lang,
        enableTime: true,
        allowInput: true,
        time_24hr: true,
        enableSeconds: true,
        altInput: true,
        altFormat: "H:i - F j, Y",
        dateFormat: "Y-m-d H:i:S"
    });

    if($('.dm-show-more').length) {
	    $('.dm-show-more').showMore({
			minheight: 57,
			buttontxtmore: 'show more..',
			buttontxtless: 'show less',
			buttoncss: 'text-muted small',
			animationspeed: 300
		});
	}

    if($('.dm-viewer-container').length) {
		new Viewer(document.querySelector('.dm-viewer-container'), {
			url: 'data-original',
			fullscreen: false,
			loop: false,
			movable: false,
			navbar: false,
			rotatable: false,
			slideOnTouch: false,
			title: false,
			toggleOnDblclick: false,
			toolbar: false,
			tooltip: false,
			zoomable: false,
			zoomOnTouch: false,
			zoomOnWheel: false
		});
    }

	window.Pilot.Common.loadAccountInfo();

});