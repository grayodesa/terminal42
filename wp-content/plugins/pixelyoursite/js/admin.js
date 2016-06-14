jQuery(function($){


	/* Tabs */
	$('.facebook-pixel-body ul li').on('click', function(event) {

		event.preventDefault();
		var i = $(this).attr('id').replace('facebook-pixel-menu-', '');
		$('.facebook-pixel-body ul li').removeClass('nav-tab-active selected');
		$(this).addClass('nav-tab-active selected');

		$('.facebook-pixel-panel').hide();
		$('#facebook-pixel-panel-'+i).show();



	});

	/* Add remove button for removing pageurl option */
	function woofp_removebutton(){


		$('.woofp-pageurl-tr').each(function(index, el) {

			
			var $this = $(this);
			if( index > 0 ){


				//insert remove button for pageurl/event option except first
				var th = $this.find('th:first');
				if( th.find('input.woofp-remove-event').length  < 1 ){
					th.html( th.html() + '<input type="button" class="button button-secondary woofp-remove-event" value="Remove" />');
				}
			
			} else {

				//insert reset button for first pageurl/event option
				var th = $this.find('th:first');
				if( th.find('input.woofp-reset-event').length  < 1 ){
					th.html( th.html() + '<input type="button" class="button button-secondary woofp-reset-event" value="Reset" />');
				}
			}

		});

	}
	woofp_removebutton();

	/* Reset standard event */
	$(document).on('click', '.woofp-reset-event', function(event) {
		
		event.preventDefault();
		var $this = $(this);
		$(this).closest('tr.woofp-pageurl-tr').find('input').not(':button, :submit, :reset, :hidden').val('')
		$(this).closest('tr.woofp-pageurl-tr').find('select option:eq(0)').prop('selected', true);

	});

	/* Remove standard event */
	$(document).on('click', '.woofp-remove-event', function(event) {
		
		event.preventDefault();
		var $this = $(this);
		$(this).closest('tr.woofp-pageurl-tr').remove();

	});

	/* Add more Event */
	$(document).on('click', '.woofp-addevent', function(event) {
		
		event.preventDefault();
		var events_options = $('tr.woofp-pageurl-tr:first td:first select.woofp-input-event').html();
		var currencty_options = $('tr.woofp-pageurl-tr:first td:first select.woofp-input-currency').html();
		var extentedoption = '';
		var html = '';

		

		html += '<tr class="woofp-pageurl-tr">';
		html += '<th scope="row">Page(URL or Partial URL)</th>';
		html += '<td>';
		
		html += '<p><input type="text" name="standardevent[pageurl][]" value="" class="woofp-input-pageurl" /></p>';
		html += '<p class="description">This Event will trigger on any URL that contains this string.<br>';
		html += 'If you add * at the end of the URL string it will match all URLs starting with the URL string.</p>';
		
		html += '<p>';
		html += '<b class="event_param">Event Type: </b>';
		html += '<select name="standardevent[eventtype][]" class="woofp-input-event">';
		html += events_options;
		html += '</select>';
		html += '</p>';
		
		html += '<p class="standarevent-value">';
		html += '<b class="event_param">Value: </b><input type="text" name="standardevent[value][]" value="" class=" woofp-input-value" />';
		html += '<span class="woofp-input-desc">&nbsp;&nbsp;*Mandatory for purchase event only.</span>';
		html += '</p>';
		
		html += '<p class="standarevent-currency">';
		html += '<b class="event_param">Currency: </b><select name="standardevent[currency][]"  class=" woofp-input-currency">';
		html += currencty_options;
		html += '</select>';
		html += '<span class="woofp-input-desc">&nbsp;&nbsp;*Mandatory for purchase event only.</span>';
		html += '</p>';

		html += '<div class="standardevent-params"></div>';
		html += '<h4>OR add Event Code here (Advances users only):</h4>';
		html += '<textarea style="width:35em;height:180px;" name="standardevent[code][]"></textarea>';
		html += '<div class="code_important">';
		html += '<p class="description"><span class="woofp-asterik">*</span>The code inside the event field will overwrite and will have priority over any other data for that event.</p>';
		html += '<p class="description"><span class="woofp-asterik">*</span>The code inserted in the field MUST be complete, including fbq(\'track\', \'AddToCart\', { …… });</p>';
		html += '</div>';
		html += '</td>';
		html += '</tr>';

		var $this = $(this);

		$this.closest('tr').before(html).hide().fadeIn();
		woofp_removebutton();

		//select newly created tr
		var new_element = $('tr.woofp-pageurl-tr').last();

		//reset event select
		new_element.find('select option:eq(0)').prop('selected', true);

		//scroll to newly created element.
		var offset = new_element.offset().top;

		$('body, html').animate({ scrollTop: parseInt(offset) - 100 }, 1000);
		// do fading 3 times
		for(i=0;i<3;i++) {
		    new_element.fadeTo('fast', 0.3).fadeTo('fast', 1.0);
		}
		
	});



function woofp_vars(){
	var vars = ['content_name','content_category','content_ids','content_type','search_string','num_items','order_id','status'];
	return vars;
}

	/*
		Save Settings
	*/
	$(document).on('click', '.woofp-savesettings', function(event){
		
		event.preventDefault();

		var data = $('#facebook-pixel-form').serialize();
			data = 'action=woofbsavesettings&'+data;

		var loader = '<img src="'+woofp.loading+'" alt="Loading..." class="woofp-loading" style="display:inline; padding:10px 10px;" />';
		var $this = $(this);

		$this.prop('disabled', true);
		$this.after(loader);

		var facebookpixel = $('#facebookpixel-activate').prop('checked');
		var standardevent = $('#standardevent-activate').prop('checked');
		var woocommerce = $('#woocommerce-activate').prop('checked');

		$.ajax({
			url: woofp.ajaxurl,
			type: 'POST',
			dataType: 'json',
			data: data,
		})
		.done(function(data) {

			if( data.woo == 0 ){
				
				location.reload(true);

			} else {

				if( standardevent ){
					$('tr.show-standardevent').removeClass('hide-standardevent');
				} else {

					$('tr.show-standardevent').addClass('hide-standardevent');
				}

				$this.next('img.woofp-loading').remove();
				$this.prop('disabled', false);

			}

		});



	});


$('#facebook-pixel-panel-2 .woofp-pageurl-tr td').each(function(index, el) {
	

	var td = $(this);
	var val = td.find('select.woofp-input-event option:selected').val();


if( val != '' ){	 	
	td.find('.standarevent-value').show();
	td.find('.standarevent-currency').show();
} else {

	td.find('.standarevent-value').hide();
	td.find('.standarevent-currency').hide();
}
	
});



$(document).on('change', 'select.woofp-input-event', function(e){


	var val = $(this).val();

	if( val == '' )
		return false;

	var params = woofp_standardevent_extented(val);
	var params_vars = woofp_vars();
	
	console.log(params);

	var html = '';
	var i = 0;
	for (var key in params) {
	  if (params.hasOwnProperty(key)) {
		
		

	    var param = params[key];


		if( param != undefined && param != '' ){

	      	for(var i = 0; i < params_vars.length; i++){
	    		
				if (params_vars[i] === param.name) params_vars.splice(i, 1);
			}

			
			html +='<div class="event_param_wrap">';

		if( param.name == 'content_type')
			html +='<b class="event_param">'+param.name+': </b><select name="standardevent['+param.name+'][]" ><option value="product">product</option><option value="product_group">product_group</option></select>';
		else	
			html +='<b class="event_param">'+param.name+': </b><input type="text" name="standardevent['+param.name+'][]" value="" />';
		

			
			html +='<p class="description">'+param.info+' i.e '+param.example+'</p>';
			html +='</div>';
		}

	  }

	  
	}

	var hidden_params = '';
	
	for(var i= 0; i < params_vars.length; i++){    	
	      	var param = params_vars[i];
	    	hidden_params +='<input type="hidden" name="standardevent['+param+'][]" value="" style="display:none;"  />';
	}	

	var tr = $(this).closest('tr');
	tr.find('.standardevent-params').html(html + hidden_params);
	
	tr.find('.standarevent-value').show();
	tr.find('.standarevent-currency').show();

});

function woofp_standardevent_extented(event){


	var params = {};
		params['0'] = params['1'] = params['2'] = params['3'] = '';
		params['4'] = params['5'] = params['6'] = params['7'] = '';

		 if( 
			event == 'ViewContent'
		|| event == 'AddToCart'
		|| event == 'AddToWishlist'
		|| event == 'InitiateCheckout'
		|| event == 'Purchase'
		|| event == 'Lead'
		|| event == 'CompleteRegistration' ){
			
			var name = 'content_name';
			var example = "'Really Fast Running Shoes'";
			var info = "Name of the page/product";
			params[0] = { name:name, example:example, info:info}		
		} 


		if( 
			event == 'Search'
		|| event == 'AddToWishlist'
		|| event == 'InitiateCheckout'
		|| event == 'AddPaymentInfo'
		|| event == 'Lead' ){
			
			var name = 'content_category';
			var example = "'Apparel & Accessories > Shoes'";
			var info = "Category of the page/product.";
			params[1] = { name:name, example:example, info:info}
			
		} 

		if( 
			event == 'ViewContent'
		|| event ==  'Search'
		|| event ==  'AddToCart'
		|| event ==  'AddToWishlist'
		|| event ==  'InitiateCheckout'
		|| event ==  'AddPaymentInfo'
		|| event ==  'Purchase' ){
			
			var name = 'content_ids';
			var example = "['1234']";
			var info = "Product ids/SKUs associated with the event.";
			params[2] = { name:name, example:example, info:info}

		} 



		if( 
			event == 'ViewContent'
		|| event == 'AddToCart'
		|| event == 'InitiateCheckout'
		|| event == 'Purchase' ){
			
			var name = 'content_type';
			var example = "'product' or 'product_group'";
			var info = "The type of content_ids.";
			params[3] = { name:name, example:example, info:info}
		} 

	

		 if( event == 'Search' ) {
		
			var name = 'search_string';
			var example = "'Shoes'";
			var info = "The string entered by the user for the search.";
			params[4] = { name:name, example:example, info:info}
		
		} 

		if( 
			event == 'Purchase' 
			|| event == 'InitiateCheckout'  ){
		
			var name = 'num_items';
			var example = "'3'";
			var info = "The number of items in the cart.";
			params[5] = { name:name, example:example, info:info}
		} 

		 if( event == 'Purchase' ){
		
			var name = 'order_id';
			var example = "19";
			var info = "The unique order id of the successful purchase.";
			params[6] = { name:name, example:example, info:info}
		} 

		 if( event == 'CompleteRegistration' ){
		
			var name = 'status';
			var example = "completed";
			var info = "The status of the registration.";
			params[7] = { name:name, example:example, info:info}
		}

	return params;
}



}); /* Dom Loaded */