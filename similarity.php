<?php
/*
Plugin Name: Similarity
Plugin URI: http://www.davidjmiller.org/similarity/
Description: Returns links to similar posts. Similarity is determined by the way posts are tagged or by their categories. Compatible with Wordpress 2.3 and above. (Tested on 2.3, 2.5, 2.6, 2.7)
Version: 1.2
Author: David Miller
Author URI: http://www.davidjmiller.org/
*/

/*
	Template Tag: Returns a list of related posts.
		e.g.: <?php sim_by_tag(); ?> determines similarity based on the tags applied to the posts
		e.g.: <?php sim_by_cat(); ?> determines similarity based on the categories assigned to the posts
	Full help and instructions at http://www.davidjmiller.org/similarity/
*/
function sim_by_tag() {
	$list = get_list("tag");
	print_similarity($list);
}
function sim_by_cat() {
	$list = get_list("cat");
	print_similarity($list);
}

function print_similarity($list) {
	$options = get_option(basename(__FILE__, ".php"));
	$limit = stripslashes($options['limit']);
	$none_text = stripslashes($options['none_text']);
	$prefix = stripslashes($options['prefix']);
	$suffix = stripslashes($options['suffix']);
	$output_template = stripslashes($options['output_template']);
	// an empty output_template makes no sense so we fall back to the default
	if ($output_template == '') $output_template = '<li>{link} ({strength})</li>';
	echo $prefix;
	if (sizeof($list) < 1) {
		echo $none_text;
	} else {
		if ($limit < 1 || $limit > sizeof($list)) {
			$limit = sizeof($list);
		}
		for ($i = 0; $i < $limit; $i++) {
			$post = get_post($list[$i]['post_id']);
			$impression = str_replace("{title}",$post->post_title,str_replace("{url}",get_permalink($list[$i]['post_id']),str_replace("{strength}",$list[$i]['strength'],str_replace("{link}","<a href=\"{url}\">{title}</a>",$output_template))));
			echo $impression;
		}
	}
	echo $suffix;
}

function get_list($type = 'tag') {
	global $post, $wpdb;
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
		foreach ($results as $result) {
			$potential += (1 / $result->rarity);
			$query = "select object_id as ID, rand() as remix from $wpdb->term_relationships where term_taxonomy_id = $result->ttid and object_id != $post->ID order by remix";
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
		add_options_page(__('Similarity Options'), __('Similarity'), 1, __FILE__, 'options_page');
	}
}
// Install the options page
add_action('admin_menu', 'similarity_option_menu');

// Prepare the default set of options
$default_options['limit'] = 5;
$default_options['none_text'] = '<li>'.__('Unique Post').'</li>';
$default_options['prefix'] = '<ul>';
$default_options['suffix'] = '</ul>';
$default_options['output_template'] = '<li>{link} ({strength})</li>';
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
		$options['output_template'] = $_POST['output_template'];

		// store the option values under the plugin filename
		update_option(basename(__FILE__, ".php"), $options);
		
		// Show a message to say we've done something
		echo '<div class="updated"><p>' . __('Options saved') . '</p></div>';
	} else {
		// If we are just displaying the page we first load up the options array
		$options = get_option(basename(__FILE__, ".php"));
	}
	//now we drop into html to display the option page form
	?>
		<div class="wrap">
		<h2><?php echo ucwords(str_replace('-', ' ', basename(__FILE__, ".php"). __(' Options'))); ?></h2>
		<h3><a href="http://www.davidjmiller.org/similarity/">Help and Instructions</a></h3>
		<form method="post" action="">
		<fieldset class="options">
		<table class="optiontable">
			<tr valign="top">
				<th scope="row"><?php _e('Number of posts to show:') ?></th>
				<td><input name="limit" type="text" id="limit" value="<?php echo $options['limit']; ?>" size="2" /></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Default display if no matches:') ?></th>
				<td><input name="none_text" type="text" id="none_text" value="<?php echo htmlspecialchars(stripslashes($options['none_text'])); ?>" size="40" /></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Text and codes before the list:') ?></th>
				<td><input name="prefix" type="text" id="prefix" value="<?php echo htmlspecialchars(stripslashes($options['prefix'])); ?>" size="40" /></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Text and codes after the list:') ?></th>
				<td><input name="suffix" type="text" id="suffix" value="<?php echo htmlspecialchars(stripslashes($options['suffix'])); ?>" size="40" /></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Output template:') ?></th>
				<td><textarea name="output_template" id="output_template" rows="4" cols="60"><?php echo htmlspecialchars(stripslashes($options['output_template'])); ?></textarea><br/><?php _e('Valid template tags:{link}, {strength}, {url}, {title}') ?></td>
			</tr>
		</table>
		</fieldset>
		<div class="submit"><input type="submit" name="update_options" value="<?php _e('Update') ?>"  style="font-weight:bold;" /></div>
		</form>    		
	</div>
	<?php	
}

$options = get_option(basename(__FILE__, ".php"));
?>