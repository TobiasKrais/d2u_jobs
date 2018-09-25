<?php
$sql = rex_sql::factory();

// Delete views
$sql->setQuery('DROP VIEW IF EXISTS ' . rex::getTablePrefix() . 'd2u_jobs_url_jobs');
$sql->setQuery('DROP VIEW IF EXISTS ' . rex::getTablePrefix() . 'd2u_jobs_url_jobs_categories');
// Delete url schemes
if(\rex_addon::get('url')->isAvailable()) {
	$sql->setQuery("DELETE FROM `". rex::getTablePrefix() ."url_generate` WHERE `table` LIKE '%d2u_jobs_url_jobs%'");
}

// Delete language replacements
d2u_jobs_lang_helper::factory()->uninstall();

// Delete tables
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'd2u_jobs_jobs');
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'd2u_jobs_jobs_lang');
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'd2u_jobs_categories');
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'd2u_jobs_categories_lang');
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'd2u_jobs_contacts');