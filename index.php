<?php

/*
 * index.php
 * ---------
 * Main page. Deals with random pages, sets up the main layout and actually displays images.
 */

// Code for grabbing a random post from the database if necessary; we then redirect to the page.
if ($_GET['do'] == 'random') {
	$random_id = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_type = 'post' AND post_password = '' AND post_status = 'publish' ORDER BY RAND() LIMIT 1");
	wp_redirect(get_permalink($random_id));
}

// Include any YAPB filters that we need.
include(TEMPLATEPATH . '/yapb_filter.php');

// Grab header.
get_header();

// Begin post loop.
if (have_posts()) : while (have_posts()) : the_post(); 

// Grab next/previous post IDs if they exist. Odd syntax is hack for PHP4.
$next_post = isobject($npobj = get_next_post()) ? $npobj->ID : 0;
$prev_post = isobject($ppobj = get_previous_post()) ? $ppobj->ID : 0;
$next_post_perm = get_permalink($next_post);
$prev_post_perm = get_permalink($prev_post);

?>

<?php if (is_home() || is_single()):?>
  <script type="text/javascript">
    Site.nextPostID = <?=$next_post?>;
    Site.prevPostID = <?=$prev_post?>;
  </script>
<?php endif;?>
  <div id="topcontent" style="width:<?=$im_dim?>px;">
    <div id="title">
      <div id="titlebits">
        <ul>
          <?php if (is_home()):?>
            <li>
              <a id="prevPostLink" href="<?php if ($prev_post): ?><?=$prev_post_perm?>">&laquo;<?php else:?>"><?php endif;?></a> | 
              <a id="nextPostLink" href="<?php if ($next_post): ?><?=$next_post_perm?>">&raquo;<?php else:?>"><?php endif;?></a>
            </li>
          <?php endif;?>
          <li><a id="comment" href="<?php comments_link();?>"><?php comments_number('0 comments','1 comment','% comments');?></a></li>
          <li><a class="panel" id="exif" href="">exif</a></li>
          <li><a <?php if(is_home()): ?>class="panel" <?php endif;?>id="info" href="<?the_permalink();?>#notes">info</a></li>
        </ul>
      </div>
      <h3 id="texttitle">
        <a href="<?php the_permalink();?>"><?php the_title();?></a> 
        <span id="inlinedate"><?php the_date('jS F Y');?></span>
      </h3>
    </div>
    <div id="imageholder">
      <div id="exif_holder" class="overlay" style="right:0;top:0;">
        <div id="exif_panel">
          <?echo get_exif();?>
        </div>
      </div>
      <?php if (is_home()): // Only enable overlays for homepage navigation. ?>
        <div id="info_holder" class="overlay" style="bottom:0;left:0;right:0">
          <div id="info_panel">
            <?php the_content('Read more...'); ?>
          </div>
        </div>
        <div id="theoverlay_holder" class="overlay" style="left:0;top:0;z-index:100">
          <div id="theoverlay_panel"></div>
        </div>
      <?php endif; ?>
      <div id="overlaynav">
        <a href="<?php if ($prev_post): ?><?=$prev_post_perm?>"<?php else:?>" style="display:none"<?php endif;?> id="overPrevLink"></a>
        <a href="<?php if ($next_post): ?><?=$next_post_perm?>"<?php else:?>" style="display:none"<?php endif;?> id="overNextLink"></a>
      </div>
      <img id="mainimage" src="<?echo get_thumbnail();?>" alt="image" />
    </div>
  </div>
  <div id="reflectionholder"></div>

  <?php if (is_single() && !is_home()): ?>
    <div id="content">
      <a name="info" id="notes"></a>
      <h3>Shot Notes</h3>
      <?php the_content(); ?>
    </div>
    <div id="comments">
      <?php comments_template(); ?>
    </div>
  <?php endif;?>

<?php break; endwhile; endif;?>

<?php get_footer(); ?>
