$(function() {

    window.Pilot.Post = {

        type: 'post',
        account: [],
        caption: null,
        allow: {
            post: true,
            album: true,
            story: true
        },

        init: function() {

            setTimeout(function(){
                $('body.post-create select.target-account').trigger('change');
            }, 500);

        },

        setAccount: function(account_id, account_username) {
            this.account.id = account_id;
            this.account.username = account_username;
        },

        getAccount: function() {
            return this.account;
        },

        setType: function(type) {
            this.type = type;
        },

        getType: function() {
            return this.type;
        },

        setCaption: function(caption) {
            this.caption = caption;
        },

        getCaption: function() {
            return this.caption;
        },

        keepFirstMedia: function() {
            $('body.post-create input.imagecheck-input:checked').each(function(i) {
                if (i > 0) {
                    $(this).prop('checked', false);
                }
            });
        },

        changeType: function() {

            $caption_text = $('textarea[data-emojiable=true].caption-text');
            $preview_timeline = $('body.post-create .preview-timeline');
            $preview_story = $('body.post-create .preview-story');

            switch (this.getType()) {
                case 'post':

                    $caption_text.data('emojioneArea').enable();
                    $location_lookup.enable();
                    $preview_story.addClass('d-none');
                    $preview_timeline.removeClass('d-none');

                    // Keep only first media
                    this.keepFirstMedia();

                    break;
                case 'album':

                    $caption_text.data('emojioneArea').enable();
                    $location_lookup.enable();
                    $preview_story.addClass('d-none');
                    $preview_timeline.removeClass('d-none');

                    break;
                case 'story':

                    $caption_text.data('emojioneArea').disable();
                    $location_lookup.disable();
                    $preview_story.removeClass('d-none');
                    $preview_timeline.addClass('d-none');

                    // Keep only first media
                    this.keepFirstMedia();

                    break;
                default:

                    break;
            }
        },

        preview: function() {

            if (this.getType() == 'story') {

                // Media
                $media = $('body.post-create input.imagecheck-input:checked').first();

                $('body.post-create .preview-story').empty();

                if ($media.length) {
                    $('<div class="image" style="background-image: url(\'' + $media.data('original') + '\');"></div>').appendTo('body.post-create .preview-story');
                }

            } else {

                // Caption
                $preview_caption = $('body.post-create .preview-caption');

                if (!this.getCaption()) {
                    $preview_caption.addClass('active').html('<span></span><span></span>');
                } else {
                    $preview_caption.removeClass('active').html( this.getCaption() );
                    $preview_caption.linky({
                        mentions: true,
                        hashtags: true,
                        urls: false,
                        linkTo: 'instagram'
                    });
                }

                // Account
                $account = this.getAccount();
                $preview_username = $('body.post-create .preview-username');

                if (!$account) {
                    $preview_username.addClass('active').empty();
                } else {
                    $preview_username.removeClass('active').html( $account.username );
                }

                // Media
                var i = 0;

                $('body.post-create .carousel-inner').empty();
                $('body.post-create .carousel-indicators').empty();

                $('body.post-create input.imagecheck-input:checked').each(function() {

                    $('<div class="carousel-item"><img src="' + $(this).data('original') + '" alt="" class="d-block w-100" data-holder-rendered="true"></div>').appendTo('body.post-create .carousel-inner');
                    $('<li data-target="#carousel" data-slide-to="' + i + '"></li>').appendTo('body.post-create .carousel-indicators')

                    i++;
                });

                $('body.post-create .carousel-item').first().addClass('active');
                $('body.post-create .carousel-indicators > li').first().addClass('active');
            }
        }
    }

    if ($('body').hasClass('post-create')) {

        // Init
        window.Pilot.Post.init();

        // Init Media Manager
        window.Pilot.MediaManager.init();

        // Post type selector
        $('body.post-create input[type=radio][name=type]').change(function() {

            window.Pilot.Post.setType(this.value);
            window.Pilot.Post.changeType();
            window.Pilot.Post.preview();

        });

        // Account selection
        $('body.post-create select.target-account').change(function() {

            $('body.post-create .preview-timeline .avatar').css('background-image', '');

            var account_id = $(this).val();
            var account_username = $(this).find('option:selected').text();

            // Account avatar
            $.get('https://www.instagram.com/' + account_username + '/', function(response) {
                var avatar = response.match(/<meta property="og:image" content="(.*?)" \/>/)[1];
                $('body.post-create .preview-timeline .avatar').css('background-image', 'url(' + avatar + ')');
            });

            window.Pilot.Post.setAccount(account_id, account_username);
            window.Pilot.Post.preview();

        });


        // Location lookup
        $location_lookup = $('body.post-create select.location-lookup').selectize({
            valueField: 'model',
            labelField: 'name',
            searchField: 'name',
            create: false,
            onChange: function(item) {

                $preview_location = $('body.post-create .preview-location');
                $location_name = $("body.post-create select.location-lookup option:selected").text();

                if (!item) {
                    $preview_location.addClass('active').empty();
                } else {
                    $preview_location.removeClass('active').html( $location_name );
                }
            },
            render: {
                option: function(item, escape) {
                    return '<option value="' + item.model + '">' + item.name + '</option>';
                }
            },
            load: function(query, callback) {

                if (!query.length) return callback();

                $.ajax({
                    url: BASE_URL + '/search/location',
                    type: 'POST',
                    data: {
                        '_token': $('meta[name="csrf-token"]').attr('content'),
                        'q': query,
                        'account_id': window.Pilot.Post.account.id
                    },
                    error: function() {
                        callback();
                    },
                    success: function(response) {
                        callback(response);
                    }
                });
            }
        })[0].selectize;


        // Scheduled
        $('body.post-create input.is-scheduled').change(function() {

            var is_scheduled = $(this).is(':checked');

            $('body.post-create input.scheduled-at').prop('disabled', !is_scheduled).val('');
            $('body.post-create button.btn-schedule').toggleClass('d-none');
            $('body.post-create button.btn-publish').toggleClass('d-none');

        });

        // Text preview
        $('textarea[data-emojiable=true].caption-text').data('emojioneArea').on('paste keyup change emojibtn.click', function(btn, event) {

            var caption = $('body.post-create .emojionearea.caption-text .emojionearea-editor').html();

            window.Pilot.Post.setCaption(caption);
            window.Pilot.Post.preview();

        });

        // Select media
        $(document).on('click', 'body.post-create input.imagecheck-input', function(e) {

            $selected_media = $('body.post-create input.imagecheck-input:checked').length;

            // Trigger switch type to album
            if ($selected_media > 1 && window.Pilot.Post.getType() != 'album') {
                $('body.post-create input[type=radio][name=type][value=album]').trigger('click');
            }

            window.Pilot.Post.preview();
        });

        // Show loading once form is submited
        $('form[name="post-create"]').on('submit', function(e) {
            $('body.post-create .btn-schedule').addClass('btn-loading').addClass('disabled');
            $('body.post-create .btn-publish').addClass('btn-loading').addClass('disabled');
        });
    }

    if ($('body').hasClass('post-edit')) {

        // Set account ID
        $account = $('body.post-edit #account');
        window.Pilot.Post.setAccount($account.data('account_id'), $account.html());

        // Scheduled
        $('body.post-edit input.is-scheduled').change(function() {

            var is_scheduled = $(this).is(':checked');

            $('body.post-edit input.scheduled-at').prop('disabled', !is_scheduled).val('');

            if (is_scheduled) {
                $('body.post-edit button.btn-schedule').removeClass('d-none');
                $('body.post-edit button.btn-publish').addClass('d-none');
            } else {
                $('body.post-edit button.btn-schedule').addClass('d-none');
                $('body.post-edit button.btn-publish').removeClass('d-none');
            }

        });

        setTimeout(function(){

            var is_scheduled = $('body.post-edit input.is-scheduled').is(':checked');

            $('body.post-edit input.scheduled-at').prop('disabled', !is_scheduled);

            if (is_scheduled) {
                $('body.post-edit button.btn-schedule').removeClass('d-none');
                $('body.post-edit button.btn-publish').addClass('d-none');
            } else {
                $('body.post-edit input.scheduled-at').val('');
                $('body.post-edit button.btn-schedule').addClass('d-none');
                $('body.post-edit button.btn-publish').removeClass('d-none');
            }

        }, 100);


        // Location lookup
        $('body.post-edit select.location-lookup').selectize({
            valueField: 'model',
            labelField: 'name',
            searchField: 'name',
            create: false,
            onChange: function(item) {

                $preview_location = $('body.post-edit .preview-location');
                $location_name = $("body.post-edit select.location-lookup option:selected").text();

                if (!item) {
                    $preview_location.addClass('active').empty();
                } else {
                    $preview_location.removeClass('active').html( $location_name );
                }
            },
            render: {
                option: function(item, escape) {
                    return '<option value="' + item.model + '">' + item.name + '</option>';
                }
            },
            load: function(query, callback) {

                if (!query.length) return callback();

                $.ajax({
                    url: BASE_URL + '/search/location',
                    type: 'POST',
                    data: {
                        '_token': $('meta[name="csrf-token"]').attr('content'),
                        'q': query,
                        'account_id': window.Pilot.Post.account.id
                    },
                    error: function() {
                        callback();
                    },
                    success: function(response) {
                        callback(response);
                    }
                });
            }
        });

        // Text preview
        if ($('textarea[data-emojiable=true].caption-text').data('emojioneArea')) {

            $('textarea[data-emojiable=true].caption-text').data('emojioneArea').on('paste keyup change emojibtn.click', function(btn, event) {

                var caption = $('body.post-edit .emojionearea.caption-text .emojionearea-editor').html();

                // Caption
                $preview_caption = $('body.post-edit .preview-caption');

                if (!caption) {
                    $preview_caption.addClass('active').html('<span></span><span></span>');
                } else {
                    $preview_caption.removeClass('active').html( caption );
                    $preview_caption.linky({
                        mentions: true,
                        hashtags: true,
                        urls: false,
                        linkTo: 'instagram'
                    });
                }

            });
        }

        $('body.post-edit .carousel-item').first().addClass('active');
        $('body.post-edit .carousel-indicators > li').first().addClass('active');
    }

});