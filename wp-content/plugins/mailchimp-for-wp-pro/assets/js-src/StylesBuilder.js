(function() {
	'use strict';

	var $ = window.jQuery;
	var iframeElement = document.getElementById('mc4wp-css-preview');
	var FormPreview = require('./FormPreview.js');
	var preview = new FormPreview( iframeElement );
	var $imageUploadTarget;
	var original_send_to_editor = window.send_to_editor;
	var Accordion = require('./Accordion.js'),
		accordion;

	// init
	$(iframeElement).load(preview.init);

	// turn settings page into accordion
	accordion = new Accordion(document.querySelector('.mc4wp-accordion'));
	accordion.init();

	// show generated CSS button
	$(".mc4wp-show-css").click(function() {

		var $generatedCss = $("#mc4wp_generated_css");
		$generatedCss.toggle();

		if( $generatedCss.is(":visible")) {
			$(this).text("Hide generated CSS");
		} else {
			$(this).text("Show generated CSS");
		}
	});

	$(".mc4wp-form-select").change( function() {
		$(this).parents('form').submit();
	});

	// show thickbox when clicking on "upload-image" buttons
	$(".upload-image").click( function() {
		$imageUploadTarget = $(this).siblings('input');
		tb_show('', 'media-upload.php?type=image&TB_iframe=true');
	});

	// attach handler to "send to editor" button
	window.send_to_editor = function(html){
		if( $imageUploadTarget ) {
			var imgurl = $('img',html).attr('src');
			$imageUploadTarget.val(imgurl);
			tb_remove();
		} else {
			original_send_to_editor(html);
		}

		preview.applyStyles();
	}

})();