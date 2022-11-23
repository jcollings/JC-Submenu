=== JC Submenu ===
Contributors: jcollings
Donate link: 
Tags: submenu, menu, dynamic, custom post type, taxonomy, child pages
Requires at least: 3.0.1
Tested up to: 5.5
Stable tag: 0.9.1
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

** What is a WordPress Split Menu **

A WordPress Split Menu is the process of creating a multi level menu, and displaying a menu section relative to the currently viewed object/page. For Example if a site has the following menu and pages:

```
Page 1
 - Page 1.1
 - Page 1.2
Page 2
 - Page 1.3
```

Creating a split menu section for this example, you would set the start depth of 1, this means that when you are viewing any page or child page of Page 1 the split menu would output related items only:

```
Page 1
 - Page 1.1
 - Page 1.2
```

and if you were viewing any page or child page of Page 2 the split menu section would be:

```
Page 2
 - Page 1.3
```

** Wordpress Split Menu Widget **

JC Submenu plugin comes with a widget titled “Split Menu Widget” which allows you to output your split menu within a wordpress sidebar.

It is located under Appearance > Widgets in your wordpress administration area.

Widget Settings:

* Choose Split Menu widget from the list of avaliable widgets
* Select Menu – Choose the menu you wish to split
* Start Level – Choose the starting depth to display
* Menu Depth – Choose how many levels you wish to display
* Show Parent – Choose to display the split menu’s parent item
* Show Hierarcy – Choose to display a flat or nested list
* Trigger Depth – Choose how many levels of children you would like to display in relation to the activate page, 0 = show all. (Added in v 0.62)

** Split Menu Action **

JC Submenu plugin comes with a built in wordpress action to easily output a split menu anywhere within your theme.

```
<?php
do_action( 'jcs/split_menu' , $menu, $args = array() );
?>
```

* hierarchy – Whether to display a flat list instead
* start – Menu depth you wish to split from
* depth – Amount of levels you wish to display
* show_parent – Whether to display the parent item
* trigger_depth – how many levels of children you would like to display in relation to the activate page, 0 = show all. (Added in v 0.62)
* menu_class – Set the class of the menu element
* menu_id – Set the id of the menu element
* container – what to wrap the ul in
* container_id – Set the id of the container element
* container_class – Set the class of the container element

= How do i output a section of menu =

** Menu Section Widget **

JC Submenu plugins comes with a widget titled "Section Menu Widget" which allows you to output a section of your wordpress menu within a sidebar.
It is located under Appearance > Widgets in your wordpress administration area.

Widget Settings:

* Choose Section Menu widget from the list of avaliable widgets
* Select Menu Part – Choose the menu section you wish display
* Menu Depth – Set how many levels of the menu you wish to display
* Show Parent – Choose whether to display the parent item

** Menu Section Action **

JC Submenu plugin comes with a built in wordpress action to easily output a section of a menu anywhere within your theme.

```
<?php
do_action( 'jcs/menu_section' , $menu, $args = array() );
?>
```

* hierarchy – Whether to display a flat list instead
* start – Menu item you wish to display from
* depth – Amount of levels you wish to display
* show_parent – Whether to display the parent item
* menu_class – Set the class of the menu element
* menu_id – Set the id of the menu element
* container – what to wrap the ul in
* container_id – Set the id of the container element
* container_class – Set the class of the container element

** Menu Section Shortcode ** 

Display a section of your wordpress menu within your visual editor using wordpress shortcodes.

```
[jcs_menu_section menu="" hierarchy="1" start="1" depth="5" show_parent="0" /]

```

* menu – Choose the menu you wish to display
* hierarchy – Whether to display a flat list instead
* start – ID Menu item you wish to display from (can be found by hovering over the menu item within the wordpress menu admin page)
* depth – Amount of levels you wish to display
* show_parent – Whether to display the parent item

= How do i automatically populate menu items =

JC Submenu WordPress plugin takes the hassle out of creating menus, by removing the manual addition of items and allowing you to automatically populate menu items from posts, categories, tags, custom post types, and taxonomies.

Each with many customisable options allowing you to refine your final menu.

** Enabling Automatic Menu Population **

Once you have the JC Submenu installed, from the menus section

