## This project is no longer actively maintained, please refer to [CMB2](https://github.com/CMB2/CMB2) for meta-boxes.

<table width="100%">
	<tr>
		<td align="left" width="70">
			<strong>HM Custom Meta Boxes for WordPress</strong><br />
			A framework for easily adding custom fields to the WordPress post edit page
		</td>
		<td align="center" width="20%">
		    <img src="https://hmn.md/content/themes/hmnmd/assets/images/hm-logo.svg" width="100" />
		</td>
	</tr>
	<tr>
		<td>
			A <strong><a href="https://hmn.md/">Human Made</a></strong> project.
		</td>
		<td align="center"></td>
	</tr>
</table>

#### [Get the latest stable release](https://github.com/humanmade/Custom-Meta-Boxes/releases/latest)

It includes several field types including WYSIWYG, media upload and dates ([see wiki for a full list](https://github.com/humanmade/Custom-Meta-Boxes/wiki)). It also supports repeatable and grouped fields.

This project is aimed at developers and is easily extended and customized. It takes a highly modular, Object Orientated approach, with each field as an extension of the CMB_Field abstract class.

The framework also features a basic layout engine for fields, allowing you to align fields to a simple 12 column grid.

![Overview](https://cloud.githubusercontent.com/assets/1039236/19131223/426658c4-8b6c-11e6-808d-b689ee6820ac.jpg)

## Usage

* Download the latest release [here](https://github.com/humanmade/Custom-Meta-Boxes/releases/latest) or clone from master.
* Include the custom-meta-boxes.php framework file.
    * In your theme: include the CMB directory to your theme directory, and add `require_once( 'Custom-Meta-Boxes/custom-meta-boxes.php' );` to functions.php
    * As an MU Plugin: [Refer to the WordPress Codex here for more information](http://codex.wordpress.org/Must_Use_Plugins)
* Filter `cmb_meta_boxes` to add your own meta boxes. [The wiki has more details and example code](https://github.com/humanmade/Custom-Meta-Boxes/wiki/Create-a-Meta-Box)

## Help

* For more information, including example code for usage of each field and instructions on how to create your own fields, refer to the [Wiki](https://github.com/humanmade/Custom-Meta-Boxes/wiki/).
* Not covered in the Wiki? Need more help? Get in touch. support@humanmade.co.uk or ping @mikeselander
* Found a bug? Feature requests? [Create an issue - Thanks!](https://github.com/humanmade/Custom-Meta-Boxes/issues/new)

## About

This plugin is maintained by [Human Made Limited](http://hmn.md)

It began as a fork of [Custom Meta Boxes](https://github.com/jaredatch/Custom-Metaboxes-and-Fields-for-WordPress), but is no longer compatible.

## Minimum Requirements:
* PHP >= 5.4
* WP >= 4.1

## Known Issues
* Some fields do not work well as repeatable fields.
* Some fields do not work well in repeatable groups.

## Contribution Guidelines ##

See [CONTRIBUTING.md](https://github.com/humanmade/Custom-Meta-Boxes/blob/master/CONTRIBUTING.md)

## Changelog ##

**1.1**

_Enhancements_
 - Added group field filter
 - Cleaned up file upload styles
 - Added Hindi translation (props @ajitbohra)
 - Move all field classes to their own files
 - Add min/max attributes to number input (props @shadvb)
 - Use site language with Google Maps field (props @barryceelen)

_Bug Fixes_
 - Filter all arguments, not just select ones
 - Only attempt to call getimagesize() if the icon is local (props @joehoyle)
 - Add Dutch and German translations (props @barryceelen)
 - Align the file button vertically (props @ocean90)
 - Fix for multiple wysiwyg fields not displaying in groups (props @tareiking)
 - Fix incorrect gmap grouped field structure (props @dan-westall)
 - Fix enqueuing of cmb-scripts (props @barryceelen)

**1.0.3**
* Fix repeatable fields bugs (props @barryceelen )
* Fix gmaps field bug where key doesn't pass in correctly (props: @shadyvb )
* PHPUnit tests for repeatable fields
* Fix all minor WordPress VIP PHPCS errors/warnings
* Write and complete inline documentation/doc blocks

**1.0.2**
* Add google maps field
* Add hide_on field argument
* Add Composer support
* Enhancement - enable for attachments
* Fix bug with unattached images on custom post types
* Fix error in WYSIWYG
* Fix fields not getting correctly initialized if meta box is collapsed on page load
* Fix bug with page-template restricted meta boxes showing if the post hasn't been saved at all.
* Hook CMB in later - most post types & taxonomies should be registered then.
* Fix Issue where different default values are used on save and init.

**1.0.1**
* Fix bug - AJAX post select field displaying incorrect content.

**1.0**
* Initial stable version of the fork.
