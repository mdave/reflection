<?php

/*
 * Template Name: Mosaic Page
 * Description: Page for creating mosaic of all images posted on the site.
 */

get_header();

if (have_posts()) : while (have_posts()) : the_post();

?>

<div id="pagecontent">
  <h2><?the_title();?></h2>
  <?the_content('');?>
<?php

$posts = get_posts('numberposts=-1&order=DESC');

$postyear = 0;

foreach ($posts as $post) {
	$this_postyear = intval(substr($post->post_date, 0, 4));
	
	if ($this_postyear != $postyear) {
		$postyear = $this_postyear;
		echo '<h2 class="mosaicheader">'.$postyear.'</h2>';
	}
	
	$image = YapbImage::getInstanceFromDb($post->ID);
	
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