* Add a menu item that you wish to have posts as a submenu and click on save menu.
* When the page reloads, click on the arrow on the right hand side of the menu item to display the advanced options.Tick the checkbox labelled JC Submenu – Automatically populate submenu to show an accordion of population options.

** Populate WordPress Menus with Posts **

1. Click on the section labelled Populate from post type.
* Post Type – Make sure Post is selected from from the drop down labelled Post Type
* Taxonomy – If you wish to only show posts from a certain category/taxonomy choose the appropriate taxonomy from the drop down labelled Taxonomy (otherwise choose All), If you wish to filter it down further by choosing a term within that taxonomy choose the appropriate term from the drop down labelled Terms.
* Order – Choose which field you want to order and how you’d like it ordered Ascending or Descending.
* Post Limit – Setting the number of posts to display, Set to 0 if you wish not to have a limit.

** Populate WordPress Menus with Taxonomies **

1. Click on the section labelled Populate from taxonomy.
* Taxonomies – Choose the taxonomy from the drop down you wish to populate from.
* Order – Choose which field you want to order and how you’d like it ordered Ascending or Descending.
* Hide Empty Terms – Hide terms that do not have any posts / custom posts.

** Populate WordPress Menus with Child Pages **

1. Click on the section labelled Populate from pages.
* Parent Page – Choose the parent page to fetch the child pages.
* Order – Choose which field you want to order and how you’d like it ordered Ascending or Descending.


= What Actions and filters are avaliable in this plugin =

** jcs/menu_item_args **

jcs/menu_item_args filter is used to edit wp_nav_menu options on a per menu item basis allowing a truely customizeable menu output.

Example – Display Menu Item Image

This example shows how to add an image before a menu item depending on that items object type.

```
add_filter( 'jcs/menu_item_args', 'jcs_menu_item_args', 10, 2);
function jcs_menu_item_args($args, $item){
 
	switch($item->object){
		case 'page':
			// add blue 10 pixel image before the menu item name
			$args->link_before = '<img alt="" src="http://placehold.it/10x10/0000FF" />';
		break;
		case 'term':
			// add green 10 pixel image before the menu item name
			$args->link_before = '<img alt="" src="http://placehold.it/10x10/00FF00" />';
		break;
		case 'post':
			// add red 10 pixel image before the menu item name
			$args->link_before = '<img alt="" src="http://placehold.it/10x10/FF0000" />';
		break;
	}
	return $args;
}
```

** jcs/item_classes **

Add extra classes to menu items containing their item type.

```
add_action( 'jcs/item_classes', 'jc_edit_item_classes', 10, 3 );
function jc_edit_item_classes($classes, $item_id, $item_type){
 
	$classes[] = "item-$item_type";
	return $classes;
}
```

** jcs/item_title **

Display the dynamic menu type at the end of the item title.

```
add_filter('jcs/item_title', 'jc_edit_item_title', 10, 3);
function jc_edit_item_title($title, $item_id, $item_type){
 
	if($item_type == 'term'){
		$title .= ' (term)';
	}elseif($item_type == 'page'){
		$title .= ' (page)';
	}else{
		$title .= " ($item_type)";
	}
 
	return $title;
}
```

= How do i use JC Submenu when my theme uses a custom menu walker =

JC Submenu allows you to disable its custom menu walker making it compatible with themes which uses there own menu walkers.

To enable compatability with other menu walkers add the following code into your themes functions.php file.

``` 
// enable compatibility with theme custom menu walkers
add_filter('jcs/enable_public_walker', 'jc_disable_public_walker');
function jc_disable_public_walker($default){
    return false;
}
```


== Screenshots ==

1. JC Submenu, Post population options
2. JC Submenu, Taxonomy population options
3. JC Submenu, Page children population options
4. JC Submenu, Advanced Submenu Widget Options

== Changelog ==

**0.9.1**

* FIX - compatability issues with new jQuery version, .live -> .on .attr('checked) -> .prop('checked')

**0.9.0**

* Add menu-item-has-children to account for populated menu items

**0.8.6**

* Fix compatability with WP 5.3 - Walker::walk ($elements, $max_depth, … $args)

**0.8.5**

* Fix menu deprecated function create_function()

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
