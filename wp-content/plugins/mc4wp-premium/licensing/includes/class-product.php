<?php

/**
 * Class MC4WP_Product
 */
class MC4WP_Product extends DVK_Product {

	/**
	 * @var string The URL of the shop running the EDD API.
	 */
	public $api_url = 'https://mc4wp.com/api/edd-licenses/';

	/**
	 * @var string The item name in the EDD shop.
	 */
	public $item_name = 'MailChimp for WordPress - Premium Bundle';

	/**
	 * @var int The item ID in EDD
	 */
	public $item_id = 103001;

	/**
	 * @var string The plugin slug (my-plugin)
	 */
	public $slug;

	/**
	 * @var string The base plugin slug (my-plugin/my-plugin.php)
	 */
	public $plugin_basename;

	/**
	 * @var string The version number of the item
	 */
	public $version = MC4WP_PREMIUM_VERSION;

	/**
	 * @var string The absolute url on which users can purchase a license
	 */
	public $item_url = 'https://mc4wp.com/';

	/**
	 * @var string Absolute admin URL on which users can enter their license key.
	 */
	public $license_page_url = 'admin.php?page=mailchimp-for-wp-other';

	/**
	 * @var string The text domain used for translating strings
	 */
	public $text_domain = 'mailchimp-for-wp';

	/**
	 * @var string The item author
	 */
	public $author = 'ibericode';

	/**
	 * @var string Prefix used to prefix stuff like ID's, option names, transient key names..
	 */
	public $prefix = 'mc4wp_';

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->plugin_basename = plugin_basename( MC4WP_PREMIUM_PLUGIN_FILE );
		$this->slug = basename( dirname( MC4WP_PREMIUM_PLUGIN_FILE ) );
		$this->license_page_url = admin_url( $this->license_page_url );
	}

}