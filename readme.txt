=== Similarity ===
Contributors: davidjmillerorg
Tags: posts, tags, categories, related posts
Requires at least: 2.3
Tested up to: 2.7
Stable tag: trunk

Similarity displays a list of posts similar to the current post with similarity being determined based on tags, categories, or both.

== Description ==
Similarity displays a list of posts similar to the current post with similarity being determined based on tags, categories, or both. The display of the list is fully customizable including the ability to display the strength of the similarity as a value, percentage, color, or custom text/code. Another option allows for a less related post to be randomly selected and appended to the list.

This plugin is translation ready and comes with English (U.S.) and French translations. Thank you to Li-An (http://li-an.fr/blog/) for providing the french translation included here.

Prior to Version 1.6 private posts would potentially show in the list of related posts. In 1.6 these were skipped. As of version 1.7 private posts will show for the post author or others with the ability to read private posts. It is strongly recommended that users who have private posts install version 1.6 or higher.

Options include:

* Number of posts to show - this is a maximum, it won’t invent connections that don’t exist, set it to less than 0  and it will display all matches.
* Default Display if no matches - if there are no matches this is what will be displayed, this is not displayed if there are matches, but fewer than the set maximum.
* Text and Codes before the list - assuming you want to do a list this is where you would place the `<ul>` or `<ol>` You may also place any other code you would like to have preceeding the list.
* Text and Codes after the list - this would be the place for `</ul>` or `</ol>` You may also place any other code you would like to have following the list.
* Display format for similarity strength - Value displays the {strength} in a decimal format (0.873), Percent displays the {strength} in a percentage format (87.3%), Text displays {strength} as a word (<strong>Strong</strong>, Mild, Weak, and <em>Tenuous</em> being the default text values), and Visual displays a color block (Green for 100% fading to Yellow and then to Red for weak connections)
* Relative mixing weights - these values determine the ratio given to the weight of tags vs categories when using the `sim_by_mix();` function.
* Custom text for strength - allows you to insert custom text (including markup) for the strength indicator when using the text display format. (Hint: using markup allows for the possibility of showing custom images.)
* Output template - this would be where you place the `<li>` tags. There are also 4 template tags you may use (in any configuration you can imagine) to define how the results are displayed
    o {link} - provides a link - equivelent to `<a href="{url}">`{title}`</a>`
    o {strength} - outputs the calculated degree of relatedness
    o {url} is the permalink for the related post
    o {title} is the title for the related post

The plugin allows for three function calls anywhere in your page templates (all use the same options):

* `<?php sim_by_tag(); ?>` - determines similarity based on the tags applied to the posts
* `<?php sim_by_cat(); ?>` - determines similarity based on the categories assigned to the posts
* `<?php sim_by_mix(); ?>` - determines similarity based on the tags and categories assigned to the posts

== Installation ==

To install it simply unzip the file linked above and save it in your plugins directory under wp-content. In the plugin manager activate the plugin. Settings for the plugin may be altered under the Similarity page of the Options menu (version 2.3) or Settings menu (version 2.5 or later).

== Frequently Asked Questions ==

= Can I mix tags and categories? =

As of version 1.3 the plugin allows for similarity to be calculated on a combination of tags and plugins. You can also use the `sim_by_tag();` and `sim_by_cat();` commands together to generate different lists of related posts.

== Screenshots ==

1. This is a sample options page displayed in Wordpress 2.7