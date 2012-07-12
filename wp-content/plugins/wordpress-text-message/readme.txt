=== Wordpress Text Message ===
Contributors: jtprattmedia, totalbounty
Donate link:http://www.totalbounty.com
Tags: wordpress, text message, sms, widget, subscription, text, cell
Requires at least: 2.8
Tested up to: 3.3.1
Stable tag: trunk

Allows people to subscribe to SMS text message updates of your web site to users via cell phone.

== Description ==

Wordpress Text Message

Allows people to subscribe to SMS text message updates of your web site.  Manually send out texts, or every time a post is published.  Plugin has a widget that you can use in your sidebar or any widgitized area or your theme, also has the ability to use a shortcode if you don't want to use the widget.  That way you can add the subscriber signup form to any page or post using the shortcode [wp-text-message-register], you can also add an unsubscribe box using the shortcode [wp-text-message-unsubscribed] anywhere as well.

Currently supports US based cell providers out of the box, the new version now allows you to add / edit / modify the cell phone gateways for your region.  For a list of SMS gateways you can use, please visit our plugin page here: [Total Bounty: WordPress Text Message](http://www.totalbounty.com/freebies/wordpress-text-message/ "Total Bounty: WordPress SMS Text Message Plugin")

When users signup for updates via SMS they enter their cell number and choose a provider from the dropdown.  When SMS text messages are sent out they are sent to that number at the email to text gateway for that provider.  In other words, the texts are sent out via email through the web and then the cellular providers in turn route them to the appropriate cell phone number at their email to SMS gateway.  

They say that email to SMS gateways carry "secondary priority" to regular SMS to SMS traffic - so there is a chance that not all the texts would be received.  In our testing to date we haven't encountered this as an issue at all - but we have received user accounts of some texts not being received.  This would probably depend on your webhost and the frequency of texts you're sending out (and if the email to SMS gateway(s) have you flagged as 'spam').

Originally this plugin only allowed manual sending of SMS text messages to subscribers.  Our enhanced version has all kinds of new features, such as 

- option to notify subscribers for new blog posts

- option to notify subscribers on specific page updates

- option to change widget footer text

- widget spam prevention support (captcha)

- shortcode support for signup and unsubscribe

- send emails out in batches to support big subscriber lists

- ability to edit and modify cell carriers

There are tremendous uses for teachers, schools, non-profits, organizations, clubs, membership websites, and service based businesses.

The cellular carriers supported by this plugin are now limitless (since you can add and / or edit them from the plugin settings sub-menu screen for "Carriers")

Development of this plugin now sponsored by:  [Total Bounty](http://www.totalbounty.com/ "Total Bounty Marketplace")

Originally enhanced by:  [JTPratt Media](http://www.jtpratt.com/ "JTPratt Media: Wordpress Consulting")


**Features**
------------
*sidebar widget allowing people to signup for SMS text updates

*widget captcha

*ability to add subscribers manually from plugin settings page

*support for all the major wireless carriers (now including MetroPCS and US Cell)

*manually send updates to all subscribers

*option to update all subscribers each time a new post is published or edited

*option to update all subscribers for specific page updates

*ability to manage and delete subscribers

*ability to send out text updates in batches

*shortcode support for both signup and / or unsubscribe

*ability to edit the email to SMS gateways for carriers to ones in your geolocation making the plugin able to be used internationally in any country worldwide

**Future Releases**
-------------------
*Please let us know if you'd like to see a feature, majority rules.  If we hear it from enough people we'll do our best to add it.  So far the last 10 things we added to the plugin have been from user suggestions.

Always post your questions and suggestions to official forum page for the plugin:  http://www.totalbounty.com/forums/topic/wordpress-text-message/

We have plans to expand the plugin to a premium version (in the near future) for an option to send out SMS text messages through an official paid SMS gateway for those needing that option (for business).  The free version of the plugin is and will always remain free.

== Changelog ==

= 2.08 =

* changed the shortcode for the adding the registration signup form to pages and posts to [wp-text-message-register] 
* added a new shortcode [wp-text-message-unsubscribed] for adding the unsubscribe form to pages and posts
* add new checkbox option to the registration widget to activate ajax unsubscribe option (on the same widget)
* added separate widget for unsubscribe only
* fixed bug for some people where plugin was only working while logged in to WordPress
* removed unnecessary .spc files
* fixed bug:  when posts were published or edited they weren't always going to the queue to send out SMS updates when that option was chosen in the plugin settings screen
* removed the plugins used of simplepie (no longer needed since magpie is included in WordPress)
*added option to plugin settings for "activate redirect" with link input field - so when users signup you can redirect them to the page/post of your choice and show them a confirmation message of your choice

= 2.07 =

*added a function to strip out that tag in the title for SMS test messages that was a conflict with All in One SEO Pack plugin

= 2.06 =

*added a new sub-menu interface to the plugin for "Carriers" which allows people to add, edit, remove, or delete cell phone carriers for the plugin.  This gives users complete flexibility to completely change the email to SMS gateways to their geolocation making it possible (for the first time) to be used in any country of the world.

= 2.05 =

*fixed issue that caused scheduled posts not be send out SMS text updates to subscribers

= 2.04 =

*fixed issue when new posts are created and option is to send updates via SMS for new posts (checked on the settings page) the link sent via SMS was the root domain.  Now it's the updated post URL (as it should be)

*fix conflict issue with the WP 3.3 drag and drop image uploader (media uploader)

*reconfigured how the plugin sends outgoing mail to try and address whether or not we can fix (at some webhosts) the SMS texts being received with a "from" address of the server (and not the email listed on the plugin settings page).  Please give us feedback on this.

= 2.03 =

*added spam prevention (captcha) to widget

*added ability to change widget footer text

*added ability to place signup form via shortcode wp-text-message

*updated plugin security to use nonce

*updated and changed code structure to be more stable

*added ability to notify subscribers for specific page updates

*added subscriber form redirect to specific URL option

*made update forcing emails to go out in batches (users per batch set in options page)

*added queue subheading so you can see when messages are waiting to go out to subscribers

= 1.65 =
* added support for US cell carrier MetroPCS

= 1.5 = 

fixed form layout issues in options.php for people that had problems saving options AND added US Cell to list of cell providers available when subscribing

= 1.2 = 
removed HTML br tag in options.php that caused people not to be able to check the Send all subscribers SMS text message each time a new post is published buttton

= 1.1 =
added ability to manually add subscribers and auto-update every time a post is published

== Upgrade Notice ==

*1.2 Fixed the inability to check the checkbox on the options page

*1.1 upgrade to get auto-update and manual subscriber features

== Installation ==

1. Always backup your site and database
2. Install plugin manually or using "Add Plugin" feature of Wordpress
3. Activate Plugin
4. Setup the plugin options
5. Edit your cell phone carrier email to SMS gateways for your location (get a PDF list of more than 325 international gateways here, http://www.totalbounty.com/freebies/wordpress-text-message/)
6. Add the widget to your sidebar or use shortcode to add the subscriber signup form to any post or page
7. Manually add subscribers if you need to
8. Send out SMS text messages to subscribers
9.  Check the option to automatically send out SMS updates anytime a new post or specific page is posted

Please let us know any bugs, improvements, comments, suggestions.

== Frequently Asked Questions ==

= Do the phone numbers need to be entered in a certain format? =

No.  (123) 456-7890 is the same as 123-456-7890 is the same as 1234567890 is
the same as 123 456 7890 ad infinitum

= Do I have to use the sidebar widget =

No, you can manually add subscribers phone numbers from the plugin settings page, or you can use the shortcode to place the signup form on any page or post of your WordPress website.

= How do texts get sent out? =

You can either manually send out SMS text updates at your discretion, or click the checkbox on the plugin settings pages to send out a text message update each time a new post is published.  You can even add page ID's to notify subscribers of specific page updates.

= Are Texts sent out always received? =

You have to remember that this plugins sends out emails to email to SMS gateways for cell phone providers (which are "secondary" priority).  We have personally not had a problem with text messages going out, but we always know that email to SMS texts are not guaranteed to be delivered.  We also know that different webhosts and different servers may have different results - espesically if you're sending out a lot of SMS text messages.  If a provider thinks you're sending too many (or somebody flags your IP as spam) some people may not receive your text messages (your mileage may vary).

== Screenshots ==

1. The main plugin settings screen where you can customize the text for the widget footer and header, as well as the "from" address, and maximum number of characters.  The last setting is the checkbox, if you wish to send out text message updates each time a new post is published.
2. This is the subscriber management screen, listing the phone number, carrier, and submit date.  Here you have the ability to delete subscribers.
3. This is the Add Subscribers page within plugin settings, where you can add subscribers manually yourself from the WordPress dashboard.
4. This is what the sidebar widget looks like by default for people to signup for SMS text updates.
5. This is the add / edit carrier screen where you can add / edit / or modify any of the cell phone provider email to SMS gateways used by the plugin
6. This is what the unsubscribe widget looks like on the front end
7. This is what the subscribe widget looks like on the front end with the integrated unsubscribe option (ajax style)
8. This is the backend Register (subscribe) widget
9. This is the front end ajax integrated popup to unsubscribe
10. This is the backend Unsubscribe widget

== Wordpress Text Message ==

Text message updates:

1. Sidebar Signup widget where people can signup for text message updates
1. Subscribers can be added manually from the plugin settings page
1. Text message updates can be sent out manually from plugin settings page
1. Checkbox option to automatically send out updates each time a new post is published

Visit our homepage at [Total Bounty](http://www.totalbounty.com/ "Total Bounty Marketplace") or our forums page at [Total Bounty Forums](http://www.totalbounty.com/forums/ "Total Bounty Forums")

We look forward to hearing your comments and suggestions.  We're not saying that we can add everything everybody wants - but the more times we hear a feature, the more likely we'll be to add it!
