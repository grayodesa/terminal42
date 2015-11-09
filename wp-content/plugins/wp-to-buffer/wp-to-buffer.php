<?php
/**
* Plugin Name: WP to Buffer
* Plugin URI: http://www.wpcube.co.uk/plugins/wp-to-buffer-pro
* Version: 3.0.2
* Author: WP Cube
* Author URI: http://www.wpcube.co.uk
* Description: Send WordPress Pages, Posts or Custom Post Types to your Buffer (bufferapp.com) account for scheduled publishing to social networks.
* Text Domain: wp-to-buffer
* License: GPL2
*/

/*  Copyright 2015 WP Cube (email : support@wpcube.co.uk)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
* WP to Buffer Class
* 
* @package WP Cube
* @subpackage WP to Buffer
* @author Tim Carr
* @version 3.0.2
* @copyright WP Cube
*/
class WPToBuffer {

    /**
    * Constructor.
    */
    function __construct() {

        // Plugin Details
        $this->plugin               = new stdClass;
        $this->plugin->name         = 'wp-to-buffer'; // Plugin Folder
        $this->plugin->settingsName = 'wp-to-buffer';
        $this->plugin->displayName  = 'WP to Buffer'; // Plugin Name
        $this->plugin->version      = '3.0.2';
        $this->plugin->folder       = plugin_dir_path( __FILE__ );
        $this->plugin->url          = plugin_dir_url( __FILE__ );

        // Upgrade Reasons
        $this->plugin->upgradeReasons = array();
        $this->plugin->upgradeReasons[] = array(
            __( 'Pinterest', $this->plugin->name ), 
            __( 'Post to your Pinterest boards', $this->plugin->name ),
        );
        $this->plugin->upgradeReasons[] = array(
            __( 'Separate Options per Social Network', $this->plugin->name ), 
            __( 'Define different statuses for each Post Type and Social Network', $this->plugin->name ),
        );
        $this->plugin->upgradeReasons[] = array(
            __( 'Post, Author and Custom Meta Tags', $this->plugin->name ), 
            __( 'Dynamically build status updates with Post, Author and Meta tags', $this->plugin->name ),
        );
        $this->plugin->upgradeReasons[] = array(
            __( 'Featured Images', $this->plugin->name ), 
            __( 'Choose to display WordPress Featured Images with your status updates', $this->plugin->name ),
        );
        $this->plugin->upgradeReasons[] = array(
            __( 'Unlimited Statuses per Profile', $this->plugin->name ), 
            __( 'Send your publish/update statuses any number of times', $this->plugin->name ),
        );
        $this->plugin->upgradeReasons[] = array(
            __( 'Individual Settings per Status', $this->plugin->name ), 
            __( 'Each status update can have its own unique settings', $this->plugin->name ),
        );
        $this->plugin->upgradeReasons[] = array(
            __( 'Powerful Scheduling', $this->plugin->name ), 
            __( 'Each status update can be added to the start/end of your Buffer queue, posted immediately or scheduled at a specific time', $this->plugin->name ),
        );
        $this->plugin->upgradeReasons[] = array(
            __( 'Conditional Publishing', $this->plugin->name ), 
            __( 'Require taxonomy term(s) to be present for Posts to publish to Buffer', $this->plugin->name ),
        );
        $this->plugin->upgradeReasons[] = array(
            __( 'Individual Post Settings', $this->plugin->name ), 
            __( 'Each Post can have its own Buffer settings', $this->plugin->name ),
        );
        $this->plugin->upgradeReasons[] = array(
            __( 'Bulk Publishing', $this->plugin->name ), 
            __( 'Publish multiple Posts, Pages and Custom Post Types to Buffer', $this->plugin->name ),
        );
        $this->plugin->upgradeReasons[] = array(
            __( 'Detailed Logging', $this->plugin->name ), 
            __( 'Logging can be enabled to troubleshoot occasional issues', $this->plugin->name ),
        );
        $this->plugin->upgradeReasons[] = array(
            __( 'WP-Cron', $this->plugin->name ), 
            __( 'Optionally enable WP-Cron to send status updates via Cron, speeding up UI performance', $this->plugin->name ),
        );
        $this->plugin->upgradeURL = 'http://www.wpcube.co.uk/plugins/wp-to-buffer-pro';
        
        // Settings
		$this->plugin->ignorePostTypes = array( 'attachment', 'revision', 'nav_menu_item') ;      
		$this->plugin->publishDefaultString = 'New Post: {title} {url}';
		$this->plugin->updateDefaultString = 'Updated Post: {title} {url}';
		
        // Dashboard Submodule
        if ( ! class_exists( 'WPCubeDashboardWidget' ) ) {
			require_once( $this->plugin->folder . '/_modules/dashboard/dashboard.php' );
		}
		$dashboard = new WPCubeDashboardWidget( $this->plugin ); 
		
		// Hooks
        add_action( 'admin_enqueue_scripts', array( &$this, 'admin_scripts_css' ) );
        add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
        add_action( 'admin_notices', array( &$this, 'admin_notices' ) ); 
        add_action( 'wp_loaded', array( &$this, 'register_publish_hooks' ) );
        add_action( 'plugins_loaded', array( &$this, 'load_language_files' ) );

    }
    
