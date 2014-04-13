<?php
/**
 * Functions which are common to all (or most) files in the theme. We
 * also define things which make it much easier to create administration
 * pages and deal with options.
 *
 * @package Reflection
 */

/**
 * Prefix to use in option names. Should _not_ be changed.
 * 
 * @global string $pfix
 */
$pfix = "reflection_";

/**
 * Version number for use in the footer.
 *
 * @global string $vnum
 */
$vnum = "1.1.2";

/**
 * Array of options we use in reflection.
 * 
 * This is an array containing all the names and default values that we use for
 * Reflection. Each element is also an array with three possible keys
 * 
 * - type: Can be text, hidden, int or check
 * - size: For int/text types, size of textfield.
 * - value: For check types, either yes or no.
 * - default: default value.
 * 
 * @global array $options
 */
$options = array(
	'copyright'  => array(
		'type'    => 'text',
		'size'    => '35',
		'default' => ''
	),
	'copyright_year' => array(
		'type'    => 'text',
		'size'    => '30',
		'default' => ''
	),
	'widthport'  => array(
		'type'    => 'text',
		'size'    => '10',
		'default' => 450
	),
	'widthland'  => array(
		'type'    => 'text',
		'size'    => '10',
		'default' => 800
	),
	'showrand'   => array(
		'type'    => 'check',
		'default' => 1
	),
	'mosaicsize' => array(
		'type'    => 'text',
		'size'    => '10',
		'default' => 100
	),
	'mosaictips' => array(
		'type'    => 'check',
		'default' => 1
	),
	'mosaicdesc' => array(
		'type'    => 'radio',
		'default' => 1,
		'values'  => array(
			'0' => 'Ascending',
			'1' => 'Descending'
		)
	),
	'archivedisp' => array(
		'type'    => 'radio',
		'default' => 0,
		'values'  => array(
			'0' => 'None',
			'1' => 'Tags'
		)
	),
	'submitted'  => array(
		'type'    => 'hidden',
		'value'   => 'yes'
	)
);

/**
 * Array of option values. Each element of the array is the option
 * value.
 *
 * @global array $values
 */
$values = array();

/**
 * On administration pages, this is set to true after we have updated
 * the options.
 * 
 * @global bool $updateflag
 */
$updateflag = false;

/**
 * For some reason, PHP doesn't include the json_encode function
 * before PHP5.2, so this is a replacement for compatibility reasons.
 * Taken from php.net.
 */
if (!function_exists('json_encode')) {
	function json_encode($a=false) {
		if (is_null($a)) return 'null';
		if ($a === false) return 'false';
		if ($a === true) return 'true';
		if (is_scalar($a)) {
			if (is_float($a)) {
				// Always use "." for floats.
				return floatval(str_replace(",", ".", strval($a)));
			}

			if (is_string($a)) {
				static $jsonReplaces = array(array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));
				return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $a) . '"';
			} else return $a;
		}
		$isList = true;
		for ($i = 0, reset($a); $i < count($a); $i++, next($a)) {
			if (key($a) !== $i) {
				$isList = false;
				break;
			}
		}
		$result = array();
		if ($isList) {
			foreach ($a as $v) $result[] = json_encode($v);
			return '[' . join(',', $result) . ']';
		} else {
			foreach ($a as $k => $v) $result[] = json_encode($k).':'.json_encode($v);
			return '{' . join(',', $result) . '}';
		}
	}
}

/**
 * Generates a square thumbnail of an image.
 * 
 * @param int $postID Post ID
 * @return string Absolute URL pointing to generated thumbnail.
 */
