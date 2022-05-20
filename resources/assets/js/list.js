$(function() {

    var $repeaterList = $('.repeater').repeater({
    	show: function () {
    		$(this).show();

            $('textarea[data-emojiable=true]').emojioneArea({
				search: false,
				useInternalCDN: false,
				tones: false
			});

        }
    });

	$('#addMultiple').on('shown.bs.modal', function (e) {
		$('#addMultiple textarea').focus();
	}).on('hidden.bs.modal', function (e) {
		$('#addMultiple textarea').val('');
		$('.searchByHashtagForm input[name="q"]').val('');
	});

    $('.searchByHashtagForm button.btn').on('click', function(e) {

    	var $button = $(this);
    	var q = $('.searchByHashtagForm input[name="q"]').val().trim();
    	var account_id = $('.searchByHashtagForm select[name="account_id"]').val();

    	if (q != '' && account_id != '') {

    		$.ajax({
	            type: 'POST',
	            url: BASE_URL + '/search/hashtag',
	            data: {
	                '_token': $('meta[name="csrf-token"]').attr('content'),
	                'account_id' : account_id,
	                'q' : q,
	            },
	            beforeSend: function(){
	                $button.addClass('btn-loading');
	            },
	            success: function( response ) {
	                $button.removeClass('btn-loading');
	            	$.each(response, function(pk, username) {
	            		$('#addMultiple textarea').append(username + "\r\n");
	            	});
	            },
	            error: function( error ) {
	            	$button.removeClass('btn-loading');
	                bootbox.alert({
	                    closeButton: false,
	                    message: 'Something went wrong. Please try again.'
	                });
	            }
	        });


    	}
    });

    $('.searchByAccountForm button.btn').on('click', function(e) {

    	var $button = $(this);
    	var q = $('.searchByAccountForm input[name="q"]').val().trim();
    	var account_id = $('.searchByAccountForm select[name="account_id"]').val();

    	if (q != '' && account_id != '') {

    		$.ajax({
	            type: 'POST',
	            url: BASE_URL + '/search/account',
	            data: {
	                '_token': $('meta[name="csrf-token"]').attr('content'),
	                'account_id' : account_id,
	                'q' : q,
	            },
	            beforeSend: function(){
	                $button.addClass('btn-loading');
	            },
	            success: function( response ) {
	                $button.removeClass('btn-loading');
	            	$.each(response, function(pk, username) {
	            		console.log(pk, username);
	            		$('#addMultiple textarea').append(username + "\r\n");
	            	});
	            },
	            error: function( error ) {
	            	$button.removeClass('btn-loading');
	                bootbox.alert({
	                    closeButton: false,
	                    message: 'Something went wrong. Please try again.'
	                });
	            }
	        });


    	}
    });
});