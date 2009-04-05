<?php
/*
Plugin Name: Similarity
Plugin URI: http://www.davidjmiller.org/2008/similarity/
Description: Returns links to similar posts. Similarity is determined by the way posts are tagged or by their categories. Compatible with Wordpress 2.3 and above. (Tested on 2.3, 2.5, 2.6, 2.7)
Version: 2.1
Author: David Miller
Author URI: http://www.davidjmiller.org/
*/

/*
	Template Tag: Returns a list of related posts.
		e.g.: <?php sim_by_tag(); ?> determines similarity based on the tags applied to the posts
		e.g.: <?php sim_by_cat(); ?> determines similarity based on the categories assigned to the posts
		e.g.: <?php sim_by_mix(); ?> determines similarity based on the categories and tags assigned to the posts weighting each according to the ratio you assign
	Full help and instructions at http://www.davidjmiller.org/2008/similarity/
*/

load_plugin_textdomain('similarity', 'wp-content/plugins/similarity'); 

function sim_by_tag() {
	$list = get_list("tag");
	print_similarity($list);
	echo '<!-- Similarity - Sim_by_Tag -->';
}
function sim_by_cat() {
	$list = get_list("cat");
	print_similarity($list);
	echo '<!-- Similarity - Sim_by_Cat -->';
}
function sim_by_mix() {
	$taglist = get_list("tag");
	$catlist = get_list("cat");
	$list = mix_lists($taglist, $catlist);
	print_similarity($list);
	echo '<!-- Similarity - Sim_by_Mix -->';
}

