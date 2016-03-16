<?php

if( ! class_exists( 'DVK_Product', false ) ) {

	/**
	 * Class DVK_Product
	 *
	 */
	abstract class DVK_Product {

		/**
		 * @var string The URL of the shop running the EDD API.
		 */
		public $api_url = '';

		/**
		 * @var string The item name in the EDD shop.
		 */
		public $item_name = '';

		/**
		 * @var int
		 */
		public $item_id = 0;

		/**
		 * @var string The theme slug or plugin file (my-plugin)
		 */
		public $slug = '';

		/**
		 * @var string The base plugin file (my-plugin/my-plugin.php)
		 */
		public $plugin_basename = '';

		/**
		 * @var string The version number of the item
		 */
		public $version = '0';

		/**
		 * @var string The absolute url on which users can purchase a license
		 */
		public $item_url = '';

		/**
		 * @var string Absolute admin URL on which users can enter their license key.
		 */
		public $license_page_url = '';

		/**
		 * @var string The text domain used for translating strings
		 */
		public $text_domain = 'dvk';

		/**
		 * @var string The item author
		 */
		public $author = 'Danny van Kooten';

		/**
		 * @var string Prefix used to prefix stuff like ID's, option names, transient key names..
		 */
		public $prefix = 'dvk_';

		/**
		 * Gets a Google Analytics Campaign url for this product
		 *
		 * @param string $path
		 * @param string $link_identifier
		 * @return string The full URL
		 */
		public function get_tracking_url( $path = '', $link_identifier = '' ) {

			$tracking_vars = array(
				'utm_campaign' => $this->item_name . ' licensing',
				'utm_medium' => 'link',
				'utm_source' => $this->item_name,
				'utm_content' => $link_identifier
			);

			// url encode tracking vars
			$tracking_vars = urlencode_deep( $tracking_vars );
			$query_string = build_query( $tracking_vars );

			return $this->item_url . ltrim( $path, '/' ) . '#' . $query_string;
		}

	}

}

