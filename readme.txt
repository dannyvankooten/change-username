=== Change Username ===
Contributors: Ibericode, DvanKooten
Tags: username, users, login
Requires at least: 4.1
Tested up to: 6.1
Stable tag: 1.0.1
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Requires PHP: 5.3

Change usernames of your WordPress users easily & effectively.

== Description ==

#### Change Username

The Change Username plugin allows you to change the usernames of your WordPress users in an easy and effective way.

We found that existing plugins to change the username in WordPress did not scale at all for sites with many users.
This plugin does not come with its own settings page but instead simply hooks into the "edit user" form.
It then processes the form over AJAX, resulting in a much better experience.

Here's what it looks like: [demo.gif](https://i.imgur.com/QgQvSIA.gif?noredirect).

**Requirements**

- PHP version 5.3 or later.


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

Please check if your server is running PHP version 5.3 or later. The plugin will not do anything if you're on an older version of PHP.


== Screenshots ==

1. The toggle as shown on the "Edit user" page.

== Changelog ==

#### 1.0.1 - Dec 23, 2022

- Always load minified JS asset by default

#### 1.0 - Dec 2016

Initial release.

