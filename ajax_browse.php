<?

require_once ('../../../wp-config.php');

$tag     = (int)$_GET['tag'];
$cat     = (int)$_GET['cat'];
$year    = $_GET['year'] == 'All' ? 'All' : intval($_GET['year']);
$orderby = get_opt_or_default('mosaicdesc') ? 'DESC' : 'ASC';

if ($year && $year == 'All') {
  query_posts('showposts=-1&order='.$orderby); 
} elseif ($year) {
  query_posts('year='.$year.'&showposts=-1&order='.$orderby);
} elseif ($cat) {
  query_posts('cat='.$cat.'&showposts=-1&order='.$orderby);
} else {
  $tag  = get_tag($tag);
  $slug = $tag->slug;
  query_posts('tag='.$slug.'&showposts=-1&order='.$orderby);
}

$posts = array();
$tips  = get_opt_or_default('mosaictips');

while(have_posts()) {
	the_post();
  
	if (($imageuri = square_thumb($post->ID)) === false)
		continue;

	$topush = array(
		'image_uri'      => str_replace("&amp;", "&", $imageuri),
		'post_title'     => $post->post_title,
		'post_date'      => get_the_time('jS F Y'),
		'permalink'      => get_permalink($post->ID)
	);

	if ($tips) {
		$topush['comment_status'] = $post->comment_status;
		$topush['comment_count']  = $post->comment_count;
	}

	array_push($posts, $topush);
}

echo json_encode($posts);

?>
