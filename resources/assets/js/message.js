$(function() {

    $('input[name="audience"]').on('change', function() {
        var selected = $(this).val();

        if (selected == '1') {
            $('select[name="speed"]').val('25');
            $('.users_list').hide('fast');
        } else if (selected == '2') {
            $('select[name="speed"]').val('25');
            $('.users_list').hide('fast');
        } else if (selected == '3') {
            $('select[name="speed"]').val('25');
            $('.users_list').show('fast');
        } else if (selected == '4') {
            $('select[name="speed"]').val('86400');
            $('.users_list').hide('fast');
        }

    });

    $('.check_post').on('click', function() {
        var $input = $('input[name="post_url"]');
        var url = $input.val();
        var account_id = $('select[name="account_id"]').val();

        if (url != '' && account_id != '') {
            $.ajax({
                url: BASE_URL + "/account/" + account_id + "/oembed/",
                dataType: "json",
                data: {
                    url: url
                },
                beforeSend: function(){
                    $input.attr('disabled', true);
                },
                success: function( response ) {

                    if (response.success) {

                        $input
                            .removeClass('is-invalid')
                            .removeClass('state-invalid')
                            .addClass('is-valid')
                            .addClass('state-valid');

                        $('input[name="media_id"]').val(response.result.media_id);

                        $('#post_preview').html(response.result.html);

                        //$('#post_preview .post_thumbnail').attr('src', response.result.thumbnail_url);
                        //$('#post_preview .post_author_name').html('<a href="' + response.result.author_url + '" target="_blank">' + response.result.author_name + '</a>');
                        //$('#post_preview .post_title').html(response.result.title);
                        $('#post_preview').show();
                    }

                },
                complete: function() {
                    $input.attr('disabled', false);
                },
                error: function() {
                    $('#post_preview').hide();
                    $('input[name="media_id"]').val('');
                    $input
                        .addClass('is-invalid')
                        .addClass('state-invalid')
                        .removeClass('is-valid')
                        .removeClass('state-valid');
                }
            });
        }

    });


    $('input[name="message_type"]').on('change', function() {
        var selected = $(this).val();

        switch(selected) {
            case 'list':
                $('.options:not(.option_list)').hide('fast', function(){
                    $('.option_list').show('fast');
                    $('input[name="disappearing"]').attr('checked', false);
                    $('textarea[name="text"]').val('');
                    $('input[name="photo"]').val('');
                    $('input[name="video"]').val('');
                    $('input[name="hashtag"]').val('');
                    $('textarea[name="hashtag_text"]').val('');
                });
            break;
            case 'text':
                $('.options:not(.option_text)').hide('fast', function(){
                    $('.option_text').show('fast');
                    $('input[name="disappearing"]').attr('checked', false);
                    $('select[name="lists_id"]').val('');
                    $('input[name="photo"]').val('');
                    $('input[name="video"]').val('');
                    $('input[name="hashtag"]').val('');
                    $('textarea[name="hashtag_text"]').val('');
                });
            break;
            case 'like':
                $('.options:not(.option_like)').hide('fast', function(){
                    $('.option_like').show('fast');
                    $('input[name="disappearing"]').attr('checked', false);
                    $('textarea[name="text"]').val('');
                    $('select[name="lists_id"]').val('');
                    $('input[name="photo"]').val('');
                    $('input[name="video"]').val('');
                    $('input[name="hashtag"]').val('');
                    $('textarea[name="hashtag_text"]').val('');
                });
            break;
            case 'hashtag':
                $('.options:not(.option_hashtag)').hide('fast', function(){
                    $('.option_hashtag').show('fast');
                    $('input[name="disappearing"]').attr('checked', false);
                    $('select[name="lists_id"]').val('');
                    $('input[name="photo"]').val('');
                    $('input[name="video"]').val('');
                });
            break;
            case 'photo':
                $('.options:not(.option_photo)').hide('fast', function(){
                    $('input[name="disappearing"]').attr('checked', false);
                    $('textarea[name="text"]').val('');
                    $('select[name="lists_id"]').val('');
                    $('input[name="video"]').val('');
                    $('input[name="hashtag"]').val('');
                    $('textarea[name="hashtag_text"]').val('');

                    $('.option_photo').show('fast', function(){
                        $('.option_disappearing').show('fast');
                    });
                });
            break;
            case 'video':
                $('.options:not(.option_video)').hide('fast', function(){
                    $('input[name="disappearing"]').attr('checked', false);
                    $('textarea[name="text"]').val('');
                    $('select[name="lists_id"]').val('');
                    $('input[name="photo"]').val('');
                    $('input[name="hashtag"]').val('');
                    $('textarea[name="hashtag_text"]').val('');

                    $('.option_video').show('fast', function(){
                        $('.option_disappearing').show('fast');
                    });
                });
            break;
            case 'post':
                $('.options:not(.option_post)').hide('fast', function(){
                    $('.option_post').show('fast');
                    $('input[name="disappearing"]').attr('checked', false);
                    $('select[name="lists_id"]').val('');
                    $('input[name="photo"]').val('');
                    $('input[name="video"]').val('');
                });
            break;
        }

    });
});