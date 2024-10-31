=== Perfect Lazy Loading ===
Contributors: loremipsumgraz, polarismedia
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.html
Tags: SEO, Lazy Loading, autosize
Requires at least: 4.8
Tested up to: 4.9.8
Stable tag: 1.0.2
Requires PHP: 7.0

Improve your pageload time by lazy loading images depending on their needed size.

== Description ==

### Perfect Lazy Loading: Automatically loads the matching imagesize

Does not only lazy loading on your WordPress-Output. It also allows you to define which image-sizes from your template are useable by the javascript to insert that image that fits best to the needed width.

#### Adjustable in backend

* Define in backend which image-sizes should be used by default
* Define differend images-sizes for each custom-post-type
* Allows to set +/- percent to switch earlier to bigger or smaller images for the depending width for using browser scaling effects
* Has special settings area for WPBbakery page builder plugin


#### Automatic replacement in frontend

* Should work with every template which is using the native WordPress functions
* Creates small 1x1 pixel thumbnails to use as placeholder before lazyloading the images
* By calculationg the needed width within the content-element it loads the next best fitting image in size for optimal bandwidth useage



== Installation ==

=== From within WordPress ===

1. Visit 'Plugins > Add New'
1. Search for 'Perfect Lazy Loading'
1. Activate Perfect Lazy Loading from your Plugins page.
1. Go to "after activation" below.

=== Manually ===

1. Upload the `perfect-lazy-loading` folder to the `/wp-content/plugins/` directory
1. Activate the Perfect Lazy Loading plugin through the 'Plugins' menu in WordPress
1. Go to "after activation" below.

=== After activation ===

1. You should find the Perfect Lazy Loading Menu-item on the bottom left.
1. Go through the configuration  and set up the plugin for your site.
1. You're done!

== Frequently Asked Questions ==

Actually there are none :P

== Screenshots ==

1. General plugin settings
2. Post Type specific overwrites
3. WP Bakery Page Builder - additional settings
4. Import / Export functionality

== Changelog ==

= 1.0.2 =
Release Date: 12 Oct 2018
1. Fixed bug that caused an error if 'sizes' was not set in image object.

= 1.0.1 =
Release Date: 09 Oct 2018
1. Added icon and banner for plugin repo
1. Added screenshots
1. Excluded acf specific post types from post type overwrite settings
1. Added Text Domain and Domain Path to plugin file
1. Updated Readme file

= 1.0 =
Release Date: 05 Oct 2018

Initial release