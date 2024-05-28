<?php

$sql = rex_sql::factory();

// Delete views
$sql->setQuery('DROP VIEW IF EXISTS ' . rex::getTablePrefix() . 'd2u_jobs_url_jobs');
$sql->setQuery('DROP VIEW IF EXISTS ' . rex::getTablePrefix() . 'd2u_jobs_url_jobs_categories');
$sql->setQuery('DROP VIEW IF EXISTS ' . rex::getTablePrefix() . 'd2u_jobs_jobs');
$sql->setQuery('DROP VIEW IF EXISTS ' . rex::getTablePrefix() . 'd2u_jobs_jobs_lang');
$sql->setQuery('DROP VIEW IF EXISTS ' . rex::getTablePrefix() . 'd2u_jobs_categories');
$sql->setQuery('DROP VIEW IF EXISTS ' . rex::getTablePrefix() . 'd2u_jobs_categories_lang');
$sql->setQuery('DROP VIEW IF EXISTS ' . rex::getTablePrefix() . 'd2u_jobs_contacts');

// Delete language replacements
if (!class_exists(d2u_jobs_lang_helper::class)) {
    // Load class in case addon is deactivated
    require_once 'lib/d2u_jobs_lang_helper.php';
}
d2u_jobs_lang_helper::factory()->uninstall();