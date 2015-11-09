<?php

if( ! defined( 'MC4WP_VERSION' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

/**
 * Gets the MailChimp for WP options from the database
 * Uses default values to prevent undefined index notices.
 *
 * @param string $key
 * @return array
 */
function mc4wp_get_options( $key = '' ) {

	static $defaults;

	if( is_null( $defaults ) ) {
		$defaults = include MC4WP_PLUGIN_DIR . '/includes/config/default-options.php';
	}

	$keys_map = array(
		'mc4wp' => 'general',
		'mc4wp_checkbox' => 'checkbox',
		'mc4wp_form' => 'form'
	);

	$options = array();

	foreach ( $keys_map as $db_key => $opt_key ) {
		$option = (array) get_option( $db_key, array() );
		$options[$opt_key] = array_merge( $defaults[$opt_key], $option );
	}

	if( '' !== $key ) {
		return $options[$key];
	}

	return $options;
}

/**
 * Gets the MailChimp for WP API class and injects it with the given API key
 *
 * @return MC4WP_API
 */
function mc4wp_get_api() {
	return MC4WP::instance()->get_api();
}

/**
 * Check whether a form was submitted
 *
 * @since 2.3.8
 * @param int $form_id The ID of the form you want to check. (optional)
 * @param string $element_id The ID of the form element you want to check, eg id="mc4wp-form-1" (optional)
 * @return boolean
 */
function mc4wp_form_is_submitted( $form_id = 0, $element_id = null ) {
	$form = MC4WP_Form::get( $form_id );

	if( ! $form instanceof MC4WP_Form ) {
		return false;
	}

	return $form->is_submitted( $element_id );
}

/**
 * @since 2.3.8
 * @param int $form_id
 * @return string
 */
function mc4wp_form_get_response_html( $form_id = 0 ) {
	$form = MC4WP_Form::get( $form_id );

	// return empty string if form doesn't exist or isn't submitted
	if( ! $form instanceof MC4WP_Form || ! $form->is_submitted() ) {
		return '';
	}

	return $form->request->get_response_html();
}