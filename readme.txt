=== Change Username ===
Contributors: Ibericode, DvanKooten
Tags: username, users, login
Tested up to: 7.0
Stable tag: 1.0.2
License: GPL-3.0-or-later
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Requires at least: 6.0
Requires PHP: 7.4

Change usernames of your WordPress users effectively.

== Description ==

## Change Username

The Change Username plugin allows you to change the usernames of your WordPress users in an easy and effective way.

By default, WordPress itself does not allow usernames to be changed. The other plugins for changing usernames do not scale all that well for sites with a large number of users.

This plugin takes a different approach by simply enhancing the default "edit user" page and then processing the username change over AJAX, resulting in a much faster and user-friendly experience.

### Features 

- Change username of any user on your WordPress site.
- Checks if username is taken before changing it.
- Checks if username is in list of illegal logins.
- Uses the exact same username requirements as WordPress core.
- Retain superadmin rights if used to change the username of a Multisite superadmin.

### Requirements 

- PHP version 7.4 or higher
- WordPress version 6.0 or higher

### About the plugin author 

[Danny van Kooten](https://www.dannyvankooten.com/) has been building WordPress plugins since 2010, starting with WordPress 3.0.

He is the founder of [ibericode](https://www.ibericode.com/), the small software company behind popular WordPress plugins including [Mailchimp for WordPress](https://wordpress.org/plugins/mailchimp-for-wp/) and [Koko Analytics](https://wordpress.org/plugins/koko-analytics/).

== Installation ==

#### Installing the plugin
1. In your WordPress admin panel, go to *Plugins > New Plugin*, search for **Change Username** and click "*Install now*"
1. Alternatively, download the plugin and upload the contents of `change-username.zip` to your plugins directory, which usually is `/wp-content/plugins/`.
1. Activate the plugin

== Frequently Asked Questions ==

#### Where is the settings page?

Change Username does not come with its own settings page. You can change the username of your users on the page where you would normally edit that user.

#### Can users change their own username?

Not right now. Only administrators with the `edit_users` capability can change usernames.

#### I've activated the plugin but nothing happens.

Please check if your server is running PHP version 7.4 or higher. The plugin will not do anything if you're on an older version of PHP.


== Screenshots ==

1. The toggle as shown on the "Edit user" page.

== Changelog ==


#### 1.0.2 - Oct 04, 2024

- Show message when new username is less than 3 characters long.
- Show message when new username is more than 60 characters long.
- Improved request validation in general.
- Remove ES6 code from JS file to support a wider range of browsers.
- Bump required PHP version to 7.2 or higher.


#### 1.0.1 - Dec 23, 2022

- Always load minified JS asset by default


#### 1.0 - Dec 2016

Initial release.

