<?php

$sql = rex_sql::factory();
if (rex_version::compare($this->getVersion(), '1.0.8', '<')) { /** @phpstan-ignore-line */
    $sql->setQuery('ALTER TABLE '. \rex::getTablePrefix() .'d2u_jobs_jobs_lang ADD COLUMN `updatedate_new` DATETIME NOT NULL AFTER `updatedate`;');
    $sql->setQuery('UPDATE '. \rex::getTablePrefix() .'d2u_jobs_jobs_lang SET `updatedate_new` = FROM_UNIXTIME(`updatedate`);');
    $sql->setQuery('ALTER TABLE '. \rex::getTablePrefix() .'d2u_jobs_jobs_lang DROP updatedate;');
    $sql->setQuery('ALTER TABLE '. \rex::getTablePrefix() .'d2u_jobs_jobs_lang CHANGE `updatedate_new` `updatedate` DATETIME NOT NULL;');
}

// move wildards to jobs addon
$prefix = 'd2u_jobs';
$wildcardsWithPrefix = $sql->getArray('SELECT * FROM '. \rex::getTablePrefix() ."sprog_wildcard WHERE wildcard LIKE '".$prefix."%'");

foreach ($wildcardsWithPrefix as $wildcard) {
    $wildcardWithoutPrefix = str_replace($prefix, 'jobs', $wildcard['wildcard']);
    $doesWildcardWithoutPrefixExist = $sql->getArray('SELECT wildcard FROM '. \rex::getTablePrefix() ."sprog_wildcard WHERE wildcard = '".$wildcardWithoutPrefix."'");

    if (count($doesWildcardWithoutPrefixExist) > 0) {
        $sql->setQuery('UPDATE '. \rex::getTablePrefix() ."sprog_wildcard SET wildcard = '".$wildcard['wildcard']."' WHERE wildcard = '".$wildcardWithoutPrefix."'");
    }
}

// use path relative to __DIR__ to get correct path in update temp dir
$this->includeFile(__DIR__.'/install.php'); /** @phpstan-ignore-line */

// move database content to new tables
$sql->setQuery('INSERT INTO '. \rex::getTablePrefix() .'jobs_categories SELECT * FROM '. \rex::getTablePrefix() .'d2u_jobs_categories');
$sql->setQuery('INSERT INTO '. \rex::getTablePrefix() .'jobs_categories_lang SELECT * FROM '. \rex::getTablePrefix() .'d2u_jobs_categories_lang');
$sql->setQuery('INSERT INTO '. \rex::getTablePrefix() .'jobs_contacts SELECT * FROM '. \rex::getTablePrefix() .'d2u_jobs_contacts');
$sql->setQuery('INSERT INTO '. \rex::getTablePrefix() .'jobs_jobs SELECT * FROM '. \rex::getTablePrefix() .'d2u_jobs_jobs');
$sql->setQuery('INSERT INTO '. \rex::getTablePrefix() .'jobs_jobs_lang SELECT * FROM '. \rex::getTablePrefix() .'d2u_jobs_jobs_lang');

// delete old tables
$sql->setQuery('DROP TABLE '. \rex::getTablePrefix() .'d2u_jobs_categories');
$sql->setQuery('DROP TABLE '. \rex::getTablePrefix() .'d2u_jobs_categories_lang');
$sql->setQuery('DROP TABLE '. \rex::getTablePrefix() .'d2u_jobs_contacts');
$sql->setQuery('DROP TABLE '. \rex::getTablePrefix() .'d2u_jobs_jobs');
$sql->setQuery('DROP TABLE '. \rex::getTablePrefix() .'d2u_jobs_jobs_lang');

// create views
$sql->setQuery('CREATE VIEW '. \rex::getTablePrefix() .'d2u_jobs_categories AS SELECT * FROM '. \rex::getTablePrefix() .'jobs_categories');
$sql->setQuery('CREATE VIEW '. \rex::getTablePrefix() .'d2u_jobs_categories_lang AS SELECT * FROM '. \rex::getTablePrefix() .'jobs_categories_lang');
$sql->setQuery('CREATE VIEW '. \rex::getTablePrefix() .'d2u_jobs_contacts AS SELECT * FROM '. \rex::getTablePrefix() .'jobs_contacts');
$sql->setQuery('CREATE VIEW '. \rex::getTablePrefix() .'d2u_jobs_jobs AS SELECT * FROM '. \rex::getTablePrefix() .'jobs_jobs');
$sql->setQuery('CREATE VIEW '. \rex::getTablePrefix() .'d2u_jobs_jobs_lang AS SELECT * FROM '. \rex::getTablePrefix() .'jobs_jobs_lang');

// copy rex_config settings
$sql->setQuery('INSERT INTO '. \rex::getTablePrefix() .'config (`namespace`, `key`, `value`) SELECT "jobs", `key`, `value` FROM '. \rex::getTablePrefix() .'config WHERE namespace = "d2u_jobs" ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)');

// add user rights
$sql->setQuery('UPDATE '. \rex::getTablePrefix() .'user_role SET `perms` = REPLACE(`perms`, "d2u_", "")');
$sql->setQuery('UPDATE '. \rex::getTablePrefix() .'user_role SET `perms` = REPLACE(`perms`, "jobs[]", "d2u_jobs[]|jobs[]")');
