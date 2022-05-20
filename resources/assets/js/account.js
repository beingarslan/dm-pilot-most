$(function() {

    $('input.dm-pincode').pincodeInput({
        inputs: 6,
        hidedigits: false
    });

    $('input.account-lookup').selectize({
        maxItems: 1,
        valueField: 'username',
        labelField: 'username',
        searchField: 'username',
        create: false,
        sortField: [
            {
                field: 'position',
                direction: 'asc'
            },
            {
                field: '$score'
            }
        ],
        render: {
            option: function (data, escape) {
                return '<div>' +
                    '<span class="image"><img src="' + data.avatar + '" alt=""></span>' +
                    '<span class="title">' + escape(data.username) + '</span>' +
                    '</div>';
            },
            item: function (data, escape) {
                return '<div>' +
                    '<span class="image"><img src="' + data.avatar + '" alt=""></span>' +
                    escape(data.username) +
                    '</div>';
            }
        },
        load: function(query, callback) {

            this.clearOptions();
            this.renderCache = {};

            if (!query.length) return callback();

            $.ajax({
                url: 'https://www.instagram.com/web/search/topsearch/',
                type: 'GET',
                cache: false,
                data: {
                    'query' : query,
                    'context' : 'blended',
                    'count' : 5
                },
                error: function() {
                    callback();
                },
                success: function(response) {

                    results = [];
                    $.each(response.users, function(i, item) {
                        results.push({
                            position: item.position,
                            username: item.user.username,
                            avatar: item.user.profile_pic_url,
                        });
                    });

                    callback(results);
                }
            });
        }
    });

});