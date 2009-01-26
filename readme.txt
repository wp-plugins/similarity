=== Similarity ===
Contributors: davidjmillerorg
Donate link: http://example.com/
Tags: posts, tags, categories, related posts
Requires at least: 2.3
Tested up to: 2.7
Stable tag: trunk

Similarity displays a list of posts similar to the current post with similarity being determined based on tags, categories, or both.

== Description ==

Options include:

* Number of posts to show - this is a maximum, it won’t invent connections that don’t exist, set it to 0 (or less) and it will display all matches.
* Default Display if no matches - if there are no matches this is what will be displayed, this is not displayed if there are matches, but fewer than the set maximum.
* Text and Codes before the list - assuming you want to do a list this is where you would place the <ul> or <ol> You may also place any other code you would like to have preceeding the list.
* Text and Codes after the list - this would be the place for </ul> or </ol> You may also place any other code you would like to have following the list.
* Relative mixing weights - these values determine the ratio given to the weight of tags vs categories when using the sim_by_mix function.
* Output template - this would be where you place the <li> tags. There are also 4 template tags you may use (in any configuration you can imagine) to define how the results are displayed
    o {link} - provides a link - equivelent to <a href=”{url}”>{title}</a>
    o {strength} - outputs the calculated degree of relatedness
    o {url} is the permalink for the related post
    o {title} is the title for the related post

The plugin allows for three function calls anywhere in your page templates (both use the same options):

* <?php sim_by_tag(); ?> - determines similarity based on the tags applied to the posts
* <?php sim_by_cat(); ?> - determines similarity based on the categories assigned to the posts
* <?php sim_by_mix(); ?> - determines similarity based on the tags and categories assigned to the posts

== Installation ==

To install it simply unzip the file linked above and save it in your plugins directory under wp-content. In the plugin manager activate the plugin. Settings for the plugin may be altered under the Similarity page of the Options menu (version 2.3) or Settings menu (version 2.5 or later).

== Frequently Asked Questions ==

= Can I mix tags and categories? =

As of version 1.3 the plugin allows for similarity to be calculated on a combination of tags and plugins. You can also use the sim-by-tag(); and sim_by_cat(); commands together to generate different lists of related posts.

== Screenshots ==
No Screenshots