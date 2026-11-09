=== Erident Custom Login and Dashboard ===
Contributors: davidvongries, libinvbabu
Tags: login, custom login, login page, login customizer, admin footer
Requires at least: 5.3
Tested up to: 7.1
Requires PHP: 5.6
Stable tag: 5.0
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Customize the WordPress login screen: logo, background, login form & links. Plus your own admin footer text.

== Description ==

**Erident Custom Login and Dashboard** is a simple way to customize the **WordPress login screen**. Add your logo, set a background, style the login form and change the admin footer text – all from one settings page under *Settings → Custom Login & Dashboard*.

**Erident is now part of [Ultimate Dashboard](https://ultimatedashboard.io/?utm_source=erident&utm_medium=repository&utm_campaign=udb).** Erident is in maintenance mode: it gets security & compatibility updates, but no new features. New login features go into Ultimate Dashboard, which is free too. Already using Erident? Move your settings over with the one-click migration (see below).

== Why choose Erident? ==

= 🎨 Your brand on the login screen =
Replace the WordPress logo with your own, set its size and change its title.

= 🖼️ Custom backgrounds =
Use a background color or image for the login screen and the login form, with full control over repeat, position and size.

= 🧩 Style the login form =
Set the form's width, border, corner radius and shadow, and the colors and font sizes of labels, input fields, the button and links.

= 🔗 Cleaner login links =
Hide the "Register | Lost your password?" and "← Go to site" links below the login form.

= 🦶 Admin footer text =
Replace the text in the left and right of the WordPress admin footer.

= 📦 Import & export =
Move your settings to another site with a .json file.

== How to use ==

1. Go to *Settings → Custom Login & Dashboard*.
2. In the *Login Screen* tab, add your logo, choose a background and style the login form.
3. In the *Dashboard* tab, change the admin footer text.
4. Click *Save Changes* and open your login page in a private window to see the result.

== 🚀 Move to Ultimate Dashboard ==

[Ultimate Dashboard](https://ultimatedashboard.io/?utm_source=erident&utm_medium=repository&utm_campaign=udb) lets you customize the login screen **live in the WordPress Customizer**, with more options, plus tools to customize the whole WordPress admin.

The one-click migration on the Erident settings page installs Ultimate Dashboard, moves your Erident settings over and removes Erident. Your login screen stays the same.

---------

=== Advanced ===

**Import & export:** under *Tools*, export your settings as a .json file and import them on another site.

**Remove data on uninstall:** turn on *Remove Data on Uninstall* in the *Dashboard* tab to delete all Erident settings when you delete the plugin.

---------

=== What's next? ===

If you like Erident, make sure to check out our other products:

* **[Ultimate Dashboard](https://ultimatedashboard.io/?utm_source=erident&utm_medium=repository&utm_campaign=udb)** - The #1 WordPress plugin to customize your WordPress dashboard and admin area.
* **[WP Video Popup](https://wp-video-popup.com/?utm_source=erident&utm_medium=repository&utm_campaign=wp_video_popup)** - Add a YouTube, Vimeo or Rumble video popup to WordPress. Lightweight, privacy-friendly lightbox that works with any page builder.
* **[Page Builder Framework](https://wp-pagebuilderframework.com/?utm_source=erident&utm_medium=repository&utm_campaign=wpbf)** - A fast & minimalistic WordPress theme designed for the new WordPress era.
* **[Better Admin Bar](https://betteradminbar.com/?utm_source=erident&utm_medium=repository&utm_campaign=bab)** - The plugin to make your clients enjoy WordPress. It replaces the default admin bar to provide the best possible user experience when editing & navigating a website.

== Installation ==
1. In your WordPress admin, go to *Plugins → Add New Plugin* and search for "Erident Custom Login".
2. Click *Install Now*, then *Activate*.
3. Go to *Settings → Custom Login & Dashboard* to customize your login screen.

== Frequently Asked Questions ==

= How do I change the logo on the WordPress login page? =
Go to *Settings → Custom Login & Dashboard*, choose an image under *Logo* and set its width and height. Click *Save Changes*.

= Is Erident still maintained? =
Yes, with security & compatibility updates. New features go into [Ultimate Dashboard](https://ultimatedashboard.io/?utm_source=erident&utm_medium=repository&utm_campaign=udb), which also has a live login customizer.

= What happens when I migrate to Ultimate Dashboard? =
Ultimate Dashboard is installed and activated, your Erident settings are moved over and Erident is removed. Your login screen stays the same, and you can keep customizing it live in the WordPress Customizer.

= I already have Ultimate Dashboard installed. Can I still migrate? =
The migration only runs if Ultimate Dashboard isn't installed yet, so your existing Ultimate Dashboard settings are never overwritten.

= Are my settings deleted when I deactivate the plugin? =
No. Settings are only deleted when you delete the plugin and *Remove Data on Uninstall* is turned on.

== Screenshots ==
1. Settings Page
2. Default Erident Login Screen
3. Customized WordPress Login Screen (Example 1)
4. Customized WordPress Login Screen (Example 2)

== Changelog ==
= 5.0 November 09, 2026 =
* New: Redesigned, accessible settings page
* New: The login form settings are combined in one box
* Tweak: The migration to Ultimate Dashboard is offered in a box on the settings page and a dismissible notice on the Dashboard & Plugins screens, instead of on every admin page
* Tweak: The migration checks that Ultimate Dashboard can be installed before anything is removed
* Tweak: "Remove Data on Uninstall" deletes the settings when the plugin is deleted, no longer on deactivation
* Tweak: Translations are loaded from translate.wordpress.org
* Tweak: Requires WordPress 5.3
* Fixed: Security improvements (capability checks, nonces, sanitized & escaped settings)
* Fixed: Import only accepts .json files
* Fixed: The migration copied a custom background size & the login form background position incorrectly
* Tested up to WordPress 7.1

= 4.3 | November 30, 2023 =
* Caution! Please follow the instructions on the admin notice and migrate to Ultimate Dashboard as this plugin is no longer actively supported.
* Tested up to WordPress 6.4

= 4.2 | July 21, 2022 =
* Fixed: Only show migration notice to users with the mandatory capabilities.
* **Notice:** This version fixes an issue where non-admins were able to trigger the migration process which fails to install Ultimate Dashboard. If that’s been the case for you, please install Ultimate Dashboard manually. Your data was already migrated successfully.

= 4.1.1 | July 20, 2022 =
* Fixed: Erident sets the logo URL to the site URL by default. This was skipped during the migration and is now fixed.

= 4.1 | July 19, 2022 =
* Announcement: Erident Custom Login & Dashboard is now Ultimate Dashboard! You will be greeted with a 1-click migration to safely migrate from Erident to Ultimate Dashboard. Learn more about why we have made this decision in the announcement post - https://ultimatedashboard.io/blog/erident-custom-login-dashboard-is-now-ultimate-dashboard/
* Fixed: Form background image button didn't trigger the media popup

= 4.0.1 | April 29, 2022 =
* Tweak: Added sidebar layout
* Fixed: "Load Default Settings" button appeared on Tools page
* Fixed: Some settings were not mobile responsive

= 4.0 | April 29, 2022 =
* Version 4.0 is a complete refactor of Erident Custom Login & Dashboard with a modern look and a much improved user experience. For even more options, check out **[Ultimate Dashboard](https://ultimatedashboard.io/?utm_source=erident&utm_medium=changelog&utm_campaign=udb)**.

= 3.5.9 (01 Apr 2021) =
* XSS Security Fix
* Compatibility with latest WordPress version

= 3.5.8 (27 May 2020) =
* Password Label field color fix
* Compatibility with latest WordPress version

= 3.5.7 (23 Jun 2019) =
* Complete shadow removal for forms
* Updating deprecated function login_headertitle

= 3.5.5 (29 Nov 2017) =
* Option to add Login button's text color
* Removed conflicting css

= 3.5.4 (9 Mar 2017) =
* Fixed the function names which may conflict with other plugins

= 3.5.3 (6 Feb 2017) =
* Fixed warning in dashboard
* Minor CSS improvement on register page

= 3.5.2 =
* Hide Register | Lost your password link
* Hide Back to your website link
* CSS updates to override default wp shadows

= 3.5.1 =
* Compatibility fix with WordPress 4.5
* Minor bug fix

= 3.5 =
* Option to keep default WordPress version on dashboard footer
* Persian translation added
* Security fix for CSRF

= 3.4.1 =
* Fixed function for php older version

= 3.4 =
* Supports WordPress media uploader inside the settings page
* Turkish translation added

= 3.3.1 =
* Extended link shadow enable/disable option to login button as well
* Moved screenshots to assets directory

= 3.3 =
* Added Background Opacity feature to the Login Form
* Now Register button will also take same color of Login button

= 3.2 =
* Minor fix for Logo size option.
* Added German translation.

= 3.1 =
* Major bug fix on backend.
* Striped slashes from apostrophe texts.

= 3.0 =
* Huge database performance upgrade on backend
* Added Import/Export settings feature
* Added French translation
* Improved UI with latest WordPress 3.9

= 2.3.1 =
* Fixed one function conflict with some themes.

= 2.3.0 =
* New option to customize Login button Color
* Localized the entire plugin and ready for translation
* Spanish translation available

= 2.2.1 =
* Fixed constants Notice.
* Fixed Image background cut on small screen resolutions.

= 2.2.0 =
* New Default UI for login page.
* Optimized the backend files.

= 2.1.1 =
* Fixed Background property overriding problem with some themes and plugins.

= 2.1.0 =
* Added Login Screen Background size Property

= 2.0.1 =
* Bug Fix

= 2.0.0 =
* Enhanced UI for easy usage (Added an option to hide/show settings block)
* Added option for Logo Width and Height

= 1.5.1 =
* Fixed a small bug
* Help texts improved and added new FAQ question

= 1.4 =
* Added an option for Login Form label and input text's font size
* Added an option for Login Form input text color
* Bug fixed for settings link conflicting

= 1.3 =
* Added an option for Form Shadow.
* Fixed Minor bugs.

= 1.2 =
* Fixed the issue of conflicting with other plugins on admin pages.

= 1.1 =
* Added "background-position" option for the screen & login form background images.
* Added a link on Settings tab for directly open the login.php page on a new tab.
* Improved the css layout of Settings page
* Added a "Quick Links" section at the bottom of settings page.
* Improved the tutorial texts on settings page.

= 1.0 =
* First version.
