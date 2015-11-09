=== WordPress to Buffer ===
Contributors: n7studios,wpcube
Donate link: https://www.wpcube.co.uk/plugins/wordpress-to-buffer-pro
Tags: buffer, bufferapp, buffer app, buffer my post, buffer old post, buffer post, post to buffer, promote old posts, promote posts, promote custom posts, promote selected posts, share posts, bulk share posts, share old posts, social, media, sharing, social media, social sharing, schedule, auto post, auto publish, facebook, facebook post, facebook selected posts, facebook plugin, auto facebook post, post facebook, post to facebook, twitter, twitter post, tweet post twitter selected posts, tweet selected posts twitter plugin, auto twitter post, auto tweet post post twitter, post to twitter, linkedin, linkedin post, linkedin selected posts, linkedin plugin, auto linkedin post, post linkedin, post to linkedin, google, google post, google selected posts, google plugin, auto google post, post google, post to google, pinterest, pinterest post, pinterest selected posts, pinterest plugin, auto pinterest post, post pinterest, post to pinterest, best wordpress social plugin, best wordpress social sharing plugin, best social plugin, best social sharing plugin, best facebook social plugin, best twitter social plugin, best linkedin social plugin, best pinterest social plugin, best google+ social plugin
Requires at least: 3.6
Tested up to: 4.3.1
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Send WordPress Pages, Posts or Custom Post Types to your Buffer (bufferapp.com) account for scheduled publishing to social networks.

== Description ==

WordPress to Buffer is a plugin for WordPress that sends updates to your Buffer (bufferapp.com) account for scheduled publishing to social networks, such as Facebook, Twitter, LinkedIn and Google+, when you publish and/or update WordPress Pages, Posts and/or Custom Post Types.

