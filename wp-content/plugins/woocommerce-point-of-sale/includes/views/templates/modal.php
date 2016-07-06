<?php
/**
 * Template for the modals
 */
?>

<script type="text/template" id="tmpl-missing-attributes">
	<table>
		<tbody>
			{{#each attr}}
			<tr>
				<td>{{name}}</td>
				<td>
					<select data-label="{{name}}" data-taxonomy="{{slug}}" class="attribute_{{slug}}" >
						<option value=""><?php _e('Choose an option', 'wc_point_of_sale'); ?></option>
						{{missingAttributesOptions}}
					</select>
				</td>
			</tr>
  			{{/each}}
		</tbody>
	</table>
</script>

<script type="text/template" id="tmpl-add-custom-item-meta">
    {{#each this}}
	<tr>
        <td class="meta_label"><input type="text" class="meta_label_value" value="{{meta_key}}"></td>
        <td class="meta_attribute"><input type="text"  class="meta_attribute_value" value="{{meta_v}}"></td>
        <td class="remove_meta"><span href="#" data-tip="<?php _e('Remove', 'wc_point_of_sale'); ?>" class="remove_custom_product_meta tips"></span></td>
    </tr>
    {{/each}}
</script>

<script type="text/template" id="tmpl-form-add-customer">
    <input type="hidden" id="customer_details_id" value="{{id}}">    
    <div id="pos_billing_details" class="pos-customer-details-tab">
        <div class="woocommerce-billing-fields">
        	<?php 
            $checkout = new WC_Checkout();
            unset($checkout->checkout_fields['order']['order_comments']);
            if( isset($checkout->checkout_fields['billing']) ){
            	foreach ( $checkout->checkout_fields['billing'] as $key => $field ) :
                    $value = str_replace('billing_', 'billing_address.', $key);
                    if( strpos($value, 'billing_address.') === false && strpos($value, 'billing_address.') !== 0 ){
                        $value = 'billing_address.'.$key;
                    }
            		woocommerce_form_field( $key, $field, '{{'.$value.'}}' );
        		endforeach; 
            }
    		?>
        </div>        
    </div>
    <div id="pos_shipping_details" class="pos-customer-details-tab">
        <div class="woocommerce-shipping-fields">
            <div class="shipping_address">
                    <p class="billing-same-as-shipping">
                        <a id="billing-same-as-shipping" class="tips billing-same-as-shipping" data-tip="<?php _e( 'Copy from billing', 'woocommerce' ); ?>"></a>
                    </p>
                    <?php 
                    if( isset($checkout->checkout_fields['shipping'])){
                    	foreach ( $checkout->checkout_fields['shipping'] as $key => $field ) :
                    		$value = str_replace('shipping_', 'shipping_address.', $key);
                    		woocommerce_form_field( $key, $field, '{{'.$value.'}}' );
                		endforeach; 
                    }
            		?>
                </div>
        </div>
    </div>
    <div id="pos_additional_fields" class="pos-customer-details-tab">
        <div class="woocommerce-additional-fields">

            <?php 
            if( isset($checkout->checkout_fields['order']) ){
                foreach ( $checkout->checkout_fields['order'] as $key => $field ) : ?>
    
                    <?php woocommerce_form_field( $key, $field, '{{additional_fields.'.$key.'}}' ); ?>

                <?php endforeach; 

            }?>

        </div>
    </div>
    <div id="pos_order_fields" class="pos-customer-details-tab">
        <div class="woocommerce-additional-fields">

            <?php 
            if( isset($checkout->checkout_fields['pos_custom_order']) ){
                foreach ( $checkout->checkout_fields['pos_custom_order'] as $key => $field ) {
                    if( $field['type'] == 'checkbox'){
                        $option_count = 0;
                        echo '<p class="form-row">';
                        echo '<label>'.$field['label'].'</label>';
                        foreach ( $field['options'] as $value => $label ) {
                            $input = sprintf( '<input type="%s" name="%s" id="%s-%s" value="%s" />', esc_attr( $field['type'] ), $key . '[]', $field['id'], $option_count, esc_attr( $value ) );
                            printf( '<label for="%1$s-%2$s">%3$s%4$s</label>', $field['id'], $option_count, $input, esc_html( $label ) );
                            $option_count++;
                        }
                        echo '</p>';

                    }else{
                        woocommerce_form_field( $key, $field, '{{custom_order_fields.'.$field['id'].'}}' );                         
                    }
                }
            }?>

        </div>
    </div>
    <div id="pos_custom_fields" class="pos-customer-details-tab">
        <div class="woocommerce-custom-fields">

            <?php if( isset($checkout->checkout_fields['pos_acf']) ) : ?>
                <?php foreach ( $checkout->checkout_fields['pos_acf'] as $group ) : ?>
                    
                    <h3><?php echo $group['title']; ?></h3>

                    <?php
                    foreach ( $group['fields'] as $key => $field ) {                        
                        switch ($field['type']) {
                            case 'checkbox':
                                $option_count = 0;
                                echo '<p class="form-row">';
                                echo '<label>'.$field['label'].'</label>';
                                foreach ( $field['options'] as $value => $label ) {
                                    $input = sprintf( '<input type="%s" name="%s" id="%s-%s" value="%s" />', esc_attr( $field['type'] ), $key . '[]', $field['id'], $option_count, esc_attr( $value ) );
                                    printf( '<label for="%1$s-%2$s">%3$s%4$s</label>', $field['id'], $option_count, $input, esc_html( $label ) );
                                    $option_count++;
                                }
                                echo '</p>';

                                break;
                            case 'radio':
                                $option_count = 0;
                                echo '<p class="form-row">';
                                echo '<label>'.$field['label'].'</label>';
                                foreach ( $field['options'] as $value => $label ) {
                                    $input = sprintf( '<input type="%s" name="%s" id="%s-%s" value="%s" />', esc_attr( $field['type'] ), $key , $field['id'], $option_count, esc_attr( $value ) );
                                    printf( '<label for="%1$s-%2$s">%3$s%4$s</label>', $field['id'], $option_count, $input, esc_html( $label ) );
                                    $option_count++;
                                }
                                echo '</p>';

                                break;
                            case 'textarea':
                            case 'password' :
                            case 'text' :
                            case 'email' :
                            case 'tel' :
                            case 'number' :
                                woocommerce_form_field( $key, $field, '{{custom_fields.'.$key.'}}' );
                                break;
                            case 'select' :
                                if( isset($field['multiple']) && $field['multiple'] )
                                    $key .=  '[]';
                                woocommerce_form_field( $key, $field );
                                break;
                            case 'wysiwyg':
                                $field['type'] = 'textarea';
                                woocommerce_form_field( $key, $field, '{{custom_fields.'.$key.'}}' );
                                break;
                            default:
                                $field['class'] = isset($field['class']) ? implode(' ', $field['class']) : '';
                                echo '<div class="form-row acf-form-row">';
                                do_action('acf/create_fields', array($field), 0);
                                echo '</div>';
                                break;
                        }


                        } ?>
                <?php endforeach; ?>
            <?php endif; ?>

        </div>
    </div>
    <div class="clear"></div>
</script>
<script type="text/template" id="tmpl-custom-shipping-shippingaddress">
    <?php
    $checkout = new WC_Checkout();
    foreach ( $checkout->checkout_fields['shipping'] as $key => $field ) : 
        $value = str_replace('shipping_', 'shipping_address.', $key);
        woocommerce_form_field( 'custom_'.$key, $field, '{{'.$value.'}}' ); 
    endforeach; ?>
</script>

<script type="text/template" id="tmpl-custom-shipping-method-title-price">
    <tr>
        <td class="shipping_title"><input type="text" id="custom_shipping_title" value="{{title}}"></td>
        <td class="shipping_price"><input type="text" id="custom_shipping_price" value="{{price}}"></td>
    </tr>
</script>

<script type="text/template" id="tmpl-retrieve-sales-orders-list">
            <table class="wp-list-table widefat fixed striped posts retrieve_sales_nav">
                <tbody>
                {{#each this}}
                <tr class="iedit author-self level-0 post-{{id}} type-shop_order status-wc-pending post-password-required hentry">
                    <td class="order_status column-order_status">{{{order_status}}}</td>
                    <td class="order_title column-order_title has-row-actions column-primary">
                        {{displayOrderTitle}}
                    </td>
                    <td class="order_items column-order_items">
                        <a class="show_order_items" href="#">
                            {{getCountItems}}
                        </a>
                        <table cellspacing="0" class="order_items" style="display: none;">
                            <tbody>
                                {{order_items_list}}                                
                            </tbody>
                        </table>
                    </td>
                    <td class="shipping_address column-shipping_address">
                        {{{formatted_shipping_address}}}
                    </td>
                    <td class="customer_message column-customer_message">{{{customer_message}}}</td>
                    <td class="order_notes column-order_notes">{{{order_notes}}}</td>
                    <td class="order_date column-order_date">{{{order_date}}}</td>
                    <td class="order_total column-order_total">{{{order_total}}}</td>
                    <td class="crm_actions column-crm_actions"><p><a href="{{this.id}}" class="button load_order_data"><?php _e('Load Order', 'wc_point_of_sale'); ?></a></p></td>
                </tr>
                {{/each}}
                </tbody>
            </table>    
</script>

<script type="text/template" id="tmpl-retrieve-sales-orders-not-found">
    <table class="wp-list-table widefat fixed striped posts retrieve_sales_nav">
        <tbody>
            <tr class="no-items"><td colspan="9" class="colspanchange"><?php _e('No Orders found', 'wc_point_of_sale'); ?></td></tr>
        </tbody>
    </table>
</script>

<script type="text/template" id="tmpl-retrieve-sales-orders-pager">
    <div class="tablenav">
        <div class="tablenav-pages">
            <span class="displaying-num">{{items}}</span>
        {{#if count}}
            <span class="pagination-links">
                {{#if urls.a}}
                <a href="#" class="first-page" onclick="{{{urls.a}}}">
                    <span class="screen-reader-text">First page</span>
                    <span aria-hidden="true">«</span>
                </a>
                {{else}}
                    <span aria-hidden="true" class="tablenav-pages-navspan">«</span>
                {{/if}}
                {{#if urls.b}}
                <a href="#" class="prev-page" onclick="{{{urls.b}}}">
                    <span class="screen-reader-text">Previous page</span>
                    <span aria-hidden="true">‹</span>
                </a>
                {{else}}
                    <span aria-hidden="true" class="tablenav-pages-navspan">‹</span>
                {{/if}}
            
                <span class="paging-input"><label class="screen-reader-text" for="current-page-selector">Current Page</label>
                    <input type="text" aria-describedby="table-paging" size="1" value="{{currentpage}}" id="current-page-selector" class="current-page" data-count="{{count}}" data-reg_id="{{reg_id}}" >
                    of <span class="total-pages">{{countpages}}</span>
                </span>

                {{#if urls.c}}
                <a href="#" class="next-page" onclick="{{{urls.c}}}">
                    <span class="screen-reader-text">Next page</span>
                    <span aria-hidden="true">›</span>
                </a>
                {{else}}
                    <span aria-hidden="true" class="tablenav-pages-navspan">›</span>
                {{/if}}
                {{#if urls.d}}
                <a href="#" class="last-page" onclick="{{{urls.d}}}">
                    <span class="screen-reader-text">Last page</span>
                    <span aria-hidden="true">»</span>
                </a>
                {{else}}
                    <span aria-hidden="true" class="tablenav-pages-navspan">»</span>
                {{/if}}
            </span>
        {{/if}}
        </div>
    </div>
</script>

<script type="text/template" id="tmpl-confirm-box-content">
    {{#if title}}
    <h3>{{{title}}}</h3>
    {{/if}}
    {{#if content}}
    <div>
        {{{content}}}
    </div>
    {{/if}}
    <div class="wrap-button">
        <button class="button" type="button" id="cancel-button">
            <?php _e('Cancel', 'wc_point_of_sale'); ?>
        </button>
        <button class="button button-primary" type="button" id="confirm-button">
            <?php _e('Ok', 'wc_point_of_sale'); ?>
        </button>
    </div>
</script>
<script type="text/template" id="tmpl-confirm-void-register">
    <div style="display: block; float: left; margin: 0 20px 20px 0;">
    	<span style="font-size: 64px; width: 64px; height: 64px;" class="dashicons dashicons-warning"></span>
    </div>
    <p><?php _e( "Are you sure you want to clear all fields and start from scratch?", 'wc_point_of_sale'); ?></p>
</script>
<script type="text/template" id="tmpl-prompt-email-receipt">
        <div style="display: block; float: left; margin: 0 20px 20px 0;">
        	<span style="font-size: 64px; width: 64px; height: 64px;" class="dashicons dashicons-email"></span>
        </div>
        <p><?php _e( "Do you want to email the receipt? If so, enter customer email.", 'wc_point_of_sale'); ?></p>        
</script>