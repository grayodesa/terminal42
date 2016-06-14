<div class="wrap">
	<h2>PixelYourSite: Manage your Facebook Pixel</h2>	
	
	<div class="facebook-pixel-wrap" id="facebook-pixel-wrap">
		<div class="facebook-pixel-body">
				<ul class="facebook-pixel-menu nav-tab-wrapper">
					<li id="facebook-pixel-menu-1" class="nav-tab nav-tab-active selected">Facebook Pixel</li>
					<li id="facebook-pixel-menu-2" class="nav-tab">On Post/Page Events</li>
					<li id="facebook-pixel-menu-3" class="nav-tab">Dynamic Events</li>
					<li id="facebook-pixel-menu-4" class="nav-tab">WooCommerce Setup</li>
					<li id="facebook-pixel-menu-5" class="nav-tab">PayPal Setup</li>
				</ul>
			
			
			<div class="facebook-pixel-content">
			<form action="" method="post" name="facebook-pixel-form" id="facebook-pixel-form">

				<?php  wp_nonce_field( 'woofp-nonce-action', 'woofpnonce' );?>

				<div id="facebook-pixel-panel-1" class="facebook-pixel-panel">

					<table class="form-table">
						<tbody>
							<tr>
								<td colspan="2"><?php echo woofp_admin_notices(true); ?></td>
							</tr>
							<tr>
								<th scope="row">&nbsp;</th>
								<td><p><b class="woofpimportant">Important:</b> <a href="http://www.pixelyoursite.com/facebook-pixel-plugin-help" target="_blank">Read this before starting.</a></p></td>
							</tr>
							
							
							<tr>
								<th scope="row">Your Pixel ID:</th>
								<td>
									<input type="text" name="facebookpixel[ID]" value="<?php echo woofp_get_option('facebookpixel', 'ID'); ?>">
									<p class="description">Enter your Facebook Pixel ID here.</p>
								</td>
							</tr>
							<tr>
								<th scope="row">&nbsp;</th>
								<td>
									<input type="checkbox" value="1" id="facebookpixel-activate" name="facebookpixel[activate]" <?php  checked( '1', woofp_get_option('facebookpixel', 'activate'), true ); ?> />Activate Plugin General Settings.
									<p class="description">Check this to enable Facebook Pixel.</p>
								</td>
							</tr>

							<tr>
								<th scope="row">&nbsp;</th>
								<td><input type="button" class="button button-primary woofp-savesettings" value="Save Settings" /></td>
							</tr>
							
										<tr>
								<th scope="row">&nbsp;</th>
								<td style="color:#333;">
									<h2 style="font-weight: 400;color:#333;">Key Readings</h2>
									
									<p  style="font-weight: 400; font-size:12px; color:#333;">
									PixelYourSite is not just a simple plugin, we try to connect with our users and offer them the best possible information.
									<br><b>Here are some of the most appreciated articles from our Learning Section:</b></p>
									<br>
									<a href="http://www.pixelyoursite.com/facebook-pixel-helper-errors" target="_blank">How to check your FB Pixel for errors</a>
								<br><br>
									<a href="http://www.pixelyoursite.com/facebook-ads-reports-optimisation" target="_blank">How to create Custom Reports to better understand conversion</a>
								<br><br>
									[Important]: <a href="http://www.pixelyoursite.com/custom-audiences-from-events" target="_blank">How to Create Custom Audiences based on Events on your website</a>

								
								</td>
							</tr>

						</tbody>	
					</table>

				</div><!-- panel 1 -->


				<?php 
					//if not active hide standard event url option
					$hide_standardevent = 'show-standardevent '; 
					if( woofp_get_option('standardevent', 'activate') != 1 ) { $hide_standardevent .= ' hide-standardevent'; } 
				?>
							
				<div id="facebook-pixel-panel-2" class="facebook-pixel-panel ">
					<table class="form-table">
						<tbody>

							<tr>
								<td colspan="2"><?php echo woofp_admin_notices(true); ?></td>
							</tr>

							<tr>
								<th scope="row">&nbsp;</th>
								<td><p><b class="woofpimportant">Important:</b> <a href="http://www.pixelyoursite.com/facebook-pixel-plugin-help" target="_blank">Read this before starting.</a></p></td>
							</tr>
							

							<tr class=" <?php echo $hide_standardevent; ?>">
								<th scope="row">&nbsp;</th>
								<td><h3 style="float:left;" class="woofpimportant">Add Standard or Custom Events on Page/Post <span class="woofp-events-help"><b class="woofp-help-icon">?</b><?php woofp_event_help(); ?></span></h3></td>
							</tr>

							<?php  
								$code = '';
								if( !empty( $standardevent['pageurl'] ) ) {

									
									foreach ( $standardevent['pageurl'] as $key => $link) {

										$pageurl 	=  isset( $standardevent['pageurl'][$key] ) ? $standardevent['pageurl'][$key] : '';
										$cartvalue 	=  isset( $standardevent['value'][$key] ) ? $standardevent['value'][$key] : '';
										$currency 	=  isset( $standardevent['currency'][$key] ) ? $standardevent['currency'][$key] : '';
										$eventtype 	=  isset( $standardevent['eventtype'][$key] ) ? $standardevent['eventtype'][$key] : '';
										$code 		=  isset( $standardevent['code'][$key] ) ? $standardevent['code'][$key] : '';

							?>

							<tr class="woofp-pageurl-tr  <?php echo $hide_standardevent; ?>">
								<th scope="row">Page( Full or Partial URL)</th>
								<td>
									<p>
										<input type="text" name="standardevent[pageurl][]" value="<?php echo $pageurl; ?>" class="woofp-input-pageurl" />
									</p>
									<p class="description">This Event will trigger on any URL that contains this string..<br>
									If you add * at the end of the URL string it will match all URLs starting with the URL string.
									</p>
									
									<p>
									<b class="event_param">Event Type: </b>
									<select name="standardevent[eventtype][]" class="woofp-input-event">
										<option value="">- Select Event -</option>
										<?php echo woofp_event_types($eventtype); ?>
									</select>
									</p>
									
									<p class="standarevent-value">
										<b class="event_param">Value: </b><input type="text" name="standardevent[value][]" value="<?php echo $cartvalue; ?>" class=" woofp-input-value" />
										<span class="woofp-input-desc">&nbsp;&nbsp;*Mandatory for purchase event only.</span>
									</p>

									<p class="standarevent-currency">	
										<b class="event_param">Currency: </b><select name="standardevent[currency][]"  class=" woofp-input-currency">
										<option value="">Select Currency</option>
										<?php echo woofp_currency_options($currency); ?>
										</select>
											<span class="woofp-input-desc">&nbsp;&nbsp;*Mandatory for purchase event only.</span>

									</p>

									<div class="standardevent-params">
										<?php 
											$woofp_vars = woofp_vars();
											$standardevent_params = woofp_standardevent_extented($eventtype);
											foreach ($standardevent_params as $k => $param) {
											$parame_name = $param['name'];
											
											unset($woofp_vars[$parame_name]);

											$param_value = ( isset( $standardevent[$parame_name][$key] ) ? $standardevent[$parame_name][$key] : '' );	
											
											?>
									<div class="event_param_wrap">
									<?php if( $parame_name == 'content_type') { ?>
									<b class="event_param"><?php echo $parame_name; ?>: </b><select name="standardevent[<?php echo $parame_name; ?>][]" >
										<option <?php selected( 'product', $param_value, true ); ?>value="product">product</option>
										<option <?php selected( 'product_group', $param_value, true ); ?>value="product_group">product_group</option>
									</select>
									<?php } else {?>
									<b class="event_param"><?php echo $parame_name; ?>: </b><input type="text" name="standardevent[<?php echo $parame_name; ?>][]" value="<?php echo stripslashes($param_value); ?>" />
									<?php } ?>
									<p class="description"><?php echo $param['info'] . ' i.e ' . $param['example']  ?></p>
									</div>

											<?php
											}
										 ?>
									<?php foreach ($woofp_vars as $woofp_vars_key => $woofp_vars_value) {?>
										<input type="hidden" name="standardevent[<?php echo $woofp_vars_value; ?>][]" value="" style="display:none;"  />
									<?php } ?>	
									</div>
									<h4>OR add event code here (Advance users only):</h4>
									<textarea style="width:35em;height:180px;" name="standardevent[code][]"><?php echo stripslashes($code); ?></textarea>
									<div class="code_important">
										<p class="description"><span class="woofp-asterik">*</span>The code inside the event field will overwrite and will have priority over any other data for that event.</p>
										<p class="description"><span class="woofp-asterik">*</span>The code inserted in the field MUST be complete, including fbq('track', 'AddToCart', { …… });</p>
									</div>
									
								</td>
							</tr>

							<?php

									} 
								} else {  
								
								?>

								<tr class="woofp-pageurl-tr  <?php echo $hide_standardevent; ?>">
								<th scope="row">Page(URL or Partial URL)</th>
								<td>
									
									<p><input type="text" name="standardevent[pageurl][]" value="" class="woofp-input-pageurl" /></p>
									<p class="description">
									This Event will trigger on any URL that contains this string.<br>
									If you add * at the end of the URL string it will match all URLs starting with the URL string.
									</p>
									
									<p>
										<select name="standardevent[eventtype][]" class="woofp-input-event">
											<option value="">- Select Event -</option>
											<?php echo woofp_event_types(''); ?>
										</select>
									</p>

									<p class="standarevent-value">
										<b class="event_param">Value: </b><input type="text" name="standardevent[value][]" value="" class=" woofp-input-value" />
									</p>
									<p class="standarevent-currency">	
										<b class="event_param">Currency: </b><select name="standardevent[currency][]"  class=" woofp-input-currency">
										<option value="">Select Currency</option>
										<?php echo woofp_currency_options(''); ?>
										</select>
										<span class="woofp-input-desc">*Mandatory for purchase event only.</span>
									</p>
									<div class="standardevent-params"></div>
									<h4>OR add event code here (Advance users only):</h4>
									<textarea style="width:35em;height:180px;" name="standardevent[code][]"></textarea>
									<div class="code_important">
										<p class="description"><span class="woofp-asterik">*</span>The code inside the event field will overwrite and will have priority over any other data for that event.</p>
										<p class="description"><span class="woofp-asterik">*</span>The code inserted in the field MUST be complete, including fbq('track', 'EventName', { …… });</p>
									</div>
									
								</td>
							</tr>

							<?php 
							}
							 ?>
							<tr class="<?php echo $hide_standardevent; ?>">
								<th scope="row">&nbsp;</th>
								<td><input type="button" class="button button-secondary woofp-addevent" value="Add More Events" /></td>
							</tr>

							<tr>
								<th scope="row">&nbsp;</th>
								<td>
									<input type="checkbox" value="1" id="standardevent-activate" name="standardevent[activate]" <?php  checked( '1', woofp_get_option('standardevent', 'activate'), true ); ?>  />Activate Standard Event Setup.
									<p class="description">Check this to enable Standard Event Setup.</p>
								</td>
							</tr>

							<tr>
								<th scope="row">&nbsp;</th>
								<td><input type="button" class="button button-primary woofp-savesettings" value="Save Standard Event Setup Settings" /></td>
							</tr>

						</tbody>	
					</table>
				</div><!-- panel 2 -->

				
				<div id="facebook-pixel-panel-3" class="facebook-pixel-panel ">
					<table class="form-table">
						<tbody>
							
							<tr>
								<td colspan="2"><?php echo woofp_admin_notices(true); ?></td>
							</tr>

							<tr>
								<th scope="row">&nbsp;</th>
								<td style="color:#333;">
									<h2 style="font-weight: 400;color:#333;">Start using Dynamic Events with the PRO version of the plugin</h2>
									
									<p  style="font-weight: 400; font-size:12px; color:#333;">
										With Dynamic Events you can optimize your ads for actions on your website, like filing up forms (Contact Form 7 supported), newsletter sign ups, <br>or any other click on links and buttons. Better ad optimization, better custom audience targeting, better conversion tracking. <br><b>If you are not using Facebook Dynamic Events you are literally leaving money on the table</b> 
									</p>
									<br>
									<b>Super Offer Ending Soon: </b><a href="http://www.pixelyoursite.com/super-offer?utm_source=woordpress-tab-fb-dynamic-events&utm_medium=tab-fb-dynamic-events&utm_campaign=tab-fb-dynamic-events" target="_blank">Update now and start to benefit from Dynamic Events</a>
								<br><br>
									Or Learn more about <a href="http://www.pixelyoursite.com/facebook-pixel-dynamic-events" target="_blank">How to use Dynamic Events - Complete guide</a>

								
								</td>
							</tr>

							<tr>
								<th scope="row">Process links</th>
								<td>
									<p><input type="checkbox" value="1" id="custom_event-content" name="custom_event[enable_post_contents]" 
									disabled="disabled"  />
									Process links in Post Content <code>( the_content(); hook )</code>
									</p>

									
									<p><input type="checkbox" value="1" id="custom_event-widget" name="custom_event[enable_widget_text]" 
									disabled="disabled"  />
										Process links in Widgets Text <code>( widget_text(); hook )</code>
									</p>
									
								</td>
								
							</tr>

							
								<tr class="woofp-hreflink-tr  ">
								<th scope="row" style="padding-top: 60px;">Full or Partial URL</th>
								<td>
									

									<p>
										<b class="event_param">Trigger Event On:</b>
										<select name="custom_event[trigger_type]">
											<option value="url">URL</option>
											<option value="css_selector">CSS Selector</option>
										</select>
									</p>

									<p class="woofp-custom-event-inputvalue">
										<input type="text" name="custom_event[hreflink][]" value="" class="woofp-input-hreflink" disabled="disabled" />
										<input type="text" name="custom_event[selector][]" value="" class="woofp-customevent-selector woofp-input-hreflink" style="display:none;"/>
									</p>
									<p class="description woofp-custom-event-valuedesc">
										<span class="woofp-custom-event-urldesc">
										This Event will trigger on any URL that contains the partial string.<br>
										If you add <b class="woofp-wildcard">*</b> at the end of the Full URL it will match all URLs starting with the URL.
										</span>
										<span class="woofp-custom-event-selectordesc" style="display:none;">
										This Event will trigger on the entered CSS Selector.<br>
										Please read about CSS Selector <a href="#">here</a>.
										</span>
									</p>
								
									
									<p>
										<b class="event_param">Event Type: </b>
										<select name="custom_event[eventtype][]" class="woofp-input-customevent" disabled="disabled">
											<option value="">- Select Event -</option>
											
										</select>
									</p>

									<p class="standarevent-value">
										<b class="event_param">Value: </b><input type="text" name="custom_event[value][]" value="" disabled="disabled" class=" woofp-input-value" />
									</p>
									
									<p class="standarevent-currency">	
										<b class="event_param">Currency: </b><select name="custom_event[currency][]"  class=" woofp-input-currency" disabled="disabled">
										<option value="">Select Currency</option>
										</select>

										<span class="woofp-input-desc">*Mandatory for purchase event only.</span>
									</p>
									<div class="custom_event-params"></div>
									<h4>OR add event code here (Advance users only):</h4>
									<textarea style="width:35em;height:180px;" name="custom_event[code][]" disabled="disabled"></textarea>
									
								</td>
							</tr>

							
							
							<tr>
								<th scope="row">&nbsp;</th>
								<td>
									<input type="checkbox" value="1" id="custom_event-activate" name="custom_event[activate]"  disabled="disabled"  />Activate Custom Event Setup.
									<p class="description">Check this to enable Custom Event Setup.</p>
								</td>
							</tr>

							<tr>
								<th scope="row">&nbsp;</th>
								<td><input type="button" class="button button-primary woofp-savesettings woopf-custom-event-setup" value="Save Custom Event Setup Settings" disabled="disabled" /></td>
							</tr>

						</tbody>	
					</table>
				</div><!-- panel 3 -->



				<div id="facebook-pixel-panel-4" class="facebook-pixel-panel">
				<?php if( woofp_is_woocommerce() ){ ?>
					<table class="form-table">
						<tbody>

							<tr>
								<td colspan="2"><?php echo woofp_admin_notices(true); ?></td>
							</tr>

							<tr>
								<th scope="row">&nbsp;</th>
								<td><p><b class="woofpimportant">Important:</b> <a href="http://www.pixelyoursite.com/facebook-pixel-plugin-help" target="_blank">Read this before starting.</a></p></td>
							</tr>
							
							<tr>
								<th scope="row">&nbsp;</th>
								<td><p><b class="woofpimportant">WooCommerce Help:</b> <a href="http://www.pixelyoursite.com/facebook-pixel-plugin-woocommerce" target="_blank">Read this before activating WooCommerce Setup.</a></p></td>
							</tr>

							<tr>
								<th scope="row">&nbsp;</th>
								<td><h3 style="float:left;">Activate WooCommerce events</h3>
								</td>
							</tr>
							<?php foreach ( woofp_woocommerce_events() as $key => $woocommerce_event) { ?>
								
							
							<tr>
								<th scope="row"><?php echo $woocommerce_event['title']; ?></th>
								<td><input type="checkbox" name="woocommerce[events][<?php echo $woocommerce_event['event']; ?>]" value="1" <?php woofp_is_woocommerce_event($woocommerce_event['event'], $woocommerce_settings); ?> /> <?php echo $woocommerce_event['label']; ?></td>
							</tr>

							<?php } ?>
							
							
							<tr>
								<th scope="row">&nbsp;</th>
								<td style="color:#333;">
									<h2 style="font-weight: 400;color:#333;">Update to the PRO to start tracking conversion value and enable Facebook Dynamic Ads for WooCommerce</h2>
									
									<p  style="font-weight: 400; font-size:12px; color:#333;">
										The PRO version will pull all the parameters for your WooCommerce events and will enable you to use<br> Facebook Dynamic Ads
									</p>
									<br>
									<b style="font-weight: 400;color:#333;">Track conversion value & Facebook Dynamic Ads for WooCommerce</b>
								</td>
							</tr>

							<tr>
								<th scope="row">&nbsp;</th>
								<td>
									<input type="checkbox" value="1" name="woocommerce_dynamic[ViewContent]"  disabled="disabled" /><span class="disabled">Activate ViewContent event for Facebook Dynamic Ads.</span>
									<a href="http://www.pixelyoursite.com/facebook-pixel-plugin-pro-woo?utm_source=woordpress-tab3-fb-dynamic&utm_medium=tab3-fb-dynamic&utm_campaign=tab3-fb-dynamic" target="_blank">Update to the Pro Version</a>
									<p class="description">This will enable value, currency, content_name, content_category, content_ids, num_items. <i>(any WooCommerce Page)</i></p>
								
								</td>
							</tr>

							<tr>
								<th scope="row">&nbsp;</th>
								<td>
									<input type="checkbox" value="1" name="woocommerce_dynamic[AddToCart]" disabled="disabled"  /><span class="disabled">Activate AddToCart event for Facebook Dynamic Ads.</span>
									<a href="http://www.pixelyoursite.com/facebook-pixel-plugin-pro-woo?utm_source=woordpress-tab3-fb-dynamic&utm_medium=tab3-fb-dynamic&utm_campaign=tab3-fb-dynamic" target="_blank">Update to the Pro Version</a>
									<p class="description">This will enable value, currency, content_name, content_category, content_ids, num_items. <i>(Cart Page)</i></p>
								</td>
							</tr>
							<tr>
								<th scope="row">&nbsp;</th>
								<td>
									<input type="checkbox" value="1" name="woocommerce_dynamic[intiatecheckout]" disabled="disabled" <?php  checked( '1', woofp_get_option('woocommerce', 'intiatecheckout'), true ); ?> /><span class="disabled">Activate InitiateCheckout for Facebook Dynamic Ads.</span>
									<a href="http://www.pixelyoursite.com/facebook-pixel-plugin-pro-woo?utm_source=woordpress-tab3-fb-dynamic&utm_medium=tab3-fb-dynamic&utm_campaign=tab3-fb-dynamic" target="_blank">Update to the Pro Version</a>
									<p class="description">This will enable value, currency, content_name, content_category, content_ids, num_items. </i>(Checkout Page)</i></p>
								</td>
							</tr>
							<tr>
								<th scope="row">&nbsp;</th>
								<td>
									<input type="checkbox" value="1" name="woocommerce_dynamic[purchase]" disabled="disabled"  /><span class="disabled">Activate Purchase event for Facebook Dynamic Ads.</span>
									<a href="http://www.pixelyoursite.com/facebook-pixel-plugin-pro-woo?utm_source=woordpress-tab3-fb-dynamic&utm_medium=tab3-fb-dynamic&utm_campaign=tab3-fb-dynamic" target="_blank">Update to the Pro Version</a>
									<p class="description">This will enable value, currency, content_name, content_category, content_ids, num_items. <i>(Order Recieved/Thank You Page)</i></p>
								</td>
							</tr>
							<tr>
								<th scope="row">&nbsp;</th>
								<td>
									<input type="checkbox" value="1" name="woocommerce_dynamic[ProductAddToCart]" disabled="disabled"  /><span class="disabled">Activate Product AddToCart event for Facebook Dynamic Ads.</span>
									<a href="http://www.pixelyoursite.com/facebook-pixel-plugin-pro-woo?utm_source=woordpress-tab3-fb-dynamic&utm_medium=tab3-fb-dynamic&utm_campaign=tab3-fb-dynamic" target="_blank">Update to the Pro Version</a>
									<p class="description">This will enable value, currency, content_name, content_category, content_ids, num_items. <i>((When Product is Added to Cart))</i></p>
								</td>
							</tr>

							<tr>
								<td colspan="2" style="padding:0;"><hr></td>
							</tr>

							<tr class="affiliate_row">
								<th scope="row">&nbsp;</th>
								<td>
									
									<h2 style="font-weight: 400;color:#333;">WooCommerce Affiliate Products Events</h2>
									<p  style="font-weight: 400; font-size:12px; color:#333;">It will add a dynamic event on WooCommerce affiliate products buttons</p>
									
									<p>
										<label>Affiliate button event:</label>
										<select name="woocommerce_affiliate[affiliate_event]" class="woofp-input-event" disabled="disabled"  >
											<?php echo woofp_event_types(''); ?>
										</select>
									</p>
									<span class="woofpbold woofp-or">OR</span>
									<p>
										<label>Name for custom event:</label>
										<input type="text" value="" name="woocommerce_affiliate[affiliate_custom]" disabled="disabled"  />
									</p>
									<p>
										<p class="description">*In the PRO version this event will have all the parameters values specific for the selected event.</p>
										<p class="description">*In the PRO version the custom event will have <i>value, currency, content_name, content_type, content_ids</i>.</p>
									</p>
									<br>
									<p>
										<input type="checkbox" value="" name="woocommerce_affiliate[affiliate_value]" disabled="disabled"  /><span class="woofpbold"> Event value = Product Price</span>
										If left unchecked the event will not pull product price as event value
									</p>
									<p>
										<label>Global value:</label><input type="text" value="" name="woocommerce_affiliate[affiliate_global]" disabled="disabled" />
										<p class="description"><span class="woofp-asterik">*</span>Set this if you want a unique global value every time the afiliate button is clicked</p>
									</p>
									<br>
									
									<br>
									<p>
										<input type="checkbox" value="1" name="woocommerce_affiliate[activate]" disabled="disabled"  /><span class="disabled"><span class="woofpbold">Activate WooCommerce Affiliate Products Events.</span></span>
										<a href="http://www.pixelyoursite.com/facebook-pixel-plugin-pro-woo?utm_source=woordpress-affiliate&utm_medium=affiliate-button&utm_campaign=affiliate-button" target="_blank"><span class="woofpbold">Update to the Pro Version</span></a>
									</p>
								</td>
							</tr>
							
							<tr>
								<td colspan="2" style="padding:0;"><hr></td>
							</tr>

							<tr>
								<th scope="row">&nbsp;</th>
								<td>
								<h2>Special Offer - Will end soon:</h2><a href="http://www.pixelyoursite.com/super-offer?utm_source=woordpress-3tab&utm_medium=special-offer&utm_campaign=wp-3t-special-offer" target="_blank">Click Here to get your HUGE discount now</a>
								</td>
							</tr>

							

							<tr>
								<th scope="row">&nbsp;</th>
								<td>
									<input type="checkbox" value="1" id="woocommerce-activate" name="woocommerce[activate]" <?php  checked( '1', woofp_get_option('woocommerce', 'activate'), true ); ?> /><b>Activate WooCommerce Setup.</b>
									<p class="description">Check this to enable WooCommerce Setup.</p>
								</td>
							</tr>
							
							<tr>
								<th scope="row">&nbsp;</th>
								<td><input type="button" class="button button-primary woofp-savesettings" value="Save WooCommerce Settings" /></td>
							</tr>
						</tbody>	
					</table>	

					<?php } else { ?>
					<div>
						<br>
						<p>Please install and activate <b>WooCommerce</b> to enable WooCommerce integration</p>
					</div>
					<?php } ?>
				</div><!-- panel 4 -->


				<div id="facebook-pixel-panel-5" class="facebook-pixel-panel">
				<?php if( woofp_is_woocommerce() ){ ?>
					<table class="form-table">
						<tbody>
							<tr>
								<td colspan="2"><?php echo woofp_admin_notices(true); ?></td>
							</tr>
							<tr class="paypal_row">
								<th scope="row">&nbsp;</th>
								<td>
									
									<h2 style="font-weight: 400;color:#333;">WooCommerce PayPal Standard Event</h2>
									<p  style="font-weight: 400; font-size:12px; color:#333;">It will add a dynamic event on WooCommerce PayPal Standard button</p>
									
									<p>
										<label>PayPal button event:</label>
										<select name="woocommerce_paypal[event_name]" class="woofp-input-event" disabled="disabled"  >
											<?php echo woofp_event_types('AddPaymentInfo'); ?>
										</select>
									</p>
									<span class="woofpbold woofp-or">OR</span>
									<p>
										<label>Name for custom event:</label>
										<input type="text" value="" name="woocommerce_paypal[event_custom]" disabled="disabled"  />
									</p>
									<p>
										<p class="description">*In the PRO version this event will have all the parameters values specific for the selected event.</p>
										<p class="description">*In the PRO version the custom event will have <i>value, currency, content_name, content_type, content_ids</i>.</p>
									</p>
									<br>
									<p>
										<input type="checkbox" value="" name="woocommerce_paypal[event_value]" disabled="disabled"  /><span class="woofpbold"> Event value = Product Price</span>
										If left unchecked the event will not pull product price as event value
									</p>
									<p>
										<label>Global value:</label><input type="text" value="" name="woocommerce_paypal[event_global]" disabled="disabled" />
										<p class="description"><span class="woofp-asterik">*</span>Set this if you want a unique global value every time the PayPal Payment button is clicked</p>
									</p>
									<br>
									
									<br>
									<p>
										<input type="checkbox" value="1" name="woocommerce_paypal[activate]" disabled="disabled"  /><span class="disabled"><span class="woofpbold">Activate WooCommerce PayPal Payment Button.</span></span>
										<a href="http://www.pixelyoursite.com/facebook-pixel-plugin-pro-woo?utm_source=woordpress-tab4-pixel-paypal&utm_medium=tab4-pixel-paypal&utm_campaign=tab4-pixel-paypal" target="_blank"><span class="woofpbold">Update to the Pro Version</span></a>
									</p>
								</td>
							</tr>

							<tr>
								<th scope="row">&nbsp;</th>
								<td><input type="button" class="button button-primary woofp-savesettings" value="Save PayPal Settings" disabled="disabled" /></td>
							</tr>
						</tbody>
					</table>

				<?php } else { ?>
					<div>
						<br>
						<p>Please install and activate <b>WooCommerce</b> to enable WooCommerce integration</p>
					</div>
				<?php } ?>
				</div><!-- panel 5 -->

			</form>
			</div>
		</div>
	</div>

</div>