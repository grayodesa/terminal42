<?php

class MC4WP_Styles_Builder {

	/**
	 * @var MC4WP_Styles_Builder
	 */
	public static $instance;

	/**
	 * @param $styles
	 *
	 * @return MC4WP_Styles_Builder
	 */
	public static function build( $styles ) {

		if( ! self::$instance instanceof MC4WP_Styles_Builder ) {
			self::$instance = new MC4WP_Styles_Builder();
		}

		$builder = self::$instance;

		// clean-up styles array
		$builder->clean();

		// sanitize submitted styles
		$builder->sanitize( $styles );

		// listen for user-triggered actions (delete, copy, ..)
		$builder->act();

		// return all styles (for WP options API)
		return $builder->styles;
	}

	/**
	 * Array with all available CSS fields, their default value and their type
	 *
	 * @var array
	 */
	public $fields = array(
		'form_width' => array(
			'default' => '',
			'type' => 'px'
		),
		'form_background_color' => array(
			'default' => '',
			'type' => 'color'
		),
		'form_background_image' => array(
			'default' => '',
			'type' => 'text'
		),
		'form_background_repeat' => array(
			'default' => 'repeat',
			'type' => 'text'
		),
		'form_font_color' => array(
			'default' => '',
			'type' => 'color'
		),
		'form_border_color' => array(
			'default' => '',
			'type' => 'color'
		),
		'form_border_width' => array(
			'default' => '',
			'type' => 'int'
		),
		'form_padding' => array(
			'default' => '',
			'type' => 'int'
		),
		'form_text_align' => array(
			'default' => '',
			'type' => 'text'
		),
		'form_font_size' => array(
			'default' => '',
			'type' => 'int'
		),
		'labels_font_color' => array(
			'default' => '',
			'type' => 'color'
		),
		'labels_font_style' => array(
			'default' => '',
			'type' => 'text'
		),
		'labels_font_size' => array(
			'default' => '',
			'type' => 'int'
		),
		'labels_display' => array(
			'default' => '',
			'type' => 'text'
		),
		'labels_vertical_margin' => array(
			'default' => '',
			'type' => 'int'
		),
		'labels_horizontal_margin' => array(
			'default' => '',
			'type' => 'int'
		),
		'labels_width' => array(
			'default' => '',
			'type' => 'px'
		),
		'fields_border_radius' => array(
			'default' => '',
			'type' => 'int'
		),
		'fields_border_color' => array(
			'default' => '',
			'type' => 'color'
		),
		'fields_focus_outline_color' => array(
			'default' => '',
			'type' => 'color'
		),
		'fields_border_width' => array(
			'default' => '',
			'type' => 'int'
		),
		'fields_width' => array(
			'default' => '',
			'type' => 'px'
		),
		'fields_height' => array(
			'default' => '',
			'type' => 'int'
		),
		'fields_display' => array(
			'default' => '',
			'type' => 'text'
		),
		'buttons_background_color' => array(
			'default' => '',
			'type' => 'color'
		),
		'buttons_hover_background_color' => array(
			'default' => '',
			'type' => 'color'
		),
		'buttons_font_color' => array(
			'default' => '',
			'type' => 'color'
		),
		'buttons_font_size' => array(
			'default' => '',
			'type' => 'int'
		),
		'buttons_border_color' => array(
			'default' => '',
			'type' => 'color'
		),
		'buttons_hover_border_color' => array(
			'default' => '',
			'type' => 'color'
		),
		'buttons_border_radius' => array(
			'default' => '',
			'type' => 'int'
		),
		'buttons_border_width' => array(
			'default' => '',
			'type' => 'int'
		),
		'buttons_width' => array(
			'default' => '',
			'type' => 'px'
		),
		'buttons_height' => array(
			'default' => '',
			'type' => 'int'
		),
		'messages_font_color_error' => array(
			'default' => '',
			'type' => 'color'
		),
		'messages_font_color_success' => array(
			'default' => '',
			'type' => 'color'
		),
		'selector_prefix' => array(
			'default' => '',
			'type' => 'selector'
		),
		'manual' => array(
			'default' => '',
			'type' => 'text'
		)

	);

	/**
	 * @var array
	 */
	public $default_form_styles = array();

