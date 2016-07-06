<?php
/**
 * Add extra profile fields for users in admin.
 *
 * @author    Actuality Extensions
 * @package   WoocommercePointOfSale/Classes/profile
 * @category	Class
 * @since     0.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WC_Pos_Barcodes' ) ) :

/**
 * WC_Pos_Barcodes Class
 */
class WC_Pos_Barcodes {

	/**
	 * @var WC_Pos_Barcodes The single instance of the class
	 * @since 1.9
	 */
	protected static $_instance = null;

	/**
	 * Main WC_Pos_Barcodes Instance
	 *
	 * Ensures only one instance of WC_Pos_Barcodes is loaded or can be loaded.
	 *
	 * @since 1.9
	 * @static
	 * @return WC_Pos_Barcodes Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.9
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'woocommerce' ), '1.9' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.9
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'woocommerce' ), '1.9' );
	}

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		
	}
	public function display_single_barcode_page()
	{
		?>
		<div class="wrap">
			<h2><?php _e( 'Barcode', 'wc_point_of_sale' ); ?></h2>
			<?php echo $this->display_messages();?>
			<div id="lost-connection-notice" class="error hidden">
				<p><span class="spinner"></span> <?php _e( '<strong>Connection lost.</strong> Saving has been disabled until you&#8217;re reconnected.' ); ?>
				<span class="hide-if-no-sessionstorage"><?php _e( 'We&#8217;re backing up this post in your browser, just in case.' ); ?></span>
				</p>
			</div>
			<form action="" method="post" id="edit_wc_pos_barcode" onsubmit="return false;">
				<?php wp_nonce_field('wc_point_of_sale_edit_barcode'); ?>
				<div id="poststuff">
					<div id="post-body" class="metabox-holder columns-2">
						<div id="postbox-container-2" class="postbox-container">
							<div class="postbox ">
								<div class="inside">

									<table id="barcode_options">
										<tbody>
											<tr class="form-field form-required">
												<td valign="top" scope="row">
													<label for="product_id"><?php _e( 'Product', 'wc_point_of_sale' ); ?></label>
												</td>
												<td>
													<input type="hidden" id="product_id" name="product_id" class="wc-product-search" style="width: 400px;" data-placeholder="<?php _e( 'Search for a product&hellip;', 'woocommerce' ); ?>" />
													<div id="product_has_sku"></div>
												</td>
											</tr>
											<tr class="form-field form-required dafault_selection" <?php echo (isset($data['default_selection']) && !empty($data['default_selection']) && $data['default_selection'] ) ? '' : 'style="display: none;"';?>>
												<td valign="top" scope="row">
													<label for="variation"><?php _e( 'Variation', 'wc_point_of_sale' ); ?></label>
												</td>
												<td>
														<select name="variation" id="variation" class="wc-enhanced-select" >
															<option value="0" ><?php _e("Select variation", "wc_point_of_sale"); ?></option>;
														</select>
														<div id="variation_has_sku"></div>
												</td>
											</tr>
											<tr class="form-field form-required">
												<td valign="top" scope="row">
													<label for="number_of_labels"><?php _e( 'Number of Labels', 'wc_point_of_sale' ); ?></label>
												</td>
												<td>
														<input type="number" step="1" name="number_of_labels" id="number_of_labels">
												</td>
											</tr>
											<tr class="form-field form-required">
												<td valign="top" scope="row">
													<label for="label_type"><?php _e( 'Label Type', 'wc_point_of_sale' ); ?></label>
												</td>
												<td>
														<select id="label_type" name="label_type" class="wc-enhanced-select">
														 <option value="continuous_feed"><?php _e( 'Continuous Feed', 'wc_point_of_sale' ); ?></option>
														 <option value="a4"><?php _e( 'A4', 'wc_point_of_sale' ); ?></option>
														 <option value="letter"><?php _e( 'Letter', 'wc_point_of_sale' ); ?></option>
														 <option value="per_sheet_30"><?php _e( 'Avery 3 x 10 (Letter)', 'wc_point_of_sale' ); ?></option>
														</select>
												</td>
											</tr>
											<tr class="form-field form-required">
												<td valign="top" scope="row">
													<label><?php _e( 'Choose which fields to print', 'wc_point_of_sale' ); ?></label>
												</td>
												<td class="fields_print">
														<div><label><input type="checkbox" name="fields_print" id="field_price"><?php _e( 'Price', 'wc_point_of_sale' ); ?></label></div>
														<div><label><input type="checkbox" name="fields_print" id="field_name"><?php _e( 'Product Name', 'wc_point_of_sale' ); ?></label></div>
														<div><label><input type="checkbox" name="fields_print" id="field_barcode"  checked="checked"><?php _e( 'Barcode', 'wc_point_of_sale' ); ?></label></div>
														<div><label><input type="checkbox" name="fields_print" id="field_sku" checked="checked"><?php _e( 'SKU', 'wc_point_of_sale' ); ?></label></div>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
						<div id="postbox-container-1" class="postbox-container">
							<div class="postbox ">
								<h3 class="hndle">
									<label ><?php _e( 'Barcode Preview', 'wc_point_of_sale' ); ?></label>
								</h3>
								<div class="inside" id="barcode_preview">
									<div class="barcode_border">
										<img src="<?php echo  plugins_url( 'includes/lib/barcode/image.php?filetype=PNG&dpi=72&scale=2&rotation=0&font_family=Arial.ttf&font_size=12&thickness=30&start=NULL&code=BCGcode128&text=111111111' , WC_POS_FILE ) ?>" alt="">
										<div class="barcode_text"></div>
									</div>
								</div>
								<div id="major-publishing-actions">
									<div id="publishing-action">
										<span class="spinner"></span>
										<input type="button" value="Print" class="button button-primary button-large" id="print_barcode">
									</div>
									<div class="clear"></div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="clear"></div>
			</form>
		</div>
			<script>
			jQuery(document).ready(function($){
				var product_price = 0;
				jQuery("#product_id").change(function(){
					var selected_produst = $(this).val();
					if(selected_produst != ''){
						$('#edit_wc_pos_barcode').block({message: null, overlayCSS: {background: '#fff url(' + wc_pos_params.ajax_loader_url + ') no-repeat center', backgroundSize: '16px 16px', opacity: 0.6}});
						var data = {
	                action: 'wc_pos_search_variations_for_product_and_sku',
	                id_product: selected_produst,
	                security: '<?php echo wp_create_nonce("search_variations_for_product_and_sku"); ?>',
	            };
	          $.post(wc_pos_params.ajax_url, data, function(response) {
	          	option = '<option value="0" selected><?php _e("Select variation", "wc_point_of_sale"); ?></option>';
	          	response = response.trim();
	          	var data = $.parseJSON( response );
	          	if(data.variation != ''){
		          	var obj = data.variation;
		          	$.each(obj, function (i, val) {
					        option += '<option value="'+i+'" data-sku="'+val.sku+'">'+val['name']+'</option>';
					    	});
					    	$('.dafault_selection').show();
					    	$('#product_has_sku, #variation_has_sku').hide();
				    	}else{
				    		if(data.sku != ''){
									$('#barcode_preview img').attr('src', wc_pos_params.barcode_url+'&font_size=12&text='+data.sku);
			            $('#product_has_sku').text(data.sku).removeClass('wrong_sku').show();
			            $('#print_barcode').show();
				    		}else{
				    			$('#product_has_sku').text(wc_pos_params.product_no_sku).addClass('wrong_sku').show();
				    			$('#print_barcode').hide();
				    		}
				    		if(data.price != ''){
									product_price = data.price;
				    		}else{
				    			product_price = 0;
				    		}
				    		$('.dafault_selection').hide();
				    		$('#variation_has_sku').hide();
				    	}
	              $('#variation').html(option);
	              $('#edit_wc_pos_barcode').unblock();
	              check_fields_print();
	          });
					}
				});
				jQuery("#variation").change(function(){
					if($('#variation').val() == '0'){
						$('#variation_has_sku').hide();
						check_fields_print();
						$('#print_barcode').hide();
						return;
					}
					var sku = $(this).find('option:selected').attr('data-sku');
					if(sku != ''){
						if($('#field_sku').is(':checked'))
							$('#barcode_preview img').attr('src', wc_pos_params.barcode_url+'&font_size=12&text='+sku);
						else
							$('#barcode_preview img').attr('src', wc_pos_params.barcode_url+'&font_size=0&text='+sku);

						$('#variation_has_sku').text(sku).removeClass('wrong_sku').show();
						$('#print_barcode').show();
					}else{
						$('#variation_has_sku').text(wc_pos_params.variation_no_sku).addClass('wrong_sku').show();
						$('#print_barcode').hide();
					}
					$('#product_has_sku').hide();
					check_fields_print();
				});

				$('td.fields_print input').change(function(){
					check_fields_print();
				});
				function check_fields_print() {
					var fields = '';
					var sku   = '';
					var name   = '';
					var price  = '';
					if( $().select2 ){
						if($('#variation option').length > 1){
							if( $('#variation').val() != '0'){
								var title = $('#variation option:selected').text();
								var pos1 = title.indexOf('–')+2;
								title = title.substr(pos1,title.length);
								var pos2 = title.lastIndexOf('–')-1;
								name  = title.substr(0,pos2);
								pos2 = pos2+3;
								price = title.substr(pos2,title.length);
							}
						}else if($('#product_id').val() != ''){
							var data = $('#product_id').select2('data');
								var title = data.text;
								if( typeof title != 'undefined'){
									var pos1 = title.indexOf('&ndash;')+8;
									name  = title.substr(pos1,title.length);
									price = product_price;									
								}
						}
					}else{
						if($('#variation option').length > 1){
							if( $('#variation').val() != '0'){
								var title = $('#variation option:selected').text();
								var pos1 = title.indexOf('–')+2;
								title = title.substr(pos1,title.length);
								var pos2 = title.lastIndexOf('–')-1;
								name  = title.substr(0,pos2);
								pos2 = pos2+3;
								price = title.substr(pos2,title.length);
							}
						}else if($('#product_id').val() != ''){
								var title = $('#product_id option:selected').text();
								var pos1 = title.indexOf('–')+2;
								name  = title.substr(pos1,title.length);
								price = product_price;
						}						
					}

					if($('#field_barcode').is(':checked') ){
						$('#barcode_preview img').show();
					}else{
						var src = $('#barcode_preview img').attr('src');
						var object = parseURL(src);
						$('#barcode_preview img').hide();
						sku = object.searchObject.text;
					}
					if($('#field_sku').is(':checked') ){
						var src = $('#barcode_preview img').attr('src');
						src = src.replace('font_size=0', 'font_size=12');
						$('#barcode_preview img').attr('src', src);
					}else{
						var src = $('#barcode_preview img').attr('src');
						src = src.replace('font_size=12', 'font_size=0');
						$('#barcode_preview img').attr('src', src);
						sku = '';
					}
					fields += sku;
					if($('#field_name').is(':checked') ){
						if(fields != '') fields += '<br/>';
						fields += name;
					}
					if($('#field_price').is(':checked') ){
						if(fields != '') fields += '<br/>';
						fields += price;
					}

					$('#barcode_preview .barcode_text').html(fields).show();
				}

				$('#print_barcode').click(function () {
					var number = parseInt($('#number_of_labels').val());
					$('#printable_barcode').html('');
					for (var i = 0; i < number; i++) {
					   $('#printable_barcode').append($('#barcode_preview .barcode_border').clone());
					}
					$('#printable_barcode').append('<div class="clear"></div>');
					
					window.print();
				});
				$('#label_type').change(function () {
					var type = $('#label_type').val();					
					$('#printable_barcode').removeAttr('class').addClass(type);

				});
				$('body').append('<div id="printable_barcode"></div>');
				
				check_fields_print();
				function parseURL(url) {

					var parser = document.createElement('a'),
						searchObject = {},
						queries, split, i;

					// Let the browser do the work
					parser.href = url;

					// Convert query string to object
					queries = parser.search.replace(/^\?/, '').split('&');
					for( i = 0; i < queries.length; i++ ) {
						split = queries[i].split('=');
						searchObject[split[0]] = split[1];
					}

					return {
						protocol: parser.protocol,
						host: parser.host,
						hostname: parser.hostname,
						port: parser.port,
						pathname: parser.pathname,
						search: parser.search,
						searchObject: searchObject,
						hash: parser.hash
					};

				}

			});
		</script>
		<?php
	}

	function display_messages()
	{
		$i = 0;
		if(isset($_GET['message']) && !empty($_GET['message']) ) $i = $_GET['message'];
		$messages = array(
			 0 => '', // Unused. Messages start at index 1.
			 1 => '<div id="message" class="updated"><p>'.  __('Barcode Template created.') . '</p></div>',
			 2 => '<div id="message" class="updated"><p>'. __('Barcode Template updated.') . '</p></div>',
		);
		return $messages[$i];
	}
	public function save_barcode()
	{
	}

}

endif;