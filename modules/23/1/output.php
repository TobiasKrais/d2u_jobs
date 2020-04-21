<?php
if(!function_exists('prepareText')) {
	/**
	 * Replaces common text changes.
	 * @param string $text Text in need of replacements
	 * @return string Replaced text
	 */
	function prepareText($text) {
		return str_replace("</li>", "</span></li>", str_replace("<li>", "<li><span>", str_replace("<ul>", '<ul class="bullets">', d2u_addon_frontend_helper::prepareEditorField($text))));
	}
}

$url_namespace = d2u_addon_frontend_helper::getUrlNamespace();
$url_id = d2u_addon_frontend_helper::getUrlId();

$category_id = "REX_VALUE[1]";
$category = FALSE;
if($category_id > 0) {
	$category = new D2U_Jobs\Category($category_id, rex_clang::getCurrentId());
}
else {
	if(filter_input(INPUT_GET, 'job_category_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || $url_namespace === "job_category_id") {
		$category_id = filter_input(INPUT_GET, 'job_category_id', FILTER_VALIDATE_INT);
		if(\rex_addon::get("url")->isAvailable() && $url_id > 0) {
			$category_id = $url_id;
		}
		$category = new D2U_Jobs\Category($category_id, rex_clang::getCurrentId());
	}
}

$hide_application_hint = "REX_VALUE[2]" == 'true' ? TRUE : FALSE;

if(rex::isBackend()) {
	// Ausgabe im BACKEND	
?>
	<h1 style="font-size: 1.5em;">Stellenmarkt Ausgabe</h1>
<?php
	if($category === FALSE) {
		print "Anzuzeigende Kategorien: Alle";
	}
	else {
		print "Anzuzeigende Kategorie: ". $category->name;
	}
}
else {
	// FRONTEND Output
	$sprog = rex_addon::get("sprog");
	$tag_open = $sprog->getConfig('wildcard_open_tag');
	$tag_close = $sprog->getConfig('wildcard_close_tag');

	// Output job details
	if(filter_input(INPUT_GET, 'job_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || $url_namespace === "job_id") {
		$job_id = filter_input(INPUT_GET, 'job_id', FILTER_VALIDATE_INT);
		if(\rex_addon::get("url")->isAvailable() && $url_id > 0) {
			$job_id = $url_id;
		}
		
		$job = new D2U_Jobs\Job($job_id, rex_clang::getCurrentId());
		// Redirect if object is not online
		if($job->online_status != "online") {
			\rex_redirect(rex_article::getNotfoundArticleId(), rex_clang::getCurrentId());
		}
		print '<div class="col-12 col-md-8">';
		print '<article class="job-box">';
		print '<img src="'. ($job->picture != "" ? 'index.php?rex_media_type=d2u_jobs_jobheader&rex_media_file='. $job->picture : \rex_url::addonAssets('d2u_jobs', 'noavatar.jpg'))  .'" alt="'. strip_tags($job->name) .'">';
		print '<h2>'. $job->name .'</h2>';
		if($job->city != "" || $job->reference_number != "") {
			print '<p><b>';
			if($job->city != "") {
				print $tag_open .'d2u_jobs_region'. $tag_close .' '. $job->city . ($job->reference_number != "" ? ' / ' : '');
			}
			if($job->reference_number != "") {
				print $tag_open .'d2u_jobs_reference_number'. $tag_close .' '. $job->reference_number;
			}
			print '</b></p><br>';
		}
		if($job->hr4you_lead_in != "") {
			print '<br>';
			print $job->hr4you_lead_in;
		}
		if($job->tasks_heading != "") {
			print '<h3>'. $job->tasks_heading .'</h3>';
			print prepareText($job->tasks_text);
		}
		if($job->profile_heading != "") {
			print '<h3>'. $job->profile_heading .'</h3>';
			print prepareText($job->profile_text);
		}
		if($job->offer_heading != "") {
			print '<h3>'. $job->offer_heading .'</h3>';
			print prepareText($job->offer_text);
		}
		if($job->hr4you_url_application_form != "") {
			print '<br><br>';
			print '<p class="appendix"><a target="_blank" href="'. $job->hr4you_url_application_form .'">'. $tag_open .'d2u_jobs_hr4you_application_link'. $tag_close .'</a></p>';
		}
		else if($hide_application_hint === FALSE) {
			print '<br><br>';
			print '<p class="appendix">'. $tag_open .'d2u_jobs_footer'. $tag_close
				.'<br><br><a href="mailto:'. rex_config::get('d2u_jobs', 'email') .'" title="'. rex_config::get('d2u_jobs', 'email') .'">'. rex_config::get('d2u_jobs', 'email') .'</a>'
				.'</p>';
		}
		print '</article>';
		print '</div>';
		print '<div class="sp sections-less hide-for-medium-up"></div>';
		print '<div class="col-12 col-md-4">';
		print '<div class="job-box">';
		print $tag_open .'d2u_jobs_questions'. $tag_close .'<br><br>';
		print '<div class="row">';
		print '<div class="col-12 col-sm-4">';
		print '<img src="'. ($job->contact->picture != "" ? 'index.php?rex_media_type=d2u_jobs_contact&rex_media_file='. $job->contact->picture : \rex_url::addonAssets('d2u_jobs', 'noavatar.jpg'))  .'" alt="'. $job->contact->name .'">';
		print '</div>';
		print '<div class="col-12 col-sm-8">';
		print '<h3>'. $job->contact->name .'</h3>';
		if($job->contact->phone != "") {
			print $tag_open .'d2u_jobs_phone'. $tag_close .': '. $job->contact->phone .'<br>';
		}
		if($job->contact->email != "") {
			print '<a href="mailto:'. $job->contact->email .'" title="'. rex_config::get('d2u_jobs', 'email') .'">'.$job->contact->email .'</a><br>';
		}
		print '</div>';
		print '</div>';
		print '</div>';
		print '</div>';
		print '</div>';
	}
	else {
		// Output Job list
		$jobs = D2U_Jobs\Job::getAll(rex_clang::getCurrentId(), $category_id, TRUE);
		print '<div class="col-12">';
		print '<div class="row" data-match-height>';
		if(count($jobs) > 0) {
			print '<div class="col-12">';
			print '<h1>'. $tag_open .'d2u_jobs_vacancies'. $tag_close .' ';
			if($category !== FALSE) {
				print $category->name;
			}
			print '</h1>';
			print '</div>';
			foreach($jobs as $job) {
				print '<div class="col-12 col-md-6 col-lg-4">';
				print '<a href="'. $job->getUrl() .'" class="job-box-list-link" title="'. strip_tags($job->name).'">';
				print '<div class="job-box job-box-list" data-height-watch>';
				print '<img src="'. ($job->picture != "" ? 'index.php?rex_media_type=d2u_jobs_joblist&rex_media_file='. $job->picture : \rex_url::addonAssets('d2u_jobs', 'noavatar.jpg'))  .'" alt="'. strip_tags($job->name) .'">';
				print '<h2>'. $job->name .'</h2>';
				if($job->city != "" || $job->reference_number != "") {
					print '<p>';
					if($job->city != "") {
						print $tag_open .'d2u_jobs_region'. $tag_close .' '. $job->city . ($job->reference_number != "" ? ' / ' : '');
					}
					if($job->reference_number != "") {
						print $tag_open .'d2u_jobs_reference_number'. $tag_close .' '. $job->reference_number;
					}
					print '</p>';
				}
				print '</div>';
				print '</a>';
				print '</div>';
			}
		}
		print '</div>';
		print '</div>';
	}
}