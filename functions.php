<?php

$pfix = "reflection_";
$updateflag = false;
$values = array();
$options = array(
	'copyright'  => array(
		'type'    => 'text',
		'size'    => '35'
	),
	'submitted'  => array(
		'type'    => 'hidden',
		'value'   => 'yes'
	),
	'exiffilter' => array(
		'type'    => 'check'
	),
);

create_exif_fields();

if ($_POST[$pfix.'submitted'] == 'yes') {
	foreach (array_keys($options) as $opt) {
		$val = $_POST[$pfix.$opt];
		
		if ($options[$opt]['type'] == 'hidden') {
			continue;
		} elseif ($options[$opt]['type'] == 'exifmap') {
			if (!is_array($val))
				continue;
			
			foreach ($val as $k => $v)
				if (!is_array($v) || count($v) > 2)
					continue 2;
			
			$val = serialize($val);
		}
		
		update_option($pfix.$opt, $_POST[$pfix.$opt]);
		$values[$opt] = $_POST[$pfix.$opt];
	}
	
	$updateflag = true;
} else {
	foreach (array_keys($options) as $opt) {
		if ($options[$opt]['type'] == 'hidden')
			continue;
		elseif ($options[$opt]['type'] == 'exifmap')
			$values[$opt] = get_tag_array();
		else
			$values[$opt] = get_option($pfix.$opt);
	}
}

if (!$values['exiftags']) {
	$values['exiftags'] = get_tag_array();
}

function reflection_add_pages() {
	add_theme_page('Reflection Options', 'Reflection', 'edit_themes', basename(__FILE__), 'reflection_admin');
}

function field_print($name) {
	global $options, $values, $pfix;

	if (!is_array($options[$name]))
		return;
	
	$value = $values[$name];
	
	switch ($options[$name]['type']) {
		case 'text':
			echo '<input type="text" name="'.$pfix.$name.'" value="'.$value.'" size="'.$options[$name]['size'].'">';
			break;
		case 'hidden':
			echo '<input type="hidden" name="'.$pfix.$name.'" value="'.$options[$name]['value'].'">';
			break;
		case 'check':
			echo '<input type="checkbox" name="'.$pfix.$name.'" value="true"'.($value ? ' checked="checked"' : '').'>';
			break;
		case 'exifmap':
			foreach ($options[$name]['data'] as $t => $d) {
				$sel = $value[$d][0] ? true : false;
				echo '<input type="checkbox" name="'.$pfix.$name.'['.$d.'][0]" value="true"'.($sel ? ' checked="checked"' : '').'>';
				echo ' '.$d.': <input type="text" name="'.$pfix.$name.'['.$d.'][1]" value="'.$value[$d][1].'"><br />';
			}
			break;
		default:
			return;
	}
}

function get_tag_array() {
	global $pfix;
	return get_option($pfix.'exiftags');
/*
	if (!($tags = get_option($pfix.'exiftags')))
		return NULL;
	else
		return unserialize($tags);
*/
}

function create_exif_fields() {
	global $options;
	
	$tags = get_option('yapb_view_exif_tagnames');

	if (!$tags)
		return false;
	
	$options['exiftags'] = array('type' => 'exifmap', 'data' => explode(',', $tags));
}

function reflection_admin() {
	global $updateflag;

	echo '<div class="wrap">';
	echo '<h2>'.__( 'Reflection Options', 'mt_trans_domain' ).'</h2>';
	if ($updateflag) { ?><div class="updated"><p><strong><?php _e('Options saved.', 'mt_trans_domain' ); ?></strong></p></div><? }

	?>
<form name="form1" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
	<?php field_print('submitted');?>
	<table class="form-table">
		<tr>
			<th scope="row" valign="top">Copyright settings</th>
			<td>
				Copyright holder of the images; leave blank if you don't want the copyright notice but highly recommended.<br />
				<?php field_print('copyright');?>
			</td>
		</tr>
		<tr>
			<th scope="row" valign="top">EXIF Mapping</th>
			<td>
				<p>EXIF mapping allows you to rename tags from YAPB's EXIF data to more sensible names. For example, the EXIF tag <em>DateCreated</em> could be mapped to <em>Date</em>. To enable this, you must have YAPB installed and EXIF filtering enabled in YAPB's options.</p>
				<p><?php field_print('exiffilter');?> Enable EXIF mapping?</p>
				<blockquote><?php field_print('exiftags');?></blockquote>
			</td>
		</tr>
	</table>
	<p class="submit">
		<input type="submit" name="Submit" value="<?php _e('Update Options', 'mt_trans_domain' ); ?>" />
	</p>
</form>
	<?
}

add_action('admin_menu', 'reflection_add_pages');

?>
