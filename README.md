# all-in-one-analytics

=
== Description ==

Version 1!

Trying to bring user-level analytics to WordPress.

All of the tracking is based on user IDs for both anonymous and registered users.

It's a custom distribution of Segment.com's analytics.js for the client-side events, and my own solution for the server-side events.

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

== Frequently Asked Questions ==

= Can this add the basic tracking codes to my site? =

Yes

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


== Screenshots ==

1. This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif).
2. This is the second screen shot

== Changelog ==

= 1.0 =
* A change since the previous version.
* Another change.

= 0.5 =
* List versions from most recent at top to oldest at bottom.
== Upgrade Notice ==

= 1.0 =
Version 1!