function print_similarity($list) {
	global $current_user;
	$options = get_option(basename(__FILE__, ".php"));
	$limit = stripslashes($options['limit']);
	$none_text = stripslashes($options['none_text']);
	$prefix = stripslashes($options['prefix']);
	$suffix = stripslashes($options['suffix']);
	$format = stripslashes($options['format']);
	$minimum_strength = stripslashes($options['minimum_strength']);
	$output_template = stripslashes($options['output_template']);
	// an empty output_template makes no sense so we fall back to the default
	if ($output_template == '') $output_template = '<li>{link} ({strength})</li>';
	echo $prefix;
	if (sizeof($list) < 1) {
		echo $none_text;
	} else {
		if ($limit < 0 || $limit > sizeof($list)) {
			$limit = sizeof($list);
		}
		$returnable = 'false';
		for ($i = 0; $i < $limit; $i++) {
			if ($minimum_strength > $list[$i]['strength']) {
				$returnable = 'true';
				$i = $limit;
			} else {
				$post = get_post($list[$i]['post_id']);
				switch ($post->post_status) {
				case 'private':
					$show = 'false';
					if (($current_user->ID == $post->post_author)
					|| ($current_user->has_cap('read_private_posts')))  { // Author and those with capability
						$show = 'true';
						$returnable = 'true';
					}
					break;
				case 'draft': //unpublished posts
					$show = 'false';
					if ($current_user->ID == $post->post_author) { // Author only
						$show = 'true';
						$returnable = 'true';
					}
					break;
				case 'inherit': //non-posts (such as images) picked up by the query (who knew)
					$show = 'false';
					break;
				default: // show non-private posts to anyone
					$show = 'true';
					$returnable = 'true';
					break;
				}
				if ($show == 'true') {
					switch ($format)
					{
					case 'percent':
						$list[$i][' strength'] = ($list[$i]['strength'] * 100) . '%';
						break;  
					case 'text':
						if ($list[$i]['strength'] > 0.75) {
							$list[$i]['strength'] = stripslashes($options['text_strong']);
						} elseif ($list[$i]['strength'] > 0.5) {
							$list[$i]['strength'] = stripslashes($options['text_mild']);
						} elseif ($list[$i]['strength'] > 0.25) {
							$list[$i]['strength'] = stripslashes($options['text_weak']);
						} else {
							$list[$i]['strength'] = stripslashes($options['text_tenuous']);
						}
						break;  
					case 'color':
						$r = 255;
						$g = 255;
						if ($list[$i]['strength'] > 0.5) {
							$r = 255 * (.5 - ($list[$i]['strength'] - .5));
						} elseif ($list[$i]['strength'] < 0.5) {
							$g = 513 * $list[$i]['strength'];
						}
						$shade = 'rgb('.number_format($r).', '.number_format($g).', 0)';
						$list[$i]['strength'] = '<span style="background-color: '.$shade.'; border: #000 1px solid">&nbsp;&nbsp;&nbsp;</span>';
						break;
					default:
						break;
					}
					$impression = str_replace("{title}",$post->post_title,str_replace("{url}",get_permalink($list[$i]['post_id']),str_replace("{strength}",$list[$i]['strength'],str_replace("{link}","<a href=\"{url}\">{title}</a>",$output_template))));
					echo $impression . '<!-- ' . $post->post_status . ' -->';
				} else {
					if ($limit < sizeof($list)) {
						$limit++;
					}
				}
			}
		}
		if ($returnable == 'false') {
			echo $none_text;
		} else if (($limit < sizeof($list)) && (stripslashes($options['one_extra']) == 'true')) {
			$show = 'false';
			$try = 0;
			while (($show =='false') && ($try < 100)) {
				srand ((double) microtime( )*1000000);
				$i = rand($limit + 1,sizeof($list));
				$post = get_post($list[$i]['post_id']);
				switch ($post->post_status) {
				case 'private':
					$show = 'false';
					if (($current_user->ID == $post->post_author)
					|| ($current_user->has_cap('read_private_posts')))  {
						$show = 'true';
					}
					break;
				case 'draft':
					$show = 'false';
					if ($current_user->ID == $post->post_author) {
						$show = 'false';
						$returnable = 'true';
					}
					break;
				case 'inherit':
					$show = 'false';
					break;
				default: // show non-private posts to anyone
					$show = 'true';
					break;
				}
				if ($show == 'true') {
					switch ($format)
					{
					case 'percent':
						$list[$i]['strength'] = __('RANDOM', 'similarity') . ' - ' . ($list[$i]['strength'] * 100) . '%';
						break;  
					case 'text':
						if ($list[$i]['strength'] > 0.75) {
							$list[$i]['strength'] = __('RANDOM', 'similarity') . ' - ' . stripslashes($options['text_strong']);
						} elseif ($list[$i]['strength'] > 0.5) {
							$list[$i]['strength'] = __('RANDOM', 'similarity') . ' - ' . stripslashes($options['text_mild']);
						} elseif ($list[$i]['strength'] > 0.25) {
							$list[$i]['strength'] = __('RANDOM', 'similarity') . ' - ' . stripslashes($options['text_weak']);
						} else {
							$list[$i]['strength'] = __('RANDOM', 'similarity') . ' - ' . stripslashes($options['text_tenuous']);
						}
						break;  
					case 'color':
						$r = 255;
						$g = 255;
						if ($list[$i]['strength'] > 0.5) {
							$r = 255 * (.5 - ($list[$i]['strength'] - .5));
						} elseif ($list[$i]['strength'] < 0.5) {
							$g = 513 * $list[$i]['strength'];
						}
						$shade = 'rgb('.number_format($r).', '.number_format($g).', 0)';
						$list[$i]['strength'] = '<span style="background-color: '.$shade.'; border: #000 1px solid">' . __('RANDOM', 'similarity') . '</span>';
						break;
					default:
						$list[$i]['strength'] = __('RANDOM', 'similarity') . ' - ' . $list[$i]['strength'];
						break;
					}
					$impression = str_replace("{title}",$post->post_title,str_replace("{url}",get_permalink($list[$i]['post_id']),str_replace("{strength}",$list[$i]['strength'],str_replace("{link}","<a href=\"{url}\">{title}</a>",$output_template))));
					echo $impression;
				} else { $try++; }
			}
		}

	}
	echo $suffix;
}

