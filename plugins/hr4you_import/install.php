<?php
\rex_sql_table::get(\rex::getTable('d2u_jobs_jobs'))
    ->ensureColumn(new \rex_sql_column('hr4you_job_id', 'INT(10)'))
    ->ensureColumn(new \rex_sql_column('hr4you_url_application_form', 'VARCHAR(191)'))
    ->alter();
\rex_sql_table::get(\rex::getTable('d2u_jobs_jobs_lang'))
    ->ensureColumn(new \rex_sql_column('hr4you_lead_in', 'VARCHAR(191)'))
    ->alter();
\rex_sql_table::get(\rex::getTable('d2u_jobs_categories'))
    ->ensureColumn(new \rex_sql_column('hr4you_category_id', 'INT(10)'))
    ->alter();

// Insert frontend translations
if (class_exists('d2u_jobs_hr4you_lang_helper')) {
    d2u_jobs_hr4you_lang_helper::factory()->install();
}

if (!rex_config::has('d2u_jobs', 'hr4you_default_lang')) {
    rex_config::set('d2u_jobs', 'hr4you_default_lang', rex_clang::getStartId());
}
if (!rex_config::has('d2u_jobs', 'hr4you_headline_tag')) {
    rex_config::set('d2u_jobs', 'hr4you_headline_tag', 'h3');
}
