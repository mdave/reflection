<?

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
	/* Any exif filtering gets done in this function. Modify if you want it. */
	
	return $exif;
}

// Uncomment the following line to enable EXIF filtering.

//add_filter('yapb_get_exif', 'yapb_get_exif_filter');

?>
