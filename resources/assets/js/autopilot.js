$(function() {

    $('input[name="message_type"]').on('change', function() {
        var selected = $(this).val();

        if (selected == 'list') {
            $('#option_message').hide('fast', function(){
                $('#option_list').show('fast');
                $('textarea[name="text"]').val('');
            });
        } else {
            $('#option_list').hide('fast', function(){
                $('#option_message').show('fast');
                $('select[name="lists_id"]').val('');
            });
        }
    });

    $('input[name="activity_period"]').on('change', function() {
        var checked = $(this).is(":checked");

        if(checked){
            $('#date_and_time').slideDown();
        } else {
            $('#date_and_time').slideUp(500, function(){
                $('input[name="starts_at"]').val('');
                $('input[name="ends_at"]').val('');
            });
        }

    });

});