(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
var Accordion = function(element) {
	'use strict';

	var accordions = [],
		accordionElements;

	var AccordionElement = function( element) {

		var heading,
			content;

		function init() {
			heading = element.querySelector('h2,h3,h4');
			content = element.querySelector('div');

			element.setAttribute('class','accordion');
			heading.setAttribute('class','accordion-heading');
			content.setAttribute('class','accordion-content');
			content.style.display = 'none';

			heading.onclick = toggle;
		}

		/**
		 * Open this accordion
		 */
		function open() {
			toggle(true);
		}

		/**
		 * Close this accordion
		 */
		function close() {
			toggle(false);
		}

		/**
		 * Toggle this accordion
		 *
		 * @param show
		 */
		function toggle(show) {

			if( typeof(show) !== "boolean" ) {
				show = ( content.offsetParent === null );
			}

			if( show ) {
				closeAll();
			}

			content.style.display = show ? 'block' : 'none';
			element.className = 'accordion ' + ( ( show ) ? 'expanded' : 'collapsed' );
		}

		/**
		 * Expose some methods
		 */
		return {
			'open': open,
			'close': close,
			'toggle': toggle,
			'init': init
		}

	};

	/**
	 * Close all accordions
	 */
	function closeAll() {
		for(var i=0; i<accordions.length; i++){
			accordions[i].close();
		}
	}

	/**
	 * Initialize the accordion functionality
	 */
	function init() {
		// add class to container
		element.className+= " accordion-container";

		// find accordion blocks
		accordionElements = element.children;

		// hide all content blocks
		for( var i=0; i < accordionElements.length; i++) {

			// only act on direct <div> children
			if( accordionElements[i].tagName.toUpperCase() !== 'DIV' ) {
				continue;
			}

			// create new accordion
			var accordion = new AccordionElement(accordionElements[i]);
			accordion.init();

			// add to list of accordions
			accordions.push(accordion);
		}

		// open first accordion
		accordions[0].open();
	}

	/**
	 * Expose some methods
	 */
	return {
		'init': init
	}


};

module.exports = Accordion;
},{}],2:[function(require,module,exports){
var FormPreview = function(context) {
	'use strict';

	var $context, $elements;
	var Option = require('./Option.js');
	var $ = window.jQuery;

	// find all elements
	$context = $(context);

	// create option elements
	var options = createOptions();

	// attach events
	$(".mc4wp-option").change(applyStyles);
	$('.color-field').wpColorPicker({
		change: applyStyles,
		clear: clearStyles
	});


	// initialize form preview
	function init() {
		var $form = $context.contents().find('.mc4wp-form');

		$elements = {
			form: $form,
			labels: $form.find('label'),
			fields: $form.find('input[type="text"], input[type="email"], input[type="url"], input[type="number"], input[type="date"], select, textarea'),
			choices: $form.find('input[type="radio"], input[type="checkbox"]'),
			buttons: $form.find('input[type="submit"], input[type="button"], button'),
			messages: $form.find('.mc4wp-alert'),
			css: $context.contents().find('#custom-css')
		};

		// apply custom styles to fields (focus)
		$elements.fields.focus(setFieldFocusStyles);
		$elements.fields.focusout(setDefaultFieldStyles);

		// apply custom styles to buttons (hover)
		$elements.buttons.hover(setButtonHoverStyles, setDefaultButtonStyles);

		// apply selected settings straight away
		applyStyles();
	}

	// create option elements from HTML elements
	function createOptions() {
		var optionElements = document.querySelectorAll('.mc4wp-option');
		var options = {};

		for( var i=0; i<optionElements.length; i++ ) {
			options[ optionElements[i].id ] = new Option( optionElements[i] );
		}

		return options;
	}

	function clearStyles() {
		$elements.form.removeAttr('style');
		$elements.labels.removeAttr('style');
		$elements.fields.removeAttr('style');
		$elements.buttons.removeAttr('style');
		$elements.choices.removeAttr('style');
		$elements.messages.removeAttr('style');
	}

	function applyStyles() {

		$elements.choices.css({
			'display': 'inline-block',
			'margin-right': '6px'
		});

		$elements.buttons.css({
			"text-align": "center",
			"cursor": "pointer",
			"padding": "6px 12px",
			"text-shadow": "none",
			"box-sizing": "border-box",
			"line-height": "normal",
			"vertical-align": "top"
		});

		// apply custom styles to form
		$elements.form.css({
			'max-width': options["form-width"].getValue(),
			'text-align': options["form-text-align"].getValue(),
			'font-size': options["form-font-size"].getPxValue(),
			"font-color": options["form-font-color"].getColorValue(),
			"background-color": options["form-background-color"].getColorValue(),
			"border-color": options["form-border-color"].getColorValue(),
			"border-width": options["form-border-width"].getPxValue(),
			"padding": options["form-padding"].getPxValue()
		});

		// responsive label width
		if( options["form-width"].getValue().length ) {
			$elements.form.css('width', '100%');
		}

		// set background image (if set, otherwise reset)
		if( options["form-background-image"].getValue().length > 0 ) {
			$elements.form.css('background-image', 'url("' + options["form-background-image"].getValue() + '")');
			$elements.form.css('background-repeat', options["form-background-repeat"].getValue() );
		} else {
			$elements.form.css('background-image', 'initial');
			$elements.form.css('background-repeat','');
		}

		if( options["form-border-width"].getValue() > 0 ) {
			$elements.form.css( 'border-style', 'solid' );
		}

		// apply custom styles to labels
		$elements.labels.css({
			"margin-bottom": "6px",
			"box-sizing": "border-box",
			"vertical-align": "top",
			"color": options["labels-font-color"].getColorValue(),
			"font-size": options["labels-font-size"].getPxValue(),
			"display": options["labels-display"].getValue(),
			"max-width": options["labels-width"].getValue()
		});

		// responsive label width
		if( options["labels-width"].getValue().length ) {
			$elements.labels.css('width', '100%');
		}

		// reset font style of <span> elements inside <label> elements
		$elements.labels.find('span').css('font-weight', 'normal' );

		// only set label text style if it's set
		var labelsFontStyle = options["labels-font-style"].getValue();
		if( labelsFontStyle.length > 0 ) {
			$elements.labels.css({
				"font-weight": (labelsFontStyle == 'bold' || labelsFontStyle == 'bolditalic') ? 'bold' : 'normal',
				"font-style": (labelsFontStyle == 'italic' || labelsFontStyle == 'bolditalic') ? 'italic' : 'normal'
			});
		}

		// apply custom styles to inputs
		$elements.fields.css({
			"padding": '6px 12px',
			"margin-bottom": "6px",
			"box-sizing": "border-box",
			"vertical-align": "top",
			"border-width": options["fields-border-width"].getPxValue(),
			"border-color": options["fields-border-color"].getColorValue(),
			"border-radius": options["fields-border-radius"].getPxValue(),
			"display": options["fields-display"].getValue(),
			"max-width": options["fields-width"].getValue(),
			"height": options["fields-height"].getPxValue()
		});

		// responsive field width
		if( options["fields-width"].getValue().length ) {
			$elements.fields.css('width', '100%');
		}

		// apply custom styles to buttons
		$elements.buttons.css({
			'border-width': options["buttons-border-width"].getPxValue(),
			'border-color': options["buttons-border-color"].getColorValue(),
			"border-radius": options["buttons-border-radius"].getPxValue(),
			'max-width': options["buttons-width"].getValue(),
			'height': options["buttons-height"].getPxValue(),
			'background-color': options["buttons-background-color"].getColorValue(),
			'color': options["buttons-font-color"].getColorValue(),
			'font-size': options["buttons-font-size"].getPxValue()
		});

		// responsive buttons width
		if( options["buttons-width"].getValue().length ) {
			$elements.buttons.css('width', '100%');
		}

		// add border style if border-width is set and bigger than 0
		if( options["buttons-border-width"].getValue() > 0 ) {
			$elements.buttons.css( 'border-style', 'solid' );
		}

		// add background reset if custom button background was set
		if( options["buttons-background-color"].getColorValue().length ) {
			$elements.buttons.css({
				"background-image": "none",
				"filter": "none"
			});

			// calculate hover color
			var hoverColor = lightenColor( options["buttons-background-color"].getColorValue(), -20 );
			options["buttons-hover-background-color"].setValue(hoverColor);
		} else {
			options["buttons-hover-background-color"].setValue('');
		}

		if( options["buttons-border-color"].getColorValue().length ) {
			var hoverColor = lightenColor( options["buttons-border-color"].getColorValue(), -20 );
			options["buttons-hover-border-color"].setValue(hoverColor);
		} else {
			options["buttons-hover-border-color"].setValue('');
		}

		// apply custom styles to messages
		$elements.messages.filter('.mc4wp-success').css({
			'color': options["messages-font-color-success"].getColorValue()
		});

		$elements.messages.filter('.mc4wp-error').css({
			'color': options["messages-font-color-error"].getColorValue()
		});

		// print custom css in container element
		$elements.css.html(options["manual-css"].getValue());
	}

	function setButtonHoverStyles() {
		// calculate darker color
		$elements.buttons.css('background-color', options["buttons-hover-background-color"].getColorValue() );
		$elements.buttons.css('border-color', options["buttons-hover-border-color"].getColorValue() );
	}

	function setDefaultButtonStyles() {
		$elements.buttons.css({
			'border-color': options["buttons-border-color"].getColorValue(),
			'background-color': options["buttons-background-color"].getColorValue()
		});
	}

	function setFieldFocusStyles() {
		if( options["fields-focus-outline-color"].getColorValue().length ) {
			$elements.fields.css('outline', '2px solid ' + options["fields-focus-outline-color"].getColorValue() );
		} else {
			setDefaultFieldStyles();
		}
	}

	function setDefaultFieldStyles() {
		$elements.fields.css('outline', '' );
	}

	function lightenColor(col, amt) {

		var usePound = false;

		if (col[0] == "#") {
			col = col.slice(1);
			usePound = true;
		}

		var num = parseInt(col,16);

		var r = (num >> 16) + amt;

		if (r > 255) r = 255;
		else if  (r < 0) r = 0;

		var b = ((num >> 8) & 0x00FF) + amt;

		if (b > 255) b = 255;
		else if  (b < 0) b = 0;

		var g = (num & 0x0000FF) + amt;

		if (g > 255) g = 255;
		else if (g < 0) g = 0;

		return (usePound?"#":"") + String("000000" + (g | (b << 8) | (r << 16)).toString(16)).slice(-6);
	}

	return {
		init: init,
		applyStyles: applyStyles
	}

};


module.exports = FormPreview;
},{"./Option.js":3}],3:[function(require,module,exports){
var Option = function( element ) {

	var $ = window.jQuery;

	// find corresponding element
	this.element = element;
	this.$element = $(element);

	// helper methods
	this.getColorValue = function() {
		if( this.element.value.length > 0 ) {
			if( this.element.className.indexOf('wp-color-picker') !== -1) {
				return this.$element.wpColorPicker('color');
			} else {
				return this.element.value;
			}
		}

		return '';
	};

	this.getPxValue = function( fallbackValue ) {
		if( this.element.value.length > 0 ) {
			return parseInt( this.element.value ) + "px";
		}

		return fallbackValue || '';
	};

	this.getValue = function( fallbackValue ) {

		if( this.element.value.length > 0 ) {
			return this.element.value;
		}

		return fallbackValue || '';
	};

	this.clear = function() {
		this.element.value = '';
	};

	this.setValue = function(value) {
		this.element.value = value;
	};
};

module.exports = Option;
},{}],4:[function(require,module,exports){
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
},{"./Accordion.js":1,"./FormPreview.js":2}]},{},[4]);
