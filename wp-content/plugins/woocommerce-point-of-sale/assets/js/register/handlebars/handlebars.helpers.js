Handlebars.registerHelper('displayProductItemTitle', function() {
	var cart_item_data = this.cart_item_data;
	var product        = this.cart_item_data.data;
	var v_data         = this.cart_item_data.v_data;
	var variation_id   = this.cart_item_data.variation_id;
	var variation_id   = this.cart_item_data.variation_id;
	var title          = product.title;

    if( wc_pos_params.user_can_edit_product == true && product.id != pos_custom_product.id ){
    	var edit_link =  (wc_pos_params.edit_link).replace('{{post_id}}', product.id);
      	title = '<a href="'+edit_link+'" class="product_title" target="_blank" >'+product.title+'</a>';
    }else{
    	title = '<span class="product_title" >'+product.title+'</a>';
    }
    
    if(variation_id > 0 && v_data){
    	if(v_data.sku != ''){
	      title = v_data.sku + " &ndash; " + title;
	    }
    }else{
	    if(product.sku != ''){
	      title = product.sku + " &ndash; " + title;
	    }
	}

    return new Handlebars.SafeString(title);
});

Handlebars.registerHelper('displayOrderTitle', function() {
	var title           = '';
	var customer        = this.customer;
	var order_number    = this.order_number;
	var billing_address = this.billing_address;
    if( wc_pos_params.user_can_edit_order == true ){
    	var edit_link =  (wc_pos_params.edit_link).replace('{{post_id}}', this.id);
    	title += '<a class="row-title" href="'+edit_link+'" target="_blank">';
	        title += '<strong>#'+order_number+'</strong>';
	    title += '</a>';
	    title += ' by ';
	    if(customer.id > 0 ){
	        title += '<a href="user-edit.php?user_id='+customer.id+'"  target="_blank">';
	        if(customer.first_name != '' || customer.last_name != ''){
	        	title += customer.first_name + ' ' + customer.last_name;
	        }else if( typeof customer.username != 'undefined' && customer.username != ''){
	        	title += customer.username;	        	
	        }
	        title += '</a>';
	    }else if( billing_address.first_name != '' || billing_address.last_name != ''){
	        title += billing_address.first_name + ' ' + billing_address.last_name;
	    }else{
        	title += 'Walk-in Customer';
        }
	}else{
        title += '<strong>#'+order_number+'</strong>';
	    title += ' by ';
	    if(customer.id > 0){
	        if(customer.first_name != '' || customer.last_name != ''){
	        	title += customer.first_name + ' ' + customer.last_name;
	        }else if( typeof customer.username != 'undefined' && customer.username != ''){
	        	title += customer.username;	        	
	        }
	    }else if( billing_address.first_name != '' || billing_address.last_name != ''){
	        title += billing_address.first_name + ' ' + billing_address.last_name;
	    }else{
        	title += 'Walk-in Customer';
        }
	}

    if( billing_address.email != '' ){
        title += '<small class="meta email">';
            title += '<a href="mailto:'+billing_address.email+'">';
                title += billing_address.email;
            title += '</a>';
        title += '</small>';
    }

    return new Handlebars.SafeString(title);
});

Handlebars.registerHelper('displayProductItemImage', function() {
	var cart_item_data = this.cart_item_data;
	var product        = this.cart_item_data.data;
	var v_data         = this.cart_item_data.v_data;
	var variation_id   = this.cart_item_data.variation_id;

	if( wc_pos_params.image_size == 'thumbnail' && typeof product.thumbnail_src != 'undefined'){
		var string = '<img width="90" height="90" src="'+product.thumbnail_src+'" class="attachment-shop_thumbnail wp-post-image">';
	}
	else if( product.featured_src !== false ){
		var string = '<img width="90" height="90" src="'+product.featured_src+'" class="attachment-shop_thumbnail wp-post-image">';
	}else{
		var string = '<img width="90" height="90" src="'+wc_pos_params.def_img+'" class="attachment-shop_thumbnail wp-post-image">';		
	}
	
	if(variation_id > 0 && v_data ){
		if( wc_pos_params.image_size == 'thumbnail' && typeof v_data.thumbnail_src != 'undefined'){
			string = '<img width="90" height="90" src="'+v_data.thumbnail_src+'" class="attachment-shop_thumbnail wp-post-image">';
		}
		else if( v_data.image.length > 0 ){
			string = '<img width="90" height="90" src="'+v_data.image[0].src+'" class="attachment-shop_thumbnail wp-post-image">';
		}
	}	
    return new Handlebars.SafeString(string);
});

