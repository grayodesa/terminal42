=== Better Google Analytics ===
Contributors: digitalpoint
Tags: analytics, google analytics, universal analytics, statistics, tracking, code, dashboard, analytics dashboard, google analytics dashboard, google analytics plugin, google analytics widget, reports, charts, multisite, api, stats, web stats, visits, javascript, pageviews, marketing, widget, realtime, real time, youtube, outbrain, taboola, adsense, twitter, pinterest, linkedin, facebook, google, digitalpoint, ab testing, ab tests, split testing, google analytics content experiments, content experiments
Donate link: https://marketplace.digitalpoint.com/better-analytics.3354/item#utm_source=readme&utm_medium=wordpress&utm_campaign=plugin
Requires at least: 3.8
Tested up to: 4.5
Stable tag: 1.1.4
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Track everything with Google Analytics (clicked links, emails opened, YouTube videos being watched, etc.). Includes real time Analytics dashboard.

== Description ==
The Better Google Analytics plugin allows you to easily add Google Analytics code to your website and gives you the power to track virtually everything.  Better Google Analytics includes heat maps, reports, charts, events and site issue tracking in your WordPress admin area without the need to log into your Google Analytics account.

Better Google Analytics allows you to manage your Google Analytics account from within your WordPress admin area (for example you can create/edit Google Analytics Goals).

In addition, Better Google Analytics makes A/B (split) testing a breeze (no code required) so you can find what variations work the best for your site by using Google Analytics Content Experiments.

> <strong>Simple To Setup</strong><br>
>
> The Better Google Analytics plugin can automatically configure your Google Analytics account to maximize what you can track with the minimum amount of effort.  Can automatically set various options on your Google Analytics account on your behalf as well as create custom dimensions and map them to where they need to go.  If you are setting up a brand new site, this plugin can even create a brand new Web Property within your selected Google Analytics account if you wish.  All with a single click.

Better Google Analytics utilizes all the latest and greatest features of Google Analytics (Universal analytics, user-ID session unification, event tracking, campaign tracking, custom dimensions, server-side tracking, social engagement tracking, remarketing, etc.)

