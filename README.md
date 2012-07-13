# SilverStripe Clean Content module

A module that provides HTML Tidy and Purify capabilities

Note: This is the SS3 version of the module! Please checkout the
ss24 branch for the SilverStripe 2.4 compatible version

## Basic Usage

Add the module to your project, and add the appropriate extension to your
data objects via your site's mysite/_config.php file


`Object::add_extension('Page', 'CleanContentExtension');`


You can have tidy/purify applied to Content fields when content is saved by 
selecting options on the Content / Cleaning tab, or use the $Clean(FieldName) 
option from your templates. Using $Clean on its own will by default use the 
Content field. 

Note that for $Clean to work, you must still select the appropriate cleaning
options on the Content / Clean tab. 

By default, new pages have cleaning on save enabled, with HTML Tidy in effect,
but not Purify. This can be changed by setting the statics in 
CleanContentExtension, which default as follows. 

	static $clean_on_save = true;
	static $default_tidy = true;
	static $default_purify = false;



## Maintainer Contacts

* Marcus Nyeholt <marcus@silverstripe.com.au>

## Requirements

* SilverStripe 2.4+

## License

This module is licensed under the BSD license at http://silverstripe.org/BSD-license

This module makes use of the HTML Purifier library from http://htmlpurifier.org/
which is licensed under the Lesser GPL, a copy of which can be found at
cleancontent/code/thirdparty/htmlpurifier-4.0.0-lite/LICENSE

## Project Links

* [GitHub Project Page](https://github.com/nyeholt/silverstripe-cleancontent)
* [Issue Tracker](https://github.com/nyeholt/silverstripe-cleancontent/issues)

