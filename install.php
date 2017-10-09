<?php
$sql = rex_sql::factory();
// Install database
$sql->setQuery("CREATE TABLE IF NOT EXISTS `". rex::getTablePrefix() ."d2u_jobs_jobs` (
	`job_id` int(10) unsigned NOT NULL auto_increment,
	`reference_number` int(10) default NULL,
	`category_ids` varchar(255) collate utf8_general_ci,
	`date` varchar(10) collate utf8_general_ci default NULL,
	`city` varchar(255) collate utf8_general_ci default NULL,
	`picture` varchar(255) collate utf8_general_ci default NULL,
	`online_status` varchar(10) collate utf8_general_ci default 'online',
	`contact_id` int(10) default NULL,
	PRIMARY KEY (`job_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1;");
$sql->setQuery("CREATE TABLE IF NOT EXISTS `". rex::getTablePrefix() ."d2u_jobs_jobs_lang` (
	`job_id` int(10) NOT NULL,
	`clang_id` int(10) NOT NULL,
	`name` varchar(255) collate utf8_general_ci default NULL,
	`tasks_heading` varchar(255) collate utf8_general_ci default NULL,
	`tasks_text` text collate utf8_general_ci default NULL,
	`profile_heading` varchar(255) collate utf8_general_ci default NULL,
	`profile_text` text collate utf8_general_ci default NULL,
	`offer_heading` varchar(255) collate utf8_general_ci default NULL,
	`offer_text` text collate utf8_general_ci default NULL,
	`translation_needs_update` varchar(7) collate utf8_general_ci default NULL,
	`updatedate` int(11) default NULL,
	`updateuser` varchar(255) collate utf8_general_ci default NULL,
	PRIMARY KEY (`job_id`, `clang_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1;");

$sql->setQuery("CREATE TABLE IF NOT EXISTS ". rex::getTablePrefix() ."d2u_jobs_categories (
	category_id int(10) unsigned NOT NULL auto_increment,
	priority int(10) default NULL,
	picture varchar(255) collate utf8_general_ci default NULL,
	PRIMARY KEY (category_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1;");
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". rex::getTablePrefix() ."d2u_jobs_categories_lang (
	category_id int(10) NOT NULL,
	clang_id int(10) NOT NULL,
	name varchar(255) collate utf8_general_ci default NULL,
	translation_needs_update varchar(7) collate utf8_general_ci default NULL,
	PRIMARY KEY (category_id, clang_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1;");

$sql->setQuery("CREATE TABLE IF NOT EXISTS `". rex::getTablePrefix() ."d2u_jobs_contacts` (
	`contact_id` int(10) unsigned NOT NULL auto_increment,
	`name` varchar(255) collate utf8_general_ci default NULL,
	`picture` varchar(255) collate utf8_general_ci default NULL,
	`phone` varchar(255) collate utf8_general_ci default NULL,
	`email` varchar(50) collate utf8_general_ci default NULL,
	PRIMARY KEY (`contact_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1;");

// Create views for url addon
$sql->setQuery('CREATE OR REPLACE VIEW '. rex::getTablePrefix() .'d2u_jobs_url_jobs AS
	SELECT lang.job_id, lang.clang_id, lang.name, lang.name AS seo_title, lang.tasks_text AS seo_description, SUBSTRING(SUBSTRING_INDEX(category_ids, "|", 2), 2) AS category_id, lang.updatedate
	FROM '. rex::getTablePrefix() .'d2u_jobs_jobs_lang AS lang
	LEFT JOIN '. rex::getTablePrefix() .'d2u_jobs_jobs AS jobs ON lang.job_id = jobs.job_id
	LEFT JOIN '. rex::getTablePrefix() .'d2u_jobs_categories_lang AS categories ON category_id = categories.category_id AND lang.clang_id = categories.clang_id
	LEFT JOIN '. rex::getTablePrefix() .'clang AS clang ON lang.clang_id = clang.id
	WHERE clang.status = 1 AND jobs.online_status = "online"
	GROUP BY job_id, clang_id, name, seo_title, seo_description, category_id, updatedate');
$sql->setQuery('CREATE OR REPLACE VIEW '. rex::getTablePrefix() .'d2u_jobs_url_jobs_categories AS
	SELECT job_urls.category_id, categories_lang.clang_id, categories_lang.name, categories_lang.name AS seo_title, categories_lang.name AS seo_description
	FROM '. rex::getTablePrefix() .'d2u_jobs_url_jobs AS job_urls
	LEFT JOIN '. rex::getTablePrefix() .'d2u_jobs_categories_lang AS categories_lang ON job_urls.category_id = categories_lang.category_id AND categories_lang.clang_id = job_urls.clang_id
	LEFT JOIN '. rex::getTablePrefix() .'clang AS clang ON categories_lang.clang_id = clang.id
	WHERE clang.status = 1
	GROUP BY category_id, clang_id, name, seo_title, seo_description');
// Insert url schemes
if(rex_addon::get('url')->isAvailable()) {
	$sql->setQuery("SELECT * FROM ". rex::getTablePrefix() ."url_generate WHERE `table` = '1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs'");
	$clang_id = count(rex_clang::getAllIds()) == 1 ? rex_clang::getStartId() : 0;
	if($sql->getRows() == 0) {
		$sql->setQuery("INSERT INTO `". rex::getTablePrefix() ."url_generate` (`article_id`, `clang_id`, `url`, `table`, `table_parameters`, `relation_table`, `relation_table_parameters`, `relation_insert`, `createdate`, `createuser`, `updatedate`, `updateuser`) VALUES
			(". rex_article::getSiteStartArticleId() .", ". $clang_id .", '', '1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs', '{\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_field_1\":\"name\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_field_2\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_field_3\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_id\":\"job_id\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_clang_id\":\"". (count(rex_clang::getAllIds()) > 1 ? "clang_id" : "") ."\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_restriction_field\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_restriction_operator\":\"=\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_restriction_value\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_url_param_key\":\"job_id\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_seo_title\":\"seo_title\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_seo_description\":\"seo_description\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_sitemap_add\":\"1\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_sitemap_frequency\":\"monthly\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_sitemap_priority\":\"1.0\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_sitemap_lastmod\":\"updatedate\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_path_names\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_path_categories\":\"0\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_relation_field\":\"category_id\"}', '1_xxx_relation_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories', '{\"1_xxx_relation_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories_field_1\":\"name\",\"1_xxx_relation_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories_field_2\":\"\",\"1_xxx_relation_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories_field_3\":\"\",\"1_xxx_relation_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories_id\":\"category_id\",\"1_xxx_relation_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories_clang_id\":\"clang_id\"}', 'before', UNIX_TIMESTAMP(), 'd2u_jobs_addon_installer', UNIX_TIMESTAMP(), 'd2u_jobs_addon_installer');");
	}
	$sql->setQuery("SELECT * FROM ". rex::getTablePrefix() ."url_generate WHERE `table` = '1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories'");
	if($sql->getRows() == 0) {
		$sql->setQuery("INSERT INTO `". rex::getTablePrefix() ."url_generate` (`article_id`, `clang_id`, `url`, `table`, `table_parameters`, `relation_table`, `relation_table_parameters`, `relation_insert`, `createdate`, `createuser`, `updatedate`, `updateuser`) VALUES
			(". rex_article::getSiteStartArticleId() .", ". $clang_id .", '', '1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories', '{\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories_field_1\":\"name\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories_field_2\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories_field_3\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories_id\":\"category_id\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories_clang_id\":\"". (count(rex_clang::getAllIds()) > 1 ? "clang_id" : "") ."\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories_restriction_field\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories_restriction_operator\":\"=\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories_restriction_value\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories_url_param_key\":\"category_id\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories_seo_title\":\"seo_title\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories_seo_description\":\"seo_description\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories_sitemap_add\":\"1\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories_sitemap_frequency\":\"always\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories_sitemap_priority\":\"0.7\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories_sitemap_lastmod\":\"updatedate\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories_path_names\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories_path_categories\":\"0\",\"1_xxx_". rex::getTablePrefix() ."d2u_jobs_url_jobs_categories_relation_field\":\"\"}', '', '[]', 'before', UNIX_TIMESTAMP(), 'd2u_jobs_addon_installer', UNIX_TIMESTAMP(), 'd2u_jobs_addon_installer');");
	}
	UrlGenerator::generatePathFile([]);
}

// Insert frontend translations
if(class_exists(d2u_jobs_lang_helper)) {
	d2u_jobs_lang_helper::factory()->install();
}

// Standard settings
if (!$this->hasConfig()) {
    $this->setConfig('article_id', rex_article::getSiteStartArticleId());
}