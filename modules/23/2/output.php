<?php
$categories = D2U_Jobs\Category::getAll(rex_clang::getCurrentId(), TRUE);

foreach ($categories as $category) {
	print '<div class="small-12 large-4 columns box-picLarge-descrBottom">';
	print '<div class="row">';

	print '<div class="small-12 columns">';
	print '<div class="sp sections hide-for-large-up"></div>';
	print '<h1>'. $category->name .'</h1>';
	print '</div>';

	print '<div class="small-12 medium-6 large-12 columns">';
	print '<a href="'. $category->getURL() .'"><img src="index.php?rex_media_type=379x189&amp;rex_media_file='. $category->picture .'" alt="'. $category->name .'"></a>';
	print '</div>';

	print '</div>';
	print '</div>';
}