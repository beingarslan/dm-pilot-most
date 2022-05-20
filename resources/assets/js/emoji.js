$(function() {

	window.emojioneVersion = "3.0.1";

	$('textarea[data-emojiable=true], input[data-emojiable=true]').emojioneArea({
		search: false,
		useInternalCDN: false,
		tones: false
	});

});