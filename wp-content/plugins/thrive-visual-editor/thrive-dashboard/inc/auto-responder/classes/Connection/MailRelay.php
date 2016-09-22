<?php
/**
 * Thrive Themes - https://thrivethemes.com
 *
 * @package thrive-dashboard
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden
}

class Thrive_Dash_List_Connection_MailRelay extends Thrive_Dash_List_Connection_Abstract {
	/**
	 * Return the connection type
	 * @return String
	 */
	public static function getType() {
		return 'autoresponder';
	}

	/**
	 * @return string
	 */
	public function getTitle() {
		return 'MailRelay';
	}

	/**
	 * output the setup form html
	 *
	 * @return void
	 */
	public function outputSetupForm() {
		$this->_directFormHtml( 'mailrelay' );
	}

	/**
	 * just save the key in the database
	 *
	 * @return mixed|void
	 */
	public function readCredentials() {
		$key = ! empty( $_POST['connection']['key'] ) ? $_POST['connection']['key'] : '';

		if ( empty( $key ) ) {
			return $this->error( __( 'You must provide a valid MailRelay key', TVE_DASH_TRANSLATE_DOMAIN ) );
		}

		$url = ! empty( $_POST['connection']['url'] ) ? $_POST['connection']['url'] : '';

		if ( filter_var( $url, FILTER_VALIDATE_URL ) === false || empty( $url ) ) {
			return $this->error( __( 'You must provide a valid MailRelay URL', TVE_DASH_TRANSLATE_DOMAIN ) );
		}

		$this->setCredentials( $_POST['connection'] );

		$result = $this->testConnection();

		if ( $result !== true ) {
			return $this->error( sprintf( __( 'Could not connect to MailRelay using the provided key (<strong>%s</strong>)', TVE_DASH_TRANSLATE_DOMAIN ), $result ) );
		}

		/**
		 * finally, save the connection details
		 */
		$this->save();

		return $this->success( __( 'MailRelay connected successfully', TVE_DASH_TRANSLATE_DOMAIN ) );
	}

	/**
	 * test if a connection can be made to the service using the stored credentials
	 *
	 * @return bool|string true for success or error message for failure
	 */
	public function testConnection() {
		/** @var Thrive_Dash_Api_MailRelay $mr */

		$mr = $this->getApi();

		try {
			$mr->getLists();
		} catch ( Thrive_Dash_Api_MailRelay_Exception $e ) {
			return $e->getMessage();
		}

		return true;
	}

	/**
	 * instantiate the API code required for this connection
	 *
	 * @return mixed
	 */
	protected function _apiInstance() {
		return new Thrive_Dash_Api_MailRelay( array(
				'host'   => $this->param( 'url' ),
				'apiKey' => $this->param( 'key' ),
			)
		);
	}

	/**
	 * get all Subscriber Lists from this API service
	 *
	 * @return array
	 */
	protected function _getLists() {
		/** @var Thrive_Dash_Api_MailRelay $api */
		$api = $this->getApi();

		$body = $api->getLists();

		$lists = array();
		foreach ( $body as $item ) {
			$lists [] = array(
				'id'   => $item['id'],
				'name' => $item['name'],
			);
		}

		return $lists;
	}

	/**
	 * add a contact to a list
	 *
	 * @param mixed $list_identifier
	 * @param array $arguments
	 *
	 * @return bool|string true for success or string error message for failure
	 */
	public function addSubscriber( $list_identifier, $arguments ) {

		$args = array();
		/** @var Thrive_Dash_Api_MailRelay $api */
		$api = $this->getApi();


		$args['email'] = $arguments['email'];

		if ( ! empty( $arguments['name'] ) ) {
			$args['name'] = $arguments['name'];
		}


		if ( ! empty( $arguments['phone'] ) ) {

			$args['customFields']['f_phone'] = $arguments['phone'];
		}

		try {

			$api->addSubscriber( $list_identifier, $args );

		} catch ( Thrive_Dash_Api_MailRelay_Exception $e ) {
			return $e->getMessage();
		}


		return true;

	}

	/**
	 * Return the connection email merge tag
	 * @return String
	 */
	public static function getEmailMergeTag() {
		return '[email]';
	}

}