function get_list($type = 'tag') {
	global $post, $wpdb, $wp_version;
	$list = array();
	$id_list = array();
	$strength_list = array();
	$potential = 0;
	$query = "select r.term_taxonomy_id as ttid, t.count as rarity, rand() as mix from $wpdb->term_relationships r, $wpdb->term_taxonomy t where r.object_id = '$post->ID' and r.term_taxonomy_id in (select term_taxonomy_id from $wpdb->term_taxonomy where taxonomy = '";
	switch ($type) {
	case "cat":
		$query .= "category";
		break;
	case "tag":
	default:
		$query .= "post_tag";
		break;
	}
	$query .= "' and count > 1) and t.term_taxonomy_id = r.term_taxonomy_id order by t.count, mix";
	$results = $wpdb->get_results($query);
	if (count($results)) {
		$now = gmdate("Y-m-d H:i:s",(time()+($time_difference*3600)));
		foreach ($results as $result) {
			$potential += (1 / $result->rarity);
			$query = "select object_id as ID, rand() as remix from $wpdb->term_relationships where term_taxonomy_id = $result->ttid and object_id != $post->ID and object_id in (select ID from $wpdb->posts where post_date <= '$now'";
			if ($wp_version > 2.5) {
				$query .= " and post_parent = 0";
			}
			$query .= ") order by remix";
			$subsets = $wpdb->get_results($query);
			if (count($subsets)) {
				foreach ($subsets as $connection) {
					if (!array_search($connection->ID,$id_list)) {
						if ($id_list[0] == $connection->ID) {
							$strength_list[0] += 1/$result->rarity;
						} else {
							array_push($id_list,$connection->ID);
							array_push($strength_list,1/$result->rarity);
						}
					} else {
						$i = array_search($connection->ID,$id_list);
						$strength_list[$i] += 1/$result->rarity;
					}
				}
			}
		}
	}
	if (sizeof($strength_list) > 1 ) {
		array_multisort($strength_list,SORT_DESC,$id_list);
	}
	while(sizeof($id_list) > 0) {
		$set = array("post_id"=>array_shift($id_list), "strength"=>number_format((array_shift($strength_list) / $potential),3));
		array_push($list,$set);
	}
	return $list;
}

function mix_lists($taglist, $catlist) {
	$options = get_option(basename(__FILE__, ".php"));
	$list = array();
	$id_list = array();
	$strength_list = array();
	$tagweight = stripslashes($options['tag_weight']);
	$catweight = stripslashes($options['cat_weight']);
	if ($tagweight + $catweight == 0) {
		$tagweight = 1;
		$catweight = 1;
	}
	while(sizeof($taglist) > 0) {
		array_push($id_list,$taglist[0]['post_id']);
		array_push($strength_list,($tagweight * $taglist[0]['strength']));
		array_shift($taglist);
	}

	while(sizeof($catlist) > 0) {
		if (!array_search($catlist[0]['post_id'],$id_list)) {
			if ($id_list[0] == $catlist[0]['post_id']) {
				$strength_list[0] += ($catweight * $catlist[0]['strength']);
			} else {
				array_push($id_list,$catlist[0]['post_id']);
				array_push($strength_list,($catweight * $catlist[0]['strength']));
			}
		} else {
			$i = array_search($catlist[0]['post_id'],$id_list);
			$strength_list[$i] += ($catweight * $catlist[0]['strength']);
		}
		array_shift($catlist);
	}
	if (sizeof($strength_list) > 1 ) {
		array_multisort($strength_list,SORT_DESC,$id_list);
	}
	while(sizeof($id_list) > 0) {
		$set = array("post_id"=>array_shift($id_list), "strength"=>number_format((array_shift($strength_list) / ($tagweight + 

$catweight)),3));
		array_push($list,$set);
	}
	return $list;
}

/*
	Define the options menu
*/

function similarity_option_menu() {
	if (function_exists('current_user_can')) {
		if (!current_user_can('manage_options')) return;
	} else {
		global $user_level;
		get_currentuserinfo();
		if ($user_level < 8) return;
	}
	if (function_exists('add_options_page')) {
		add_options_page(__('Similarity Options', 'similarity'), __('Similarity', 'similarity'), 1, __FILE__, 'options_page');
	}
}

// Install the options page
add_action('admin_menu', 'similarity_option_menu');

