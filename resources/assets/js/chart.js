$(function() {

    window.Pilot.Chart = {

    	init: function() {

    		this.initAccountSelect();

    		var first_option = $('.chart-container select[name="account_id"] option:first').val();

    		$('.chart-container select[name="account_id"]').val(first_option).trigger('change');
    	},

    	loadChart: function(account_id, widget) {

    		var type  = widget.data('type');
			var title = widget.data('title');
			var color = widget.data('color');

    		$.ajax({
                url: BASE_URL + '/account/' + account_id + '/chart',
                method: 'get',
                data: {
                	'type': type
                },
                beforeSend: function(){
                    $('.chart-container select[name="account_id"]').attr('disabled', true);
                },
                complete: function() {
                	$('.chart-container select[name="account_id"]').attr('disabled', false);
                },
                success: function(response) {

                	widget.find('.widget-title').html(title);
					widget.find('.rise').css('color', color).html(response.rise);
					widget.find('.total-count').html(response.total_count);

                	c3.generate({
				        bindto: '.chart-widget[data-type="' + type + '"] .chart-ui',
				        padding: {
				            bottom: -10,
				            left: -1,
				            right: -1
				        },
				        data: {
				            names: {
				                data: title
				            },
				            columns: [
				                response.chart
				            ],
				            type: 'area'
				        },
				        legend: {
				            show: false
				        },
				        transition: {
				            duration: 0
				        },
				        point: {
				            show: false
				        },
				        tooltip: {
				            format: {
				                title: function (x) {
				                    return '';
				                }
				            }
				        },
				        axis: {
				            y: {
				                padding: {
				                    bottom: 0,
				                },
				                show: false,
				                tick: {
				                    outer: false
				                }
				            },
				            x: {
				                padding: {
				                    left: 0,
				                    right: 0
				                },
				                show: false
				            }
				        },
				        color: {
				            pattern: [color]
				        }
				    });

                }
            });
        },

        initAccountSelect: function() {

        	that = this;

        	$(document).on('change', '.chart-container select[name="account_id"]', function() {

        		var account_id = $(this).val();

        		if (account_id.length > 0) {

        			$('.chart-widget').each(function(i, widget) {
						that.loadChart(account_id, $(this));
					});

        		}

        	});
        }

    }

    window.Pilot.Chart.init();
});