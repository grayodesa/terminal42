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