This is a WordPress plugin. [Official download available on wordpress.org](https://wordpress.org/plugins/duplicate-menu/).

# Duplicate Menu

Easily duplicate your WordPress menus with one click

## Description

Some WordPress installs use very elaborate navigation systems powered by core Menus. They're a fantastic feature that can often make or break a theme. Menus aren't very portable out of the box, however. If you're looking to make a change to a Menu you're pretty much working live without a quick way to revert back to an old version. That's where Duplicate Menu comes in.

Duplicate Menu will allow you to create a second (or third, or fourth, etc.) copy of an existing Menu to do with what you will. It generates the clone on a programmatic level and recreates all necessary relationships to ensure the structure is retained as well.

Find out more information in my [explanatory article on Duplicate Menu](http://mondaybynoon.com/wordpress-plugin-duplicate-menu/)

## Installation

1. Download the plugin and extract the files
1. Upload `duplicate-menu` to your `~/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

## Usage

After activation, navigate to Appearance > Duplicate Menu to create a copy of an existing Menu

![Easily create a carbon copy of an existing Menu](https://mondaybynoon.com/wp-content/uploads/2017/10/screenshot-1.png)

### Changelog

#### 0.2.2
- Fixes Deprecation Warning

#### 0.2.1
- Change capability to <code>edit_theme_options</code>
- Added some inline documentation
- Fixed link in readme
- Updated screenshot

#### 0.2
- Added <code>duplicate_menu_item</code> action, allowing devs to bolt on custom functionality

#### 0.1.1
- Removed anonymous function call to support PHP <5.3 installs
- Added link to GitHub, please contribute!

#### 0.1
- Initial release
