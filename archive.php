<?php
/*
 * Template Name: Archives
 * Description: Allows users to browse through your archived posts by tag and date.
 */

$headinclude = '<script type="text/javascript" src="'.get_bloginfo('template_directory').'/js/browser.js"></script>

<script type="text/javascript">
  Browse.templateDir = \''.get_bloginfo('template_directory').'\';
  window.addEvent(\'load\', Browse.init.bind(Browse));
</script>';

// Get header.
include (TEMPLATEPATH . '/header.php');

// Determine what kind of archive we have.
$post     = $posts[0];
$thisyear = is_year()     ? get_the_time('Y')           : false;
$thistag  = is_tag()      ? single_tag_title('', false) : false;
$thiscat  = is_category() ? single_cat_title('', false) : false;
$all      = !$thisyear && !$thistag;
$curtag   = get_query_var('tag_id');
$curcat   = get_query_var('cat_id');
$curstr   = ' class="current"';
$postinfo = array();
$disptype = get_opt_or_default('archivedisp');

// Attempt to find permalink to correct page ID
$allid    = $wpdb->get_var("SELECT p.ID FROM $wpdb->posts AS p, $wpdb->postmeta AS m
                            WHERE p.ID = m.post_id AND p.post_status='publish' AND m.meta_key='_wp_page_template' AND m.meta_value='archive.php'");
$alluri   = '';
if ($allid)
	$alluri = get_permalink($allid);

?>

<div id="pagecontent">
	<table cellpadding="5" cellspacing="0" id="taxonomyTable">
		<?php if ($disptype): ?>
		<tr><th>By year</th><th style="text-align:right"><?=$disptype == 1 ? 'By tag' : 'By category'?></th></tr>
		<?php else: ?>
		<tr><th style="width:100%">By year</th></tr>
		<?php endif; ?>
		<tr>
			<td id="yearCloud">
		    	<?php
    			  	$post_years = $wpdb->get_results("SELECT YEAR(post_date) AS posty FROM " . $wpdb->prefix . "posts 
				                                  WHERE post_status='publish' AND post_type='post' GROUP BY posty ORDER BY posty DESC;");
      				echo '<a href="'.$alluri.'"'.($all ? $curstr : '').'>All</a> ';
				foreach ($post_years as $y)
					echo '<a href="'.get_year_link($y->posty).'"'.($thisyear && $thisyear == $y->posty ? $curstr : '').'>'.$y->posty.'</a> ';
			?>
  			</td>
			<?php if ($disptype == 1): ?>
				<td id="tagCloud">
					<?php 
						if ($thistag !== false)
							echo implode(' ', str_replace('tag-link-'.$curtag, 'tag-link-'.$curtag.' current', wp_tag_cloud('format=array')));
						else
							wp_tag_cloud('');
					?>
				</td>
			<?php elseif ($disptype == 2): ?>
				<td id="catCloud">
					<?php
						wp_list_categories();
					?>
				</td>
			<?php endif;?>
  		</tr>
  	</table>
  	
	<div style="margin-top: 8px">
    	<b class="tagbox">
    	<b class="tagbox1"><b></b></b>
    	<b class="tagbox2"><b></b></b>
    	<b class="tagbox3"></b>
    	<b class="tagbox4"></b>
    	<b class="tagbox5"></b></b>

        <div id="tagPics">
          <div id="tagContainer">
<?php

// Grab all posts from the database in correct order.
$orderby = get_opt_or_default('mosaicdesc') ? 'DESC' : 'ASC';

if ($all) 
	query_posts('showposts=-1&orderby=date&order='.$orderby);
else
	query_posts($query_string.'&orderby=date&order='.$orderby.'&showposts=-1');

// Begin The Loop.
if (have_posts()) : while (have_posts()) : the_post();
	array_push($postinfo, array(
		'post_title' => $post->post_title,
		'post_date' => get_the_time('jS F Y'),
		'comment_status' => $post->comment_status,
		'comment_count' => $post->comment_count
	));

	echo '<a href="'.get_permalink($post->ID).'">';
	echo '<img class="mosaic" src="'.square_thumb($post->ID).'" alt="thumb" />';
	echo '</a>';

endwhile; endif;
?>
      </div>
      <img src="<?php bloginfo('template_directory'); ?>/images/browse-load.gif" id="tagProgress" alt="loading" />
    </div>

    <b class="tagbox">
    <b class="tagbox5"></b>
    <b class="tagbox4"></b>
    <b class="tagbox3"></b>
    <b class="tagbox2"><b></b></b>
    <b class="tagbox1"><b></b></b></b>
  </div>
  <?php if (get_opt_or_default('mosaictips')): ?>
    <script type="text/javascript">
      Browse.initialPosts = <?php echo json_encode($postinfo);?>;
    </script>
  <?php endif; ?>
</div>
<?

// Get footer.
get_footer();

?>
