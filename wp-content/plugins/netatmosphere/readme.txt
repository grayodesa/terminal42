=== NetAtmoSphere ===
Contributors: ToniMa
Donate link: http://www.teni.at/
Tags: netatmo, weather, wordpress, widget, shortcode, weather data
Version: 2.0.16
Requires at least: 4.0
Tested up to: 4.5
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin gives you the ability to display the weather data provided by your Netatmo weather station on your Wordpress website.

== Description ==

**PREAMBLE:**
This plugin is developed and maintained by [ToniMa](http://www.teni.at "ToniMa Homepage") and has no any relation to [NetAtmo](http://www.netatmo.com "NetAtmo Homepage").
Probably they dont even know that it is existing. :)

**FEATURES:**
This plugin gives you the ability to display the weather data provided by your NetAtmo weather station on your Wordpress website.

In particular:

* **netatmo caching**:
  - You just need to login and the plugin will display you all the devices and modules what are linked to that account.
  - All the devices connected (including "friend" netatmo stations) to that account will be cached in your wordpress database.
  - Additionally all measurements done by this modules will be synchronised to the local database.
  - Synchronisation will be done via wordpress cron framework (see: [wp_cron](https://codex.wordpress.org/Function_Reference/wp_cron)), so its asynchronous
    - but the data will be merged, that means all the missing measurements from the last synch will be fetched on the next run.

* **plugin info**:
  - On plugin activation the necessary tables and views will be created.
  - Shortcodes to display device data, measurement data and parameters to configure the output (examples are in the admin section)
  - I created a widget to display the latest outdoor measurements.
  - Admin section to login to netatmo, change display values (&deg;C/&deg;F ...) and cleanup on uninstall
  - 100% ready for translations (german already done)
  
**AVAILABLE DATA:** With the initial version (1.0) you will be able to cache all your data to your local database, including device parameters. As soon as the wind measurement module will be released, this data will be cached also.

**SUPPORT:** This plugin is free, but I offer it to you without any warranty. Use it at your own risk, I'm not responsible for any improper use of this plugin, nor for any damage it might cause to your site. Always backup all the site before installing a new plugin.

Anyway, I'll be glad to support you when I have time if you're in trouble. Just write me on wordpress support site of my plugin or write me netatmosphere@teni.at .

Here is a plugin demo: [plugin demo](http://www.teni.at/wp/2015/09/15/netatmo-plugin-demo/)

== Installation ==

- Install like all your plugins, if you dont know how to do it, [this will guide you through](https://codex.wordpress.org/Managing_Plugins)


== Frequently Asked Questions ==

On problems or feature requests, please contact me through the plugin support site or write me netatmosphere@teni.at

== Upgrade Notice ==

NetAtmo Login changed to OAuth, therefor its necessary to check the admin section again after the upgrade to version 2.0.0!

== Screenshots ==

1. Options page
2. Administration functions
3. Examples for using on posts/pages.

== Changelog ==

= 2.0.16 (2016-05-04) =
* Widget: Latest measures disabled due to database performance issues

= 2.0.15 (2016-04-22) =
* Update synch flag directly instead of rereading devices from netatmo
* Display last synched module and active devices/measures in admin
* Last module id moved to option class

= 2.0.14 (2016-04-19) =
* DB changes (synch flag on each module, group flag on measurements)
* options for automatic or manual synch of devices and / or measurements
* blacklist for modules to be excluded on only on measurement synch (thanks Josef Bindhammer for that idea)

= 2.0.13 (2016-04-14) =
* measurements merge strategy changed: continue at the last module id to start merge
* translations fixed

= 2.0.12 (2016-04-11) =
* Grouped SQL example in chart section (admin panel)

= 2.0.11 (2016-04-11) =
* admin panel empty issue fixed (empty timezone from wp caused that error)

= 2.0.10 (2016-04-05) =
* forgot to checkin a new file - sorry :S

= 2.0.9 (2016-04-05) =
* options for caching added (interval of refresh/merge)

= 2.0.8 (2016-02-25) =
* Bug in NetAtmo API getstationsdata: Wind module has only 1 data_type (instead of 4), will be temporarly fixed in code.
**Please refresh manually your device list, if you have a Wind sensor running!**
* Example of shortcode for "WP Business Intelligence Lite" also added

= 2.0.7 (2016-02-22) =
* translations added
* easy to use connection to "WP Business Intelligence Lite"
* useful queries display added to admin (for WPBI plugin)
* new shortcode for getting latest timestamp of measure record
* elevation calculated (and stored in db) based on the coordinates (cloud base calculation)
* cron-synch-data.php added for directly calling the measure record merge (some hosts provide real cron jobs)

= 2.0.6 =
* field list in views fixed, it seems that the SVN sabotaged me :( sry

= 2.0.5 =
* menu added to toolbar (admin bar on top)
* admin section: overview of data (min/max values, min/max dates, count, ...) of each module
* typo fixed again in "create view" for devices details

= 2.0.4 =
* weather data in widget table styled
* dew point calculation and display on widget

= 2.0.3 =
* typo fixed in "create view"
* widget table more informative

= 2.0.2 =
* screenshots updated
* remove old crons, if they still exist

= 2.0.1 =
* admin section display bug fixed
* compatible wordpress version udpated

= 2.0.0 =
* API major release, credentials changed to OAuth (date: 2016-02-04)
* Sync improved (huge data will be loaded asynch every 5minutes)
* All timestamps stored now in UTC and only displayed in Time Zone
* Widget shows current and average data of today
* Device table (from shortcode) will be rerendered by datatables.net
* HUGE structural refactor of plugin

= 1.0.6 =
* Database index on measurement table to improve database performance
* nonce introduced to admin functions

= 1.0.5 =
* All php files have the "no script kiddies" security check
* Finally try to make a "update" visibile to users by setting the version of main php file

= 1.0.4 =
* NetAtmo PHP SDK: new calls used
* getmeasure limit constant moved to PHP SDK
* amCharts: global for active amCharts plugin
* DB version option: only update database when necessary

= 1.0.3 =
* Widget: Show location on google maps optionally.

= 1.0.2 =
* Structural change: device tables split up
* NetAtmo API: migrated to new interface
* Admin: Examples improved with options

= 1.0.1 =
* Admin features fixed

= 1.0 =
* First public version

