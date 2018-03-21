<?php
$categories = D2U_Jobs\Category::getAll(rex_clang::getCurrentId(), TRUE);

foreach ($categories as $category) {
	print '<div class="col-12 col-lg-4">';
	print '<div class="row">';

	print '<div class="col-12">';
	print '<h1>'. $category->name .'</h1>';
	print '</div>';

	print '<div class="col-12 col-md-6 col-lg-4">';
	print '<a href="'. $category->getURL() .'"><img src="index.php?rex_media_type=379x189&amp;rex_media_file='. $category->picture .'" alt="'. $category->name .'"></a>';
	print '</div>';

	print '</div>';
	print '</div>';
}