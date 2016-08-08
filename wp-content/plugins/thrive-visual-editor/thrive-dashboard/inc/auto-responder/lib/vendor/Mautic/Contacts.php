<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic, NP. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     MIT http://opensource.org/licenses/MIT
 */

/**
 * Contacts Context
 */
class Thrive_Dash_Api_Mautic_Contacts extends Thrive_Dash_Api_Mautic_Api {
	/**
	 * {@inheritdoc}
	 */
	protected $endpoint = 'contacts';

	/**
	 * Get a list of users available as lead owners
	 *
	 * @return array|mixed
	 */
	public function getOwners() {
		return $this->makeRequest( 'contacts/list/owners' );
	}

	/**
	 * Get a list of custom fields
	 *
	 * @return array|mixed
	 */
	public function getFieldList() {
		return $this->makeRequest( 'contacts/list/fields' );
	}

	/**
	 * Get a list of lead segments
	 *
	 * @return array|mixed
	 */
	public function getSegments() {
		return $this->makeRequest( 'contacts/list/segments' );
	}

	/**
	 * Get a list of a lead's notes
	 *
	 * @param int $id Contact ID
	 * @param string $search
	 * @param int $start
	 * @param int $limit
	 * @param string $orderBy
	 * @param string $orderByDir
	 * @param bool $publishedOnly
	 *
	 * @return array|mixed
	 */
	public function getContactNotes( $id, $search = '', $start = 0, $limit = 0, $orderBy = '', $orderByDir = 'ASC' ) {
		$parameters = array();

		$args = array( 'search', 'start', 'limit', 'orderBy', 'orderByDir' );

		foreach ( $args as $arg ) {
			if ( ! empty( $$arg ) ) {
				$parameters[ $arg ] = $$arg;
			}
		}

		return $this->makeRequest( 'contacts/' . $id . '/notes', $parameters );
	}

	/**
	 * Get a segment of smart segments the lead is in
	 *
	 * @param $id
	 */
	public function getContactSegments( $id ) {
		return $this->makeRequest( 'contacts/' . $id . '/segments' );
	}

	/**
	 * Get a segment of campaigns the lead is in
	 *
	 * @param $id
	 */
	public function getContactCampaigns( $id ) {
		return $this->makeRequest( 'contacts/' . $id . '/campaigns' );
	}
}