function square_thumb($postID) {
	global $pfix, $options;
	
	// Grab YapbImage from the database depending upon the post ID.
	$image = YapbImage::getInstanceFromDb($postID);

	if (!$image)
		return false;

	if ($image->width > $image->height) {
		$thumb_param = array(
			'sx='.intval(($image->width - $image->height)/2),
			'sy=0',
			'sw='.$image->height,
			'sh='.$image->height
		);
	} elseif ($image->width < $image->height) {
		$thumb_param = array(
			'sx=0',
			'sy='.intval(($image->height - $image->width)/2),
			'sw='.$image->width,
			'sh='.$image->width
		);
	} else {
		$thumb_param = array();
	}
	
	$height = get_opt_or_default('mosaicsize');
	array_push($thumb_param, 'h='.$height, 'q=70');
	
	return $image->getThumbnailHref($thumb_param);
}

/**
 * Get an option or fall-back on default value.
 * 
 * Since WordPress doesn't seem to have a registration hook for 
 * themes (akin to plugins), we use this function to avoid epic
 * failure when looking up options.
 * 
 * @param string $optname Option name
 * @return mixed Option value from database or default
 */
function get_opt_or_default($optname) {
	global $pfix, $options;
	$opt = get_option($pfix.$optname);
	return $opt === false ? $options[$optname]['default'] : $opt;
}

/**
 * Filter function to create the option page for Reflection.
 */
function reflection_add_pages() {
	add_theme_page('Reflection Options', 'Reflection', 'edit_themes', basename(__FILE__), 'reflection_admin');
}

/**
 * Prints a data field defined in $options.
 * 
 * @param string $name Field name
 */
function field_print($name) {
	global $options, $values, $pfix;

	if (!is_array($options[$name]))
		return;
	
	$value = $values[$name];
	$fname = $pfix.$name;
	
	switch ($options[$name]['type']) {
		case 'text':
			echo '<input type="text" name="'.$fname.'" value="'.$value.'" size="'.$options[$name]['size'].'">';
			break;
		case 'hidden':
			echo '<input type="hidden" name="'.$fname.'" value="'.$options[$name]['value'].'">';
			break;
		case 'check':
			echo '<input type="checkbox" name="'.$fname.'" value="1"'.($value ? ' checked="checked"' : '').'>';
			break;
		case 'radio':
			foreach ($options[$name]['values'] as $k => $v)
				echo '<input type="radio" name="'.$fname.'" value="'.$k.'"'.($value==$k ? ' checked="checked"' : '').'> '.$v.'<br />';
			break;
		default:
			break;
	}
}

/**
 * Sets up the administration page itself.
 */
function reflection_admin() {
	global $updateflag;

	echo '<div class="wrap">';
	echo '<h2>'.__('Reflection Options').'</h2>';
	if ($updateflag) { ?><div class="updated"><p><strong><?php _e('Options saved.'); ?></strong></p></div><? }

	?>
<form name="form1" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
	<?php field_print('submitted');?>
	<h3>General settings</h3>
	<table class="form-table">
		<tr>
			<th scope="row" valign="top">Copyright holder</th>
			<td>
				<?php field_print('copyright');?><br />
				<span class="setting-description">Copyright holder of the images - i.e. your name. Leave blank if you don't want the copyright notice.</span>
			</td>
		</tr>
		<tr>
			<th scope="row" valign="top">Copyright year range</th>
			<td>
				<?php field_print('copyright_year');?></br />
				<span class="setting-description">The year(s) over which you claim copyright: for example "2000", "2000-2009", "2000, 2001-2009" etc. If blank, current year will be used.</span>
			</td>
		</tr>
		<tr>
			<th scope="row" valign="top">Portrait image width</th>
			<td>
				<?php field_print('widthport');?> px<br />
				<span class="setting-description">Desired width in pixels of portrait images. Cannot be larger than 800px.</span>
			</td>
		</tr>
		<tr>
			<th scope="row" valign="top">Landscape image width</th>
			<td>
				<?php field_print('widthland');?> px<br />
				<span class="setting-description">Desired width in pixels of landscape images. Cannot be larger than 800px.</span>
			</td>
		</tr>
		<tr>
			<th scope="row" valign="top">Show <em>Random</em> page</th>
			<td>
				<?php field_print('showrand');?>
				<span class="setting-description">If true, then add a page called <em>Random</em> to the list which takes users to random images..</span>
			</td>
		</tr>
	</table>
	
	<h3>Mosaic configuration</h3>
	<table class="form-table">
		<tr>
			<th scope="row" valign="top">Taxonomy display</th>
			<td>
				<?php field_print('archivedisp');?>
				<span class="setting-description">If you want to display photos by tag name as well as by date, then select the 'tags' option.</span>
			</td>
		</tr>
		<tr>
			<th scope="row" valign="top">Mosaic image size</th>
			<td>
				<?php field_print('mosaicsize');?> px<br />
				<span class="setting-description">Size of the square images shown in the mosaic.</span>
			</td>
		</tr>
		<tr>
			<th scope="row" valign="top">Show mosaic tooltips</th>
			<td>
				<?php field_print('mosaictips');?>
				<span class="setting-description">If enabled, tooltips will be shown as you hover over each image with the name, post date and number of comments.</span>
			</td>
		</tr>
		<tr>
			<th scope="row" valign="top">Post order</th>
			<td>
				<?php field_print('mosaicdesc');?>
			</td>
		</tr>
	</table>
	<p class="submit">
		<input type="submit" name="Submit" value="<?php _e('Update Options', 'mt_trans_domain' ); ?>" />
	</p>
</form>
	<?
}

