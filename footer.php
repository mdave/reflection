  </div>

<?php

// Deal with copyright settings

$copy = get_opt_or_default('copyright');
$year = ($yeartmp = get_opt_or_default('copyright_year')) ? $yeartmp : date('Y');

?>

<div id="footer">
Powered by <a href="http://wordpress.org/">WordPress</a>, <a href="http://xyloid.org/projects/reflection/">Reflection <?=$vnum?></a> 
and <a href="http://johannes.jarolim.com/yapb/">YAPB</a>: <a href="#">Entries (RSS)</a> - <a href="#">Comments (RSS)</a>.<br />

<?php if ($copy):?>&copy; <?=$copy?> <?=$year?>.<?php endif;?>
</div>

</body>

</html>
