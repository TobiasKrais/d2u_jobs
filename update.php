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

if(rex_addon::get("url")->isAvailable()) {
	$clang_id = count(rex_clang::getAllIds()) == 1 ? rex_clang::getStartId() : 0;
	$sql_replace = rex_sql::factory();
	$sql->setQuery("SELECT * FROM ". rex::getTablePrefix() ."url_generate WHERE `table` = '1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs'");
	if($sql->getRows() == 0) {
		$sql_replace->setQuery("DELETE FROM `". rex::getTablePrefix() ."url_generate` WHERE `table` LIKE '%d2u_jobs_url_jobs';");
		$sql->setQuery("INSERT INTO `". rex::getTablePrefix() ."url_generate` (`article_id`, `clang_id`, `url`, `table`, `table_parameters`, `relation_table`, `relation_table_parameters`, `relation_insert`, `createdate`, `createuser`, `updatedate`, `updateuser`) VALUES
			(". rex_article::getSiteStartArticleId() .", ". $clang_id .", '', '1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs', '{\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_field_1\":\"name\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_field_2\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_field_3\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_id\":\"job_id\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_clang_id\":\"". (count(rex_clang::getAllIds()) > 1 ? "clang_id" : "") ."\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_restriction_field\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_restriction_operator\":\"=\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_restriction_value\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_url_param_key\":\"job_id\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_seo_title\":\"seo_title\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_seo_description\":\"seo_description\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_seo_image\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_sitemap_add\":\"1\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_sitemap_frequency\":\"monthly\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_sitemap_priority\":\"1.0\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_sitemap_lastmod\":\"updatedate\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_path_names\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_path_categories\":\"0\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_relation_field\":\"category_id\"}', '1_xxx_relation_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories', '{\"1_xxx_relation_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories_field_1\":\"name\",\"1_xxx_relation_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories_field_2\":\"\",\"1_xxx_relation_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories_field_3\":\"\",\"1_xxx_relation_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories_id\":\"category_id\",\"1_xxx_relation_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories_clang_id\":\"clang_id\"}', 'before', UNIX_TIMESTAMP(), 'd2u_jobs_addon_installer', UNIX_TIMESTAMP(), 'd2u_jobs_addon_installer');");
	}
	$sql->setQuery("SELECT * FROM ". rex::getTablePrefix() ."url_generate WHERE `table` = '1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories'");
	if($sql->getRows() == 0) {
		$sql_replace->setQuery("DELETE FROM `". rex::getTablePrefix() ."url_generate` WHERE `table` LIKE '%d2u_jobs_url_jobs_categories';");
		$sql->setQuery("INSERT INTO `". rex::getTablePrefix() ."url_generate` (`article_id`, `clang_id`, `url`, `table`, `table_parameters`, `relation_table`, `relation_table_parameters`, `relation_insert`, `createdate`, `createuser`, `updatedate`, `updateuser`) VALUES
			(". rex_article::getSiteStartArticleId() .", ". $clang_id .", '', '1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories', '{\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories_field_1\":\"name\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories_field_2\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories_field_3\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories_id\":\"category_id\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories_clang_id\":\"". (count(rex_clang::getAllIds()) > 1 ? "clang_id" : "") ."\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories_restriction_field\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories_restriction_operator\":\"=\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories_restriction_value\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories_url_param_key\":\"job_category_id\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories_seo_title\":\"seo_title\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories_seo_description\":\"seo_description\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories_seo_image\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories_sitemap_add\":\"1\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories_sitemap_frequency\":\"always\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories_sitemap_priority\":\"0.7\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories_sitemap_lastmod\":\"updatedate\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories_path_names\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories_path_categories\":\"0\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories_relation_field\":\"\"}', '', '[]', 'before', UNIX_TIMESTAMP(), 'd2u_jobs_addon_installer', UNIX_TIMESTAMP(), 'd2u_jobs_addon_installer');");
	}

	UrlGenerator::generatePathFile([]);
}

// Insert frontend translations
d2u_jobs_lang_helper::factory()->install();