/**
 * Grabs a thumbnail from the database.
 * 
 * @param bool $removeamps If true, replace XHTML ampersand with standard ampersand.
 */
function get_thumbnail($removeamps=false)
{
	global $post;
	
	$uri = $post->image->getThumbnailHref(array('w='.im_dim(), 'q=70'));
	
	return $removeamps ? str_replace("&amp;", "&", $uri) : $uri;
}

/**
 * Determines the image width.
 */
function im_dim()
{
	global $post;

	if ($post->image->width > $post->image->height) {
		$wl = get_opt_or_default('widthland');
		return $post->image->width > $wl ? $wl : $post->image->width;
	} else {
		$wp = get_opt_or_default('widthport');
		return $post->image->height > $wp ? $wp : $post->image->width;
	}
}

/**
 * Grabs EXIF information from the database.
 */
function get_exif()
{
	global $post;
	
	$exif_info = yapb_get_exif(true);
	
	if (empty($exif_info))
		return "No EXIF information available.";
	
	$output = '<ul>';
	
	foreach ($exif_info as $k => $v)
		$output .= '<li><label>'.$k.'</label>'.$v.'</li>';
	
	return $output.'</ul>';
}

// --------------------------------------------------------------------
// End of functions
// --------------------------------------------------------------------

// If we're in the administration panel, then set up all of our options
// and put the values in $values.

if (is_admin()) {
	if ($_POST[$pfix.'submitted'] == 'yes') {
		foreach (array_keys($options) as $opt) {
			$val = $_POST[$pfix.$opt];
			if ($options[$opt]['type'] == 'hidden')
				continue;
			elseif ($options[$opt]['type'] == 'check')
				$val = $val ? 1 : 0;
			update_option($pfix.$opt, $val);
			$values[$opt] = $val;
		}
		
		$updateflag = true;
	} else {
		foreach (array_keys($options) as $opt) {
			if ($options[$opt]['type'] == 'hidden')
				continue;
			$values[$opt] = get_option($pfix.$opt);
			
			// Set up default options
			if ($values[$opt] === false) {
				$values[$opt] = $options[$opt]['default'];
				add_option($pfix.$opt, $values[$opt]);
			}
		}
	}

	add_action('admin_menu', 'reflection_add_pages');
}

/*
 * If you want to filter your exif data by hand, then uncomment this
 * and put the code in here.
 */

/*
function yapb_get_exif_filter($exif) 
{

}

add_filter('yapb_get_exif', 'yapb_get_exif_filter');
*/

?>
