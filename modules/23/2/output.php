<?php
$categories = D2U_Jobs\Category::getAll(rex_clang::getCurrentId(), TRUE);

print '<div class="col-12">';
print '<div class="row" data-match-height>';
foreach ($categories as $category) {
	print '<div class="col-12 col-sm-6 col-md-4 col-lg-3 job-box-list">';
	print '<a href="'. $category->getURL() .'" class="job-box-list-link" title="'. $category->name.'"><div class="job-box" data-height-watch>';
	print '<img src="index.php?rex_media_type=d2u_jobs_joblist&amp;rex_media_file='. $category->picture .'" alt="'. $category->name .'">';
	print '<h2>'. $category->name .'</h2>';
	print '</div></a>';
	print '</div>';
}
print '</div>';
print '</div>';