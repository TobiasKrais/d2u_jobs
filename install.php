<?php

// Install database
\rex_sql_table::get(\rex::getTable('d2u_jobs_jobs'))
    ->ensureColumn(new rex_sql_column('job_id', 'INT(11) unsigned', false, null, 'auto_increment'))
    ->setPrimaryKey('job_id')
    ->ensureColumn(new \rex_sql_column('reference_number', 'VARCHAR(20)', true))
    ->ensureColumn(new \rex_sql_column('category_ids', 'VARCHAR(191)', true))
    ->ensureColumn(new \rex_sql_column('contact_id', 'INT(11)', false))
    ->ensureColumn(new \rex_sql_column('internal_name', 'VARCHAR(191)', true))
    ->ensureColumn(new \rex_sql_column('date', 'VARCHAR(10)', true))
    ->ensureColumn(new \rex_sql_column('city', 'VARCHAR(50)', true))
    ->ensureColumn(new \rex_sql_column('zip_code', 'VARCHAR(10)', true))
    ->ensureColumn(new \rex_sql_column('country_code', 'VARCHAR(2)', true))
    ->ensureColumn(new \rex_sql_column('picture', 'VARCHAR(191)', true))
    ->ensureColumn(new \rex_sql_column('online_status', 'VARCHAR(10)', true))
    ->ensureColumn(new \rex_sql_column('type', 'VARCHAR(20)', true))
    ->ensureColumn(new \rex_sql_column('priority', 'INT(11)', true, '0'))
    ->ensureColumn(new \rex_sql_column('hr4you_job_id', 'INT(10)'))
    ->ensureColumn(new \rex_sql_column('hr4you_url_application_form', 'VARCHAR(191)'))
    ->ensure();
\rex_sql_table::get(\rex::getTable('d2u_jobs_jobs_lang'))
    ->ensureColumn(new rex_sql_column('job_id', 'INT(11) unsigned', false, null, 'auto_increment'))
    ->ensureColumn(new \rex_sql_column('clang_id', 'INT(11)', false, '1'))
    ->setPrimaryKey(['job_id', 'clang_id'])
    ->ensureColumn(new \rex_sql_column('name', 'VARCHAR(191)'))
    ->ensureColumn(new \rex_sql_column('prolog', 'TEXT', true, null))
    ->ensureColumn(new \rex_sql_column('tasks_heading', 'VARCHAR(191)', true, null))
    ->ensureColumn(new \rex_sql_column('tasks_text', 'TEXT', true, null))
    ->ensureColumn(new \rex_sql_column('profile_heading', 'VARCHAR(191)', true, null))
    ->ensureColumn(new \rex_sql_column('profile_text', 'TEXT', true, null))
    ->ensureColumn(new \rex_sql_column('offer_heading', 'VARCHAR(191)', true, null))
    ->ensureColumn(new \rex_sql_column('offer_text', 'TEXT', true, null))
    ->ensureColumn(new \rex_sql_column('translation_needs_update', 'VARCHAR(7)', true, 'no'))
    ->ensureColumn(new \rex_sql_column('hr4you_lead_in', 'VARCHAR(191)'))
    ->ensureColumn(new \rex_sql_column('updatedate', 'DATETIME'))
    ->ensureColumn(new \rex_sql_column('updateuser', 'VARCHAR(191)'))
    ->ensure();

\rex_sql_table::get(\rex::getTable('d2u_jobs_categories'))
    ->ensureColumn(new rex_sql_column('category_id', 'INT(11) unsigned', false, null, 'auto_increment'))
    ->setPrimaryKey('category_id')
    ->ensureColumn(new \rex_sql_column('priority', 'INT(11)', true, '0'))
    ->ensureColumn(new \rex_sql_column('picture', 'VARCHAR(255)', true))
    ->ensureColumn(new \rex_sql_column('hr4you_category_id', 'INT(10)'))
    ->ensure();
\rex_sql_table::get(\rex::getTable('d2u_jobs_categories_lang'))
    ->ensureColumn(new rex_sql_column('category_id', 'INT(11) unsigned', false, null, 'auto_increment'))
    ->ensureColumn(new \rex_sql_column('clang_id', 'INT(11)', false, '1'))
    ->setPrimaryKey(['category_id', 'clang_id'])
    ->ensureColumn(new \rex_sql_column('name', 'VARCHAR(255)'))
    ->ensureColumn(new \rex_sql_column('translation_needs_update', 'VARCHAR(7)'))
    ->ensure();

