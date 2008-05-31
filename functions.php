<?php

add_action('admin_menu', 'reflection_add_pages');

$p = 'reflection';

function reflection_add_pages() {
	add_theme_page('Reflection Options', 'Reflection', 'edit_themes', basename(__FILE__), 'reflection_admin');
}

function reflection_admin() {
	echo '<div class="wrap"><h2>'.__( 'Reflection Options', 'mt_trans_domain' ).'</h2>';

	?>
<form name="form1" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
	<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">
	<p>
		<?php _e("Copyright info:", 'mt_trans_domain' ); ?> <input type="text" name="rofl" value="" size="20">
	</p>
	<hr />
	<p class="submit">
		<input type="submit" name="Submit" value="<?php _e('Update Options', 'mt_trans_domain' ) ?>" />
	</p>
</form>
	<?
}

?>