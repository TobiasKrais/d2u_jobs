<?php
$sql = rex_sql::factory();
if (rex_version::compare($this->getVersion(), '1.0.8', '<')) {
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_jobs_jobs_lang ADD COLUMN `updatedate_new` DATETIME NOT NULL AFTER `updatedate`;");
	$sql->setQuery("UPDATE ". \rex::getTablePrefix() ."d2u_jobs_jobs_lang SET `updatedate_new` = FROM_UNIXTIME(`updatedate`);");
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_jobs_jobs_lang DROP updatedate;");
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_jobs_jobs_lang CHANGE `updatedate_new` `updatedate` DATETIME NOT NULL;");
}

// use path relative to __DIR__ to get correct path in update temp dir
$this->includeFile(__DIR__.'/install.php'); /** @phpstan-ignore-line */

// Media Manager media types
if (rex_version::compare($this->getVersion(), '1.2.1', '<')) {
	$sql->setQuery("UPDATE ". \rex::getTablePrefix() ."media_manager_type SET status = 0 WHERE name LIKE 'd2u_jobs_%'");
	if(rex_addon::get('sprog')->isAvailable()) {
		$sql->setQuery("UPDATE ". \rex::getTablePrefix() ."sprog_wildcard SET wildcard = 'd2u_jobs_application_link' WHERE wildcard = 'd2u_jobs_hr4you_application_link'");
	}
}