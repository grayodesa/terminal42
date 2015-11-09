(function($) {
"use strict";

	//Shortcodes
	tinymce.PluginManager.add( 'zillaShortcodes', function( editor, url ) {

		editor.addCommand("zillaPopup", function ( a, params ) {
			var popup = params.identifier;
			tb_show("Insert a Shortcode", url + "/popup.php?popup=" + popup + "&width=" + 800);
		});

	    editor.addButton( 'zilla_button', {
	        type: 'menubutton',
	        icon: 'code',
			title:  'Shortcodes',
			menu: [
				{
					text: 'Columns',
					onclick:function(){
						editor.execCommand("zillaPopup", false, {title: 'Columns',identifier: 'columns'})
					}
				},
				{
					text: 'Dividers',
					onclick:function(){
						editor.execCommand("zillaPopup", false, {title: 'Dividers',identifier: 'divider'})
					}
				},
				{
					text: 'Simple Button',
					onclick:function(){
						editor.execCommand("zillaPopup", false, {title: 'Simple Button',identifier: 'button'})
					}
				},
				{
					text: 'Border Button',
					onclick:function(){
						editor.execCommand("zillaPopup", false, {title: 'Border Button',identifier: 'borderbutton'})
					}
				},
				{
					text: 'Alternate Button',
					onclick:function(){
						editor.execCommand("zillaPopup", false, {title: 'Alternate Button',identifier: 'altbutton'})
					}
				},
				{
					text: 'Style Box',
					onclick:function(){
						editor.execCommand("zillaPopup", false, {title: 'Style Box',identifier: 'stylebox'})
					}
				},
				{
					text: 'Style Box 2',
					onclick:function(){
						editor.execCommand("zillaPopup", false, {title: 'Style Box 2',identifier: 'stylebox2'})
					}
				},
				{
					text: 'Alerts',
					onclick:function(){
						editor.execCommand("zillaPopup", false, {title: 'Alerts',identifier: 'alert'})
					}
				},
				{
					text: 'Promo',
					onclick:function(){
						editor.execCommand("zillaPopup", false, {title: 'Promo',identifier: 'promo'})
					}
				},
				{
					text: 'Feature Boxes',
					onclick:function(){
						editor.execCommand("zillaPopup", false, {title: 'Feature Boxes',identifier: 'feature'})
					}
				},
				{
					text: 'Icon',
					onclick:function(){
						editor.execCommand("zillaPopup", false, {title: 'Icon',identifier: 'soloicon'})
					}
				},
				{
					text: 'Toggle',
					onclick:function(){
						editor.execCommand("zillaPopup", false, {title: 'Toggle',identifier: 'toggle'})
					}
				},
				{
					text: 'Accordion',
					onclick:function(){
						editor.execCommand("zillaPopup", false, {title: 'Accordion',identifier: 'accordion'})
					}
				},
				{
					text: 'Tabs',
					onclick:function(){
						editor.execCommand("zillaPopup", false, {title: 'Tabs',identifier: 'tabs'})
					}
				},
				{
					text: 'Icon Tabs',
					onclick:function(){
						editor.execCommand("zillaPopup", false, {title: 'Icon Tabs',identifier: 'icontabs'})
					}
				},
				{
					text: 'Individual Post',
					onclick:function(){
						editor.execCommand("zillaPopup", false, {title: 'Individual Post',identifier: 'ipost'})
					}
				},
				{
					text: 'Posts Block',
					onclick:function(){
						editor.execCommand("zillaPopup", false, {title: 'Posts Block',identifier: 'posts'})
					}
				},
				{
					text: 'Portfolio Block',
					onclick:function(){
						editor.execCommand("zillaPopup", false, {title: 'Portfolio Block',identifier: 'portfolio'})
					}
				},
				{
					text: 'Portfolio Carousel',
					onclick:function(){
						editor.execCommand("zillaPopup", false, {title: 'Portfolio Carousel',identifier: 'portfoliocarousel'})
					}
				},
				{
					text: 'Slider',
					onclick:function(){
						editor.execCommand("zillaPopup", false, {title: 'Slider',identifier: 'slider'})
					}
				},
				{
					text: 'FAQs',
					onclick:function(){
						editor.execCommand("zillaPopup", false, {title: 'FAQs',identifier: 'faqs'})
					}
				},
				{
					text: 'Google Maps',
					onclick:function(){
						editor.execCommand("zillaPopup", false, {title: 'Google Maps',identifier: 'gmap'})
					}
				},
				{
					text: 'Pricing',
					onclick:function(){
						editor.execCommand("zillaPopup", false, {title: 'Pricing',identifier: 'pricing'})
					}
				},
				{
					text: 'Team',
					onclick:function(){
						editor.execCommand("zillaPopup", false, {title: 'Team',identifier: 'team'})
					}
				},
				{
					text: 'Skills',
					onclick:function(){
						editor.execCommand("zillaPopup", false, {title: 'Skills',identifier: 'skills'})
					}
				},
				{
					text: 'Clients Scroller',
					onclick:function(){
						editor.execCommand("zillaPopup", false, {title: 'Clients Scroller',identifier: 'clients'})
					}
				},
				{
					text: 'Testimonials',
					onclick:function(){
						editor.execCommand("zillaPopup", false, {title: 'Testimonials',identifier: 'testimonials'})
					}
				},
				{
					text: 'Icon List',
					onclick:function(){
						editor.execCommand("zillaPopup", false, {title: 'Icon List',identifier: 'iconlist'})
					}
				},
				{
					text: 'Newsletter Subscribe',
					onclick:function(){
						editor.execCommand("zillaPopup", false, {title: 'Newsletter Subscribe',identifier: 'subscribe'})
					}
				},
				{
					text: 'Blockquote',
					onclick:function(){
						editor.execCommand("zillaPopup", false, {title: 'Blockquote',identifier: 'blockquote'})
					}
				},
				{
					text: 'Responsive Content',
					onclick:function(){
						editor.execCommand("zillaPopup", false, {title: 'Responsive Content',identifier: 'responsive'})
					}
				}
			]

		});

	});

})(jQuery);