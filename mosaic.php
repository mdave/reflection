<?php

/*
 * Template Name: Mosaic Page
 * Description: Page for creating mosaic of all images posted on the site.
 */

// Grab header
get_header();

// Begin The Loop.
if (have_posts()) : while (have_posts()) : the_post();

?>

<div id="pagecontent">
  <h2><?the_title();?></h2>
  <?php the_content(''); ?>
<?php

// Grab all posts from the database in descending order.
$posts = get_posts('numberposts=-1&order=DESC');

// Variable to store the current year.
$postyear = 0;

foreach ($posts as $post) {
	// Grab the year this photo was taken and change postyear if necessary.
	$this_postyear = intval(substr($post->post_date, 0, 4));
	
	if ($this_postyear != $postyear) {
		$postyear = $this_postyear;
		echo '<h2 class="mosaicheader">'.$postyear.'</h2>';
	}
	
	// Grab YapbImage from the database depending upon the post ID.
	$image = YapbImage::getInstanceFromDb($post->ID);
	
	if (!$image)
		continue;
	
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
	
	array_push($thumb_param, 'h=100', 'q=70');
	echo '<a href="'.get_permalink($post->ID).'">';
	echo '<img class="mosaic" src="'.$image->getThumbnailHref($thumb_param).'" />';
	echo '</a>';
}

?>

</div>

<?

endwhile; endif;

get_footer();

?>
