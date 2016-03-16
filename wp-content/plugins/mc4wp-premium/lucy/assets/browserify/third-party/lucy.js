'use strict';

var lucy = function( site_url, algolia_app_id, algolia_api_key, algolia_index_name, links, contactLink ) {

	// make sure site url does not have trailing slash
	if( site_url.charAt(site_url.length - 1) == "/") {
		site_url = site_url.substr(0, site_url.length - 1);
	}

	var m = require('mithril');
	var algoliasearch = require('algoliasearch');
	var client = algoliasearch( algolia_app_id, algolia_api_key );
	var index = client.initIndex( algolia_index_name );

	var isOpen = false;
	var loader, loadingInterval = 0;
	var searchResults = m.prop([]);
	var searchQuery = m.prop('');
	var nothingFound = false;

	// create element
	var element = document.createElement('div');
	element.setAttribute('class','lucy closed');
	document.body.appendChild(element);

	function addEvent(element,event,handler) {
		if(element.addEventListener){
			element.addEventListener(event,handler,false);
		} else {
			element.attachEvent('on' + event, handler);
		}
	}

	function removeEvent(element,event,handler){
		if(element.removeEventListener){
			element.removeEventListener(event,handler);
		} else {
			element.detachEvent('on' + event, handler);
		}
	}

	function maybeClose(event) {
		event = event || window.event;

		// close when pressing ESCAPE
		if(event.type === 'keyup' && event.keyCode == 27 ) {
			close();
			return;
		}

		// close when clicking ANY element outside of Lucy
		var clickedElement = event.target || event.srcElement;
		if(event.type === 'click' && element.contains && ! element.contains(clickedElement) )  {
			close();
		}
	}

	function open() {
		if( isOpen ) return;
		isOpen = true;
		m.redraw();

		element.setAttribute('class', 'lucy open' );

		addEvent(document,'keyup',maybeClose);
		addEvent(document,'click',maybeClose);
	}

	function close() {
		if( ! isOpen ) return;
		isOpen = false;
		reset();

		element.setAttribute('class', 'lucy closed' );

		removeEvent(document,'keyup',maybeClose);
		removeEvent(document,'click',maybeClose);
	}

	function reset() {
		searchQuery('');
		searchResults([]);
		nothingFound = false;
		m.redraw();
	}

	function listenForInput(event) {
		event = event || window.event;

		// revert back to list of links when empty
		if( this.value === '' && searchQuery() !== '' ) {
			return reset();
		}

		searchQuery(this.value);

		// perform search on [ENTER]
		if(event.keyCode == 13 ) {
			return search(this.value);
		}
	}

	var module = {};
	module.view = function() {
		var content;

		return [
			m('div.lucy--content', { style: { display: isOpen ? 'block' : 'none' } }, [
				m('span.close-icon', { onclick: close }, ""),
				m('div.header', [
					m('h4', 'Looking for help?'),
					m('div.search-form', {
						onsubmit: search
					}, [
						m('input', {
							type: 'text',
							value: searchQuery(),
							onkeyup: listenForInput,
							config: function(el) { isOpen && el.focus(); },
							placeholder: 'What are you looking for?'
						}),
						m('span', {
							"class": 'loader',
							config: function(el) {
								loader = el;
							}
						}),
						m('input', { type: 'submit' })
					])
				]),
				m('div.list', [

					m('div.links', { style: { display: searchQuery().length > 0 ? 'none' : 'block' } }, links.map(function(l) {
						return m('a', { href: l.href }, m.trust(l.text) );
					})),


					m('div.search-results', [
						m("em.search-pending", { style: { display: ( searchQuery().length > 0 && searchResults().length == 0 ) ? 'block' : 'none' } }, ( nothingFound ? "Nothing found for " : "Hit [ENTER] to search for" ) + "\""+ searchQuery() +"\".."),
						searchResults().map(function(l) {
							return m('a', { href: l.href }, m.trust(l.text) );
						})
					])

				]),
				m('div.footer', [
					m("span", "Can't find the answer you're looking for?"),
					m("a", { "class": 'button button-primary', href: contactLink, target: "_blank" }, "Contact Support")
				])
			]),
			m('span.lucy-button', {
				onclick: open,
				style: { display: isOpen ? 'none' : 'block' }
			}, [
				m('span.lucy-button-text',  "Need help?")
			])
		];
	};

	function showResults(results) {

		if( results.length ) {
			var parser = document.createElement('a');
			searchResults(results.map(function(r) {
				parser.href = r.path;
				var url = site_url + parser.pathname + ( parser.search || '' );
				return { href: url, text: r._highlightResult.title.value};
			}));
		} else {
			nothingFound = true;
		}

		m.redraw();
	}


	function search(query) {

		// start loader
		loader.innerText = '.';
		loadingInterval = window.setInterval(function() {
			loader.innerText += '.';

			if( loader.innerText.length > 3 ) {
				loader.innerText = '.';
			}
		}, 333 );

		// search
		index.search( query, { hitsPerPage: 5 }, function( error, result ) {

			if( error ) {
				console.log(error);
			 } else {
				showResults(result.hits);
			}

			/* clear loader */
			loader.innerText = '';
			window.clearInterval(loadingInterval);
		} );

	}

	m.mount(element,module);
};

module.exports = lucy;