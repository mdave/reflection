<?

/*
 * 404.php
 * -------
 * Error finding requested page. Output error page with links to most popular
 * pages and also contact e-mail.
 */

// Grab header.
get_header();

?>

<div id="pagecontent">
<h1>Not Found</h1>

<p>Sorry, that page could not be found. Please <a href="mailto:<?bloginfo('admin_email');?>">contact me</a> if you tried to get here from a link on the site. Here's some popular links:</p>

<ul>
<li><a href="/">Home Page</a></li>
<li><a href="/mosaic/">Mosaic</a></li>
<li><a href="/about/">About</a></li>
</div>

<?

// Grab footer.
get_footer();

?>