    /**
    * Registers publish hooks against all public Post Types
    */
    function register_publish_hooks() {   

    	$types = get_post_types( array(
    		'public' => true,
    	) );
    	foreach ( $types as $type ) {
    		add_action( 'publish_' . $type, array( &$this, 'publish_now' ) );
			add_action( 'publish_future_' . $type, array( &$this, 'publish_future' ) );	
    	}

    }
    
    /**
    * Register and enqueue any JS and CSS for the WordPress Administration
    */
    function admin_scripts_css() {

    	// JS
    	wp_enqueue_script( $this->plugin->name . '-admin', $this->plugin->url . 'js/admin.js', array( 'jquery' ), $this->plugin->version, true );
    	        
    	// CSS
        wp_enqueue_style( $this->plugin->name . '-admin', $this->plugin->url . 'css/admin.css', array(), $this->plugin->version ); 

    }
    
    /**
    * Register the plugin settings panel
    */
    function admin_menu() {

        add_menu_page( $this->plugin->displayName, $this->plugin->displayName, 'manage_options', $this->plugin->name, array(&$this, 'admin_screen'), $this->plugin->url . 'images/icons/small.png' );
    
    }
    
    /**
    * Outputs a notice if:
    * - Buffer hasn't authenticated i.e. we do not have an access token
    * - A Post has been sent to Buffer and we have a valid message response
    */
    function admin_notices() {

        // Don't check on plugin main page
        if ( isset( $_GET['page'] ) && $_GET['page'] == $this->plugin->name ) {
            return false; 
        }

        // Get settings
        $this->settings = get_option( $this->plugin->name );
        
        // Check if no access token
        if ( ! isset( $this->settings['accessToken'] ) || empty( $this->settings['accessToken'] ) ) {
        	echo ( ' <div class="error"><p>' . $this->plugin->displayName . ' requires authorisation with Buffer in order to post updates to your account.
        			Please visit the <a href="admin.php?page=' . $this->plugin->name . '" title="Settings">Settings Page</a> to grant access.</p></div>' );
            return false;	
        }
        
        // Output success and/or error messages if we are on a post and it has a meta key
        if ( isset( $_GET['message'] ) && isset( $_GET['post'] ) ) {
        	// Success
        	$success = get_post_meta( $_GET['post'], $this->plugin->settingsName . '-success', true );
        	if ($success == 1) {
        		// Get Message
        		$message = get_post_meta( $_GET['post'], $this->plugin->settingsName . '-success-message', true );
        		$message = ( ( ! empty( $message ) && trim( $message ) != '' ) ? $message : __( 'Post added to Buffer successfully', $this->plugin->name ) );
 				
 				// Output + clear meta
        		echo ( '<div class="updated success"><p>' . $this->plugin->displayName . ': ' . $message . '</p></div>' );
        		delete_post_meta( $_GET['post'], $this->plugin->settingsName . '-success' );	
        		delete_post_meta( $_GET['post'], $this->plugin->settingsName . '-success-message' );
        	}
        	
        	// Error
        	$error = get_post_meta( $_GET['post'], $this->plugin->settingsName . '-error', true );
        	if ($error == 1) {
        		echo ('<div class="error"><p>' . get_post_meta( $_GET['post'], $this->plugin->settingsName . '-error-message', true ) . '</p></div>' );
        		delete_post_meta( $_GET['post'], $this->plugin->settingsName . '-error' );
        		delete_post_meta( $_GET['post'], $this->plugin->settingsName . '-error-message' );	
        	}
        }

    } 
    
    /**
    * Alias function called when a post is published or updated
    *
    * Passes on the request to the main Publish function
    *
    * @param int $postID Post ID
    */
    function publish_now( $postID ) {

    	$this->publish( $postID );

    }
    
    /**
    * Alias function called when a post, set to be published in the future, reaches the time
    * when it is being published
    *
    * Passes on the request to the main Publish function
    *
    * @param int $postID Post ID
    */
    function publish_future( $postID ) {

    	$this->publish( $postID, true );

    }
    
    /**
    * Called when any Page, Post or Custom Post Type is published or updated, live or for a scheduled post
    *
    * @param int $postID Post ID
    */
    function publish( $postID, $isPublishAction = false ) {
    	$defaults = get_option($this->plugin->settingsName); // Get settings
        if (!isset($defaults['accessToken']) OR empty($defaults['accessToken'])) return false; // No access token so cannot publish to Buffer
        
        // Get post
        $post = get_post($postID);
        
        // If request has come from XMLRPC, force $isPublishAction
        if (defined('XMLRPC_REQUEST')) {
        	$isPublishAction = true;
        }
        
        // Assume we don't publish to Buffer
    	$updateType = '';
    	$doPostToBuffer = false;
        
        // Check at least one account is enabled
        if (!isset($defaults['ids'])) {
        	return false;
        }
        if (!isset($defaults['ids'][$post->post_type])) {
        	return false;
        }

		// Determine if this is a publish or update action
        if ($_POST['original_post_status'] == 'draft' OR 
        	$_POST['original_post_status'] == 'auto-draft' OR 
        	$_POST['original_post_status'] == 'pending' OR
        	$_POST['original_post_status'] == 'future' OR
        	$isPublishAction) {
        	
        	// Publish?
        	if ($defaults['enabled'][$post->post_type]['publish'] != '1') return false; // No Buffer needed for publish
        	$updateType = 'publish';
        	$doPostToBuffer = true; 
        }
        
		if ($_POST['original_post_status'] == 'publish') {
        	// Update?
        	if ($defaults['enabled'][$post->post_type]['update'] != '1') return false; // No Buffer needed for update
        	$updateType = 'update';
        	$doPostToBuffer = true;
        }
        
        // If not posting to Buffer, exit
        if (!$doPostToBuffer) {
	    	return false;
	    }
        
		// 1. Get post categories if any exist
		$catNames = '';
		$cats = wp_get_post_categories($postID, array('fields' => 'ids'));
		if (is_array($cats) AND count($cats) > 0) {
			foreach ($cats as $key=>$catID) {
				$cat = get_category($catID);
				$catName = strtolower(str_replace(' ', '', $cat->name));
				$catNames .= '#'.$catName.' ';
			}
		}
		
		// 2. Get author
		$author = get_user_by('id', $post->post_author);
		
		// 3. Check if we have an excerpt. If we don't (i.e. it's a Page or CPT with no excerpt functionality), we need
		// to create an excerpt
		if (empty($post->post_excerpt)) {
			$excerpt = wp_trim_words(strip_shortcodes($post->post_content));
		} else {
			$excerpt = $post->post_excerpt;
		}
		
		// 3a. Decode certain entities for FB + G+ compatibility
		$excerpt = str_replace('&hellip;', '...', $excerpt);
		
		// 4. Parse text and description
		$params['text'] = $defaults['message'][$post->post_type][$updateType];
		$params['text'] = str_replace('{sitename}', get_bloginfo('name'), $params['text']);
		$params['text'] = str_replace('{title}', $post->post_title, $params['text']);
		$params['text'] = str_replace('{excerpt}', $excerpt, $params['text']);
		$params['text'] = str_replace('{category}', trim($catNames), $params['text']);
		$params['text'] = str_replace('{date}', date('dS F Y', strtotime($post->post_date)), $params['text']);
		$params['text'] = str_replace('{url}', rtrim(get_permalink($post->ID), '/'), $params['text']);
		$params['text'] = str_replace('{author}', $author->display_name, $params['text']);
		
		// 5. Check if we can include the Featured Image (if available) in the media parameter
		// If not, just attach the Post URL
		$media['link'] = rtrim(get_permalink($post->ID), '/');
		$featuredImageID = get_post_thumbnail_id($postID);
		if ($featuredImageID > 0) {
			// Get image source
			$featuredImageSrc = wp_get_attachment_image_src($featuredImageID, 'large');
			if (is_array($featuredImageSrc)) {
				$media['title'] = $post->post_title; // Required for LinkedIn to work
				$media['picture'] = $featuredImageSrc[0];
				$media['thumbnail'] = $featuredImageSrc[0];
				$media['description'] = $post->post_title;
				unset($media['link']); // Important: if set, this attaches a link and drops the image!
			}
		}
		
		// Assign media array to media argument
		$params['media'] = $media;
		
		// 6. Add profile IDs
		foreach ($defaults['ids'][$post->post_type] as $profileID=>$enabled) {
			if ($enabled) $params['profile_ids'][] = $profileID; 
		}

        // 6a. Shorten Links
        $params['shorten'] = true;
		
		// 7. Send to Buffer
		delete_post_meta($postID, $this->plugin->settingsName.'-success');
		delete_post_meta($postID, $this->plugin->settingsName.'-error');
		$result = $this->request($defaults['accessToken'], 'updates/create.json', 'post', $params);
		
		if (is_object($result)) {
			update_post_meta($postID, $this->plugin->settingsName.'-success', 1);
		} else {
			update_post_meta($postID, $this->plugin->settingsName.'-error', 1);
		}
    }
    
	/**
    * Output the Administration Panel
    * Save POSTed data from the Administration Panel into a WordPress option
    */
    function admin_screen() {

    	// Save Settings
        if (isset($_POST['submit'])) {

            // Run security checks
            // Missing nonce 
            if ( ! isset( $_POST[ $this->plugin->name . '_nonce' ] ) ) { 
                $this->errorMessage = __( 'Nonce field is missing. Settings NOT saved.', $this->plugin->name );
            } elseif ( ! wp_verify_nonce( $_POST[$this->plugin->name.'_nonce'], $this->plugin->name ) ) {
                $this->errorMessage = __('Invalid nonce specified. Settings NOT saved.', $this->plugin->name );
            } else {
            	// Check the access token, in case it hasn't been copied / pasted correctly
            	// This happens when you double click the Access Token on http://bufferapp.com/developers/apps, which doesn't
            	// quite select the entire access token
            	$tokenLength = strlen($_POST[$this->plugin->name]['accessToken']);
            	if ($tokenLength > 0) {
            		// Check if token is missing 1/ at the start
            		if (substr($_POST[$this->plugin->name]['accessToken'], 0, 2) != '1/') {
            			// Missing
            			$this->errorMessage = __('Oops - you\'ve not quite copied your access token from Buffer correctly. It should start with 1/. Please try again.');
            		} elseif (substr($_POST[$this->plugin->name]['accessToken'], $tokenLength-4, 4) == 'Edit') {
            			$this->errorMessage = __('Oops - you\'ve not quite copied your access token from Buffer correctly. It should not end with the word Edit. Please try again.');
            		}
            	} else {
            		$this->errorMessage = __('Please enter an access token to use this plugin. You can obtain one by following the instructions below.');
            	}
            }
        	
        	// Test access token to make sure it's valid
        	if (!isset($this->errorMessage)) {
        		$user = $this->Request($_POST[$this->plugin->name]['accessToken'], 'user.json');
        		if (!is_object($user)) {
        			$this->errorMessage = $user;
        		} else {
        			// Ok - save
        			update_option($this->plugin->name, $_POST[$this->plugin->name]);
            		$this->message = __('Settings Updated.');
        		}	
            }
        }
        
        // Disconnect?
        if (isset($_GET['disconnect'])) {
        	$this->settings = get_option($this->plugin->name);
        	$this->settings['accessToken'] = '';
        	update_option($this->plugin->name, $this->settings);	
        }
        
        // Get latest settings
        $this->settings = get_option($this->plugin->name);
        
        // If we have an access token, try to get the user's profile listing their accounts
        $this->buffer = new stdClass;
        if ($this->settings['accessToken'] != '') {
        	$profiles = $this->Request($this->settings['accessToken'], 'profiles.json');
        	if (is_wp_error($profiles)) {
        		$this->errorMessage = $profiles->get_error_message().'. '.__('Some functionality on this screen may not work correctly.');
        	} else {
        		$this->buffer->accounts = $profiles;
        	}
        }
        
        // Get selected tab
		$this->tab = (isset($_GET['tab']) ? $_GET['tab'] : 'auth');
        
		// Load Settings Form
        include_once(WP_PLUGIN_DIR.'/'.$this->plugin->name.'/views/settings.php');  
    }
    
    /**
    * Loads plugin textdomain
    */
    function load_language_files() {

    	load_plugin_textdomain( $this->plugin->name, false, $this->plugin->name . '/languages/' );

    }
    
    /**
    * Sends a GET request to the Buffer API
    *
    * @param string $accessToken Access Token
    * @param string $cmd Command
    * @param string $method Method (get|post)
    * @param array $params Parameters (optional)
    * @return mixed JSON decoded object or error string
    */
    function request($accessToken, $cmd, $method = 'get', $params = array()) {
    	// Check for access token
    	if ($accessToken == '') return __('Invalid access token', $this->plugin->name);
		
		// Send request
		switch ($method) {
			case 'get':
				$result = wp_remote_get('https://api.bufferapp.com/1/'.$cmd.'?access_token='.$accessToken, array(
		    		'body' => $params,
		    		'sslverify' => false
		    	));
				break;
			case 'post':
				$result = wp_remote_post('https://api.bufferapp.com/1/'.$cmd.'?access_token='.$accessToken, array(
		    		'body' => $params,
		    		'sslverify' => false
		    	));
				break;
		}
    	
    	// Check the request is valid
    	if (is_wp_error($result)) return $result;
		if ($result['response']['code'] != 200) return 'Error '.$result['response']['code'].' whilst trying to authenticate: '.$result['response']['message'].'. Please try again.';

		return json_decode($result['body']);		
    }
}

// Invoke class
$WPToBuffer = new WPToBuffer();