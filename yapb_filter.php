<?

$pfix = "reflection_";
$im_dim = $post->image->width > $post->image->height ? 800 : 450;

function get_thumbnail($removeamps=false)
{
	global $post, $im_dim;
	
	$uri = $post->image->getThumbnailHref(array('w='.$im_dim, 'q=70'));
	
	return $removeamps ? str_replace("&amp;", "&", $uri) : $uri;
}

function get_exif()
{
	global $post;
	
	$exif_info = yapb_get_exif();
	
	if (empty($exif_info))
		return "No EXIF information available.";
	
	$output = '<ul>';
	
	foreach ($exif_info as $k => $v)
		$output .= '<li><label>'.$k.'</label>'.$v.'</li>';
	
	return $output.'</ul>';
}

function yapb_get_exif_filter($exif) 
{
	global $pfix;
	
	if (!get_option($pfix.'exiffilter'))
		return;
	
	$maparray = get_option($pfix.'exiftags');
	$mapped = array();
	
	foreach ($maparray as $t => $m) {
		if (!array_key_exists($t, $exif) || !$m[0])
			continue;
		$mapped[$m[1]] = $exif[$t];
	}
	
	return $mapped;
}

add_filter('yapb_get_exif', 'yapb_get_exif_filter');

?>
