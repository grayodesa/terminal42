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