> #### WordPress to Buffer Pro
> <a href="https://www.wpcube.co.uk/plugins/wordpress-to-buffer-pro/" rel="friend" title="WordPress to Buffer Pro - Publish to Facebook, Twitter, LinkedIn, Google+ and Pinterest">WordPress to Buffer Pro</a> provides additional functionality:<br />
>
> - Pinterest: Post to your Pinterest boards<br />
> - Separate Options per Social Network: Define different statuses for each Post Type and Social Network<br />
> - Post, Author and Custom Meta Tags: Dynamically build status updates with Post, Author and Meta tags<br />
> - Featured Images: Choose to display WordPress Featured Images with your status updates<br />
> - Unlimited Statuses per Profile: Send your publish/update statuses any number of times<br />
> - Individual Settings per Status: Each status update can have its own unique settings<br />
> - Powerful Scheduling: Each status update can be added to the start/end of your Buffer queue, posted immediately or scheduled at a specific time<br />
> - Conditional Publishing: Require taxonomy term(s) to be present for Posts to publish to Buffer<br />
> - Individual Post Settings: Each Post can have its own Buffer settings<br />
> - Detailed Logging: Logging can be enabled to troubleshoot occasional issues<br />
> - WP-Cron: Optionally enable WP-Cron to send status updates via Cron, speeding up UI performance<br />
> - Support: Access to one on one email support<br />
> - Documentation: Detailed documentation on how to install and configure the plugin<br />
> - Updates: Receive one click update notifications, right within your WordPress Adminstration panel<br />
> - Seamless Upgrade: Retain all current settings when upgrading to Pro<br />
>
> [Upgrade to WordPress to Buffer Pro](https://www.wpcube.co.uk/plugins/wordpress-to-buffer-pro/)

= Support =

We will do our best to provide support through the WordPress forums. However, please understand that this is a free plugin, 
so support will be limited. Please read this article on <a href="http://www.wpbeginner.com/beginners-guide/how-to-properly-ask-for-wordpress-support-and-get-it/">how to properly ask for WordPress support and get it</a>.

If you require one to one email support, please consider <a href="http://www.wpcube.co.uk/plugins/wordpress-to-buffer-pro" rel="friend">upgrading to the Pro version</a>.

= WP Cube =
We produce free and premium WordPress Plugins that supercharge your site, by increasing user engagement, boost site visitor numbers
and keep your WordPress web sites secure.

Find out more about us at <a href="https://www.wpcube.co.uk" rel="friend" title="Premium WordPress Plugins">wpcube.co.uk</a>

== Installation ==

1. Upload the `wp-to-buffer` folder to the `/wp-content/plugins/` directory
2. Active the WordPress to Buffer plugin through the 'Plugins' menu in WordPress
3. Configure the plugin by going to the `WordPress to Buffer` menu that appears in your admin menu

== Frequently Asked Questions ==



== Screenshots ==

1. Settings Panel when plugin is first installed.
2. Settings Panel when Buffer Access Token is entered.
3. Settings Panel showing available options for Posts, Pages and any Custom Post Types when the plugin is authenticated with Buffer.
4. Post level settings meta box.

== Changelog ==

= 3.0.2 =
* Fix: Dashboard Feed URL

= 3.0.1 =
* Fix: Shorten links

= 3.0 =
* Fix: WordPress 4.2 compatibility
* Fix: Better security on form submissions

= 2.3.6 =
* Fix: &hellip; HTML character code appearing on Facebook + Google+ status updates when no excerpt defined on a Post

= 2.3.5 =
* Fix: Removed logging

= 2.3.4 =
* Fix: Double posts in Buffer when a scheduled Post goes live.

= 2.3.3 =
* Dropped html_entity_decode and apply_filters on Post Title - causing too many issues.

= 2.3.2 =
* Fix: Settings tabs not working / all settings panels displaying at once
* Added translation support and .pot file 

= 2.3.1 =
* Fix: Issue with characters in the title being HTML encoded

= 2.3 =
* Fix: Uses get_the_title() when generating status updates for social networks
* Fix: Check that at least one social media profile has been chosen before trying to update via the API

= 2.2.1 =
* Fix: Prevent double posting when Posts with category filtering are enabled, and a Post is added via third party apps using the XML RPC API
* Fix: Pages can be posted to Buffer via XML RPC API

= 2.2 =
* Fix: Twitter Images attached to tweets
* Fix: Featured Images on Facebook

= 2.1.8 =
* Fix: Stops URLs and images being stripped from some updates to LinkedIn

= 2.1.7 =
* Fix: Removed unused addPublishActions function

= 2.1.6 =
* Fix: Dashboard widget
* Fix: Some Posts not adding to Buffer due to meta key check

= 2.1.5 =
* Fix: Don't show success message when Post/Page not posted to Buffer
* Fix: Removed Post to Buffer meta box, which wasn't honouring settings / causing double postings
* Settings: changed to tabbed interface

= 2.1.4 =
* Fix: Dashboard: PHP fatal error

= 2.1.3 =
* Fix: Posts with an image no longer show the image link, but instead show the Page / Post URL

= 2.1.2 =
* Fix: Donation Form

= 2.1.1 =
* Fix: Some assets missing from SVN checkin on 2.1

= 2.1 =
* Fix: 'Creating default object from empty value' warning
* Fix: {excerpt} tag working on Pages and Custom Post Types that do not have an Excerpt field
* Fix: Capabilities for add_menu_page
* Fix: Check for page $_GET variable

= 2.0.1 =
* Fix: Removed console.log messages
* Fix: Added Google+ icon for Buffer accounts linked to Google+ Pages

= 2.0 =
* Fix: admin_enqueue_scripts used to prevent 3.6+ JS errors
* Fix: Force older versions of WP to Buffer to upgrade to 2.x branch.
* Fix: Check for Buffer accounts before outputting settings (avoids invalid argument errors).
* Enhancement: Validation of access token to prevent several errors.
* Enhancement: Add callback URL value (not required, but avoids user confusion).
* Enhancement: Check the access token pasted into the settings field is potentially valid (avoids questions asking why the plugin doesn't work,
because the user hasn't carefully checked the access token).

= 1.1 =
* Enhancement: Removed spaces from categories in hashtags (thanks, Douglas!)
* Fix: "Error creating default object from empty value" message.
* Enhancement: Added Featured Image when posting to Buffer, if available.
* Fix: Simplified authentication process using Access Token. Fixes many common oAuth issues.

= 1.03 =
* Fix: Publish hooks now based on settings instead of registered post types, to ensure they hook early enough to work on custom post types.

= 1.02 =
* Fix: Scheduled Posts now post to Buffer on scheduled publication.

= 1.01 =
* SSL verification fix for Buffer API authentication.

= 1.0 =
* First release.

== Upgrade Notice ==

