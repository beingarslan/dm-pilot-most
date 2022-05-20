const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.styles([
	'node_modules/tabler-ui/dist/assets/css/dashboard.css',
	'node_modules/tabler-ui/dist/assets/plugins/charts-c3/plugin.css',
	'node_modules/flatpickr/dist/flatpickr.css',
	'node_modules/emojionearea/dist/emojionearea.min.css',
	'node_modules/viewerjs/dist/viewer.min.css',
	'node_modules/bootstrap-pincode-input/css/bootstrap-pincode-input.css',
	'resources/assets/css/post-create.css',
	'resources/assets/css/post-edit.css',
	'resources/assets/css/pages.css',
	'resources/assets/css/settings.css',
	'resources/assets/css/dm.css'
], 'public/css/dm.bundle.css');

mix.scripts([
	'node_modules/tabler-ui/src/assets/js/vendors/jquery-3.2.1.min.js',
	'node_modules/tabler-ui/src/assets/js/vendors/bootstrap.bundle.min.js',
	'node_modules/tabler-ui/src/assets/js/vendors/selectize.min.js',
	'node_modules/jquery.repeater/jquery.repeater.min.js',
	'node_modules/flatpickr/dist/flatpickr.js',
	'node_modules/flatpickr/dist/l10n/ru.js',
	'node_modules/flatpickr/dist/l10n/pt.js',
	'node_modules/flatpickr/dist/l10n/tr.js',
	'node_modules/emojionearea/dist/emojionearea.min.js',
	'node_modules/bootbox/dist/bootbox.all.min.js',
	'node_modules/timeago.js/dist/timeago.min.js',
	'node_modules/timeago.js/dist/timeago.locales.min.js',
	'node_modules/tabler-ui/dist/assets/plugins/charts-c3/js/d3.v3.min.js',
	'node_modules/tabler-ui/dist/assets/plugins/charts-c3/js/c3.min.js',
	'node_modules/blueimp-file-upload/js/vendor/jquery.ui.widget.js',
	'node_modules/blueimp-file-upload/js/jquery.iframe-transport.js',
	'node_modules/blueimp-file-upload/js/jquery.fileupload.js',
	'node_modules/show-more/jquery.show-more.js',
	'node_modules/viewerjs/dist/viewer.min.js',
	'node_modules/sortablejs/Sortable.min.js',
	'node_modules/jquery-sortablejs/jquery-sortable.js',
	'node_modules/bootstrap-pincode-input/js/bootstrap-pincode-input.js',
	'resources/assets/js/jquery.linky.js',
	'resources/assets/js/account.js',
	'resources/assets/js/message.js',
	'resources/assets/js/autopilot.js',
	'resources/assets/js/list.js',
	'resources/assets/js/emoji.js',
	'resources/assets/js/direct.js',
	'resources/assets/js/media.js',
	'resources/assets/js/post.js',
	'resources/assets/js/chart.js',
	'resources/assets/js/rss.js',
	'resources/assets/js/bot.js',
	'resources/assets/js/common.js'
], 'public/js/dm.bundle.js');


mix.copyDirectory('node_modules/tabler-ui/dist/assets/fonts', 'public/fonts')
   .copyDirectory('node_modules/trumbowyg/dist', 'public/trumbowyg')
   .copyDirectory('resources/assets/img', 'public/img');
