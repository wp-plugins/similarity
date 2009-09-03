=== Similarity ===
Contributors: davidjmillerorg
Tags: posts, tags, categories, related posts, related, similarity, similar posts, similar
Requires at least: 2.3
Tested up to: 2.8.4
Stable tag: trunk

Similarity displays a list of posts similar to the current post with similarity being determined based on tags, categories, or both.

== Description ==
Similarity displays a list of posts similar to the current post with similarity being determined based on tags, categories, or both. The display of the list is fully customizable including the ability to display the strength of the similarity as a value, percentage, color, or custom text/code. Another option allows for a less related post to be randomly selected and appended to the list. The list can be limited by one or both of a numerical limit or (as of version 2.1) match strength.

This plugin is translation ready and comes with English (U.S.), French, Polish, German, italian, and Spanish translations. Thanks to Li-An (http://li-an.fr/blog/) for providing the French translation. Thanks to Krzysztof Kudlacik (http://born66.net/) for providing the Polish translation. Thanks to Bjoern Buerstinghaus (http://www.buerstinghaus.net/) for providing the German, Italian and Spanish translations.

Prior to Version 1.6 private posts would potentially show in the list of related posts. In 1.6 these were skipped. As of version 1.7 private posts will show for the post author or others with the ability to read private posts. It is strongly recommended that users who have private posts install version 1.6 or higher.

The plugin allows for three function calls anywhere in your page templates (all use the same options):

* `<?php sim_by_tag(); ?>` - determines similarity based on the tags applied to the posts
* `<?php sim_by_cat(); ?>` - determines similarity based on the categories assigned to the posts
* `<?php sim_by_mix(); ?>` - determines similarity based on the tags and categories assigned to the posts

There are also three function calls that are better suited for use in sidebars (specifically when the sidebar is used on the main page)

* `<?php sim_by_tag_multi(); ?>` - determines similarity based on the tags applied to the first post on milti-post pages
* `<?php sim_by_cat_multi(); ?>` - determines similarity based on the categories assigned to the first post on milti-post pages
* `<?php sim_by_mix(_multi); ?>` - determines similarity based on the categories and tags assigned to the first post on milti-post pages weighting each according to the ratio you assign
* To display a Similarity list as a widget use a text widget with one of the following shortcodes - `[SIM-BY-TAG]` `[SIM-BY-CAT]` `[SIM-BY-MIX]` for single post pages only or for all pages use `[SIM-BY-TAG-MULTI]` `[SIM-BY-CAT-MULTI]` or `[SIM-BY-MIX-MULTI]`
* To display a Similarity list without altering your templates simply select the function that you would like to auto-display at the bottom of the options page.
== Installation ==

To install it simply unzip the file linked above and save it in your plugins directory under wp-content. In the plugin manager activate the plugin. Settings for the plugin may be altered under the Similarity page of the Options menu (version 2.3) or Settings menu (version 2.5 or later).

== Frequently Asked Questions ==

= Can I mix tags and categories? =

As of version 1.3 the plugin allows for similarity to be calculated on a combination of tags and plugins using the `sim_by_mix();` command. You can set the way the lists are combined using the relaive mixing weights. You can also use the `sim_by_tag();` and `sim_by_cat();` commands together to generate different lists of related posts.

= Why does my list include matches weaker than the match strength I chose? =

If you use the 'Show one more random related post' option the last item in the list is random and does not consider the standard minimum strength limitations. You can set a separate minimum strength level for the random post.

== Screenshots ==

1. This is a sample options page displayed in Wordpress 2.8

== Changelog ==

= 2.12 =
* Pending posts were previously treated like public posts in Similarity lists (an oversight on my part) - they are now treated like drafts

= 2.11 =
* Improved accuracy in determining output when minimum similarity is set above zero. (Previously an empty list might be displayed rather than the output for empty lists if there were related posts but only below the minimum similarity.)

= 2.10 =
* Shortcodes for multi-post pages now point to the new multi-page functions introduced in version 2.9 for use in sidebars where you want a similarity list for the first post on a multi-post page.
* There are also new translations for German, Italian, and Spanish. (In addition to the existing translations for French and Polish)

= 2.9 =
* Functions sim_by_tag_multi, sim_by_cat_multi, and sim_by_mix_multi were added for use in sidebars where you want a similarity list for the first post on a multi-post page.
* New button on the Similarity Options page to restore default settings

= 2.8 =
* Automatic Similarity lists may now be shown on pages. When no similar results are found only “Default display if no matches:” is shown.
* ”Text and codes before/after the list” are replaced by "Default display if no matches"
** If you wish to display the contents of the before and after variables simply insert them into the “Default display if no matches:” field.

= 2.7 =
* Fixed a bug in the output so that the Auto Display function lists similar posts at the end of the post consistently.
* Added div tags with class “similarity” around auto-generated list to allow for custom styling.

= 2.6 =
* Added Shorcodes SIM-BY-TAG-MULTI, SIM-BY-CAT-MULTI, and SIM-BY-MIX-MULTI for use on multi post pages.
** These codes may potentially cause slow page loads (which is why I restricted the old shortcodes to work on single post pages as of version 2.5).
** I added these codes after a report in the comments that the old codes worked fine in at least one multi-post setting.

= 2.5 =
* Option to automatically display Similarity lists at the end of single posts without altering your templates – just select the function you want to use on the options page!
* Shorcodes are now fixed to only display on single post pages.

= 2.3 =
* Added Shorcodes SIM-BY-TAG, SIM-BY-CAT, and SIM-BY-MIX for use in text widgets

= 2.2.1 =
* Bug fixes for the random minimum strength.

= 2.2 =
* Option to place a minimum similarity strength for random item at the end of the list (this is separate from the minimum strength for the list as a whole).

= 2.1 =
* Option to place a minimum similarity strength for list inclusion.
* The Plus One option ignores the minimum strength for the final list entry.

= 2.0 =
* Non-posts such as pictures are saved as posts by Wordpress with a post-status of “inherit” these are now excluded from results.

= 1.9 =
* Drafts are excluded from results. If, for some reason, all related posts are private or drafts users will get the no results output (before there would have been an empty list without explanation).

= 1.8 =
* Future dated posts are excluded from results.

= 1.7 =
* Private posts may be listed as similar for the post author and users with “read private posts” capability.

= 1.6 =
* Allows for the display of a random related post after the list of strongest related posts.

= 1.5 =
* Added an option to display {strength} as a word, or a colored indicator.
* Similarity is now coded for translation
* The bugfix in 1.4 is now backwards compatible to Wordpress 2.3 again.

= 1.4 =
* Fixed a bug related to post revisions 
* Added an option to display {strength} as a percentage instead of as a decimal value.

= 1.3 =
* Added the sim_by_mix function and the associated options.

= 1.2 =
* Tested compatibility with WP 2.7 
* Minor modification to the documentation.

= 1.1 =
* Added a randomizer in case there are too many posts that are all equally related.