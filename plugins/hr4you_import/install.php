<?php
$sql = rex_sql::factory();

$sql->setQuery('ALTER TABLE `'. rex::getTablePrefix() . 'd2u_jobs_jobs` ADD `hr4you_job_id` int(10) default NULL;');
$sql->setQuery('ALTER TABLE `'. rex::getTablePrefix() . 'd2u_jobs_jobs` ADD `hr4you_url_application_form` varchar(255) default NULL;');
$sql->setQuery('ALTER TABLE `'. rex::getTablePrefix() . 'd2u_jobs_jobs_lang` ADD `hr4you_lead_in` varchar(255) default NULL;');
$sql->setQuery('ALTER TABLE `'. rex::getTablePrefix() . 'd2u_jobs_categories` ADD `hr4you_category_id` int(10) default NULL;');

// Insert frontend translations
if(class_exists(d2u_jobs_hr4you_lang_helper)) {
	d2u_jobs_hr4you_lang_helper::factory()->install();
}

if (!rex_config::has('d2u_jobs', 'hr4you_default_lang')) {
	rex_config::set('d2u_jobs', 'hr4you_default_lang', rex_clang::getStartId());
}
if (!rex_config::has('d2u_jobs', 'hr4you_headline_tag')) {
	rex_config::set('d2u_jobs', 'hr4you_headline_tag', 'h3');
}