	/**
	 * @var array
	 */
	public $styles = array();

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->default_form_styles = $this->load_default_form_styles();
		$this->styles = $this->load_styles();
	}

	/**
	 * Act on user-triggered actions
	 *
	 * @return array
	 */
	protected function act() {
		// was delete button clicked?
		if( isset( $_POST['_mc4wp_delete_form_styles'] ) ) {
			$form_id_to_delete = absint( $_POST['_mc4wp_delete_form_styles'] );
			$this->delete_form_styles( $form_id_to_delete );
		} elseif( isset( $_POST['_mc4wp_copy_form_styles'] ) ) {
			$this->copy_form_styles( $_POST['copy_from_form_id'], $_POST['form_id'] );
		}

		// recreate stylesheet (with new values)
		if( ! defined( 'MC4WP_DOING_UPGRADE' ) ) {
			$this->delete_stylesheets();
			$this->build_stylesheet();
		}

	}

	/**
	 * @param int $form_id
	 *
	 * @return bool
	 */
	public function delete_form_styles( $form_id ) {
		if( isset( $this->styles['form-' . $form_id ] ) ) {
			unset( $this->styles['form-' . $form_id ] );
			return true;
		}

		return false;
	}

	/**
	 * @param int $from_id
	 * @param int $to_id
	 * @return bool
	 */
	public function copy_form_styles( $from_id, $to_id ) {
		if( isset( $this->styles['form-' . $from_id ] ) ) {
			$this->styles['form-' . $to_id ] = $this->styles['form-' . $from_id ];
			return true;
		}

		return false;
	}

	/**
	 * Get the default theme settings
	 *
	 * @return array
	 */
	protected function load_default_form_styles() {
		$default_form_styles = array();

		foreach( $this->fields as $key => $field ) {
			$default_form_styles[ $key ] = $field['default'];
		}

		return $default_form_styles;
	}

	/**
	 * Get all form themes, merged with defaults
	 *
	 * @return array
	 */
	protected function load_styles() {

		$all_styles = (array) get_option( 'mc4wp_form_styles', array() );

		if( empty( $all_styles ) ) {
			return array();
		}

		// merge all theme settings with the defaults array
		foreach( $all_styles as $form_id => $form_styles ) {
			$all_styles[ $form_id ] = array_merge( $this->default_form_styles, $form_styles );
		}

		// return merged array
		return $all_styles;
	}

	/**
	 * Get saved CSS values from option
	 *
	 * @param int $form_id
	 *
	 * @return array
	 */
	public function get_form_styles( $form_id = 0 ) {
		$form_styles = ( isset( $this->styles[ 'form-' . $form_id ] ) ) ? $this->styles[ 'form-' . $form_id ] : $this->default_form_styles;
		return $form_styles;
	}

	/**
	 * Clean complete $styles array, remove deleted forms..
	 *
	 * @return array
	 */
	protected function clean() {
		// clean-up existing form styles
		foreach( $this->styles as $form_id => $form_styles ) {
			// skip these styles if form no longer exists
			$form = get_post( substr( $form_id, 5 ) );
			if( ! $form instanceof WP_Post || $form->post_status !== 'publish' ) {
				unset( $this->styles[ $form_id ] );
			}
		}

		return $this->styles;
	}

	/**
	 * Validate the given CSS values according to their type
	 *
	 * @param $dirty_form_styles
	 *
	 * @return mixed
	 */
	protected function sanitize( $dirty_form_styles = array() ) {

		// start sanitizing new form styles
		foreach( $dirty_form_styles as $form_id => $new_form_styles ) {

			// start with empty array of styles
			$sanitized_form_styles = array();

			foreach( $new_form_styles as $key => $value ) {

				// skip field if it's not a valid field
				if( ! isset( $this->fields[ $key ] ) ) {
					continue;
				}

				// add field value to css array
				$sanitized_form_styles[ $key ] = $value;

				// skip if field is empty or has its default value
				if( '' === $value || $value === $this->fields[$key]['default'] ) {
					continue;
				}

				// sanitize field since it's not default
				$type = $this->fields[ $key ]['type'];
				$value = call_user_func( array( $this, 'sanitize_' . $type ), $value );

				// save css value
				$sanitized_form_styles[ $key ] = $value;
			}

			// save sanitized styles in array with all styles
			$this->styles[ $form_id ] = $sanitized_form_styles;
		}

		return $this->styles;
	}

	/**
	 * Sanitize color values
	 *
	 * @param $value
	 *
	 * @return string
	 */
	public function sanitize_color( $value ) {
		// make sure colors start with #
		return '#' . ltrim( trim( $value ), '#' );
	}

	/**
	 * Sanitize pixel value
	 *
	 * @param $value
	 *
	 * @return mixed|string
	 */
	public function sanitize_px( $value ) {
		// make sure px and % end with 'px' or '%'
		$value = str_replace( ' ', '', strtolower( $value ) );

		if( substr( $value, -1 ) !== '%' && substr( $value, -2 ) !== 'px') {
			$value = floatval( $value ) . 'px';
		}

		return $value;
	}

	/**
	 * Sanitize integer value
	 *
	 * @param $value
	 *
	 * @return int
	 */
	public function sanitize_int( $value ) {
		return intval( $value );
	}

	/**
	 * Sanitize CSS selector value
	 *
	 * @param $value
	 *
	 * @return string
	 */
	public function sanitize_selector( $value ) {
		return trim( $value ) . ' ';
	}

	/**
	 * Sanitize text value
	 *
	 * @param $value
	 *
	 * @return string
	 */
	public function sanitize_text( $value ) {
		return trim( $value );
	}

	/**
	 * Delete all stylesheets from the WP uploads dir
	 */
	protected function delete_stylesheets() {
		$upload = wp_upload_dir();

		// find all stylesheets created by Styles Builder
		$stylesheets = glob( $upload['path'] . '/mailchimp-for-wp*.css' );

		if( is_array( $stylesheets ) ) {
			// unlink all stylesheets
			array_map( 'unlink', $stylesheets );
		}

		return true;
	}

	/**
	 * Build file with given CSS values
	 *
	 * @return bool
	 */
	protected function build_stylesheet() {

		$css_string = $this->get_css_string();

		// upload CSS file with CSS string as content
		$file = wp_upload_bits( 'mailchimp-for-wp.css', null, $css_string );

		// Check if file was successfully created
		if( ! is_array( $file ) || $file['error'] !== false ) {
			$message = sprintf( __( 'Error creating stylesheet: %s', 'mailchimp-for-wp' ), '</strong>' . $file['error'] ) . '<br />';
			$message .= sprintf( __( 'Please add the generated CSS to your theme stylesheet manually or use a plugin like <em>%s</em>.', 'mailchimp-for-wp' ), '<a href="https://wordpress.org/plugins/simple-custom-css/">Simple Custom CSS</a>' ) . '<br />';
			$message .= '<a class="mc4wp-show-css button" href="javascript:void(0);">' . __( 'Show generated CSS', 'mailchimp-for-wp' ) . '</a>';
			$message .= '<textarea id="mc4wp_generated_css" readonly style="display:none; width: 100%; min-height: 300px; margin-top: 20px;">'. esc_html( $css_string ) .'</textarea><strong>';
			add_settings_error( 'mc4wp', 'mc4wp-css', $message );
			return false;
		}

		// store protocol relative url to CSS file in option
		$custom_stylesheet = str_ireplace( array( 'http://', 'https://' ), '//', $file['url'] );

		// add timestamp to url
		$custom_stylesheet .= '?' . time();
		update_option( 'mc4wp_custom_css_file', $custom_stylesheet );

		// show notice
		add_settings_error( 'mc4wp', 'mc4wp-css', sprintf( __( 'The <a href="%s">CSS Stylesheet</a> has been created.', 'mailchimp-for-wp' ), $file['url'] ), 'updated' );
		return true;
	}

	/**
	 * Turns array of CSS values into CSS stylesheet string
	 *
	 * @return string
	 */
	protected function get_css_string() {

		// Build CSS String
		$css_string = '';
		ob_start();

		echo '/* CSS Generated by MailChimp for WordPress v'. MC4WP_VERSION .' */' . "\n\n";

		// Loop through all form styles
		foreach( $this->styles as $form_id => $form_styles ) {

			$form_selector = '.mc4wp-' . $form_id;
			$actual_form_id = substr( $form_id, 5 );

			// Build CSS styles for this form
			extract( $form_styles );
			include MC4WP_PLUGIN_DIR . 'includes/views/parts/css-styles.php';
		}

		// get output buffer
		$css_string = ob_get_contents();
		ob_end_clean();

		return $css_string;
	}

	/**
	 * Checks whether a custom CSS rule value was set for this element
	 *
	 * @param $form_id
	 * @param $element_name
	 *
	 * @return bool
	 */
	public function form_has_rules_for_element( $form_id, $element_name ) {

		if( ! isset( $this->styles[ $form_id ] ) ) {
			return false;
		}

		// loop through all form styles
		foreach( $this->styles[ $form_id ] as $rule_name => $rule_value ) {

			// is this a rule for the given element?
			if( strpos( $rule_name, $element_name ) === 0 ) {

				// is this rule filled with a value?
				if( ! empty( $rule_value ) ) {
					return true;
				}
			}
		}

		// no filled rules for this element found
		return false;
	}

	/**
	 * @param $rule
	 * @param $value
	 */
	public function maybe_echo( $rule, $value ) {
		if( ! empty( $value ) ) {
			printf( $rule, $value );
		}
	}

}