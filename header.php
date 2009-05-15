<?

/*
 * header.php
 * ----------
 * Header file for every page. Does standard stuff like including the JavaScript files
 * necessary for each site and a bunch of other stuff.
 */

// Hack for always displaying a single post. Taken from the Viewfinder Grain theme
// which you can find at: http://mac.defx.de/grain-theme/

if (is_home()) {
	$myposts = get_posts('numberposts=1&orderby=ID&order=DESC');
	foreach ($myposts as $post)
		break;
	// pretend we are on a single page so that next/prev post functions work
	$wp_query->is_single = true;
}

echo '<?xml version="1.0" encoding="UTF-8"?>';

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
    "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"
     xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
     xsi:schemaLocation="http://www.w3.org/MarkUp/SCHEMA/xhtml11.xsd"
     xml:lang="en" >
<head profile="http://gmpg.org/xfn/11">
  <meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
  <title><?php bloginfo('name'); ?> <?php if (is_home()): ?> &raquo; latest <?php else: ?><?php wp_title(); ?><?php endif;?></title>

  <meta name="generator" content="WordPress <?php bloginfo('version'); ?>" />
  
  <script type="text/javascript" src="<?php bloginfo('template_directory');?>/js/mootools-1.2.1-core.js"></script>
  <script type="text/javascript" src="<?php bloginfo('template_directory');?>/js/mootools-1.2-more.js"></script>
  <?if (is_home() || is_single()):?><script type="text/javascript" src="<?php bloginfo('template_directory');?>/js/blog.js"></script>

  <script type="text/javascript">
    Site.templateDir = '<?php bloginfo('template_directory');?>';
    window.addEvent('load', Site.init.bind(Site));
  </script><?php endif;?>
  <link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
  <link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
  <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

  <?php wp_head(); ?>
  
  <?= $headinclude ?>
</head>
<body>

<div id="topcontainer">
  <div id="header">
    <a href="<?php bloginfo('url');?>">
      <img src="<?php bloginfo('template_directory');?>/images/logo.jpg" alt="logo" style="border:0;" />
    </a>
    <div id="navbar">
      <ul>
        <?if(get_opt_or_default('showrand')):?><li><a href="<?php bloginfo('url');?>/?do=random"><?_e('Random',TD);?></a></li><?endif;?>
        <?php wp_list_pages('title_li=&sort_column=menu_order'); ?>
        <li><?php wp_loginout(); ?></li>
      </ul>
    </div>
  </div>
