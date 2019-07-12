=== All In One Analytics ===
Contributors: juanin8
Donate link: https://in8.io
Tags: google analytics, google ads, google tag manager, facebook pixel, zapier, analytics, tracking
Requires at least: 5
Tested up to: 5.2.2
Stable tag: trunk
Requires PHP: 5.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

All your analytics stuff sorted. Send events from your WordPress plugins to Google Analytics, Facebook Pixel, Zapier, Google Ads, Google Tag Manager... and more soon.

== Description ==

Version 1!

Trying to improve user-level analytics and tracking for WordPress site owners.

All of the tracking is based on user IDs for both anonymous and registered users.

It's a custom distribution of Segment.com's analytics.js for the client-side events, and my own solution for the server-side events.

**You'll need a recent version of WordPress. I can't guarantee it will work with older versions.**

If you're on an older version and it works for you, let me know.

For version 1 here is what's supported:

**EVENT SOURCES**

We collect detailed event data from these sources.

- **WordPress Core:** Signups, log ins, comments, page views, etc...


- **WooCommerce:** Adds to cart (ajax or not), check out events, order events, there are a lot of WooCommerce events. And each one has a lot of properties. I will document it better in the upcoming versions.


- **LearnDash:** Enrollments, topics, lessons, quizzes (completed, passed and failed), courses and assignments


- **Ninja Forms & Gravity Forms**:
Track forms :)


**DATA DESTINATIONS**

This is where we send the data to. Advanced settings are available for each destination (think custom dimensions, advanced matching, hashing, whitelisting, etc...)

- **Google Analytics**
- **Google Ads**
- **Google Tag Manager**
- **Facebook Pixel**
- **Zapier**


The idea is to integrate more events sources and data destinations, as requested by the community.



For now, there are advanced settings that allow you to track custom meta based on meta keys, and a lot of other settings I haven't had time to document yet.

== Installation ==


e.g.

1. Upload `all-in-one-analytics.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to 'Settings' and choose 'All In One Analytics and Tracking' to configure the plugin
4. NOTE: Make sure you switch off any existing analytics/pixels

== Frequently Asked Questions ==

= Can this add the basic tracking codes to my site? =

Yes

= Do I have to remove the existing tracking codes? =

Yes or you'll double count events.

= What properties are attached to each event? =

I will document this more, but basically everything!

For example, an 'Add to Cart' event in WooCommerce will include everything from the product's SKU, price and variation to the product's image url... some events have 20+ properties. All the data.

= Who is this tool for? =

You're able to use it to simply add the tracking codes to your site, but you also have the most advanced tracking features for each destination. So, it's all in one. It's meant for everyone!

= Why haven't you included more sources? =

I have several more in the works including Gamipress, FV Player and CF7. If you have one you want let me know.

= Why haven't you included more destinations? =

I specifically selected the 'launch' destinations: GA, AdWords, Tag Manager and Zapier because with these you can cover a lot of bases!

I will be adding more destinations in the future. Segment's analytics.js supports 100+ destinations already, it's a matter of deciding how to add them so that they don't slow your site down :)

= How does the tracking work? =

There are 3 basic types of 'calls' used by the plugin:
- Page: The details of the page
- Identify: The details of the user
- Track: The details of the event being tracked

Combining the three results in a consistent tracking model.

This is version 1 of the plugin, but I will try to document how to use it and how to make the most of it :) It is all based on Segment.com's open source analytics.js and their tracking models.

= What is the user level tracking section for? =

It's part of functionality that will be included in the next release, for now it just helps with FB advanced matching.

== Screenshots ==

1. Settings for WordPress, Forms, WooCommerce etc...
2. Track core events like comments, sign ups, logins, etc... and name them however you want
3. Track WooCommerce events including clicks, adds to cart, order events, etc...
4. A lot of GA settings
5. A lot of FB pixel settings
6. A lot of Google Ads/AdWords settings
7. Choose which user traits to track or add custom ones


== Changelog ==

= 1.0.1 =
- Fixed a php notice
- Removed a menu item from UI
- Updated readme, minimum requirements and added screenshots.

= 1.0 =
Version 1!


== Upgrade Notice ==

= 1.0 =
Version 1!