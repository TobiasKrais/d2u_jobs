<?php
// Update modules
if(class_exists(D2UModuleManager)) {
	$modules = [];
	$modules[] = new D2UModule("23-1",
		"D2U Jobs - Stellen",
		1);
	$modules[] = new D2UModule("23-2",
		"D2U Jobs - Kategorien",
		1);

	$d2u_module_manager = new D2UModuleManager($modules, "", "d2u_jobs");
	$d2u_module_manager->autoupdate();
}

// 1.0.2 Update database
rex_sql_table::get(rex::getTable('d2u_jobs_jobs'))->ensureColumn(new \rex_sql_column('reference_number', 'varchar(20)', true, null))->alter();

$sql->setQuery('CREATE OR REPLACE VIEW '. rex::getTablePrefix() .'d2u_jobs_url_jobs AS
	SELECT lang.job_id, lang.clang_id, lang.name, lang.name AS seo_title, lang.tasks_text AS seo_description, SUBSTRING(SUBSTRING_INDEX(category_ids, "|", 2), 2) AS category_id, lang.updatedate
	FROM '. rex::getTablePrefix() .'d2u_jobs_jobs_lang AS lang
	LEFT JOIN '. rex::getTablePrefix() .'d2u_jobs_jobs AS jobs ON lang.job_id = jobs.job_id
	LEFT JOIN '. rex::getTablePrefix() .'d2u_jobs_categories_lang AS categories ON category_id = categories.category_id AND lang.clang_id = categories.clang_id
	LEFT JOIN '. rex::getTablePrefix() .'clang AS clang ON lang.clang_id = clang.id
	WHERE clang.status = 1 AND jobs.online_status = "online"
	GROUP BY job_id, clang_id');
$sql->setQuery('CREATE OR REPLACE VIEW '. rex::getTablePrefix() .'d2u_jobs_url_jobs_categories AS
	SELECT job_urls.category_id, categories_lang.clang_id, categories_lang.name, categories_lang.name AS seo_title, categories_lang.name AS seo_description
	FROM '. rex::getTablePrefix() .'d2u_jobs_url_jobs AS job_urls
	LEFT JOIN '. rex::getTablePrefix() .'d2u_jobs_categories_lang AS categories_lang ON job_urls.category_id = categories_lang.category_id AND categories_lang.clang_id = job_urls.clang_id
	LEFT JOIN '. rex::getTablePrefix() .'clang AS clang ON categories_lang.clang_id = clang.id
	WHERE clang.status = 1
	GROUP BY category_id, clang_id, name');

if(rex_addon::get("url")->isAvailable()) {
	$clang_id = count(rex_clang::getAllIds()) == 1 ? rex_clang::getStartId() : 0;
	$sql_replace = rex_sql::factory();
	$sql->setQuery("SELECT * FROM ". rex::getTablePrefix() ."url_generate WHERE `table` = '1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs'");
	if($sql->getRows() == 0) {
		$sql_replace->setQuery("DELETE FROM `". rex::getTablePrefix() ."url_generate` WHERE `table` LIKE '%d2u_jobs_url_jobs';");
		$sql->setQuery("INSERT INTO `". rex::getTablePrefix() ."url_generate` (`article_id`, `clang_id`, `url`, `table`, `table_parameters`, `relation_table`, `relation_table_parameters`, `relation_insert`, `createdate`, `createuser`, `updatedate`, `updateuser`) VALUES
			(". rex_article::getSiteStartArticleId() .", ". $clang_id .", '', '1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs', '{\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_field_1\":\"name\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_field_2\":\"job_id\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_field_3\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_id\":\"job_id\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_clang_id\":\"". (count(rex_clang::getAllIds()) > 1 ? "clang_id" : "") ."\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_restriction_field\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_restriction_operator\":\"=\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_restriction_value\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_url_param_key\":\"job_id\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_seo_title\":\"seo_title\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_seo_description\":\"seo_description\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_seo_image\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_sitemap_add\":\"1\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_sitemap_frequency\":\"monthly\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_sitemap_priority\":\"1.0\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_sitemap_lastmod\":\"updatedate\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_path_names\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_path_categories\":\"0\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_relation_field\":\"category_id\"}', '1_xxx_relation_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories', '{\"1_xxx_relation_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories_field_1\":\"name\",\"1_xxx_relation_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories_field_2\":\"\",\"1_xxx_relation_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories_field_3\":\"\",\"1_xxx_relation_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories_id\":\"category_id\",\"1_xxx_relation_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories_clang_id\":\"clang_id\"}', 'before', UNIX_TIMESTAMP(), 'd2u_jobs_addon_installer', UNIX_TIMESTAMP(), 'd2u_jobs_addon_installer');");
	}
	$sql->setQuery("SELECT * FROM ". rex::getTablePrefix() ."url_generate WHERE `table` = '1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories'");
	if($sql->getRows() == 0) {
		$sql_replace->setQuery("DELETE FROM `". rex::getTablePrefix() ."url_generate` WHERE `table` LIKE '%d2u_jobs_url_jobs_categories';");
		$sql->setQuery("INSERT INTO `". rex::getTablePrefix() ."url_generate` (`article_id`, `clang_id`, `url`, `table`, `table_parameters`, `relation_table`, `relation_table_parameters`, `relation_insert`, `createdate`, `createuser`, `updatedate`, `updateuser`) VALUES
			(". rex_article::getSiteStartArticleId() .", ". $clang_id .", '', '1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories', '{\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories_field_1\":\"name\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories_field_2\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories_field_3\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories_id\":\"category_id\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories_clang_id\":\"". (count(rex_clang::getAllIds()) > 1 ? "clang_id" : "") ."\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories_restriction_field\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories_restriction_operator\":\"=\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories_restriction_value\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories_url_param_key\":\"job_category_id\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories_seo_title\":\"seo_title\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories_seo_description\":\"seo_description\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories_seo_image\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories_sitemap_add\":\"1\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories_sitemap_frequency\":\"always\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories_sitemap_priority\":\"0.7\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories_sitemap_lastmod\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories_path_names\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories_path_categories\":\"0\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories_relation_field\":\"\"}', '', '[]', 'before', UNIX_TIMESTAMP(), 'd2u_jobs_addon_installer', UNIX_TIMESTAMP(), 'd2u_jobs_addon_installer');");
	}

	UrlGenerator::generatePathFile([]);
}

// Insert frontend translations
d2u_jobs_lang_helper::factory()->install();