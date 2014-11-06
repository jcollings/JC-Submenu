=== JC Submenu ===
Contributors: jcollings
Donate link: 
Tags: submenu, menu, dynamic, custom post type, taxonomy, child pages
Requires at least: 3.0.1
Tested up to: 4
Stable tag: 0.8.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

JC Submenu plugin allows you to automatically populate your navigation menus with custom post_types, taxonomies, or child pages.

== Description ==

JC Submenu plugin allows you to automatically populate your navigation menus with custom post_types, taxonomies, or child pages. An easy to use plugin created to be a lightweight menu extension.

Also output a selected section of your dynamic menu through our advanced submenu widget.

== Installation ==

1. First grab a copy from [wordpress.org](http://downloads.wordpress.org/plugin/jc-submenu.zip).
1. Extract the plugin to your wordpress plugins folder.
1. Activate the plugin from your wordpress administration area (under the plugins section).
1. You should thee should now be able to use JC Submenu Plugin.

For further documentation on installing and using JC Submneu features can be found [here](http://jamescollings.co.uk/wordpress-plugins/jc-submenu/).

== Frequently asked questions ==

= How do i use the split menu functionality =

The documentation for using the split menu functionality (widget, shortcode, action) can be found [here](http://jamescollings.co.uk/jc-submenu/resources/output-split-menu-section/)

= How do i output a section of menu =

The documentation for displaying a section of menu (widget, shortcode, action) can be found [here](http://jamescollings.co.uk/jc-submenu/resources/how-to-output-a-section-of-a-wordpress-menu/) 

= How do i automatically populate menu items =

The documentation for automatically populating menu items can be found [here](http://jamescollings.co.uk/jc-submenu/resources/how-to-automatically-populate-wordpress-submenus/)

= What Actions and filters are avaliable in this plugin =

A list of all actions and filters can be found [here](http://jamescollings.co.uk/jc-submenu/sections/actions-filters/)

= How do i use JC Submenu when my theme uses a custom menu walker =

The documentation to disable JC_Submenu_Nav_Walker and use your own can be found [here](http://jamescollings.co.uk/docs/v1/jc-submenu/how-tos/enable-menu-walker-compatability/) 


== Screenshots ==

1. JC Submenu, Post population options
2. JC Submenu, Taxonomy population options
3. JC Submenu, Page children population options
4. JC Submenu, Advanced Submenu Widget Options

== Changelog ==

**0.8.4**

* Fix menu highlighting when not using JC_Submenu_Nav_Walker

**0.8.3**

* Fix add_post_meta not set to unique when saving menu items, causing multiple entries to be saved.

**0.8.2**

* Fix filter jcs/enable_public_walker to actually be used, as before it was not able to be overwritten by themes only plugins

**0.8.1**

* Added jcs/split_menu/populated_elements, jcs/section_menu/populated_elements, jcs/populated_elements to allow modification of populated menu elements
* Updated Readme to be tested upto wp 4

**0.8**

* Fix error when populating with an empty array of posts
* Add jcs/enable_public_walker filter to disable/enable the public walker to allow jc submenu be used with other custom walkers.
* Add jcs/split_widget_title to overwrite split menu title
* Add {{PARENT_TITLE}} template tags to show the active parent item in the split menu widget.

**0.7.2**

* Added filter to change submenu level class jcs/menu_level_class, return array of classes
* Added the option to populate by post date archive
* Added post date archive grouping by year

**0.7.1**

* Fixed infinite loop error when passed badly formed menu items.

**0.7**

* Simplified dynamic menu population
* Added the ability to replace the current item with dynamically populated items

**0.6.2**

* Fixed clone() warning
* Added trigger_depth to split menu
* Fixed Menu Ordering
* Updated FAQ Section

**0.6.1**

* Renamed filter from jci/menu_item_args to jcs/menu_item_args

**0.6**

* Add menu item filters jcs/item_title, jcs/item_url, jcs/page_item_title, jcs/page_item_url, jcs/post_item_title, jcs/post_item_url, jcs/term_item_title, jcs/term_item_url
* Add admin-menu notice to show if item is dynamically populated
* Add compatability to other plugins who use a custom admin walker
* Add setting to disable ajax menu edit
* Add menu item argument filters jci/menu_item_args to allow customisation of output per item

**0.5.5**

* Add class filter jcs/item_classes
* Add class filter jcs/term_item_classes
* Add class filter jcs/post_item_classes
* Add class filter jcs/page_item_classes
* Add WP_Query arguments filter jcs/post_query_args, jcs/post_$menu-item-id_query_args
* Add get_pages arguments filter jcs/page_query_args, jcs/page_$menu-item-id_query_args
* Add get_terms arguments filter jcs/term_query_args, jcs/term_$menu-item-id_query_args
* Ouput post_type with hierarchy
* Removed php strict warnings

**0.5.4**

* Added option into populate by taxonomy to set the term parent

**0.5.3**

* Removed PHP Warning for imploding false in exclude terms list
* Remove PHP Warning for missing array in exclude pages list

**0.5.2**

* Fixed SubmenuWalker replaces order_by with orderby
* Added in basic CSV input taxonomy term exclusion.

**0.5**

* Added option to limit taxonomy depth
* Added option to exclude child pages
* Added Split menu shortcode
* Added Menu section shortcode
* Fixed possible function conflict
* Fixed menu depth
* Fixed split menu
* Fixed menu section
* Fixed post_type / page with taxonomy not highlighting menu item

**0.4.1**

* Added child page order support
* Fixed Javascript jumping bug
* Added version to js,css to fix cache problem
* Added documentation notification link on plugin update
* Added Menu Section Widget

**0.4**

* Added Split menu output action jcs/split_menu, Menu Section output action jcs/split_menu to allow theme developers to output submenus.

**0.3**

* Split Menu Widget Added

**0.2**

*   Interface update
*   Custom post type population can now be filtered by a Taxonomy
*   Javascript update
*   Compatability with wordpress 3.6


== Upgrade notice ==
