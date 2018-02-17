=== Inserter ===
Contributors: aristath
Tags: underscorejs, rest api, rest, templates
Donate link: https://aristath.github.io/donate
Requires at least: 4.9
Tested up to: 4.9
Stable tag: 1.1
License: MIT
License URI: http://opensource.org/licenses/https://opensource.org/licenses/MIT

Create custom underscore.js templates and inject them in your pages

== Description ==

Inserter allows you to create custom templates from your dashboard and insert the in your pages, replacing a CSS selector of your choosing.

You can use the [WordPress Templates Interpolation](https://codex.wordpress.org/Javascript_Reference/wp.template#Template_Interpolation) for [underscore.js](http://underscorejs.org/) and include JS-based logic in your templates.

You can expose data using the REST API, using the global `$post` object, or provide your own JSON-formatted data.
To see the available data, you can add this line in your template and then check the console when your site loads:
`<# console.log( data ); #>`

You can read more about how to use the plugin on [https://aristath.github.io/wordpress/introducing-wordpress-inserter-plugin](https://aristath.github.io/wordpress/introducing-wordpress-inserter-plugin).

== Installation ==

Simply install as a normal WordPress plugin and activate.

== Changelog ==

= 1.1 =

February 17, 2018

* Add `append` and `prepend` modes in addition to `replace`.
* Require "element" field to be defined when adding/editing template

= 1.0 =

February 12, 2018

* Initial version
