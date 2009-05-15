<?

/*
 * ajax_request.php
 * ----------------
 * Request image information for a post in the blog. Returns a json object to
 * the browser for use in the JavaScript code.
 */

require_once('../../../wp-config.php');

$id = intval($_GET['id']);
query_posts('p='.$id);

while(have_posts()) {
	the_post();
	
	$next_post = get_next_post();
	$prev_post = get_previous_post();
	
	// Grab post information.
	ob_start();
	the_content();
	$content = ob_get_clean();
	
	$info = array(
		'image_uri'      => get_thumbnail(true),
		'post_content'   => $content,
		'post_title'     => $post->post_title,
		'post_date'      => get_the_time('jS F Y'),
		'permalink'      => get_permalink($post->ID),
		'next_post'      => $next_post ? $next_post->ID : 0,
		'prev_post'      => $prev_post ? $prev_post->ID : 0,
		'next_post_perm' => $next_post ? get_permalink($next_post->ID) : '',
		'prev_post_perm' => $prev_post ? get_permalink($prev_post->ID) : '',
		'comment_status' => $post->comment_status,
		'comment_count'  => $post->comment_count,
		'exif'           => get_exif(),
	);
	
	echo json_encode($info);
}

?>
