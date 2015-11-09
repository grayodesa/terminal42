// start the popup specefic scripts
// safe to use $
jQuery(document).ready(function($) {
    var zillas = {
    	loadVals: function()
    	{
    		var shortcode = $('#_zilla_shortcode').text(),
    			uShortcode = shortcode;

    		// fill in the gaps eg {{param}}
    		$('.zilla-input').each(function() {
    			var input = $(this),
    				id = input.attr('id'),
    				id = id.replace('zilla_', ''),		// gets rid of the zilla_ prefix
    				re = new RegExp("{{"+id+"}}","g");

    			uShortcode = uShortcode.replace(re, input.val());
    		});

    		// adds the filled-in shortcode as hidden input
    		$('#_zilla_ushortcode').remove();
    		$('#zilla-sc-form-table').prepend('<div id="_zilla_ushortcode" class="hidden">' + uShortcode + '</div>');
    	},
    	cLoadVals: function()
    	{
    		var shortcode = $('#_zilla_cshortcode').text(),
    			pShortcode = '';
    			shortcodes = '';

    		// fill in the gaps eg {{param}}
    		$('.child-clone-row').each(function() {
    			var row = $(this),
    				rShortcode = shortcode;

    			$('.zilla-cinput', this).each(function() {
    				var input = $(this),
    					id = input.attr('id'),
    					id = id.replace('zilla_', '')		// gets rid of the zilla_ prefix
    					re = new RegExp("{{"+id+"}}","g");

    				rShortcode = rShortcode.replace(re, input.val());
    			});

    			shortcodes = shortcodes + rShortcode + "\n";
    		});

    		// adds the filled-in shortcode as hidden input
    		$('#_zilla_cshortcodes').remove();
    		$('.child-clone-rows').prepend('<div id="_zilla_cshortcodes" class="hidden">' + shortcodes + '</div>');

    		// add to parent shortcode
    		this.loadVals();
    		pShortcode = $('#_zilla_ushortcode').text().replace('{{child_shortcode}}', shortcodes);

    		// add updated parent shortcode
    		$('#_zilla_ushortcode').remove();
    		$('#zilla-sc-form-table').prepend('<div id="_zilla_ushortcode" class="hidden">' + pShortcode + '</div>');
    	},
    	children: function()
    	{
    		// assign the cloning plugin
    		$('.child-clone-rows').appendo({
    			subSelect: '> div.child-clone-row:last-child',
    			allowDelete: false,
    			focusFirst: false
    		});

    		// remove button
    		$('.child-clone-row-remove').live('click', function() {
    			var	btn = $(this),
    				row = btn.parent();

    			row.remove();

    			return false;
    		});

    		// assign jUI sortable
    		$( ".child-clone-rows" ).sortable({
				placeholder: "sortable-placeholder",
				items: '.child-clone-row'

			});
    	},
    	resizeTB: function()
    	{
			var	ajaxCont = $('#TB_ajaxContent'),
				tbWindow = $('#TB_window'),
				zillaPopup = $('#zilla-popup');

            if( ( zillaPopup.outerHeight() + 50 ) > 500 ) {
                var windowHeightTB = 500;
                var windowWidthTB = zillaPopup.outerWidth() + 17;
            } else {
                var windowHeightTB = zillaPopup.outerHeight() + 50;
                var windowWidthTB = zillaPopup.outerWidth();
            }

            tbWindow.css({
                height: windowHeightTB,
                width: windowWidthTB,
                marginLeft: -(zillaPopup.outerWidth()/2)
            });

			ajaxCont.css({
				paddingTop: 0,
				paddingLeft: 0,
				paddingRight: 0,
				height: (tbWindow.outerHeight()-47),
				overflow: 'auto', // IMPORTANT
				width: windowWidthTB
			});

			$('#zilla-popup').addClass('no_preview');
    	},
    	load: function()
    	{
    		var	zillas = this,
    			popup = $('#zilla-popup'),
    			form = $('#zilla-sc-form', popup),
    			shortcode = $('#_zilla_shortcode', form).text(),
    			popupType = $('#_zilla_popup', form).text(),
    			uShortcode = '';

    		// resize TB
    		zillas.resizeTB();
    		$(window).resize(function() { zillas.resizeTB() });

    		// initialise
    		zillas.loadVals();
    		zillas.children();
    		zillas.cLoadVals();

    		// update on children value change
    		$('.zilla-cinput', form).live('change', function() {
    			zillas.cLoadVals();
    		});

    		// update on value change
    		$('.zilla-input', form).change(function() {
    			zillas.loadVals();
    		});

            $('.colorpicker-input').each(function(){
    			var Othis = this;

    			$(this).ColorPicker({
                    color: '#FFFFFF',
                    onShow: function (colpkr) {
                        $(colpkr).fadeIn(500);
                        return false;
                    },
                    onHide: function (colpkr) {
                        $(colpkr).fadeOut(500);
                        return false;
                    },
                    onChange: function (hsb, hex, rgb) {
                        $(Othis).attr('value','#' + hex);
                        zillas.loadVals();
                        zillas.cLoadVals();
                    }
                });

    		}); //end color picker

    		// when insert is clicked
    		$('.zilla-insert', form).click(function() {
    			if(parent.tinymce) {
                    parent.tinymce.activeEditor.execCommand('mceInsertContent',false,$('#_zilla_ushortcode', form).html());
                    tb_remove();
                }
    		});
    	}

	}

    // run
    $('#zilla-popup').livequery( function() { zillas.load(); } );
});