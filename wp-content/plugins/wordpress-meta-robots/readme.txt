=== WordPress Meta Robots ===
Contributors: destio
Donate link: http://www.destio.de/tools/wp-meta-robots/
Tags: crawler, meta elements, meta robots, meta tags, search engine, search index, seo, spiders
Requires at least: 3.0+
Tested up to: 3.2.1
Stable tag: 1.9

With this plugin you can control the indexing behaviour of search engines for any post or page.

== Description ==

This plugin will give you full control of the `meta robots` tag for each post or page. `meta robots` have a direct influence to the indexing behaviour of search engines.
After activation you will find it as a WordPress typical meta box on the edit page in admin area.

From a dropdown menu you can choose the following options:

* index, follow
* index, nofollow
* noindex, follow
* noindex, nofollow

By creating an new post/page the default value is automatically set to `index, follow` without any user input. Categories, tags, and archiv are set to `noindex, follow`. Search results, 404 and others to `noindex, nofollow`.

**Related Links**

* [Homepage](http://www.destio.de/tools/wp-meta-robots/ "Homepage of WordPress Meta Robots")
* [Changelog](http://wordpress.org/extend/plugins/wordpress-meta-robots/changelog/ "Changelog for WordPress Meta Robots")
* [Questions](http://wordpress.org/extend/plugins/wordpress-meta-robots/faq/ "FAQ for WordPress Meta Robots")
* [Information](http://googlewebmastercentral.blogspot.com/2007/03/using-robots-meta-tag.html "Using the robots meta tag")

**Note:**
Please update now! Before installing or upgrading new plugins, please backup your database first!

== Installation ==

There are several ways to install a plugin in WordPress. For newbies this way is highly recommended:

1. Navigate to 'Add New' under 'Plugins' menu in admin area
1. Upload or choose `wp-meta-robots.zip` from 'Install Plugins'
1. Activate the plugin through the 'Plugins' menu in WordPress

For correct working make sure that your site visibility isn't blocked. You can find this option under 'Privacy' in 'Settings' menu. Prevent to intall more than one plugin for the same task. Prevent double entries in the header and values which are in conflict to each other.

== Frequently Asked Questions ==

= Do I have to add any code in the template? =

No, the plugin will automatically add the `meta tag` in the header.

= How I can be sure that the plugin works properly? =

Go to the frontend of your page and check the source code.

= Will this plugin works together with other SEO plugins? =

Yes, for sure. But check your code for double entries.

= What to do when the plugin is not shown on the edit page? =

Check if 'Meta Robots'are visible under 'Screen Options'.

== Screenshots ==

1. screenshot-1.gif

== Changelog ==

= 1.9 =
* Version 1.9: Small optimization in code and readme.txt

= 1.8 =
* Version 1.8: Adding forgotten line in code for showing meta box in posts

= 1.7 =
* Better description text for 'WordPress Plugin Directory'

= 1.6 =
* Optimizing code with ternary operator

= 1.5 =
* Prevent indexing category, tags, search, archiv, 404

= 1.4 =
* Isolating functions in own class.

= 1.3 =
* Adding meta robots tag in wp_head by activation.

= 1.2 =
* Display meta tag only for posts/pages.

= 1.1 =
* Variable plugin basename.

= 1.0 =
* Basic functionality with extra field in database.

== Upgrade Notice ==

**Before installing or upgrading new plugins, please update you database first.**