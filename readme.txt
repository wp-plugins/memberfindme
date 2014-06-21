=== Plugin Name ===
Contributors: sourcefound
Donate link: http://memberfind.me
Tags: memberfindme, event calendar, event tickets, directory, membership management, subscription, billing, stripe, paypal, quickbooks
Requires at least: 3.0
Tested up to: 3.9.1
Stable tag: 2.2
License: GPL2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

All-in-one membership and subscription management, event calendar, event ticketing, shopping cart, donations and member directory system for membership groups and organizations.

== Description ==

[MemberFindMe](http://memberfind.me/) is a all-in-one membership management, subscription management, event calendar, event ticketing, and member directory solution for chambers of commerce, professional groups, associations and other membership groups.

This plugin integrates MemberFindMe membership, event and directory system to your Wordpress site. MemberFindMe account required; MemberFindMe is free for small groups and also free to try with our 50 member/account plan.

= Membership and Subscription Management =

* Dashboard lets you to see membership metrics, member signups, member renewals, expired memberships, and event sales in one place
* Labels lets you find members by committees, categories and more
* Member Timeline \(CRM\) allows you to see a member's history at a glance
* Group memberships for families, businesses or organizations
* Collect membership subscriptions and dues with [Stripe](http://stripe.com) or [Paypal](https://paypal.com)
* Multiple billing options for each membership level - including automatic recurring billing, online billing, pay by check and lifetime billing
* Setup tax rates for membership by city, state, zip or country
* Automatically send upcoming renewal and past-due notices to members, customized for each membership level
* Members can manage their membership billing and profile from your website
* Financial dashboard charts your membership, donation, event and tax revenue
* Export membership data, payments, invoices, and fees to QuickBooks or a spreadsheet
* Bulk email members
* Custom fields and customizable membership forms

= Restrict Access for Members =

* Restrict access to members only on any page or post with a shortcode
* Restrict access by membership level or label

= Event Calendar =

* Infinitely scrolling event calendar promotes discovery and reduces friction
* Event list view
* Color coded events on event calendar
* Event categories
* Allow members to add or edit events with layered permissions
* SEO optimized with Rich Snippets
* Upcoming events widget

= Event Ticketing and Registration =

* Pay for event tickets on-page through [Stripe](http://stripe.com) or [Paypal](https://paypal.com)
* Automatic event registration confirmation email with iCalendar attachment let attendees add your event to their calendars \(works with Outlook, gmail, etc\)
* Create custom event registration questions
* Create multiple event tickets
* Create free or paid event tickets
* Setup tax rates for events by city, state, zip or country, override tax settings by event or ticket
* Limit event ticket quantity, event capacity, event tickets per registration, and more
* Member only event tickets
* Email event attendees with updates
* Cancel and refund a single event registration, or the entire event
* Edit event registrations and issue partial refunds
* Export registration information, event invoices, event payments and fees to QuickBooks or a spreadsheet
* Event registration and event check-in updates membership Timeline for members

= Member Directory and Deals =

* Smart keyword search delivers more relevant search results
* Search directory by area or by labels/categories
* Interactive map view for directory
* Multiple locations for a business or organization
* Business card motif allows your members to express their identity
* Rich, customizable member profiles with logos, pictures, map, social media links and more
* Create member deals or offers
* Comply with CAN-SPAM act and protect member emails from spam with our messaging system
* Generate leads with user recommendations
* SEO optimized with Rich Snippets
* Member slideshow widget

= Forms, Shopping Cart and Donations =

* Create forms for committee or volunteer signup, contact forms, and more
* Sell items 
* Collect fixed or flexible donations
* Combine shopping cart and donations on one form
* One page checkout for higher conversions
* Create custom questions for each item
* Setup item quantities and checkout limits
* Setup tax rates by city, state, zip or country, override tax settings by form or by item
* Create custom email receipts by form, item or donation
* Automated notifications by item, donation or form
* Log form activity to membership Timelines
* Setup actions to add or remove labels, or create new accounts in specific folders
* Combine shopping cart, form actions and member only access to sell access to online content
* Track donations and item sales for financial analysis or for export to QuickBooks or spreadsheet

...and much more\!

== Installation ==

1. Install the MemberFindMe plugin via the WordPress.org plugin directory or upload it to your plugins directory.
1. Activate the plugin
1. Sign in with your MemberFindMe account
1. Create pages for your membership sign up form, member sign-in form, member directory, member deals page, event calendar using our shortcodes
1. Create your membership levels, and setup Stripe or Paypal for membership billing
1. Setup your membership and event tax rates as needed
1. Import your members with a csv file
1. To restrict access to pages or posts for members, also install the MemberFindMe Login Connector plugin
1. For help, videos and documentation, see the Help section in the plugin

== Frequently Asked Questions ==

= Can I use this plugin without a MemberFindMe account? =

This plugin is an interface to the MemberFindMe service, so a MemberFindMe account is required. Please visit [MemberFindMe](http://memberfind.me/) for more information.

= How do I setup the membership sign-up form, member sign-in form, directory, deals, event calendar? =

Create pages and use the corresponding shortcode on the page. For the most up-to-date list of shortcodes please refer to the plugin help section under MemberFindMe > Help > Plugin > MemberFindMe widgets & shortcodes.

The membership forms and directory profiles are pre-setup with a good default. You can further customize the form and profile templates under MemberFindMe > Customization.

= Can I customize the colors in the membership forms, events, etc? =

Information about customizing css can be found in the plugin under MemberFindMe > Help > Customization > CSS and colors

= Can I use Paypal Standard for membership billing? =

No, at this time MemberFindMe requires Paypal Pro in order to interface directly with Paypal, in order to track fees and payments in the Timeline, generate membership invoices and financial data, issue refunds, and more.

If you do not have Paypal Pro and do not want to deal with the fees, we recommend using Stripe.

= Can I restrict access to a page or post by membership level? =

Yes you can restrict access by membership level or label, use the shortcode \[memberonly label="..."\]

Note that the MemberFindMe Login Connector plugin is required.

= Can I create event tickets restricted to a membership level? =

Yes you can restrict event tickets to specific membership levels and/or labels.

= What automated membership or event emails are generated by the system? =

MemberFindMe can generate the following automated emails to your members:

* Upcoming membership renewal 
* Past-due membership
* New member welcome
* Membership payment receipts
* Membership payment failure for automatic recurring billing \(eg. expired credit card\)

MemberFindMe generates the following automated emails to your event attendees:

* Event registration confirmation with iCalendar attachment
* Event payment receipt

MemberFindMe also generates the following automated emails to you as the administrator:

* New member signup
* Member signup failure due to billing error \(eg. invalid credit card\)
* Event registration
* Email delivery bounce or failure for receipts, bulk emails, welcome email, and more

= Can I email my members directly from the system? =

You can email any group of members directly from the system, including:

* Individual member or individual contact under member account
* Members who signed-up in any date range
* Members who renewed in any date range
* Members with upcoming membership renewals for any date range
* Members with past-due memberships for any date range
* Members with a specific membership level
* Members with a specific label
* Members in any folder
* Members pulled up with a keyword search

Within any group you can also individually remove a member from the email. You can also include all contacts stored under a member account in the email.

= How do I create categories for my directory? =

You can enable membership levels or labels as categories for the directory, simply by enabling the option to make them searchable in the directory.

= Can I create multiple directory pages, or create custom directories? =

You can create custom directory pages or custom actions on a directory page, that corresponds to a search by area, by label or membership level, or by keyword, or any combination of.

For more information, please refer to MemberFindMe > Help > Customization > Members Directory

== Screenshots ==

1. Member profiles.
2. Event calendar.
3. Membership Timeline \(CRM\).
4. Membership metrics, signups, renewals and event registrations.
5. Chart membership and event revenue.
6. Interactive member directory map.
7. Member deals.

== Changelog ==

= 0.1 =
* Initial release

= 1.0 =
* Stable release

= 1.2 =
* SEO improvements

= 1.5 =
* Adds menu for label and membership

= 1.6 =
* Adds menu for advanced customization

= 1.6.1 =
* Adds option to disable social share buttons
* Adds option to load js/css inline

= 1.6.2 =
* Widget can now pull contacts from a label
* Adds option to display contact name on cards

= 1.7 =
* Moves plugin settings under MemberFindMe menu
* Improved http request compatibility

= 1.7.1 =
* Adds listlabel and listfolder shortcodes
* Adds support for label specific directory

= 1.7.2 =
* Adds support for group specific event calendar

= 1.7.3 =
* Minor bug fix

= 1.8 =
* Adds listevents shortcode
* Adds ability redirect to another page after signing in \(from account manage screen\)

= 1.8.1 =
* Fixes minor bug with member slideshow widget

= 1.8.2 =
* Fix for members slideshow widget for themes that do not set ID on widget

= 1.8.3 =
* Fix for page titles on themes that do not provide correct number of parameters
* Adds support for directories by folder

= 2.0 =
* Adds support for Member Sign in and redirect to page
* Adds forms, shopping cart and donations feature

= 2.1 =
* Fixes escaped html in event and member widgets
* Search engine support for directories by folder

= 2.1.1 =
* Adds separator between dates in event widget and event list shortcode
* Minor bug fixes

= 2.2 =
* Prevents W3 Total Cache and WP Super Cache from caching dynamic pages
* Adds setting for redirect url on log out