// Prepare the default set of options
$default_options['limit'] = 5;
$default_options['none_text'] = '<li>'.__('Unique Post', 'similarity').'</li>';
$default_options['prefix'] = '<ul>';
$default_options['suffix'] = '</ul>';
$default_options['format'] = 'value';
$default_options['output_template'] = '<li>{link} ({strength})</li>';
$default_options['tag_weight'] = 1;
$default_options['cat_weight'] = 1;
$default_options['text_strong'] = '<strong>'.__('Strong', 'similarity').'</strong>';
$default_options['text_mild'] = __('Mild', 'similarity');
$default_options['text_weak'] = __('Weak', 'similarity');
$default_options['text_tenuous'] = '<em>'.__('Tenuous', 'similarity').'</em>';
$default_options['one_extra'] = 'false';
// the plugin options are stored in the options table under the name of the plugin file sans extension
add_option(basename(__FILE__, ".php"), $default_options, 'options for the Similarity plugin');

// This method displays, stores and updates all the options
function options_page(){
	global $wpdb;
	// This bit stores any updated values when the Update button has been pressed
	if (isset($_POST['update_options'])) {
		// Fill up the options array as necessary
		$options['limit'] = $_POST['limit'];
		$options['none_text'] = $_POST['none_text'];
		$options['prefix'] = $_POST['prefix'];
		$options['suffix'] = $_POST['suffix'];
		$options['format'] = $_POST['format'];
		$options['output_template'] = $_POST['output_template'];
		$options['tag_weight'] = $_POST['tag_weight'];
		$options['cat_weight'] = $_POST['cat_weight'];
		$options['text_strong'] = $_POST['text_strong'];
		$options['text_mild'] = $_POST['text_mild'];
		$options['text_weak'] = $_POST['text_weak'];
		$options['text_tenuous'] = $_POST['text_tenuous'];
		$options['one_extra'] = $_POST['one_extra'];
		if ((floatval($_POST['minimum_strength']) < 0) || (floatval($_POST['minimum_strength']) > 1)) {
			$options['minimum_strength'] = 0;
		} else {
			$options['minimum_strength'] = floatval($_POST['minimum_strength']);
		}

		// store the option values under the plugin filename
		update_option(basename(__FILE__, ".php"), $options);
		
		// Show a message to say we've done something
		echo '<div class="updated"><p>' . __('Options saved', 'similarity') . '</p></div>';
	} else {
		// If we are just displaying the page we first load up the options array
		$options = get_option(basename(__FILE__, ".php"));
	}
	//now we drop into html to display the option page form
	?>
		<div class="wrap">
		<h2><?php echo ucwords(str_replace('-', ' ', basename(__FILE__, ".php"). __(' Options', 'similarity'))); ?></h2>
		<h3><a href="http://www.davidjmiller.org/2008/similarity/"><?php _e('Help and Instructions', 'similarity') ?></a></h3>
		<form method="post" action="">
		<fieldset class="options">
		<table class="optiontable">
			<tr valign="top">
				<th scope="row" align="right"><?php _e('Number of posts to show', 'similarity') ?>:</th>
				<td><input name="limit" type="text" id="limit" value="<?php echo $options['limit']; ?>" size="2" /></td>
			</tr>
			<tr valign="top">
				<th scope="row" align="right"><?php _e('Minimum match strength', 'similarity') ?>:</th>
				<td><input name="minimum_strength" type="text" id="minimum_strength" value="<?php echo $options['minimum_strength']; ?>" size="5" /> <?php _e('(Any number between .00 and 1 with 1 being a perfect match.)', 'similarity') ?></td>
			</tr>
			<tr valign="top">

				<th scope="row" align="right"><?php _e('Default display if no matches', 'similarity') ?>:</th>
				<td><input name="none_text" type="text" id="none_text" value="<?php echo htmlspecialchars(stripslashes($options['none_text'])); ?>" size="40" /></td>
			</tr>
			<tr valign="top">
				<th scope="row" align="right"><?php _e('Text and codes before the list', 'similarity') ?>:</th>
				<td><input name="prefix" type="text" id="prefix" value="<?php echo htmlspecialchars(stripslashes($options['prefix'])); ?>" size="40" /></td>
			</tr>
			<tr valign="top">
				<th scope="row" align="right"><?php _e('Text and codes after the list', 'similarity') ?>:</th>
				<td><input name="suffix" type="text" id="suffix" value="<?php echo htmlspecialchars(stripslashes($options['suffix'])); ?>" size="40" /></td>
			</tr>
			<tr valign="top">
				<th scope="row" align="right"><?php _e('Display format for similarity strength', 'similarity') ?>:</th>
				<td>
					<input type="radio" name="format" id="format" value="color"<?php if ($options['format'] == 'color') echo ' checked'; ?>><?php _e('Visual', 'similarity') ?></input>&nbsp;
					<input type="radio" name="format" id="format" value="percent"<?php if ($options['format'] == 'percent') echo ' checked'; ?>><?php _e('Percent', 'similarity') ?></input>&nbsp;
					<input type="radio" name="format" id="format" value="text"<?php if ($options['format'] == 'text') echo ' checked'; ?>><?php _e('Text', 'similarity') ?></input>&nbsp;
					<input type="radio" name="format" id="format" value="value"<?php if ($options['format'] == 'value') echo ' checked'; ?>><?php _e('Value', 'similarity') ?></input>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" align="right"><?php _e('Custom text for strength', 'similarity') ?>:</th>
				<td><input name="text_strong" type="text" id="text_strong" value="<?php echo htmlspecialchars(stripslashes($options['text_strong'])); ?>" size="40" /> &gt;75%</td>
			</tr>
			<tr valign="top">
				<th scope="row" align="right">&nbsp;</th>
				<td><input name="text_mild" type="text" id="text_mild" value="<?php echo htmlspecialchars(stripslashes($options['text_mild'])); ?>" size="40" /> 75% &gt; 50%</td>
			</tr>
			<tr valign="top">
				<th scope="row" align="right">&nbsp;</th>
				<td><input name="text_weak" type="text" id="text_weak" value="<?php echo htmlspecialchars(stripslashes($options['text_weak'])); ?>" size="40" /> 50% &gt; 25%</td>
			</tr>
			<tr valign="top">
				<th scope="row" align="right">&nbsp;</th>
				<td><input name="text_tenuous" type="text" id="text_tenuous" value="<?php echo htmlspecialchars(stripslashes($options['text_tenuous'])); ?>" size="40" /> &lt; 25%</td>
			</tr>
			<tr valign="top">
				<th scope="row" align="right"><?php _e('Relative mixing weights', 'similarity') ?>:</th>
				<td><input name="tag_weight" type="text" id="tag_weight" value="<?php echo htmlspecialchars(stripslashes($options['tag_weight'])); ?>" size="40" /> <?php _e('Tags', 'similarity') ?></td>
			</tr>
			<tr valign="top">
				<th scope="row" align="right">&nbsp;</th>
				<td><input name="cat_weight" type="text" id="cat_weight" value="<?php echo htmlspecialchars(stripslashes($options['cat_weight'])); ?>" size="40" /> <?php _e('Categories', 'similarity') ?></td>
			</tr>
			<tr valign="top">
				<th scope="row" align="right"><?php _e('Output template', 'similarity') ?>:</th>
				<td><textarea name="output_template" id="output_template" rows="4" cols="60"><?php echo htmlspecialchars(stripslashes($options['output_template'])); ?></textarea><br/><?php _e('Valid template tags', 'similarity') ?>:{link}, {strength}, {url}, {title}</td>
			</tr>
			<tr valign="top">
				<th scope="row" align="right"><?php _e('Show one more random related post', 'similarity') ?>:</th>
				<td>
					<input type="radio" name="one_extra" id="one_extra" value="true"<?php if ($options['one_extra'] == 'true') echo ' checked'; ?>><?php _e('Yes', 'similarity') ?></input>&nbsp;
					<input type="radio" name="one_extra" id="one_extra" value="false"<?php if ($options['one_extra'] == 'false') echo ' checked'; ?>><?php _e('No', 'similarity') ?></input>&nbsp;
				</td>
			</tr>
		</table>
		</fieldset>
		<div class="submit"><input type="submit" name="update_options" value="<?php _e('Update', 'similarity') ?>"  style="font-weight:bold;" /></div>
		</form>    		
	</div>
	<?php	
}

$options = get_option(basename(__FILE__, ".php"));
?>