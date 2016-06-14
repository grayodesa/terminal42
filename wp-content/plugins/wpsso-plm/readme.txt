=== WPSSO Place / Location and Local Business + SEO Meta for Pinterest, Facebook and Google ===
Plugin Name: WPSSO Place / Location and Local Business Meta (WPSSO PLM)
Plugin Slug: wpsso-plm
Text Domain: wpsso-plm
Domain Path: /languages
Contributors: jsmoriss
Donate Link: https://wpsso.com/
Tags: wpsso, place, location, venue, longitude, latitude, address, local, business, hours, seo
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.txt
Requires At Least: 3.1
Tested Up To: 4.5.2
Stable Tag: 2.0.5-1

WPSSO extension to provide Pinterest Place, Facebook / Open Graph Location, Schema Local Business + Local SEO meta tags.

== Description ==

<p><img src="https://surniaulula.github.io/wpsso-plm/assets/icon-256x256.png" width="256" height="256" style="width:33%;min-width:128px;max-width:256px;float:left;margin:0 40px 20px 0;" /><strong>Could your website or business use location information, like geo coordinates, store location / street address, business hours (daily and seasonal), and/or restaurant menu URL?</strong> Let Pinterest, Facebook, Google and other search engines know by using this plugin.</p>

WPSSO Place / Location and Local Business Meta (WPSSO PLM) works in conjunction with the [WordPress Social Sharing Optimization (WPSSO)](https://wordpress.org/plugins/wpsso/) plugin, extending its features with additional settings pages, tabs, and options, to include Facebook / Open Graph *Location*, Pinterest Rich Pin / Schema *Place*, and Google / Schema *Local Business* meta tags in your webpages.

= Quick List of Features =

**WPSSO PLM Free / Basic Features**

* Extends the features of either the Free or Pro versions of WPSSO.
* Select an Address for a Non-static Homepage
* Manage Multiple Addresses / Contact Information
	* Pinterest Rich Pin / Schema Place
		* Street Address
		* P.O. Box Number
		* City
		* State / Province
		* Zip / Postal Code
		* Country
	* Facebook / Open Graph Location
		* Latitude
		* Longitude
		* Altitude in Meters
	* Schema Local Business
		* Local Business Type
		* Business Days and Hours
		* Seasonal Business Dates
		* Food Establishment Menu URL
		* Accepts Reservations
* Combine WPSSO PLM with the [WPSSO Schema JSON-LD Markup (WPSSO JSON) Pro](http://wpsso.com/extend/plugins/wpsso-json/) extension to include complete Place and Local Business using Schema JSON-LD markup.

**WPSSO PLM Pro / Power-User Features**

* Extends the features of WPSSO Pro (requires a licensed and active WPSSO Pro plugin).
* Add a custom "Place / Location" settings tab to Posts, Pages, and Custom Post Types. Allows the selection of an existing Address, or entering custom Address information.

= Example Meta Tags and Markup =

Example WPSSO PLM meta tags for a Restaurant (Local Business). The image and video meta tags for the restaurant have been excluded for brevety. ;-) The [WPSSO Schema JSON-LD Markup (WPSSO JSON) Pro](http://wpsso.com/extend/plugins/wpsso-json/) extension can be used to include complete Schema JSON-LD markup instead of Schema meta tags.

<pre>
&lt;head itemscope itemtype="http://schema.org/Restaurant"&gt;
    &lt;meta property="og:type" content="place"/&gt;
    &lt;meta property="og:latitude" content="10"/&gt;
    &lt;meta property="og:longitude" content="-10"/&gt;

    &lt;meta property="place:street_address" content="123 A Road"/&gt;
    &lt;meta property="place:locality" content="Cityname"/&gt;
    &lt;meta property="place:region" content="Somestate"/&gt;
    &lt;meta property="place:postal_code" content="123456"/&gt;
    &lt;meta property="place:country_name" content="US"/&gt;
    &lt;meta property="place:location:latitude" content="10"/&gt;
    &lt;meta property="place:location:longitude" content="-10"/&gt;

    &lt;noscript itemprop="openingHoursSpecification" itemscope itemtype="https://schema.org/OpeningHoursSpecification"&gt;
    	&lt;meta itemprop="dayofweek" content="saturday"/&gt;
    	&lt;meta itemprop="opens" content="12:00"/&gt;
    	&lt;meta itemprop="closes" content="22:00"/&gt;
    	&lt;meta itemprop="validfrom" content="2016-05-01"/&gt;
    	&lt;meta itemprop="validthrough" content="2016-09-01"/&gt;
    &lt;/noscript&gt;

    &lt;meta itemprop="menu" content="http://restaurant.example.com/restaurant-menu.html"/&gt;
    &lt;meta itemprop="acceptsreservations" content="true"/&gt;
&lt;/head&gt;
</pre>

= Available in Multiple Languages =

* English (US)
* French (France)
* More to come...

= Extends the WPSSO Plugin =

The WordPress Social Sharing Optimization (WPSSO) plugin is required to use the WPSSO PLM extension.

Use the Free version of WPSSO PLM with *both* the Free and Pro versions of WPSSO. The [WPSSO PLM Pro](http://wpsso.com/extend/plugins/wpsso-plm/) extension (along with all WPSSO Pro extensions) requires the [WPSSO Pro](http://wpsso.com/extend/plugins/wpsso/) plugin as well.

Purchase the [WPSSO Place / Location and Local Business Meta (WPSSO PLM) Pro](http://wpsso.com/extend/plugins/wpsso-plm/) extension (includes a *No Risk 30 Day Refund Policy*).

== Installation ==

= Install and Uninstall =

* [Install the Plugin](http://wpsso.com/codex/plugins/wpsso-plm/installation/install-the-plugin/)
* [Uninstall the Plugin](http://wpsso.com/codex/plugins/wpsso-plm/installation/uninstall-the-plugin/)

== Frequently Asked Questions ==

= Frequently Asked Questions =

* None

== Other Notes ==

= Additional Documentation =

* None

== Screenshots ==

01. The WPSSO PLM settings page &mdash; manage a selection of addresses, geo location, business hours, the food establishment menu URL, and if reservation are accepted.
02. The Place / Location tab in the Social Settings metabox on individual posts, pages, and custom post types &mdash; manage custom addresses, geo location, business hours, the food establishment menu URL, and if reservation are accepted.
03. Google's Structured Data Testing Tool &mdash; Results for an example Restaurant webpage showing the WPSSO PLM meta tags.

== Changelog ==

= Free / Basic Version Repository =

* [GitHub](https://github.com/SurniaUlula/wpsso-plm)
* [WordPress.org](https://wordpress.org/plugins/wpsso-plm/developers/)

= Changelog / Release Notes =

**Version 2.0.6-1 (2016/06/13)**

Official announcement: N/A

* *New Features*
	* None
* *Improvements*
	* Added a new checkbox to delete addresses (instead of leaving its name blank).
* *Bugfixes*
	* None
* *Developer Notes*
	* Added localization support when retrieving address information.
	* Replaced the `WpssoPlmAddress::get_names()` method by `SucomUtil::get_multi_key_locale()`.
	* Replaced the `WpssoPlmAddress::get_first_next_ids()` method by `SucomUtil::get_first_last_next_nums()`.
	* Added a new hook for the 'wpsso_place_options' filter.

**Version 2.0.5-1 (2016/06/05)**

Official announcement: N/A

* *New Features*
	* None
* *Improvements*
	* None
* *Bugfixes*
	* Fixed missing comment string argument to `get_single_mt()` when creating container meta tags.
* *Developer Notes*
	* None

== Upgrade Notice ==

= 2.0.6-1 =

(2016/06/13) Added a new checkbox to delete addresses. Replaced two methods in WpssoPlmAddress for SucomUtil methods. Added a new hook for the 'wpsso_place_options' filter.

= 2.0.5-1 =

(2016/06/05) Fixed missing comment string argument to `get_single_mt()` when creating container meta tags.

