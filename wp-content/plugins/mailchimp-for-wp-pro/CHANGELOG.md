== Changelog ==

= 2.7.26 - November 23, 2015 =

This release contains an upgrade wizard to get you to version 3.0 of the plugin.

= 2.7.25 - November 11, 2015 =

**Improvements**

- AJAX loader image is now created & loaded in JavaScript to make sure it's always there. 
- Preparations for [major upcoming changes in version 3.0](https://mc4wp.com/blog/breaking-backwards-compatibility-in-version-3-0/).
- Don't show cookie data in email notification.

= 2.7.24 - November 2, 2015 =

**Fixes**

- AJAX forms not working when loader image not found on server.
- Prevent double AJAX request (edge case bug)

**Improvements**

- Usage tracking is now scheduled once a week, instead of daily.

= 2.7.23 - October 22, 2015 =

**Fixes**

- Honeypot field being autofilled in Chrome, causing a form error.

**Improvements**

- Updated Portugese translations
- Manual CSS field in Styles Builder now adjusts to the number of lines in the textarea.

= 2.7.22 - October 14, 2015 =

**Improvements**

- Textual improvements to general settings page.
- Connectivity issues with MailChimp will now _always_ show an error message.
- Renewing MailChimp lists will now also update the output of the `{subscriber_count}` tag.

= 2.7.21 - October 12, 2015 =

Added anonymous usage tracking to help us further improve the plugin. No sensitive data is tracked.

= 2.7.20 - October 2, 2015 =

Fixes license issue with network-activated plugins.

= 2.7.19 - October 1, 2015 =

**Fixes**

- Changelog not showing for available updates.
- JavaScript error when page has no submit button.

**Improvements**

- Various improvements to the license management & plugin update functionality.
- Styles Builder: improved error message when stylesheet can't be created.
- Reports now uses local WordPress' timezone.

**Additions**

- You can now control whether a background image should repeat itself in the Styles Builder.

= 2.7.18 - September 25, 2015 =

**Fixes**

- Honeypot causing horizontal scrollbar on RTL sites.

**Improvements**

- Minor styling improvements for RTL sites.
- MailChimp list fields of type "website" will now become HTML5 `url` type fields.
- Auto-prefix fields of type `url` with `http://`

= 2.7.17 - September 23, 2015 =

**Fixes**

- Licenses were showing as expired, when they were not.
- Certain fields were incorrectly being hidden when using one of the default form themes.

= 2.7.16 - September 21, 2015 =

**Fixes**

- Issue with interest groupings not being fetched after updating to v2.7.15

= 2.7.15 - September 21, 2015 =

**Fixes**

- Honeypot field being filled by browser's autocomplete.
- Empty response from MailChimp API
- Your license expiration date is now updated with each update check.

**Improvements**

- Do not query MailChimp API for interest groupings if list has none.
- Integration errors are now logged to PHP's error log for easier debugging.
- Email notification now uses local date & time format.

**Additions**

- You can now use shortcodes in the form content.

= 2.7.14 - September 9, 2015 =

**Fixes**

- CSS issue with rounded corners for buttons on Mobile Safari.

= 2.7.13 - September 7, 2015 =

**Fixes**

- Showing "not connected" when the plugin was actually connected to MailChimp.
- Issue with `address` fields when `addr1` was not given.
- Comment form checkbox not outputted for some older themes.

**Improvements**

- Do not flush MailChimp cache on every settings save.
- Add default CSS styles for `number` fields.

= 2.7.12 - September 1, 2015 =

**Fixes**

- Line height inconsistency between Styles Builder preview and actual result

**Improvements**

- MailChimp lists cache is now automatically flushed after changing your API key setting.
- Better field population after submitting a form with errors.
- More helpful error message when no list is selected.
- Translate options when installing plugin from a language other than English.
- Add form mark-up to WPML configuration file.
- Sign-up checkbox in comment form is now shown before the "submit comment" button.
- URL-encode variables in "Redirect URL" setting.
- Better error message when connected to MailChimp but account has no lists.

**Additions**

- Add `mc4wp_form_action` filter to set a custom `action` attribute on the form element.

= 2.7.11 - August 18, 2015 =

**Fixes**

- Multisite stripping all form HTML for all users except superadmins.

= 2.7.10 - August 18, 2015 =

**Fixes**

- Options from free plugin were not automatically transferred
- Prevented JS error when outputting forms with no submit button.
- Using `0` as a Redirect URL resulted in a blank page.
- Sign-up checkbox was showing twice in the Easy Digital Downloads checkout when showing registration fields, thanks [Daniel Espinoza](https://github.com/growdev).
- Default form was not automatically translated for languages other than English.

**Improvements**

- Better way to hide the honeypot field, which stops bots from subscribing to your lists.
- role="form" is no longer needed, thanks [XhmikosR](https://github.com/XhmikosR)!
- Filter `mc4wp_form_animate_scroll` now disables just the scroll animation, not the scroll itself.
- Revamped UI for MailChimp lists overview
- Updated German & Greek translations.

**Additions**

- Added `mc4wp_form_is_submitted()` and `mc4wp_form_get_response_html()` functions.

= 2.7.9 - July 14, 2015 =

**Bugfixes**

- Only show Dashboard widget with most recent log entries to users with the required capability.

= 2.7.8 - July 13, 2015 =

**Bugfixes**

- Fix for MultiSite activated licenses that keep on deactivating.
- Fatal error in Styles Builder form preview when BuddyPress was activated.
- Visual editors were not loading when using the Page Builder in Nevada theme

**Improvements**

- Use the same order as MailChimp.com, which is useful when you have over 100 MailChimp lists.
- Use `/* ... */` for inline JavaScript comments to prevent errors with minified HTML.
- Add IP address and referring URL to email notification
- Various textual improvements

**Additions**

- Filter: `mc4wp_form_animate_scroll` to disable animated scroll-to after submitting a form.
- Add `{current_path}` variable to use in form templates.
- Add `default` attribute to `{data_name}` variables, usage: `{data_something default="The default value"}`

= 2.7.7 - July 6, 2015 =

**Fixes**

- Undefined index notice when visitor's USER_AGENT is not set.

**Improvements**

- Relayed the browser's Accept-Language header to MailChimp for auto-detecting a subscriber's language.
- Better CSS for form reset
- Updated HTML5 placeholder polyfill

= 2.7.6 - June 16, 2015 =

**Fixes**

- Default messages in general form settings not saving correctly

= 2.7.5 - May 30, 2015 =

**Fixes**

- Styles Builder only showing 5 forms
- GROUPINGS not being sent to MailChimp

**Improvements**

- Minor improvements to generated CSS by Styles Builder

= 2.7.4 - May 26, 2015 =

**Fixes**

- Get correct IP address when using a proxy like Cloudflare
- JS error when using new style of updating plugins

**Improvements**

This release was mainly focused the Form Styles Builder and includes the following improvements.

- Copy styles from your other forms.
- Upload & set a background image or pattern for your form.
- Set a border radius on buttons & fields
- Cleaner generated stylesheet, only the minimum amount of rules are outputted.
- Responsiveness improvements to generated CSS rules.
- Styles are no longer generated for deleted forms.
- Stylesheet files are now updated, instead of creating a separate new file.
- All text in the Styles Builder interface is now translatable
- Various other code & usability improvements

Other improvements:

- Create short form on plugin installation

**Additions**

- Added Russian translations

= 2.7.3 - May 10, 2015 =

**Fixes**

- Requests were no longer logged since v2.7.2

= 2.7.2 - May 10, 2015 =

**Fixes**

- Log report no longer showed which form was used since v2.7.

= 2.7.1 - May 7, 2015 =

**Fixes**

- Fixes a JavaScript issue with lazy-loaded AJAX enabled forms
- New style of updating plugins not working for MailChimp for WordPress

**Improvements**

- IE8 compatibility for JS-helpers (honeypot hider & mc4wp-form-submitted class)

= 2.7 - May 6, 2015 =

**Improvements**

- Various code & testability improvements.
- You can now use [form variables in both forms, messages as checkbox label texts](https://mc4wp.com/kb/using-variables-in-your-form-or-messages/).
- Use Composer for autoloading all plugin classes (PHP 5.2 compatible)

**Additions**

- Add `role` attribute to form elements
- Unsubscribe calls can be handled using our forms now too.
- Added Indonesian translation.

= 2.6.6 - April 27, 2015 =

**Fixes**: "Invalid argument" warning when generating text for email confirmation

= 2.6.5 - April 24, 2015 =

**Fixes**

- Fatal error when trying to create a new form for some users on v2.6.4
- Groupings showing in notification email as "Array"
- Use correct property names for WP_User
- Fixes issue with download package on IIS servers

**Improvements**

- Major code refactor for improved readability and maintenance
- Show pretty fields names in Reports > Log.
- `?mc4wp_email=...` is no longer automatically added to URL's as it is against most tracking policies. To add it, use `?email={email}` in your Redirect URL setting.


- Call to un

= 2.6.4 - April 20, 2015 =

**Fixes**

- Integration with any type of user registration not correctly picking up first- and last names
- AJAX forms not activated when other plugin throw JavaScript error in global scope
- Allow `0` in "Redirect URL" again, to override general setting.
- Add explicit menu item position, fixes very rare menu item issue.

**Improvements**
- Make sure plugin loads jQuery dependency
- AJAX requests are now pointed at `/wp-admin/admin-ajax?mc4wp_action=subscribe`
- Updated all translations

**Additions**
- Portugese, Russian, Turkish, Vietnamese and German (CH) translations.


= 2.6.3 - March 24, 2015 =

**Fixes**

- MultiSite was stripping all form fields form non-superadmins.
- API key field value was not properly escaped.
- Background images were stripped from submit buttons.

**Improvements**

- Use `$wpdb->prepare` for querying Reports graph data.
- Better sanitising of all settings.
- Updated all translations.

**Additions**

- Added `mc4wp_before_checkbox` and `mc4wp_after_checkbox` filters to easily add more fields to sign-up checkbox integrations.
- Added some helper methods related to interest groupings to `MC4WP_MailChimp` class.
- Allow setting custom MailChimp lists to subscribe to using `lists` attribute on shortcode.


= 2.6.2 - March 11, 2015 =

**Fixes**

- Honeypot field was visible for themes or templates not calling `wp_head()` and `wp_footer()`

**Improvements**

- Major refactor of AJAX functionality, now using vanilla JavaScript where possible.
- Major refactor of code for Reports graph.
- The jQuery Forms library is no longer used for AJAX forms, saving an additional HTTP request per page load.
- Various minor code improvements
- Updated German, Spanish, Brazilian, French, Hungarian and Russian translations.

**Additions**

- Added [mc4wp_form_success](https://github.com/ibericode/mailchimp-for-wordpress/blob/06f0c833027f347a288d2cb9805e0614767409b6/includes/class-form-request.php#L292-L301) action hook to hook into successful sign-ups
- Added [mc4wp_form_data](https://github.com/ibericode/mailchimp-for-wordpress/blob/06f0c833027f347a288d2cb9805e0614767409b6/includes/class-form-request.php#L138-L142) filter hook to modify all form data before processing


= 2.6.1 - February 26, 2015 =

**Fixes**

- `mc4wp-submitted` class was not added in IE8
- Incorrect `action` attribute on form element for some server configurations
- Custom date range on Reports page didn't show 2015 as a year option
- CSS reset wasn't working for WooCommerce checkout sign-up checkbox
- License key wasn't saved properly when using certain security plugins
- AJAX response not appearing when form is rendered from POST request

**Improvements**

- Anti-SPAM improvements: a better honeypot field and a timestamp field to prevent instant form submissions.
- Refactor JS for AJAX functionality. All events have been renamed (with backwards compatibility).
- Reset `background-image` on submit buttons when using CSS themes
- Smarter email detection when integrating with third-party forms
- Added various missing translations for the Reports page
- Textual improvements to settings pages
- Updated all translations

**Additions**

- Custom fallback for browsers not supporting date fields


= 2.6 - February 12, 2015 =

**Fixes**

- Translations were loaded too late for certain strings, like admin menu items.
- Renewal link now points to correct URL.
- WooCommerce checkout sign-up not working for cheque's.

**Improvements**

- Presence of required fields in your form mark-up is now validates as you type.
- Massive improvements to logging functionality. All MailChimp requests are now logged, showing you failed requests as well.
- Various JavaScript improvements.
- Updated all translations
- Improved WPML compatibility.

**Additions**

- [MailChimp Top Bar](https://wordpress.org/plugins/mailchimp-top-bar/) now integrates with log.


### 2.5.6 - February 2, 2015

**Fixes**

- Issue with "insufficient permissions" message showing when using Styles Builder and changing forms.

**Improvements**

- You will now be asked for a confirmation when deleting a form
- Updated all URL's to use https protocol
- On error, number fields will now be repopulated as well
- Code style improvements


2.5.5 - January 24, 2015

Minor additions and improvements for compatibility with the [MailChimp Sync plugin](https://wordpress.org/plugins/mailchimp-sync/).

2.5.4 - January 19, 2014

**Fixes**

- Issue where license auto-deactivated after each update check

2.5.3 - January 14, 2014

**Fixes**

- Plugin wouldn't connect for people with API keys ending in `-us10`
- Issue where form response wasn't shown when form was set to hide after success.

2.5.2 - January 13, 2014

**Fixes**

- Issue with changelog not showing for plugin updates

**Improvements**

- Use JS object to transfer lists data to Field Wizard.
- Field Wizard strings are now translatable
- Add `is_spam` method to checkbox integration to battle spam sign-ups
- Easy Digital Downloads & WooCommerce checkbox integration now runs after payment is complete, since that's more important
- Many minor code & code style improvements
- Updated Danish, German, Spanish, French, Italian and Portugese (Brazil) translations

**Additions**

- You can now set `MC_LOCATION`, `MC_NOTES` and `MC_LANGUAGE` from your form HTML
- The submit button now has a default value when generating HTML for it


2.5.1 - December 8, 2014

**Fixes**

- Email in URLs after redirecting lost the `@` character.
- Textarea's are now re-populated on errors as well.

**Improvements**
Email is no longer showed on a separate line in email notifications.


2.5 - December 5, 2014

**Fixes**

- Fixed JS event bug for older versions of IE

**Improvements**

- Major refactor of the code that handles form submissions
- Notification emails will now show "pretty" field names
- Form fields are now only sent to lists that actually have that field
- Updated Dutch, Italian, Hungarian and Spanish translation
- Minor code improvements

**Additions**

- Added {email} tag to use in form mark-up
- Added French and Spanish (Puerto Rico) translation


2.4.6 - November 18, 2014

**Fixes**

- `mc4wp_get_current_url()` fix for IIS servers
- Protocol issue when using custom stylesheet

**Improvements**

- Refactored AJAX script
- Added & updated translations
- Better handling of expired licenses

**Additions**

- Automatic formatting of `MC_LANGUAGE` field
- `mc4wp_form_redirect_delay` filter to control time before redirect when using AJAX.

2.4.5 - October 9, 2014

**Fixes**

- WooCommerce integration was sending last name as `FNAME`.

**Improvements**

- Fix issue with aggressive WP Super Cache configuration
- Make sure `is_email` is only called on strings

**Additions**

- Added a filter to control the length of `mc4wp_email` cookie


2.4.4 - September 24, 2014

**Improvements**

- AJAX Loader is now lazy loaded
- Minor performance improvement to class autoloader
- Updated Dutch translations

**Additions**

- Added `.mc4wp-form-submitted` CSS class to style errors after submission attempts
- Added `mc4wp-form-success` and `.mc4wp-form-error` CSS classes to AJAX forms
- Added Hungarian translations

2.4.3 - September 14, 2014

**Improvements**

- Various security improvements
- Added Slovak translation
- Updated Dutch, Italian and Spanish translations
- Improvements to Styles Builder default CSS
- Improvements to WooCommerce checkbox integration

**Additions**
- Allow to change position of checkbox in WooCommerce checkout forms
- Add `mc4wp.ajax.before` and `mc4wp.ajax.after` event hooks.
- Added some missing text domains

2.4.2 - September 1, 2014

**Bugfixes**

- Dequeue zTranslate assets on mc4wp-form pages

**Improvements**

- URL clean-up for license related requests.
- Add double opt-in note to email summaries
- Add `-webkit-appearance` reset to checkbox CSS

**Additions**

- Integration with Events Manager


2.4.1 - August 26, 2014

**Bugfixes**

- Minified JS files for changes in Styles Builder

**Improvements**

- Improved process for license (de)activation and performing update checks.


2.4 - August 25, 2014

**Additions**

- Feature to create multiple form styles, where styles only apply to 1 individual form.

**Improvements**

- Minor CSS improvement for WordPress 4.0 compatibility
- Add vertical align reset to Styles Builder
- Change API url to new site: https://mc4wp.com/
- Updated Italian translations
- Take array fields into consideration when checking if fields are present in form mark-up
- Don't use {response} tag if form is hidden after success

**Bugfixes**

- Remove obsolete closing form tag
- Remove `text` attribute from `textarea` elements


2.3.2 - August 12, 2014

**Fixes**

- `mc4wp_get_current_url()` now takes ports and the WP site url option into account
- Quicktags buttons were not showing because script was not loaded, now it is.
- Fixed CSS bug for table column widths in other languages than English

**Improvements**

- Added deprecated warning to some functions
- The "license inactive" notice can now be hidden
- Improved CSS reset for the sign-up checkbox
- Updated Dutch translations
- Updated English translations

**Additions**

- Added `mc4wp_form_error_{ERROR_CODE}` action hook to allow hooking into all form errors.
- Added `{response}` tag to allow setting a custom response position
- Added various filters to customize form HTML
- Added German language, thanks to [Jochen Gererstorfer](http://slotnerd.de/)
- Added Italian language, thanks to [Gianpaolo](http://www.casalegnovernici.it/)
- Added link to plugin documentation

2.3.1 - July 29, 2014

Improvements
- Required MailChimp fields are now validated server side as well.
- Birthday and address fields are now automatically formatted in the correct format
- Improved code, memory usage and class documentation

Additions
- Option to set the text for when a required field is missing


2.3 - July 25, 2014

Fixes
- Generated CSS was showing 'Array' when file couldn't be automatically created

Improvements
- Improved JS code for AJAX forms
- Minified assets (CSS & JSS files) will now be loaded, unless SCRIPT_DEBUG is true
- Submit button is now disabled during the AJAX request, to prevent double sign-ups

Additions
- Added `mc4wp_lazy_load_ajax_scripts` filter to disable lazy loading of AJAX scripts
- Added JS events: mc4wp.success, mc4wp.error and mc4wp.submit hooks
- Added `mc4wp_form_message_position` filter to choose position of form messages
- Added `mc4wp_form_error_message` to register custom error messages
- Added Brazilian language files

2.2.5 - July 22, 2014

Improvements
- Various improvements and fixes related to options and transients

2.2.4 - July 21, 2014

Fixes
- CSS Builder throwing an undefined index notice.
- CSS Builder not saving Manual CSS values.

Improvements
- Textual usability improvements to CSS Builder
- Show MailChimp field tag in Lists overview table
- Minor code improvements

Additions
- Add visitor IP Address to sign-up data


2.2.3 - July 16, 2014

Fixes
- Fix Spanish translation breaking optional form settings

Improvements
- Allow for `0` values in CSS Builder
- Increase width of line toggle labels

2.2.2 - July 7, 2014

Fixes
- Fixed unordered Log table
- Fix notice in Log table for deleted comments

Improvements
- Various optimisations to SQL query to retrieve logs
- Sign-up types in Log table are now translatable

2.2.1 - July 6, 2014

Improvements
- Code improvements: Decreased memory usage, lower runtime and optimized functions
- Various improvements to AJAX JavaScript
- Improved layout of line toggles on Reports page
- Better visibility of form ID's throughout admin pages

Additions
- Added {language} text variable
- Add CF7 help text to Checkboxes page

2.2 - July 1, 2014

Fixes
- Preview URL not changing when selecting different forms
- `Show generated CSS` button not working
- Double sign-ups for checkbox integrations

Improvements
- Reset label position for in checkbox stylesheet
- Reset form width for some themes that set a fixed width on all form elements
- Send fixed URL for license activation requests
- Improve readability of generated CSS and show version number in it
- Make sure CSS color values start with #
- Improved default success message

Additions
- Introduce filter `mc4wp_disable_logging` to disable logging

2.1.5 - June 13, 2014

Fixed
- Fix fatal error when using `mc4wp_checkbox()` function
- No more double API request when integrating with Contact Form 7
- CSS Builder form preview now works with HTTPS WP Admin as well

Improvements
- Various checkbox integration improvements
- Various CSS improvements to colored form themes
- Required CSS classes can no longer be accidentally removed
- Method that loads AJAX scripts is now public

2.1.4 - June 2, 2014

Fixed
	- Slashes breaking required CHOICE fields

2.1.3 - May 26, 2014

Fixed
	- MultiSite network activated plugins not being able to activate their license
	- Fixed various output issues

Improvements
	- Added license key to support email
	- Prevent JavaScript error for sites where jquery-forms is not properly loaded


2.1.2 - May 22, 2014

Fixed
	- Too few arguments for printf function in Email Copy function.

Added
	- Plugin will now show warning when required fields are missing from the form mark-up.

Updated
	- Spanish translations


2.1.1 - May 18, 2014

Added
	- Plugin now checks if form contains an EMAIL field and submit button.
	- Spanish translations

Improvements
	- More translatable strings
	- Updated translations


2.1 - April 28, 2014

Added:
	- Support for Captcha fields using the BWS Captcha plugin
	- The plugin admin pages are now translatable
	- Dutch translations
	- The anti-spam honeypot is now added to the sign-up checkbox as well.

Improvements:
	- Plugin is now serving static CSS files instead of serving CSS over PHP
	- Various code improvements
	- Better CSS reset for the various form themes to increase theme compatibility


2.0.6 - April 23, 2014

- Improved saving of the custom stylesheet. Now using native wp_upload_bit function.

2.0.5 - April 21, 2014

- Fixed notices messing with AJAX response when error debugging is enabled

2.0.4 - April 14, 2014

- Fixed: Install function not running on plugin activation
- Fixed: Inline CSS printed where it should be returned instead. Now printing in footer.

2.0.3 - April 10, 2014

- Fixed: Form stylesheets not working

2.0.2 - April 9, 2014
- Fixed: Various errors in the form preview showing up when using WooCommerce
- Fixed: Slow backend when connections to dannyvankooten.com fail.
- Improved: Better CSS validation on CSS Builder values
- Improved: License manager upgrade

2.0.1 - April 3, 2014
- Fixed: Invalid post type 'mc4wp-form' when creating a new form

2.0 - April 2, 2014
- Fixed: make sure template functions are available when using Avia Layout Builder
- Fixed: Various CSS Builder fixes and improvements
- Fixed: Possible fix for font-rendering issue in some Chrome versions when using AJAX
- Improved: Refactored various Checkbox integrations into their own classes
- Improved: Implemented autoloader
- Improved: Various licensing improvements
- Improved: Updated Placeholder polyfill

1.99.4 - March 19, 2014
- Fixed link issue to CSS Builder tab
- Minor form class improvements
- Fixed BIRTHDAY field format (mm/dd)

1.99.3 - March 15, 2014

- Fixed issue with special characters in group names
- Improved loading of plugin files
- Improved code and documentation
- Improved error messages when API calls fail (up to HTTP level)
- Improved first- and lastname guessing
- Improved: form tags are now automatically stripped from form content
- Improved cached data table
- Added error code to mc4wp_form_error_message filter
- Added option to set email_type from HTML
- Added: email addresses are now stored in a cookie after successful sign-ups


1.99.2 - March 11, 2014

- Some form designer fixes
- Fixed honeypoy textarea showing in some themes

1.99.1 - March 3, 2014

- Changed email copy receiver input field type to 'text'

1.99 - February 28, 2014

- Fixed: Display value not working in Form Designer
- Improved: Switched to new Licensing class

1.98.9 - February 25, 2014

- Fixed: field generator only generating `text` fields

1.98.8 - February 22, 2014

- Fixed: WooCommerce checkout opt-in
- Fixed: "Add to form mark-up" button not working with CKEditor for WordPress
- Improved: Cleaned-up Admin JS
- Improved: You can now use `[mc4wp_checkbox]` inside your CF7 email templates
- Improved: You can now add `default:1` or `default:0` to the CF7 shortcode to check or uncheck the sign-up checkbox.
- Improved: Plugin will now working with custom plugin folder names as well


1.98.7 - February 10, 2014

- Fixed: Custom Form Styles
- Added: Form width option to custom form styles
- Added: You can now have select boxes as a list choice

1.98.6 - February 9, 2014 

- Improved: Improved direct file access security
- Improved: Now using native WP function to catch SSL requests
- Improved: Changed call method in API class to public.
- Added: Filter to edit the required capability to access settings pages
- Added: Filter to edit form action
- Added: Filters to allow extra form validation, like a captcha field.
- Added: Added get_member_info and list_has_subscriber method to API class.
- Added: List choice field so visitors can choose which list to subscribe to


1.98.5 - January 20, 2014

- Added: You can customize the email address to send an email copy to

1.98.4 - January 9, 2014

- Fixed: CSS Custom Form Styling tab will now remember label font color.

1.98.3 - January 7, 2014
- Fixed a bug with www-hosts
- Added option to send an email copy
- Scroll to form now waits for full page load
- Getting MailChimp data now caches every step, for slow servers.

1.98.2 - December 23, 2013

- Fixed a bug with W3 Total Cache default Minify settings.

1.98.1 - December 17, 2013
- Code improvements
- Better script loading
- When not using AJAX, fields will stay populated
- BIRTHDAY and ADDRESS improvements
- Fixed a CSS rule when using the custom form styles
- Added label width to custom form styling tab

1.97 - December 9, 2013
- Fixed: Double activation calls where the second disables the first one
- Improved: Scroll to form element when AJAX is disabled.
- Improved: Code refactoring
- Improved: Upped cache lifetime of CSS file to 1 year
- Improved: Settings pages now more usable on small screens

1.961 - November 26, 2013

- Fixed: Checkbox subscriptions
- Improved: now passes form ID to mc4wp_before_subscribe filter.
- Improved: default checkbox CSS
- Improved: various usability improvements
- Added: 4kb JavaScript fallback for placeholder attributes in old browsers (<= IE9)
- Added: plugin will now show error for administrators even when using AJAX.

1.94 - November 20, 2013

- Improved: licensing changes. 
- Fixed: deleting a single item from the log
- Added: more filters and action hooks while processing a form.

1.93 - November 14, 2013
- Added: filter to override lists before sending request to MailChimp
- Fixed: requests with groupings failing on some servers 
- Improved: various improvements to the custom form styling tab.

1.91 - October 27, 2013
- Fixed CSS selector prefix not properly being saved in Custom Form Styling

1. 90 - October 23, 2013
- Added: default form styles to choose from
- Added: custom color theme
- Added: delay before redirecting when using AJAX.
- Improved: auto-select form when upgrading from Lite
- Improved: Field wizard now defaults to wrap in paragraph tags
- Improved: some usability improvements
- Improved: many improvements to form designer / custom styles creator

1.84 - October 22, 2013
- Added support for groupings in third party forms

1.83, October 20, 2013
- Fixed: bug when calling MailChimp API for PHP 5.2
- Improved: Detecting of local development environment
- Improved: Better default form CSS stylesheet
- Improved: Combined checkbox & form stylesheets + encouraged browser caching.
- Removed: Edit form links after forms for administrators

1.82 - October 17, 2013

- Added: Line graphs (Reports) for each individual sign-up form
- Added: Toggle line graphs (Reports) 
- Added: you can now override the default AJAX loader using CSS by targeting `.mc4wp-ajax-loader`
- Improved: AJAX Forms JavaScript for IE8

1.81

- Improved: Added and remove some buttons from QuickTags form editor
- Improved: Added user_name to name fields for third-party forms integration (Events Manager Pro)
- Improved: Reports now show source for form and checkbox sign-ups, log entries have been batched for multiple lists
- Improved: Removed WordPress SEO Metabox from edit form screen.
- Improved: Updates & license checks now use a secure connection
- Added: Filter to form content so plugins can alter form mark-up
- Added: Local license so single-site license can run on multiple environments


1.80
- Fixed: choices (dropdowns and radio buttons) not available in field wizard
- Added: option to prefix CSS selector for custom CSS
- Added: CSS media queries to admin settings
- Added: Version number to assets on frontend (to prevent cache errors)
- Added: Accept-Encoding header for remote requests, prevent encoding issues on some servers
- Added: Widget to make showing a form in widget areas easier
- Added: Animated scroll for non-AJAX forms
- Improved: default form CSS


<h4>1.70 - 1.74</h4>
- Fixed list field merge vars when integrating with third-party plugins like Contact Form 7
- Improved: cleared output before returning AJAX results, stops other plugins from breaking your forms.
- Improved: various usability and code improvements
- Improved: default form CSS
- Improved: only show debug message in non-AJAX requests
- Added: CSS Customizer, build a customized CSS stylesheet to style your forms from your admin panel. No CSS knowledge required!
- Added: option to send welcome e-mail after using sign-up checkbox with double opt-in disabled
- Added: {subscriber_count} to use in form mark-up. Shows the subsciber counts of the selected list(s) for that form.
- Added: ability to show generated CSS when wp-content is not writable.

<h4>1.60 - 1.61</h4>
- Added: Total revamp of the field wizard, many improvements!
- Added: sign-up checkbox debug message for WP Administrators
- Improved: various usability improvements
- Fixed: email support link + signs
- Fixed: transfer of lite v1.2+ settings to pro

<h4>1.50 & 1.51</h3>
- Fixed Contact Form 7 custom checkbox label
- Fixed label functionality when more than 1 sign-up checkbox per page
- Added: total sign-ups overview to statistics
- Added: update existing subscribers option
- Added: replace interests option
- Added: send welcome email option
- Added: search to subscribers log
- Added: delete to subscribers log
- Added: statistics charts
- Improved: AJAX forms now work with FORCE_ADMIN_SSL set to true as well.
- Improved: responsiveness of setting pages
- Improved: enabled logging by default

<h4>1.40 - 1.42</h4>
- Fixed empty checkbox label text after first save.
- Fixed: notice undefined index 'edd_checkout'
- Added: nice name for various subscribe methods in subscribers log
- Added: the database log table will now be removed when uninstalling the plugin
- Added: integration with WooCommerce checkout form
- Added: integration with bbPress
- Added: possibility to change checkbox label texts for each individual sign-up checkbox hook.
- Improved: automatic remote deactivating of license upon plugin deactivation, to allow licen
se activation on different URL. (test environment / production environment for example)
- Improved AJAX spinner CSS
- Improved form submit function, field names are case insensitive again
- Improved: Lite settings are no longer overwriting Pro settings when switching between plugins.
- Improved: Better compatibility with third-party registration form plugins.
- Improved: Usability improvements
- Improved: Checkbox label now wraps checkbox input element
- Improved: Checkbox default CSS

<h4>1.30 - 1.33</h4>
- Fixed: interest groupings now work with new MailChimp API.
- Fixed: AJAX enabled but JavaScript disabled now shows feedback again.
- Fixed spam comments not being filtered
- Fixed: checkbox shortcode for Contact Form 7
- Fixed: checkbox in other forms
- Fixed colon after placeholder
- Added groupings and groups to field wizard
- Added form markup transfer from Lite upon activation
- Added: {current_url} and {time} to form variables
- Added subscriber log
- Added: merge field support for third-party forms, use mc4wp- prefix.
- Added: integration with Easy Digital Downloads checkout form
- Improved: field wizard now wraps checkboxes and radio inputs with label element.
- Improved integration with Contact Form 7
- Improved integration with third-party forms
- Improved: showed sign-up forms on form settings page, no need for separate page
- Improved: showed warning message in overview when no lists selected
- Improved code, less memory usage.
- Improved default form and checkbox CSS
- Improved now using MailChimp API version 2.0
- Improved removed MailChimp API wrapper, now using WordPress HTTP API.