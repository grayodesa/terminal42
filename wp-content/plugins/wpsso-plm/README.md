<h1>WPSSO Place / Location and Local Business + SEO Meta for Pinterest, Facebook and Google</h1>

<table>
<tr><th align="right" valign="top" nowrap>Plugin Name</th><td>WPSSO Place / Location and Local Business Meta (WPSSO PLM)</td></tr>
<tr><th align="right" valign="top" nowrap>Summary</th><td>WPSSO extension to provide Pinterest Place, Facebook / Open Graph Location, Schema Local Business + Local SEO meta tags.</td></tr>
<tr><th align="right" valign="top" nowrap>Stable Version</th><td>2.1.1-1</td></tr>
<tr><th align="right" valign="top" nowrap>Requires At Least</th><td>WordPress 3.1</td></tr>
<tr><th align="right" valign="top" nowrap>Tested Up To</th><td>WordPress 4.6</td></tr>
<tr><th align="right" valign="top" nowrap>Contributors</th><td>jsmoriss</td></tr>
<tr><th align="right" valign="top" nowrap>Website URL</th><td><a href="https://wpsso.com/?utm_source=wpssoplm-readme-donate">https://wpsso.com/?utm_source=wpssoplm-readme-donate</a></td></tr>
<tr><th align="right" valign="top" nowrap>License</th><td><a href="http://www.gnu.org/licenses/gpl.txt">GPLv3</a></td></tr>
<tr><th align="right" valign="top" nowrap>Tags / Keywords</th><td>wpsso, place, location, venue, longitude, latitude, address, local, business, local business, seasonal, hours, seo, coordinates, restaurant</td></tr>
</table>

<h2>Description</h2>

<p align="center"><img src="https://surniaulula.github.io/wpsso-plm/assets/icon-256x256.png" width="256" height="256" /></p><p><strong>Include location / local business meta tags for your website, business, and/or webpage content:</strong></p>

<ul>
<li>Geo coordinates (latitude, longitude, altitude).</li>
<li>Store or business location / street address.</li>
<li>Business hours (daily and seasonal).</li>
<li>Restaurant menu URL.</li>
<li>Service area.</li>
</ul>

<p><strong>Let Pinterest, Facebook and Google know about your locations</strong> &mdash; WPSSO PLM includes Facebook / Open Graph <em>Location</em> Pinterest Rich Pin / Schema <em>Place</em> and Google / Schema <em>Local Business</em> meta tags in your webpages.</p>

<blockquote>
<p><strong>Prerequisite</strong> &mdash; WPSSO Place / Location and Local Business Meta (WPSSO PLM) is an extension for the <a href="https://wordpress.org/plugins/wpsso/">WordPress Social Sharing Optimization (WPSSO)</a> plugin.</p>
</blockquote>

<h4>Quick List of Features</h4>

<p><strong>WPSSO PLM Free / Basic Features</strong></p>

<ul>
<li>Extends the features of either the Free or Pro versions of WPSSO.</li>
<li>Select an Address for a Non-static Homepage</li>
<li>Manage Multiple Addresses / Contact Information

<ul>
<li>Pinterest Rich Pin / Schema Place

<ul>
<li>Street Address</li>
<li>P.O. Box Number</li>
<li>City</li>
<li>State / Province</li>
<li>Zip / Postal Code</li>
<li>Country</li>
</ul></li>
<li>Facebook / Open Graph Location

<ul>
<li>Latitude</li>
<li>Longitude</li>
<li>Altitude</li>
</ul></li>
<li>Schema Local Business

<ul>
<li>Local Business Type</li>
<li>Business Days + Hours</li>
<li>Business Dates (Season)</li>
<li>Service Radius</li>
<li>Food Menu URL</li>
<li>Accepts Reservations</li>
</ul></li>
</ul></li>
<li>Combine WPSSO PLM with the <a href="http://wpsso.com/extend/plugins/wpsso-json/">WPSSO Schema JSON-LD Markup (WPSSO JSON) Pro</a> extension to include complete Place and Local Business using Schema JSON-LD markup.</li>
</ul>

<p><strong>WPSSO PLM Pro / Power-User Features</strong></p>

<ul>
<li>Extends the features of WPSSO Pro (requires a licensed WPSSO Pro plugin).</li>
<li>Add a custom "Place / Location" settings tab to Posts, Pages, and Custom Post Types. Allows the selection of an existing Address, or entering custom Address information.</li>
</ul>

<h4>Example Meta Tags and Markup</h4>

<p>Example WPSSO PLM meta tags for a Restaurant (Local Business). The image and video meta tags for the restaurant have been excluded for brevety. ;-) The <a href="http://wpsso.com/extend/plugins/wpsso-json/">WPSSO Schema JSON-LD Markup (WPSSO JSON) Pro</a> extension can be used to include complete Schema JSON-LD markup instead of Schema meta tags.</p>

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

<h4>Available in Multiple Languages</h4>

<ul>
<li>English (US)</li>
<li>French (France)</li>
<li>More to come...</li>
</ul>

<h4>Extends the WPSSO Plugin</h4>

<p>The WordPress Social Sharing Optimization (WPSSO) plugin is required to use the WPSSO PLM extension.</p>

<p>Use the Free version of WPSSO PLM with <em>both</em> the Free and Pro versions of WPSSO. The <a href="http://wpsso.com/extend/plugins/wpsso-plm/?utm_source=wpssoplm-readme-extends">WPSSO PLM Pro</a> extension (along with all WPSSO Pro extensions) requires the <a href="http://wpsso.com/extend/plugins/wpsso/?utm_source=wpssoplm-readme-extends">WPSSO Pro</a> plugin as well.</p>

<p><a href="http://wpsso.com/extend/plugins/wpsso-plm/?utm_source=wpssoplm-readme-purchase">Purchase the WPSSO Place / Location and Local Business Meta (WPSSO PLM) Pro extension</a> (includes a <em>No Risk 30 Day Refund Policy</em>).</p>


<h2>Installation</h2>

<h4>Install and Uninstall</h4>

<ul>
<li><a href="http://wpsso.com/codex/plugins/wpsso-plm/installation/install-the-plugin/">Install the Plugin</a></li>
<li><a href="http://wpsso.com/codex/plugins/wpsso-plm/installation/uninstall-the-plugin/">Uninstall the Plugin</a></li>
</ul>


<h2>Frequently Asked Questions</h2>

<h4>Frequently Asked Questions</h4>

<ul>
<li>None</li>
</ul>


<h2>Other Notes</h2>

<h3>Other Notes</h3>
<h4>Additional Documentation</h4>

<ul>
<li>None</li>
</ul>

