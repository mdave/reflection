<?php
/**
 * Main index page. 
 * 
 * If we're on the homepage, then we enable all of the AJAX features. If 
 * displaying a single image, then we disable the info panel and previous/next 
 * links, instead displaying the shot info. However the EXIF panel will still
 * load. 
 *
 * If the user has clicked the 'Random' page, then we automatically redirect
 * to a random page. 
 *
 * @package Reflection
 */

// Code for grabbing a random post from the database if necessary; we then redirect to the page.
if ($_GET['do'] == 'random') {
	$random_id = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_type = 'post' AND post_password = '' AND post_status = 'publish' ORDER BY RAND() LIMIT 1");
	wp_redirect(get_permalink($random_id));
}

// Grab header.
get_header();

// Begin post loop.
if (have_posts()) : while (have_posts()) : the_post(); 

// Grab next/previous post IDs if they exist. Odd syntax is hack for PHP4.
$next_post      = is_object($npobj = get_next_post())     ? $npobj->ID : 0;
$prev_post      = is_object($ppobj = get_previous_post()) ? $ppobj->ID : 0;
$next_post_perm = get_permalink($next_post);
$prev_post_perm = get_permalink($prev_post);

?>
<?php if (is_home() || is_single()):?>
  <script type="text/javascript">
    Site.nextPostID = <?=$next_post?>;
    Site.prevPostID = <?=$prev_post?>;
  </script>
<?php endif;?>
  <div id="topcontent" style="width:<?=im_dim()?>px;">
    <div id="title">
      <div id="titlebits">
        <ul>
          <?php if (is_home()):?>
            <li>
              <a id="prevPostLink" href="<?=$prev_post ? $prev_post_perm.'">&laquo;' : '">';?></a> | 
              <a id="nextPostLink" href="<?=$next_post ? $next_post_perm.'">&raquo;' : '">';?></a>
            </li>
          <?php endif;?>
          <li><a id="comment" href="<?php comments_link();?>"><?php comments_number(__('0 comments',TD),__('1 comment',TD),__('% comments',TD));?></a></li>
          <li><a class="panel" id="exif" href=""><?_e('exif',TD);?></a></li>
          <li><a <?=is_home() ? 'class="panel" ' : ''?>id="info" href="<?the_permalink();?>#notes"><?_e('info',TD);?></a></li>
        </ul>
      </div>
      <h3 id="texttitle">
        <a href="<?php the_permalink();?>"><?php the_title();?></a> 
        <span id="inlinedate"><?php the_date('jS F Y');?></span>
      </h3>
    </div>
    <div id="imageholder">
      <div id="panel_exif" class="overlay" style="right:0;top:0;">
        <?echo get_exif();?>
      </div>
      <?php if (is_home()): // Only enable overlays for homepage navigation. ?>
        <div id="panel_info" class="overlay bottomPanel" style="bottom:0;left:0;right:0;z-index:6">
          <?php the_content(__('Read more...')); ?>
        </div>
        <div id="panel_overlay" class="overlay" style="left:0;top:0;z-index:100">
        </div>
      <?php endif; ?>
      <div id="overlaynav">
        <a href="<?=$prev_post ? $prev_post_perm.'"' : '" style="display:none"'?> id="overPrevLink"></a>
        <a href="<?=$next_post ? $next_post_perm.'"' : '" style="display:none"'?> id="overNextLink"></a>
      </div>
      <img id="mainimage" src="<?=get_thumbnail();?>" alt="image" />
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

<?php require(TEMPLATEPATH . '/footer.php'); ?>
