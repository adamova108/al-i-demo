# AL-I Demo

A simple demo plugin based on WordPress. :relaxed:

## Installation

### Composer

If this repository was *public* or if you had an auth token or an SSH key, the plugin could be simply installed with 
composer, 
like so:

```$ composer require adamova108/al-inpsyde``` 

### Without Composer

#### Direct upload

1. Upload the plugin _zip_ file in WordPress admin manually as you would normally do with other plugins.
2. Alternatively you can upload the **unpacked** plugin folder to the *wp-content/plugins* folder directly. 
3. Activate the plugin on the *Plugins* admin page.

## Configuration

Once the plugin is activated, it will add a menu item called ```"AL Inpsyde"```. There is only one menu page where 
you can set the transient expiration in seconds and turn "pseudo" debug mode on or off.  

## Usage

There are two built-in URL which show the users table:

1. *yoursite.url/al_users* - which shows the table "raw" without the active theme's header and footer, but using the 
   theme's styling.
2. *yoursite.url/al_users_page* - same as #1 with the difference that this outputs the active theme's default page 
   header and footer as well.
3. You can use the ```[al_users_table]``` shortcode wherever shortcodes can be used (such as post and page editors).

## License and Copyright

Copyright (c) 2021 Ádám Luzsi

The _Al Inpsyde Plugin_ code is licensed under [GPL-3.0](https://www.gnu.org/licenses/gpl-3.0.html).