Handlebars.registerHelper('displayProductItemMeta', function() {
	var cart_item_data = this.cart_item_data;
	var product        = this.cart_item_data.data;
	var v_data         = this.cart_item_data.v_data;
	var variation      = this.cart_item_data.variation;
	var variation_id   = this.cart_item_data.variation_id;
	var string = '';

	if(variation_id > 0 && v_data){
		
		var stock_quantity = parseInt(v_data.stock_quantity);
		
		if(wc_pos_params.show_stock == 'yes' && !isNaN(stock_quantity) ){
			string += '<span class="register_stock_indicator"><b>'+v_data.stock_quantity+'</b> In Stock </span>';
		}
		if( v_data.on_sale === true ){
			regular_price = accountingPOS(v_data.regular_price, 'formatMoney');
			string += '<span class="register_sale_indicator"><del>'+regular_price+'<del></span>';
	    }
	    var meta = '';
	    
	    for(var name in variation) { 
		   if (variation.hasOwnProperty(name)) {
		       var attr = variation[name];
		       meta += '<li><span class="meta_label">'+name+'</span><span class="meta_value">'+attr+'</span></li>';
		   }
		}
	    if(meta != ''){
	    	string += '<ul class="display_meta">'+meta+'</ul>';
	    }

	}else{
		var stock_quantity = parseInt(product.stock_quantity);
		
		if(wc_pos_params.show_stock == 'yes' && !isNaN(stock_quantity) ){
			string += '<span class="register_stock_indicator"><b>'+product.stock_quantity+'</b> In Stock </span>';
		}
		if( product.on_sale === true ){
			regular_price = accountingPOS(product.regular_price, 'formatMoney');
			string += '<span class="register_sale_indicator"><del>'+regular_price+'<del></span>';
	    }
	    var meta = '';
	    
	    for(var name in variation) { 
		   if (variation.hasOwnProperty(name)) {
		       var attr = variation[name];
		       meta += '<li><span class="meta_label">'+name+'</span><span class="meta_value">'+attr+'</span></li>';
		   }
		}
	    if(meta != ''){
	    	string += '<ul class="display_meta">'+meta+'</ul>';
	    }
	}
    return new Handlebars.SafeString(string);
});
Handlebars.registerHelper('missingAttributesOptions', function() {
	
	var string = '';
	[].forEach.call(this.options, function(val) {
		var slug = val.slug;
		var name = val.name;
		string += '<option value="'+slug+'">'+name+'</option>';				  
	});
    return new Handlebars.SafeString(string);
});
Handlebars.registerHelper('getCountItems', function() {
	
	var count = sizeof(this.line_items);
	if( count == 1 ){
		count += ' ' + pos_i18n[25][0];
	}else{
		count += ' ' + pos_i18n[25][1];
	}
    return new Handlebars.SafeString(count);
});

Handlebars.registerHelper('order_items_list', function() {
	var html = '';
	[].forEach.call(this.line_items, function(val) {
	 	html += '<tr>';
		 	html += '<td class="qty">'+val.quantity+'</td>';
		 	html += '<td class="name">';
		 	html += val.sku != '' ? val.sku + ' - ' : '';
				if( wc_pos_params.user_can_edit_product == true ){
			    	var edit_link =  (wc_pos_params.edit_link).replace('{{post_id}}', val.product_id);
			      	html += '<a title="'+val.name+'" href="'+edit_link+'" target="_blank" >'+val.name+'</a>';
			    }else{
			    	html += val.name;
			    }

			    if(sizeof(val.meta) > 0){
			    	var dat_tip = [];
			    	[].forEach.call(val.meta, function(meta) {
		    			dat_tip.push(meta.label + ': ' + meta.value );
			    	});
			    	
			    	html += '<a data-tip="' + ( dat_tip.join(', ') ) + '" class="tips" href="#">[?]</a>';
			    }			    

		 	html += '</td>';
	 	html += '</tr>';
	});
	return new Handlebars.SafeString(html);
});