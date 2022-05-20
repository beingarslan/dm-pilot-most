$(function() {

    window.Pilot.MediaManager = {

    	init: function() {
    		this.getFiles();
    		this.initFileUpload();
    		this.initDeleteMedia();
    	},

    	getFiles: function() {

    		if($('.media-manager').length) {

	            $.ajax({
	                url: BASE_URL + '/media/files',
	                method: 'GET',
	                beforeSend: function(){
	                    $('.media-manager .dimmer').addClass('active');
	                },
	                success: function(response) {

	                    $media_container = $('.media-manager .media-files-container');
	                    $media_container.empty();
	                    $('.media-manager .dimmer').removeClass('active');

	                    $.each(response, function(i, media) {

	                        $media_container.append(
	                              '<li class="media-file">'
	                            + '    <label class="imagecheck m-1">'
	                            + '        <input name="media[]" type="checkbox" value="' + media['id'] + '" data-original="' + media['original'] + '" class="imagecheck-input" />'
	                            + '        <figure class="imagecheck-figure">'
	                            + '            <img src="' + media['thumb'] + '" alt="' + media['file_name'] + '" class="imagecheck-image">'
	                            + '        </figure>'
	                            + '    </label>'
	                            + '</li>'
	                        );
	                    });

	                }
	            });

	            $('ul.media-files-container').sortable({
					onEnd: function(e) {
						window.Pilot.Post.preview();
					}
				});
    		}

        },

        initFileUpload: function() {

	        $('.media-manager input[name="files[]"]').fileupload({
	            dataType: 'json',
	            formData: {
	                '_token': $('meta[name="csrf-token"]').attr('content')
	            },
	            always: function (jqXHR, textStatus) {

	                $('.media-manager .btn-upload').removeClass('btn-loading');

	                if (textStatus.result.success == false) {
	                	bootbox.alert({
		                    closeButton: false,
		                    message: textStatus.result.message
		                });
	                }

	                window.Pilot.MediaManager.getFiles();
	            },
	            start: function (e, data) {
	                $('.media-manager .btn-upload').addClass('btn-loading');
	            }
	        });

        },

        initDeleteMedia: function() {

        	$(document).on('click', '.media-manager .media-files-container input[name="media[]"]', function() {

        		selected = $('.media-manager .media-files-container input[name="media[]"]:checked');

        		if (selected.length > 0) {
        			$('.media-manager .btn-delete').attr('disabled', false);
        		} else {
        			$('.media-manager .btn-delete').attr('disabled', true);
        		}

        	});

        	$(document).on('click', '.media-manager .btn-delete', function() {

        		var id = [];
        		$.each(selected, function() {
	                id.push($(this).val());
	            });

	            $.ajax({
	                url: BASE_URL + '/media',
	                method: 'DELETE',
	                data: {
	                	'_token': $('meta[name="csrf-token"]').attr('content'),
	                	'id': id
	                },
	                beforeSend: function(){
	                    $('.media-manager .dimmer').addClass('active');
	                    $('.media-manager .btn-delete').addClass('btn-loading');
	                },
	                complete: function() {
	                	$('.media-manager .dimmer').removeClass('active');
	                	$('.media-manager .btn-delete').removeClass('btn-loading');
	                    window.Pilot.MediaManager.getFiles();
	                }
	            });

        	});


        	$(document).on('click', '.media-manager .btn-clear', function() {

        		if (confirm('Are you sure want to delete all media?')) {

		            $.ajax({
		                url: BASE_URL + '/media/clear',
		                method: 'DELETE',
		                data: {
		                	'_token': $('meta[name="csrf-token"]').attr('content'),
		                },
		                beforeSend: function(){
		                    $('.media-manager .dimmer').addClass('active');
		                    $('.media-manager .btn-clear').addClass('btn-loading');
		                },
		                complete: function() {
		                	$('.media-manager .dimmer').removeClass('active');
		                	$('.media-manager .btn-clear').removeClass('btn-loading');
		                    window.Pilot.MediaManager.getFiles();
		                }
		            });
        		}

        	});
        }

    }

    window.Pilot.MediaManager.init();
});