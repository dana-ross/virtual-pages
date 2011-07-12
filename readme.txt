=== Plugin Name ===
Contributors: csixty4
Donate link: http://catguardians.org
Tags: page, query
Requires at least: 2.9
Tested up to: 3.0
Stable tag: 0.4.1

Allows creation of "virtual pages" that query posts and display results using regular page templates.

== Description ==

Please note: This is an experimental plugin at the moment and should not be used on live sites. Testing and feedback are welcome, and will help me get to a stable 1.0 release quicker. Thanks!

Virtual Pages is my first stab at a WordPress equivalent to Drupal's powerful Views module. It lets site creators choose from query_posts parameters (category, tag, author, etc.) as well as a date range to display posts on a "virtual page" at a given permalink.

These pages exist outside the page table, so nobody has to learn any shortcodes or do any PHP coding.

Results output to the standard page templates included in themes, so developers no longer need to write a custom template with a call to query_posts() in order to do more than a simple list of posts.

Looking for this same functionality, but in a widget? Check out the Query Posts Widget plugin.

NOTE: Virtual Pages requires PHP 5.0 or higher and permalinks enabled.

== Installation ==

1. Make sure your server is running PHP 5.0 or higher
1. Make sure permalinks are enabled in your WordPress site
1. Install the plugin through the "Add New" option in the Plugins menu, or upload the `virtual-pages` directory to your site's `/wp-content/plugins/` directory
1. Activate the plugin
1. Set up some virtual pages! Go to the 'Settings' menu and pick 'Virtual Pages'

== Screenshots ==

1. Virtual pages index

2. Virtual pages editor

3. Example of a virtual page

4. Deleting a virtual page in the editor

== Changelog ==

= v0.4.1 =
* 2010-07-05  Dave Ross  <dave@davidmichaelross.com>
* Open beta
* Include $title in the title
* Fixed edit link when clicking on page title (was hard-coded to virtualpages.localhost...) - thanks Tom!
* Specifying tags (tag__in) and categories (category__in) was returning 0 results. Going with (cat) instead, although this will also select child categories.
* Editor was losing the value of posts_per_page

= v0.4 =
* 2010-07-05  Dave Ross  <dave@davidmichaelross.com>
* Open beta
* WordPress 3.0 subdirectory multisite compatibility

= v0.3.2 =
* 2010-06-26  Dave Ross  <dave@davidmichaelross.com>
* Open beta
* Fixed array errors

= v0.3.1 =
* 2010-06-26  Dave Ross  <dave@davidmichaelross.com>
* Open beta
* Removed debugging code to turn on error reporting

= v0.3 =
* 2010-06-23  Dave Ross  <dave@davidmichaelross.com>
* Open beta

= v0.1 =
* 2010-05-21  Dave Ross  <dave@davidmichaelross.com>
* Closed beta