<?php

/**
 * Class MC4WP_Three_O_Installer
 *
 * @ignore
 */
class MC4WP_Three_O_Installer {

	/**
	 * The required capability
	 */
	const CAPABILITY = 'install_plugins';

	/**
	 * @var string
	 */
	protected $download_url = 'https://mc4wp.com/api/download/premium';

	/**
	 * @var string Absolute path to plugins direcotyr
	 */
	public $plugins_dir;

	public $core_slug = 'mailchimp-for-wp';
	public $core_file = 'mailchimp-for-wp/mailchimp-for-wp.php';

	public $bundle_slug = 'mc4wp-premium';
	public $bundle_file = 'mc4wp-premium/mc4wp-premium.php';


	/**
	 * Construct
	 *
	 * @param string $plugins_dir
	 * @param string $license_key
	 */
	public function __construct( $plugins_dir, $license_key ) {
		$this->plugins_dir = rtrim( $plugins_dir, '/' );
		$this->download_url .= '?license_key=' . $license_key;
	}

	/**
	 * Add hooks
	 */
	public function add_hooks() {
		add_action( 'mc4wp_menu_items', array( $this, 'add_menu_item' ), 99 );
		add_action( 'admin_notices', array( $this, 'notice' ) );
	}

	/**
	 * Show a notice linking to upgrade page.
	 */
	public function notice() {

		global $pagenow;
		$page = ! empty( $_GET['page'] ) ? $_GET['page'] : '';

		// only show to users for which this is relevant
		if( ! current_user_can( self::CAPABILITY ) ) {
			return;
		}

		// only show on plugins.php & settings pages
		if( $pagenow !== 'plugins.php' && strpos( $page, 'mailchimp-for-wp' ) !== 0 ) {
			return;
		}

		// don't show on update page itself
		if( $page === 'mailchimp-for-wp-upgrade-to-v3' ) {
			return;
		}

		?>
		<div class="updated">
			<p>There is a major update available for MailChimp for WordPress. Please <a href="<?php echo admin_url( 'admin.php?page=mailchimp-for-wp-upgrade-to-v3' ); ?>">visit the upgrade page</a> to read more and install the update.</p>
		</div>
		<?php
	}

	/**
	 * Registers the new menu item
	 *
	 * @param $items
	 * @return array
	 */
	public function add_menu_item( $items ) {

		// only show to users who have the required capability
		if( ! current_user_can( self::CAPABILITY ) ) {
			return $items;
		}

		$items['three_o'] = array(
			'title' => __( 'Upgrade to 3.0', 'mailchimp-for-wp' ),
			'text' => __( 'Upgrade to 3.0', 'mailchimp-for-wp' ),
			'slug' => 'upgrade-to-v3',
			'callback' => array( $this, 'show_page' )
		);

		return $items;
	}

