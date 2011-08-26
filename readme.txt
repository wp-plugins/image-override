=== Image Override ===
Contributors: billerickson
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=K9K2YFSJAMLKE
Tags: image, thumbnail, featured, 
Requires at least: 3.0
Tested up to: 3.2.1
Stable tag: 1.0

Allows you to override WordPress' auto generated thumbnails. 

== Description ==

When you upload an image, WordPress will automatically scale/crop it to many different sizes. If you're not happy with the auto-crops, use this plugin to upload an alternative image. 

By default it will add a metabox to every post type and allow you to modify every image size (built-in and custom ones added using add_image_size). You can use two filters to change these (image_override_post_types and image_override_sizes). For examples, see the [Image Override Plugin website](http://www.billerickson.net/image-override-plugin).

If you change your image sizes, deactivate and then reactivate the plugin to update.

== Installation ==

1. Upload the `image-override` folder to your `/wp-content/plugins/` directory

2. Activate the "Image Override" plugin in your WordPress administration interface

3. Add `do_action( 'image_override_display', 'medium' );` wherever you'd like the image to be displayed, replacing medium with the image size you'd like to use.

4. Create (or edit) a page or a post with a featured image.

5. Down below, in the Image Override metabox, upload an alternative image for one of the sizes, and save the post.



== Changelog ==

= 1.0 = 
* Initial release