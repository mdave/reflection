<?
/**
 * Error finding requested page. Output error page with links to most popular
 * pages and also contact e-mail.
 *
 * @package Reflection
 */

// Grab header.
get_header();

?>

<div id="pagecontent">
<h1>Not Found</h1>

<p>Sorry, that page could not be found.</p>

<p>Please <a href="mailto:<?bloginfo('admin_email');?>">contact me</a> if you tried to get here from a link on the site.</p>

<?

// Grab footer.
get_footer();

?>