\rex_sql_table::get(\rex::getTable('d2u_jobs_contacts'))
    ->ensureColumn(new rex_sql_column('contact_id', 'int(10) unsigned', false, null, 'auto_increment'))
    ->setPrimaryKey('contact_id')
    ->ensureColumn(new \rex_sql_column('name', 'VARCHAR(255)', true))
    ->ensureColumn(new \rex_sql_column('picture', 'VARCHAR(255)', true))
    ->ensureColumn(new \rex_sql_column('phone', 'VARCHAR(50)', true))
    ->ensureColumn(new \rex_sql_column('phone_video', 'VARCHAR(50)', true))
    ->ensureColumn(new \rex_sql_column('email', 'VARCHAR(50)', true))
    ->ensure();

$sql = rex_sql::factory();

// Update database 1.2.1:
$sql->setQuery('SELECT * FROM '. rex::getTablePrefix() .'d2u_jobs_jobs_lang AS lang '
    . 'LEFT JOIN '. rex::getTablePrefix() .'d2u_jobs_jobs AS jobs ON lang.job_id = jobs.job_id');
for ($i = 0; $i < $sql->getRows(); ++$i) {
    $sql_update = rex_sql::factory();
    // set internal name
    if ('' === $sql->getValue('internal_name')) {
        $sql_update->setQuery('UPDATE '. \rex::getTablePrefix() .'d2u_jobs_jobs SET internal_name = "'. $sql->getValue('name') .'" WHERE job_id = '. $sql->getValue('job_id'));
    }
    // decode HTML entities
    if (rex_version::compare($this->getVersion(), '1.2.3', '<')) { /** @phpstan-ignore-line */
        $sql_update->setQuery('UPDATE '. \rex::getTablePrefix() .'d2u_jobs_jobs_lang SET tasks_text = "'. addslashes(stripslashes(html_entity_decode(htmlspecialchars_decode((string) $sql->getValue('tasks_text'))))) .'", '
            . 'profile_text = "'. addslashes(stripslashes(html_entity_decode(htmlspecialchars_decode((string) $sql->getValue('profile_text'))))) .'", '
            . 'offer_text = "'. addslashes(stripslashes(html_entity_decode(htmlspecialchars_decode((string) $sql->getValue('offer_text'))))) .'" '
            . 'WHERE job_id = '. $sql->getValue('job_id') .' AND clang_id = '. $sql->getValue('clang_id'));
    }
    $sql->next();
}

// Create views for url addon
$sql->setQuery('CREATE OR REPLACE VIEW '. rex::getTablePrefix() .'d2u_jobs_url_jobs AS
	SELECT lang.job_id, lang.clang_id, lang.name, lang.name AS seo_title, TRIM(ExtractValue(lang.tasks_text, "//text()")) AS seo_description, jobs.picture, SUBSTRING(SUBSTRING_INDEX(category_ids, "|", 2), 2) AS category_id, lang.updatedate
	FROM '. rex::getTablePrefix() .'d2u_jobs_jobs_lang AS lang
	LEFT JOIN '. rex::getTablePrefix() .'d2u_jobs_jobs AS jobs ON lang.job_id = jobs.job_id
	LEFT JOIN '. rex::getTablePrefix() .'d2u_jobs_categories_lang AS categories ON category_id = categories.category_id AND lang.clang_id = categories.clang_id
	LEFT JOIN '. rex::getTablePrefix() .'clang AS clang ON lang.clang_id = clang.id
	WHERE clang.`status` = 1 AND jobs.online_status = "online"
	GROUP BY job_id, clang_id, name, seo_title, seo_description, picture, updatedate;');
$sql->setQuery('CREATE OR REPLACE VIEW '. rex::getTablePrefix() .'d2u_jobs_url_jobs_categories AS
	SELECT job_urls.category_id, categories_lang.clang_id, categories_lang.name, categories_lang.name AS seo_title, categories_lang.name AS seo_description, categories.picture, (SELECT MAX(job_urls_updatedate.updatedate) FROM '. rex::getTablePrefix() .'d2u_jobs_url_jobs AS job_urls_updatedate WHERE job_urls_updatedate.category_id = job_urls.category_id) AS updatedate
	FROM '. rex::getTablePrefix() .'d2u_jobs_url_jobs AS job_urls
	LEFT JOIN '. rex::getTablePrefix() .'d2u_jobs_categories_lang AS categories_lang ON job_urls.category_id = categories_lang.category_id AND categories_lang.clang_id = job_urls.clang_id
	LEFT JOIN '. rex::getTablePrefix() .'d2u_jobs_categories AS categories ON categories_lang.category_id = categories.category_id
	LEFT JOIN '. rex::getTablePrefix() .'clang AS clang ON categories_lang.clang_id = clang.id
	WHERE clang.`status` = 1
	GROUP BY category_id, clang_id, name, picture;');

// old plugin hr4you_import still exists ? -> delete
$plugin_path = rex_path::addon('d2u_jobs', 'plugins');
if (file_exists($plugin_path)) {
    rex_config::set('d2u_jobs', 'use_hr4you', true);
    rex_dir::delete($plugin_path);
}