	/**
	 * Show "Upgrade to 3.0" page.
	 */
	public function show_page() {

		// if Install Button for Bundle was clicked, process download.
		if( isset( $_POST['download'] ) ) {
			$this->download();
			return;
		}

		if( ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		echo '<div class="wrap" id="mc4wp-admin" style="max-width: 560px;">';
		add_thickbox();

		echo "<h2 class=\"page-title\">Upgrade to version 3.0</h2>";

		echo sprintf( '<p>Good news!</p><p>There is a major update available for the MailChimp for WordPress plugin, <a href="%s">introducing some much needed changes & improvements</a>.</p>', 'https://mc4wp.com/blog/whats-new-in-mailchimp-for-wordpress-the-big-three-o/' );
		echo '<p>The most important change is that the plugin is split into two separate plugins, a core plugin with base functionality and an add-on plugin containing all premium functionality.</p>';
		echo sprintf( '<p>A few other things have changed, so please have a look at the <a href="%s">upgrade guide</a> before installing the new plugins using the buttons below.</p>', 'https://mc4wp.com/kb/upgrading-to-3-0/' );

		echo '<div style="margin: 30px 0;"></div>';

		echo '<h3>How to update?</h3>';
		echo '<p>You can update to the new version by installing or updating the two plugins listed below.</p>';

		echo '<table class="widefat striped">';
		echo '<tr><th>Plugin</th><th width="1%"></th></tr>';

		// 1: Core Plugin
		echo '<tr>';
		echo '<td><p><strong>MailChimp for WordPress</strong></p><p>This is the core plugin, containing all basic functionality. You should have version 3.0 or higher installed before trying to install the Premium plugin.</p></td>';

		// status
		$absolute_file = trailingslashit( $this->plugins_dir ) . $this->core_file;
		if( file_exists( $absolute_file ) ) {
			// installed
			$data = get_file_data( $absolute_file, array( 'Version' => 'Version' ), 'plugin' );
			if( version_compare( $data['Version'], '3.0', '>=' ) ) {
				// at version
				if( is_plugin_active( $this->core_file ) ) {
					echo '<td>Ready</td>';
				} else {
					// output activate link
					$url = wp_nonce_url( 'plugins.php?action=activate&amp;plugin='.$this->core_file.'&amp;action=activate', 'activate-plugin_' . $this->core_file );
					$activate_url = network_admin_url( $url );
					echo sprintf( '<td><a class="button button-primary" href="%s">Activate</a></td>', $activate_url );
				}
			} else {
				$url = network_admin_url( 'plugin-install.php?tab=plugin-information&plugin='. $this->core_slug .'&TB_iframe=true&width=600&height=550' );
				echo sprintf( '<td><a href="%s" class="button button-primary thickbox">Update</a></td>', $url );
			}
		} else {
			$url = network_admin_url( 'plugin-install.php?tab=plugin-information&plugin='. $this->core_slug .'&TB_iframe=true&width=600&height=550' );
			echo sprintf( '<td><a href="%s" class="button button-primary thickbox">Install</a></td>', $url );
		}

		echo '</tr>';

		// 2: Bundle Plugin
		echo '<tr>';
		echo '<td><p><strong>Premium Functionality</strong></p><p>This plugin adds all premium functionality to the core MailChimp for WordPress plugin.</p></td>';

		// status
		$absolute_file = trailingslashit( $this->plugins_dir ) . $this->bundle_file;
		if( file_exists( $absolute_file ) ) {

			echo '<td>';
			// at version
			if( is_plugin_active( $this->bundle_file ) ) {
				echo 'Ready';
			} else {
				// output activate link
				$url = wp_nonce_url( 'plugins.php?action=activate&amp;plugin='.$this->bundle_file.'&amp;action=activate', 'activate-plugin_' . $this->bundle_file );
				$activate_url = network_admin_url( $url );
				echo sprintf( '<a class="button button-primary" href="%s">Activate</a>', $activate_url );
			}
		} else {
			$url = network_admin_url( 'plugin-install.php?tab=plugin-information&plugin='. $this->bundle_slug .'&TB_iframe=true&width=600&height=550' );
			echo sprintf( '<form method="post"><button type="submit" name="download" class="button button-primary">Install</button></form>', $url );
		}

		echo '</td>';
		echo '</tr>';
		echo '</table>';

		echo '<div style="margin: 30px 0;"></div>';

		echo sprintf( '<p>Alternatively, you can <a href="%s">download the premium bundle here</a> so you can install it manually.</p><p>If you are unsure how to proceed after downloading the plugin, please <a href="%s">have a look at the installation guide</a>.</p>', $this->download_url, 'https://mc4wp.com/kb/installation-guide/' );

		echo '<div style="margin: 40px 0;"></div>';

		echo '<h3>What happens next?</h3>';
		echo '<p>Once you have the two plugins installed & activated at the required version, the Pro plugin will self-deactivate. You can then safely delete it, as it is no longer needed.</p>';

		echo '</div>';

	}

	/**
	 * Downloads & Installs the Premium Bundle package
	 */
	public function download() {
		include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' ); //for wp_upgrader

		$upgrader = new Plugin_Upgrader(
			new Plugin_Upgrader_Skin(
				array(
					'title' => 'MailChimp for WordPress - Premium',
					'plugin' => 'mc4wp-premium'
				)
			)
		);
		$upgrader->install( $this->download_url );
	}


}