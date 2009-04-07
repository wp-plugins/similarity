=== Similarity ===
Contributors: davidjmillerorg
Tags: posts, tags, categories, related posts, related, similarity, similar posts, similar
Requires at least: 2.3
Tested up to: 2.7.1
Stable tag: trunk

Similarity displays a list of posts similar to the current post with similarity being determined based on tags, categories, or both.

== Description ==
Similarity displays a list of posts similar to the current post with similarity being determined based on tags, categories, or both. The display of the list is fully customizable including the ability to display the strength of the similarity as a value, percentage, color, or custom text/code. Another option allows for a less related post to be randomly selected and appended to the list. The list can be limited by one or both of a numerical limit or (as of version 2.1) match strength.

This plugin is translation ready and comes with English (U.S.) and French translations. Thank you to Li-An (http://li-an.fr/blog/) for providing the french translation included here.

Prior to Version 1.6 private posts would potentially show in the list of related posts. In 1.6 these were skipped. As of version 1.7 private posts will show for the post author or others with the ability to read private posts. It is strongly recommended that users who have private posts install version 1.6 or higher.

The plugin allows for three function calls anywhere in your page templates (all use the same options):

* `<?php sim_by_tag(); ?>` - determines similarity based on the tags applied to the posts
* `<?php sim_by_cat(); ?>` - determines similarity based on the categories assigned to the posts
* `<?php sim_by_mix(); ?>` - determines similarity based on the tags and categories assigned to the posts

== Installation ==

To install it simply unzip the file linked above and save it in your plugins directory under wp-content. In the plugin manager activate the plugin. Settings for the plugin may be altered under the Similarity page of the Options menu (version 2.3) or Settings menu (version 2.5 or later).

== Frequently Asked Questions ==

= Can I mix tags and categories? =

As of version 1.3 the plugin allows for similarity to be calculated on a combination of tags and plugins using the `sim_by_mix();` command. You can set the way the lists are combined using the relaive mixing weights. You can also use the `sim_by_tag();` and `sim_by_cat();` commands together to generate different lists of related posts.

= Why does my list include matches weaker than the match strength I chose? =

If you use the 'Show one more random related post' option the last item in the list is random and does not consider the standard minimum strength limitations. You can set a separate minimum strength level for the random post.

== Screenshots ==

1. This is a sample options page displayed in Wordpress 2.7.1