> <strong>Google Analytics Account Management</strong><br>
>
> * The Better Google Analytics plugin can create a new web property/profile on your Google Analytics account if needed (if it's a new site)
> * Auto-Configure system will set certain options automatically on your Google Analytics account if needed
> * Auto-Configure system can create custom dimensions for your Google Analytics Web Property and map them to the appropriate Better Google Analytics settings
> * Google Analytics Goal Management system allows you to create, edit, activate and disable Google Analytics Goals from within your WordPress admin area
> * Google Analytics Content Experiment Management allows you to run A/B (split) testing variations easily

If you link your Google Analytics account, the Better Google Analytics plugin is able to make extensive use of the Google Analytics API to give you a plethora of reporting options (both historical and realtime).  Google Analytics API calls are cached to make them as fast as possible.

Better Google Analytics uses lightweight (and client-side cacheable) JavaScript to give your users the fastest possible experience on your website.  Fully compatible with multisite network setups.

= Better Google Analytics Basic Tracking Features (each can be enabled/disabled): =

* Link Attribution
* User-ID/Session Unification
* User Engagement
* Comment Creation
* User Registration
* YouTube Video Engagement
* Emails Sent/Opened
* External Link Clicks
* File Downloads
* Page Scroll Percent
* Time On Page
* Anonymize IPs
* Demographic & Interest
* Force Google Analytics Traffic Over SSL
* RSS/Email Link Source Tracking
* Advertising Ad Clicks
* Page Not Found (404)
* AJAX Requests

 = Better Google Analytics Dimension Tracking: =

* Categories
* Author
* Tags
* Publication Year
* User Role
* User

= Better Google Analytics Social Button Engagement Tracking: =

* Facebook
* Twitter
* Google+
* Pinterest
* LinkedIn

= Better Google Analytics Reporting Features (can be viewed site-wide or for individual page/URL): =

* Dashboard Charts (real time or historical)
* Weekly Heat Maps
* Historical Area Percent Charts
* Events
* Issue Monitoring

= Better Google Analytics Advanced Features: =

* Suppress Google Analytics Tracking By User Role
* View Analytics Reports By User Role
* Adjust Location Of Google Analytics Code
* Google Analytics Campaign Tracking By Anchor or Parameters
* Adjustable Analytics Sample Rate
* Insert Your Own Custom Google Analytics JavaScript
* Debugging Mode

= Better Google Analytics Widgets Included: =

* Popular Posts
* Statistics based on selectable Analytics metric

> <strong>Better Analytics Pro</strong><br>
>
> If you would like additional advanced functions for Google Analytics, we offer a Pro version.
>
> * Additional ad networks for ad click tracking
> * More options for site issue monitoring
> * More heat map metrics
> * More charting dimensions
> * More objective metrics for A/B tests (Google Analytics Content Experiments)
> * eCommerce tracking (coming soon)
> * Option for server-side tracking of users (or bots)
> * Faster Google Analytics API calls (uses a custom system for parallel requests)
> * Priority support
>
> [Pro license available here](https://marketplace.digitalpoint.com/better-analytics-pro.3355/item#utm_source=readme&utm_medium=wordpress&utm_campaign=plugin)

== Installation ==
1. Upload `better-analytics` folder to the `/wp-content/plugins/` directory.
1. Activate the Better Google Analytics plugin through the 'Plugins' menu in the WordPress admin area.
1. Link your Google Analytics account under 'Settings -> Better Analytics -> API'.
1. Pick your Google Analytics Web Property ID under 'Settings -> Better Analytics -> General'.

It's probably a good idea to disable any other Google Analytics plugins/systems you may have installed, unless you are intentionally wanting to feed data into multiple Analytics Web Properties.

= Google PageSpeed =
If you are using Google PageSpeed to combine JavaScript files, you will want to define an exclusion for the Better Google Analytics JavaScript file with the following directives:

> <strong>Apache</strong><br>
> ModPagespeedDisallow "\*better-analytics/js/loader.php\*"

> <strong>Nginx</strong><br>
> pagespeed Disallow "\*better-analytics/js/loader.php\*"

== Frequently Asked Questions ==
= What are the requirements of the Better Google Analytics plugin? =
You need a WordPress site (of course), running WordPress 3.8 or higher and a Google Analytics account (which is [free over here](https://www.google.com/analytics/) if you don't already have a Google Analytics account).

= Can Better Google Analytics be used with legacy Google Analytics code? =
No, the Better Google Analytics plugin is for Google Universal Analytics.  And really, you wouldn't want to.  All Google Analytics accounts have been upgraded to Universal Analytics, and the [non-Universal tracking libraries have been deprecated](https://developers.google.com/analytics/devguides/collection/upgrade/) by Google.

= What do I do with the JavaScript code that Google gives me from within my Google Analytics account? =
Nothing.  Better Google Analytics handles all the JavaScript code generation internally.  If you want to add your own custom JavaScript, there is an option for that under the Advanced settings.

= Can you add [insert feature here] to Better Google Analytics? =
If it's possible and it makes sense, then yes.  The best way to put in a feature request for Better Google Analytics would be to create a thread in the [support forum over here](https://forums.digitalpoint.com/forums/better-analytics.31/#utm_source=readme&utm_medium=wordpress&utm_campaign=plugin).

= How does the A/B testing system work with Google Analytics Content Experiments? =
The Google Analytics Content Experiments are run automatically on the server-side, so there is no special configuration or JavaScript you need to be injecting.  You also don't need to make duplicate pages with your variations.  We've simplified it so anyone can easily run A/B testing on their site with Google Analytics Content Experiments.

= I speak a language that isn't supported by Better Google Analytics, can I help translate it? =
Yes.  Unfortunately we don't speak every language in the world, so if you would like to help with translating the Better Google Analytics plugin, you can use the [translation system on wordpress.org](https://translate.wordpress.org/projects/wp-plugins/better-analytics).

= Does Better Google Analytics Support A WordPress Multisite Network? =
Yes, you can install the Better Google Analytics plugin for a single site in the network or for all sites in the network.  Additionally, you can optionally link a single Google Analytics account for all sites in the network (or you can link unique Google Analytics accounts for each site in the network... either way, it's up to you).

= Do you have access to our Google Analytics data? =
In no way, shape, or form do we have access to your Google Analytics data.

= How can I ensure you don't really have access to my Google Analytics data? =
The way OAuth2 works with your Google Analytics account, it wouldn't be possible for us to access your Google Analytics data even if we wanted to (which we don't).  If you utilize the default Google Analytics API project credentials, the system will ask you for permission to access your data.  Google will then issue a one-time use code that is exchanged for OAuth2 credentials that are used when making Google Analytics API calls.  <strong>The code is one-time use</strong> (meaning if someone intercepted it and redeemed it for credentials, you wouldn't be able to yourself).  After your site redeems the code, it's no longer valid.  The resulting credentials are stored inside your installation and are never sent anywhere.  That being said, if you are still worried about the security of your Google Analytics data, you are able to utilize your own Google Analytics API project credentials (it's just a little more work for you to set up that Google API project - the only API type that you need to enable under that project is the Google Analytics API).

== Note To Other Google Analytics Plugin Authors ==

Truthfully, there are far too many Google Analytics plugins for WordPress that generally do the same thing (especially the ones that simply add Google Analytics code to WordPress pages).  If any Google Analytics plugin authors are interested in somehow consolidating plugins, let us know (not really sure how that would work to be honest, but something we could brainstorm and figure out the best way to do it that works for everyone).

== CDN ==

The JavaScript used by Better Google Analytics should be able to be cached properly by content delivery networks (it has been tested with CloudFlare).  This means if your site uses CloudFlare, the JavaScript code used by Google Analytics will be cached in their data centers and delivered to end users via the closest data center (long story short is that it will make for a faster user experience).

== Thanks ==

Thank you to all the individuals who have contributed translations for Better Google Analytics (please send us a note if you have helped translating Better Google Analytics):

* Indonesian: [Arick](http://www.developingwp.com/#utm_source=readme&utm_medium=wordpress&utm_campaign=plugin)

== Screenshots ==

1. Google Analytics dashboard in real time mode.
2. Google Analytics dashboard showing page views by normalized categories for the last month.
3. Google Analytics dashboard showing organic search traffic by country for the last 3 months.
4. Google Analytics dashboard showing sessions by date for the last 3 months.
5. Google Analytics dashboard showing the publication year of posts being viewed by users coming in through organic search for the last month.
6. Google Analytics dashboard showing page views by normalized tags for the last month.
7. One of over 2,000 metric/segment combinations for Google Analytics weekly heat maps (showing all sessions for the last 4 weeks).
8. Stacked area percent charts of your Google Analytics data allow you to see historical changes (browser usage for the last 10 years shows the rise of Chrome and the fall of Internet Explorer).
9. Better Google Analytics event report shows things like external links being clicked, YouTube video engagement, comments being created, etc.  You are able to correlate that data against any other metrics from your Google Analytics account.  For example maybe you wanted to see what countries users are in that watch YouTube videos.
10. Better Google Analytics issue monitoring report alerts to you client-side issues with your site.  Things like invalid pages being accessed (404), JavaScript errors, images not loading, embedded YouTube videos that the author removed, etc.
11. An automated system that is able to check your Google Analytics account and helps you configure your Google Analytics web property settings properly is included.
12. A built-in auto-configure mode takes the hassle out of setting up your Google Analytics account for all tracking options.
13. Better Google Analytics includes an optional front-end widget that shows popular pages/posts being viewed right now (data comes from Google Analytics Real Time API).
14. Better Google Analytics includes an optional front-end widget that allows you to display your Google Analytics stats based on any metric you wish.
15. Better Google Analytics has a full-blown goal management system that allows you to create and edit all the goals on your Google Analytics profile.
16. You have the ability to edit/create your Google Analytics Goals from within the WordPress admin area.  Includes advanced Google Analytics Goal functions such as funnels.
17. Better Google Analytics includes a fully integrated system for managing Google Analytics Content Experiments (split or A/B testing).
18. A Google Analytics Content Experiment showing CSS variations where the site owner is looking for the variation that yields the most page views.
19. A Google Analytics Content Experiment setup to utilize different WordPress themes to determine which variation results in the most AdSense ads being clicked.
20. Google Analytics Content Experiments can be used to run variations of the title of any individual post/page.
21. Better Google Analytics General settings allows you to enable/disable all sorts of tracking features in your Google Analytics account.
22. Google Google Analytics custom dimension tracking allows you to track categories, authors, tags, publication year, user roles and registered users.
23. Social button engagement allows you to track things like Likes/Unlikes/Tweets/Shares right within your Google Analytics account.
24. Track clicks on the ads on your site within your Google Analytics account.
25. Issue monitoring settings allow you to utilize your Google Analytics account to keep on top of client-side issues with your site.
26. Advanced settings allow you to fine tune how the system works with Google Analytics.

== Changelog ==
= 1.1.4 =
* Updated options container to pass W3C validation
* Updated for WordPress 4.5

= 1.1.3 =
* Feature: New option under Advanced tab - Hide "API Not Linked" Notice
* Feature: New option under Advanced tab - Ability to limit access to settings to current admin account
* Enhancement: Added option for tracking downloads of .dmg files
* Fixed cosmetic issue within Goal Management with WordPress 4.4
* Updated for WordPress 4.4

= 1.1.2 =
* Feature: User Engagement Time can be set by the user (under Advanced settings tab)
* Bug: Workaround Internet Explorer not having location origin variable available

= 1.1.1 =
* Enhancement: Changed how JavaScript loads so it still works with other plugins that throw JavaScript exceptions/errors (should also allow capturing of those errors within the Issue Monitoring area of Better Google Analytics)
* Enhancement: Reintroduced option to put Google Analytics code in the page header (changed how loading system works to make it viable with running Google Analytics Content Experiments)

= 1.1.0 =
* Feature: Google Analytics Content Experiments (A/B Testing) for post titles, page titles, CSS and themes
* Feature: Google Analytics Event tracking for Page Scroll Percent (percentage of the page a user scrolled down before they left the page)
* Feature: Google Analytics Event tracking for Time On Page (the number of seconds the user stayed on the page)
* Security: Added user role checking for each type of Google Analytics Content Experiments
* Change: Removed option to put Google Analytics code in the page header (needs to be in the footer for the new Google Analytics Content Experiments to work)
* Change: Individual Google Analytics Goals that are being activated/deactivated, rely on WordPress's internal function to hide _wpnonce URL variable
* Enhancement: Compiled version of live Google Analytics Content Experiments are automatically rebuilt hourly via cron (for multi-armed bandit experiments with variable weighting)
* Enhancement: Check if the object returned from making an HTTP request is a WP_Error object and display the error message if so
* Enhancement: Admin top menu bar shows the number of running Google Analytics Content Experiments when there are any
* Usability: Added title attribute on admin menu for screen readers
* Bug: Issue where secondary bulk actions drop-down at bottom of Google Analytics Goal management table wouldn't work
* Bug: Issue where the "Add Variation" button would sometimes not do anything when creating new Google Analytics Content Experiments
* Bug: Cosmetic issue with green border for enabled Google Analytics Goals on the display table

= 1.0.10 =
* Change: Removed async attribute from script tag for better compatibility with certain themes
* Enhancement: Set Google Analytics sample rate to 100 if something internal to WordPress removes that setting somehow
* Enhancement: Updated Google Analytics Goal management to take advantage of new responsive table methods in WordPress 4.3
* Enhancement: Google Analytics Experiments allow adding custom third-party experiment types via hooks and actions
* Security: Added input sanitization callback to settings
* Flagged for WordPress 4.3.0 compatibility

= 1.0.9 =
* Feature: Google Analytics Stats Widget has two new options that allows you to show stats on a per page basis if you want as well as making the widget private (only viewable to certain roles)
* Enhancement: Disallow PHP auto_append_file directive within the JavaScript loader
* Enhancement: Added a 1 Day option for the Google Analytics dashboard widget
* Flagged for WordPress 4.2.3 compatibility

= 1.0.8 =
* Made a few minor cosmetic changes to Google Analytics Goal list view (including responsive)
* Moved Web Property ID slot on Test Setup tool to make it clear a new Web Property can be automatically created if needed (new site)
* Changed how settings are passed to JavaScript code to make plugin more compatible with systems that attempt to alter/consolidate JavaScript files (Google PageSpeed and WordPress caching systems)
* Feature: Advanced setting that allows the author of a post to view Page Analytics regardless of role
* Added stubs and API calls for Google Analytics Experiments management

= 1.0.7 =
* Feature: Added support for tracking RevContent ad clicks
* Feature: Manage Google Analytics Goals from within WordPress admin area (create, edit, enable, disable, etc.)
* Feature: Google Analytics Goal management includes all four types of goals (destination URL [including optional funnels], session duration, page views per session, events)
* Change: Google Analytics Goal management requires different Google Analytics API credentials (needs edit access)
* Change: Option to Auto-Configure Google Analytics account no longer requires a separate permissions request since the base Google Analytics account permissions now include edit access
* Note: You can authenticate for the new Google Analytics permissions under Analytics -> Settings -> API -> Link/Authenticate A Different Google Analytics Account
* Note: If you do not authenticate your Google Analytics account for new permissions, all existing features you have always had will continue to work just fine (it's only needed when trying to edit/add new Google Analytics Goals from within the WordPress admin)

= 1.0.6 =
* Added stubs for Better Google Analytics eCommerce module
* Bug: Fixed cosmetic issue on "Test Setup" page when you have linked a Google Analytics with multiple profiles that all share the same web property ID
* Bug: Prevent converting emails to HTML if something else already converted it to HTML beforehand
* Feature: Auto-Configure (if your Google Analytics account is linked, this option will be available on the "Test Setup" page) - will configure your Google Analytics property and profile via the Google Analytics API (can set industry vertical, site search parameters, Ecommerce tracking options, create custom dimensions and then map those as necessary)
* Feature: Added option to create a new Web Property & Profile/View under your existing Google Analytics account automatically (useful if you are setting up a new site)
* Feature: Added better_analytics_metrics WordPress filter to hook into Google Analytics metrics list
* Feature: Added better_analytics_dimensions WordPress filter to hook into Google Analytics dimensions list
* Feature: Added better_analytics_segments WordPress filter to hook into Google Analytics segments list
* Enhancement: If user has already linked a Google account without Google Analytics access, discard the authentication tokens
* Enhancement: If a user revokes OAuth2 access to their Google Analytics account, discard the authentication tokens
* Enhancement: Check for Google Analytics account access when user links a new Google account
* Enhancement: Check if Google Analytics Web Property/Profile would benefit from updating before blindly doing it when requested by site admin
* Enhancement: Check Google Analytics API quota when using write functions when Auto-Configure function is making changes to Google Analytics account (write operation quota is more tightly controlled by Google)
* Enhancement: Added 3 additional Google Analytics dimensions for reporting on dashboard widget
* Usability: Added links to help new users with configuration (link to create new Google Analytics account, link to configure Google Analytics API, link to new Auto-Configuration area, etc.)
* Security: When requesting Google Analytics API edit permissions (when Auto-Configuration is used), make that permissions request only for a one-time use token (don't store or have long-term tokens that allow write access)

= 1.0.5 =
* Enhancement: Made some changes so that Better Google Analytics Pro can be uploaded manually from the "Upload Plugin" feature of WordPress
* Added internal framework for future option that allows users to auto-configure their Google Analytics account (waiting for Google Analytics Management API approval from Google before we can roll it out)
* Enhancement: Added 21 additional Google Analytics metrics for reporting
* Enhancement: Added 9 additional Google Analytics dimensions for reporting
* Enhancement: Added 11 additional Google Analytics segments for reporting
* Enhancement: Replaced deprecated Google Analytics metrics
* Enhancement: Replaced deprecated Google Analytics dimensions
* Enhancement: Replaced deprecated Google Analytics segments

= 1.0.4 =
* Bug: Fixed cosmetic formatting issue on settings page when on very thin screens (responsive mobile)
* Feature: Added new Google Analytics Custom Dimension tracking option (Publication Year)
* Feature: Added new Google Analytics Custom Dimension tracking option (User Role)
* Feature: Added ability to optionally have a single linked Google Analytics account for all sites in a multisite network setup
* Feature: Added ability to specify a custom Google Analytics API project ID for a multisite network (similar to how you can already for a single site)
* Added "Verify Domain" link to plugin page when Pro version is installed on an unknown domain

= 1.0.3 =
* Enhancement: Custom Dimensions settings generates a drop-down list of custom dimensions defined within Google Analytics account (if you have a Google Analytics account linked via API)
* Translation: Added a few missed phrases to WordPress translation system
* Removed some unnecessary debugging code

= 1.0.2 =
* Bug: Fixed cosmetic error message when creating a new site in a multi-site setup with debugging on
* Feature: Better Google Analytics heat maps, charts, event tracking and issue monitoring can be viewed on a per page basis via new Page Analytics option on admin bar
* Feature: Better Google Analytics Stats Widget

= 1.0.1 =
* Translation: Indonesian

= 1.0.0 =
* Initial release of Better Google Analytics