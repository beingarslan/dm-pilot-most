window.Pilot = {
    account_id: null,
    inbox_cursor_id: null,
    thread_id: null,
    thread_cursor_id: null,
    inbox_timeout: 180,
    thread_timeout: 120
}

$(function() {

    setInterval(function(){
        if (window.Pilot.account_id != null) {
            window.Pilot.inbox_cursor_id = null;
            $('.unseen_count span').empty();
            getInbox();
        }
    }, window.Pilot.inbox_timeout * 1000);

    setInterval(function(){
        if (window.Pilot.account_id != null && window.Pilot.thread_id != null) {
            window.Pilot.thread_cursor_id = null;
            $('.unseen_count span').empty();
            getThread();
        }
    }, window.Pilot.thread_timeout * 1000);


    $('#account_id').on('change', function() {

        var account_id = $(this).val();

        if (account_id == '') {
            $('.alert-no-account').show();
            $('.dm-container').hide();
        } else {
            $('.alert-no-account').hide();
            $('.dm-container').show();
            $('.load-more-container .btn-load-more').hide();
            $('.unseen_count span').empty();

            window.Pilot.inbox_cursor_id = null;
            window.Pilot.thread_cursor_id = null;
            window.Pilot.account_id = account_id;

            getInbox();
        }
    });


    $('.btn-reload').on('click', function() {

        window.Pilot.inbox_cursor_id = null;
        window.Pilot.thread_cursor_id = null;
        $('.unseen_count span').empty();
        $('.load-more-container .btn-load-more').hide();

        getInbox();
    });


    $(document).on('click', '#threads_list table > tbody > tr', function(){

        var thread_id = $(this).data('id');

        if (typeof thread_id !== "undefined") {

            window.Pilot.thread_id = thread_id;

            $('#threads_list table > tbody > tr').removeClass('table-active');
            $(this).addClass('table-active');

            getThread();
        }

    });

    $(document).on('click', '.btn-load-more', function(){

        $btn = $('.btn-load-more');

        if ($btn.hasClass('btn-loading') == false) {
            $btn.addClass('btn-loading');
            getInbox();
        }

    });


    $('.btn-send-message').on('click', function() {

        $text = $('input.message-text').val().trim();
        if ($text.length > 0) {
            sendMessage($text);
            $('input.message-text').val('');
            $('div.emojionearea-editor').empty();

        }
    });


    scrollSmoothToBottom = function() {

        $div = $('#messages_list div.o-auto');
        $div.scrollTop($div[0].scrollHeight);
    }


    truncateText = function(text, cut_to) {
        return text.length > cut_to ? `${text.substr(0, cut_to)}...` : text;
    }


    getInbox = function() {

        $threads_container = $('#threads_list table > tbody');

        if (window.Pilot.inbox_cursor_id == null) {
            $threads_container.empty();
            $('#messages_list ul').empty();
        }

        $.ajax({
            type: 'POST',
            url: BASE_URL + '/direct/' + window.Pilot.account_id,
            data: {
                '_token': $('meta[name="csrf-token"]').attr('content'),
                'cursor_id' : window.Pilot.inbox_cursor_id
            },
            beforeSend: function(){
                if (window.Pilot.inbox_cursor_id == null) {
                    $('#threads_list').addClass('active');
                }
            },
            success: function( response ) {

                if (response['result'] == 'success') {

                    if (response['threads'].length > 0) {

                        $('.unseen_count span').html(response['unseen_count']);

                        var html = '';

                        $.each(response['threads'], function(i, thread) {

                            var users_count = thread.users.length;

                            html += '<tr data-id="' + thread.thread_id + '">';
                            html += '    <td width="1" class="text-center text-nowrap">';

                            if (users_count == 1) {

                                html += '<div class="avatar d-block" style="background-image: url(\'' + thread.users[0].profile_pic_url + '\')"></div>';

                            } else {

                                html += '<div class="avatar-list avatar-list-stacked">';

                                $.each(thread.users, function(c, user) {

                                    if (c > 1) {
                                        return false;
                                    }

                                    html += '<span class="avatar" style="background-image: url(\'' + user.profile_pic_url + '\')"></span>';

                                });

                                html += '<span class="avatar">+' + (users_count - 2) + '</span>';

                                html += '</div>';
                            }

                            html += '    </td>';
                            html += '    <td>';
                            if (thread.is_new) {
                                html += '    <i class="fe fe-circle float-right text-primary"></i>';
                            }
                            html += '        <div>' + thread.users[0].username + '</div>';
                            html += '        <div class="small text-muted">';
                            html += '            ' + truncateText(thread.last_item_text, 25) + '';
                            html += '        </div>';
                            html += '    </td>';
                            html += '</tr>';

                        });

                        if(response['has_older'] == true) {

                            window.Pilot.inbox_cursor_id = response['next_cursor_id'];

                            $('.load-more-container .btn-load-more').show();

                        } else {
                            $('.load-more-container .btn-load-more').hide();
                        }

                        $threads_container.append(html);
                    }

                } else {

                    bootbox.alert({
                        locale: document.documentElement.lang,
                        centerVertical: true,
                        title: response['title'],
                        message: response['message'],
                        closeButton: false
                    });

                }
            },
            complete: function() {
                $('#threads_list').removeClass('active');
                $('.btn-load-more').removeClass('btn-loading');
            },
            error: function( error ) {
                bootbox.alert({
                    closeButton: false,
                    message: 'Something went wrong. Please try again.'
                });
            }
        });
    }


    getThread = function() {

        $('#messages_list > div.dimmer-content > div.o-auto > ul').empty();

        $.ajax({
            type: 'POST',
            url: BASE_URL + '/direct/' + window.Pilot.account_id + '/' + window.Pilot.thread_id,
            data: {
                '_token': $('meta[name="csrf-token"]').attr('content'),
                'cursor_id' : window.Pilot.thread_cursor_id
            },
            beforeSend: function(){
                $('#messages_list').addClass('active');
            },
            success: function( response ) {

                if (response['result'] == 'success') {

                    if (response.items.length > 0) {

                        $.each(response['items'], function(i, item) {

                            var html = '';

                            if (item.user.self == true) {

                                html += '<li class="list-group-item py-4">';
                                html += '   <div class="text-right">';
                                html += '       <small class="text-muted">' + timeago.format(item.item.timestamp / 1000, document.documentElement.lang) + '</small>';
                                html += '   </div>';
                                html += '   <div class="text-right">';
                                html +=        item.item.message;
                                html += '   </div>';
                                html += '</li>';

                            } else {

                                html += '<li class="list-group-item py-4">';
                                html += '    <div class="media">';
                                html += '        <div class="media-object avatar avatar-md mr-4" style="background-image: url(\'' + item.user.profile_pic_url + '\')"></div>';
                                html += '        <div class="media-body">';
                                html += '            <div class="media-heading">';
                                html += '                <small class="text-muted">' + timeago.format(item.item.timestamp / 1000, document.documentElement.lang) + '</small>';
                                html += '            </div>';
                                html += '            <div>';
                                html += '                <strong>' + item.user.username + '</strong>';
                                html +=                  item.item.message;
                                html += '            </div>';
                                html += '        </div>';
                                html += '    </div>';
                                html += '</li>';

                            }

                            $('#messages_list > div.dimmer-content > div.o-auto > ul').append(html);

                            scrollSmoothToBottom();
                        });
                    }

                } else {

                    bootbox.alert({
                        locale: document.documentElement.lang,
                        centerVertical: true,
                        title: response['title'],
                        message: response['message'],
                        closeButton: false
                    });

                }
            },
            complete: function() {
                $('#messages_list').removeClass('active');
            },
            error: function( error ) {
                bootbox.alert({
                    closeButton: false,
                    message: 'Something went wrong. Please try again.'
                });
            }
        });

    }


    sendMessage = function(text) {

        $.ajax({
            type: 'POST',
            url: BASE_URL + '/direct/' + window.Pilot.account_id + '/' + window.Pilot.thread_id + '/send',
            data: {
                '_token': $('meta[name="csrf-token"]').attr('content'),
                'cursor_id' : window.Pilot.thread_cursor_id,
                'message': text
            },
            beforeSend: function(){

                var html = '';

                // ToDo: Highlight sending

                html += '<li class="list-group-item py-4">';
                html += '   <div class="text-right">';
                html += '       <small class="text-muted">' + timeago.format(new Date(), document.documentElement.lang) + '</small>';
                html += '   </div>';
                html += '   <div class="text-right">';
                html +=        text;
                html += '   </div>';
                html += '</li>';

                $('#messages_list > div.dimmer-content > div.o-auto > ul').append(html);

                scrollSmoothToBottom();

            },
            success: function( response ) {

                if (response['result'] == 'success') {

                    // ToDo: Remove highlight

                } else {

                    bootbox.alert({
                        locale: document.documentElement.lang,
                        centerVertical: true,
                        title: response['title'],
                        message: response['message'],
                        closeButton: false
                    });

                }
            },
            complete: function() {
                $('#messages_list').removeClass('active');
            },
            error: function( error ) {
                bootbox.alert({
                    closeButton: false,
                    message: 'Something went wrong. Please try again.'
                });
            }
        });
    }


});