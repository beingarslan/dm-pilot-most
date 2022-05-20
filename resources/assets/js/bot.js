$repeater = $('body.bot-index form.qa-repeater').repeater({
    ready: function (setIndexes) {

        $('body.bot-index input.hears').selectize({
		    delimiter: ',',
		    plugins: ['remove_button'],
		    persist: false,
		    create: function (input) {
		        return {
		            value: input,
		            text: input
		        }
		    }
		});

    },
	show: function () {

		$(this).show();

		$('body.bot-index input.hears').selectize({
		    delimiter: ',',
		    plugins: ['remove_button'],
		    persist: false,
		    create: function (input) {
		        return {
		            value: input,
		            text: input
		        }
		    }
		});

    }
});

$('body.bot-index ul.dialogue-qa').sortable({
	onEnd: function(e) {
		$repeater.repeater();
	}
});