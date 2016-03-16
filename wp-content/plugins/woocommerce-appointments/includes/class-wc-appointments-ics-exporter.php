<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * .ics Exporter
 */
class WC_Appointments_ICS_Exporter {

	/**
	 * Appointments list to export
	 *
	 * @var array
	 */
	protected $appointments = array();

	/**
	 * File path
	 *
	 * @var string
	 */
	protected $file_path = '';

	/**
	 * UID prefix.
	 *
	 * @var string
	 */
	protected $uid_prefix = 'wc_appointments_';

	/**
	 * End of line.
	 *
	 * @var string
	 */
	protected $eol = "\r\n";

	/**
	 * Get appointment .ics
	 *
	 * @param  WC_Appointment $appointment Appointment data
	 *
	 * @return string .ics path
	 */
	public function get_appointment_ics( $appointment ) {
		$product          = $appointment->get_product();
		$this->file_path  = $this->get_file_path( $appointment->id . '-' . $product->get_title() );
		$this->appointments[] = $appointment;

		// Create the .ics
		$this->create();

		return $this->file_path;
	}

	/**
	 * Get .ics for appointments.
	 *
	 * @param  array  $appointments Array with WC_Appointment objects
	 * @param  string $filename .ics filename
	 *
	 * @return string .ics path
	 */
	public function get_ics( $appointments, $filename = '' ) {
		// Create a generic filename.
		if ( '' == $filename ) {
			$filename = 'appointments-' . date_i18n( get_option( 'date_format' ) . '-' . get_option( 'time_format' ), current_time( 'timestamp' ) );
		}

		$this->file_path = $this->get_file_path( $filename );
		$this->appointments  = $appointments;

		// Create the .ics
		$this->create();

		return $this->file_path;
	}

	/**
	 * Get file path
	 *
	 * @param  string $filename Filename
	 *
	 * @return string
	 */
	protected function get_file_path( $filename ) {
		$upload_data = wp_upload_dir();

		return $upload_data['path'] . '/' . sanitize_title( $filename ) . '.ics';
	}

	/**
	 * Create the .ics file
	 *
	 * @return void
	 */
	protected function create() {
		$handle = @fopen( $this->file_path, 'w' );
		$ics = $this->generate();
		@fwrite( $handle, $ics );
		@fclose( $handle );
	}

	/**
	 * Format the date
	 *
	 * @param  int  $timestamp
	 * @param  bool $all_day
	 *
	 * @return string
	 */
	protected function format_date( $timestamp, $all_day = false ) {
		$pattern = ( $all_day ) ? 'Ymd' : 'Ymd\THis';

		return date( $pattern, $timestamp );
	}

	/**
	 * Sanitize strings for .ics
	 *
	 * @param  string $string
	 *
	 * @return string
	 */
	protected function sanitize_string( $string ) {
		$string = preg_replace( '/([\,;])/', '\\\$1', $string );
		$string = str_replace( "\n", '\n', $string );
		$string = sanitize_text_field( $string );

		return $string;
	}

	/**
	 * Generate the .ics content
	 *
	 * @return string
	 */
	protected function generate() {
		$sitename = get_option( 'blogname' );

		// Set the ics data.
		$ics = 'BEGIN:VCALENDAR' . $this->eol;
		$ics .= 'VERSION:2.0' . $this->eol;
		$ics .= 'PRODID:-//BizzThemes//WooCommerce Appointments ' . WC_APPOINTMENTS_VERSION . '//EN' . $this->eol;
		$ics .= 'CALSCALE:GREGORIAN' . $this->eol;
		$ics .= 'X-WR-CALNAME:' . $this->sanitize_string( $sitename ) . $this->eol;
		$ics .= 'X-ORIGINAL-URL:' . $this->sanitize_string( home_url( '/' ) ) . $this->eol;
		$ics .= 'X-WR-CALDESC:' . $this->sanitize_string( sprintf( __( 'Appointments from %s', 'woocommerce-appointments' ), $sitename ) ) . $this->eol;
		$ics .= 'X-WR-TIMEZONE:' . wc_appointment_get_timezone_string() . $this->eol;

		foreach ( $this->appointments as $appointment ) {
			$product     = $appointment->get_product();
			$all_day     = $appointment->is_all_day();
			$url         = ( $appointment->get_order() ) ? $appointment->get_order()->get_view_order_url() : '';
			$summary     = '#' . $appointment->id . ' - ' . $product->get_title();
			$description = '';

			if ( $staff = $appointment->get_staff_member() ) {
				$description .= __( 'Staff #', 'woocommerce-appointments' ) . $staff->ID . ' - ' . $staff->display_name . '\n\n';
			}

			if ( '' != $product->post->post_excerpt ) {
				$description .= __( 'Appointment description:', 'woocommerce-appointments' ) . '\n';
				$description .= wp_kses( $product->post->post_excerpt, array() );
			}

			$ics .= 'BEGIN:VEVENT' . $this->eol;
			$ics .= 'DTEND:' . $this->format_date( $appointment->end, $all_day ) . $this->eol;
			$ics .= 'UID:' . $this->uid_prefix . $appointment->id . $this->eol;
			$ics .= 'DTSTAMP:' . $this->format_date( time() ) . $this->eol;
			$ics .= 'LOCATION:' . $this->eol;
			$ics .= 'DESCRIPTION:' . $this->sanitize_string( $description )  . $this->eol;
			$ics .= 'URL;VALUE=URI:' . $this->sanitize_string( $url ) . $this->eol;
			$ics .= 'SUMMARY:' . $this->sanitize_string( $summary ) . $this->eol;
			$ics .= 'DTSTART:' . $this->format_date( $appointment->start, $all_day ) . $this->eol;
			$ics .= 'END:VEVENT' . $this->eol;
		}

		$ics .= 'END:VCALENDAR';

		return $ics;
	}
}
