Changelog
==========

##### 3.1.5 - April 18, 2016

**Fixes**

- WooCommerce orders with an associated user account (instead of guest email address) were not being recorded by the "add past orders" screen.
- Running the log export on PHP 5.2 would return an empty CSV file


##### 3.1.4 - March 30, 2016

**Fixes**

- eCommerce360 would try to add orders without an email address to MailChimp.

 **Improvements**

 - Ensure all WooCommerce filters run when sending order data to MailChimp.
 - When AJAX form script errors, the form now falls back to a default form submission.
 - Grouping data is now shown in log table again.

**Additions**

- New "Order Action" for WooCommerce to manually add or delete an eCommerce360 order to/from MailChimp.


##### 3.1.3 - March 22, 2016

**Fixes**

- Script for plotting Reports graph wasn't loaded on some servers.

**Improvements**

- Use later hook priority for rendering form preview in Styles Builder for compatibility with Pagelines DMS.
- Update script dependencies to their latest versions.
- Escape form name throughout settings pages.


##### 3.1.2 - February 29, 2016

**Fixes**

- Email notification didn't get correct `Content-Type` header for HTML.

**Improvements**

- Get form preview to work with unsaved Styles Builder stylesheet.
- Minor improvements for setting button colors in Styles Builder stylesheet.

##### 3.1.1 - February 16, 2016

**Fixes**

- Log would throw error when list had percentage-sign in their name.

**Improvements**

- All `mc4wp_form_email_notification_*` filters now have access to the submitted form (as the second parameter).
- Add JavaScript sourcemaps to minified JS scripts.
- Remove sourcemaps from unminified JS scripts
- Log now takes `mc4wp_lists` filter into account.
- Use earlier hook for Styles Builder preview to prevent incompatibility with some themes.
- Load Styles Builder stylesheet in TinyMCE editor for improved [Shortcake](https://wordpress.org/plugins/shortcode-ui/) support.

**Additions**

- Added `mc4wp_form_email_notification_headers` filter.
- Added `mc4wp_form_email_notification_attachments` filter.


##### 3.1 - January 26, 2016

**Additions**

- [eCommerce360 integration](https://mc4wp.com/kb/what-is-ecommerce360/) for WooCommerce and Easy Digital Downloads.
- [WP-CLI commands for eCommerce360](https://mc4wp.com/kb/ecommerce360-wp-cli-commands/).

**Improvements**

- Log: modify items per page in screen options when viewing log.
- Miscellaneous code improvements


##### 3.0.6 - January 18, 2016

**Fixes**

- Pagination not showing on Log overview page.

**Improvements**

- Memory usage improvements to Log export for huge datasets.
- Make sure Styles Builder stylesheet is loaded over HTTPS when needed.


##### 3.0.5 - December 15, 2015

**Fixes**

- Button to export log was no longer working after version 3.0

**Improvements**

- Reintroduced support for `data-loading-text` on submit buttons.
- Improved logic for loading animation in submit buttons.
- Styles Builder: Success & error color is now applied to paragraph element, to make sure theme doesn't override the given style.

**Additions**

- Improved email notifications. You can now easily modify the subject line & message body of the email that is sent.


##### 3.0.4 - December 10, 2015

**Fixes**

- Not being able to access Forms page when using strict error reporting.

**Additions**

- Use `mc4wp_use_sslverify` filter to detect whether SSL verification should be used in remote requests.


##### 3.0.3 - December 1, 2015

**Fixes**

- Fatal error when visiting Forms overview page on older PHP versions.

##### 3.0.2 - November 26, 2015

**Fixes**

- Stylesheet file not loaded because of 403 error (due to incorrect file permissions).

= 3.0.1 - November 25, 2015 =

**Improvements**

- AJAX Forms: Perform core logic before triggering events, to prevent erorrs in event callbacks from messing up form flow.
- Styles Builder: Changes are now applied instantly.
- Styles Builder: Clearing a color no longer resets all styles.
- Styles Builder: Try to set correct permissions before writing stylesheet to file.
- KB Search: Make sure all links point to [mc4wp.com](https://mc4wp.com).

**Additions**

- Add link to "Appearance" tab on Forms overview page.


##### 3.0.0 - November 23, 2015

Initial release.

This plugin requires [MailChimp for WordPress](https://wordpress.org/plugins/mailchimp-for-wp/) v3.0 or higher to work.