<?php
if(rex::isBackend() && is_object(rex::getUser())) {
	rex_perm::register('d2u_jobs[]', rex_i18n::msg('d2u_jobs_rights_all'));
	rex_perm::register('d2u_jobs[edit_lang]', rex_i18n::msg('d2u_jobs_rights_edit_lang'), rex_perm::OPTIONS);
	rex_perm::register('d2u_jobs[edit_data]', rex_i18n::msg('d2u_jobs_rights_edit_data'), rex_perm::OPTIONS);
	rex_perm::register('d2u_jobs[settings]', rex_i18n::msg('d2u_jobs_rights_settings'), rex_perm::OPTIONS);	
}

if(rex::isBackend()) {
	rex_extension::register('ART_PRE_DELETED', 'rex_d2u_jobs_article_is_in_use');
	rex_extension::register('CLANG_DELETED', 'rex_d2u_jobs_clang_deleted');
	rex_extension::register('MEDIA_IS_IN_USE', 'rex_d2u_jobs_media_is_in_use');
}

/**
 * Checks if article is used by this addon
 * @param rex_extension_point $ep Redaxo extension point
 * @return string[] Warning message as array
 * @throws rex_api_exception If article is used
 */
function rex_d2u_jobs_article_is_in_use(rex_extension_point $ep) {
	$warning = [];
	$params = $ep->getParams();
	$article_id = $params['id'];
	
	// Settings
	$addon = rex_addon::get("d2u_jobs");
	if($addon->hasConfig("article_id") && $addon->getConfig("article_id") == $article_id) {
		$message = '<a href="index.php?page=d2u_jobs/settings">'.
			 rex_i18n::msg('d2u_jobs_rights_all') ." - ". rex_i18n::msg('d2u_helper_settings') . '</a>';
		if(!in_array($message, $warning)) {
			$warning[] = $message;
		}
	}

	if(count($warning) > 0) {
		throw new rex_api_exception(rex_i18n::msg('d2u_helper_rex_article_cannot_delete') ."<ul><li>". implode("</li><li>", $warning) ."</li></ul>");
	}
	else {
		return "";
	}
}

/**
 * Deletes language specific configurations and objects
 * @param rex_extension_point $ep Redaxo extension point
 * @return string[] Warning message as array
 */
function rex_d2u_jobs_clang_deleted(rex_extension_point $ep) {
	$warning = $ep->getSubject();
	$params = $ep->getParams();
	$clang_id = $params['id'];

	// Delete
	$categories = \D2U_Jobs\Category::getAll($clang_id);
	foreach ($categories as $category) {
		$category->delete(FALSE);
	}
	$jobs = \D2U_Jobs\Job::getAll($clang_id, FALSE);
	foreach ($jobs as $job) {
		$job->delete(FALSE);
	}
	
	// Delete language settings
	if(rex_config::has('d2u_jobs', 'lang_replacement_'. $clang_id)) {
		rex_config::remove('d2u_jobs', 'lang_replacement_'. $clang_id);
	}
	// Delete language replacements
	d2u_jobs_lang_helper::factory()->uninstall($clang_id);

	return $warning;
}

/**
 * Checks if media is used by this addon
 * @param rex_extension_point $ep Redaxo extension point
 * @return string[] Warning message as array
 */
function rex_d2u_jobs_media_is_in_use(rex_extension_point $ep) {
	$warning = $ep->getSubject();
	$params = $ep->getParams();
	$filename = addslashes($params['filename']);

	// Jobs
	$sql_jobs = rex_sql::factory();
	$sql_jobs->setQuery('SELECT lang.job_id, name FROM `' . rex::getTablePrefix() . 'd2u_jobs_jobs_lang` AS lang '
		.'LEFT JOIN `' . rex::getTablePrefix() . 'd2u_jobs_jobs` AS jobs ON lang.job_id = jobs.job_id '
		.'WHERE picture = "'. $filename .'" '
		.'GROUP BY job_id');
	
	// Categories
	$sql_categories = rex_sql::factory();
	$sql_categories->setQuery('SELECT lang.category_id, name FROM `' . rex::getTablePrefix() . 'd2u_jobs_categories_lang` AS lang '
		.'LEFT JOIN `' . rex::getTablePrefix() . 'd2u_jobs_categories` AS categories ON lang.category_id = categories.category_id '
		.'WHERE picture = "'. $filename .'"');  

	// Contacts
	$sql_contacts = rex_sql::factory();
	$sql_contacts->setQuery('SELECT contact_id, name FROM `' . rex::getTablePrefix() . 'd2u_jobs_contacts` '
		.'WHERE picture = "'. $filename .'"');  

	// Prepare warnings
	// Jobs
	for($i = 0; $i < $sql_jobs->getRows(); $i++) {
		$message = '<a href="javascript:openPage(\'index.php?page=d2u_jobs/jobs&func=edit&entry_id='.
			$sql_jobs->getValue('job_id') .'\')">'. rex_i18n::msg('d2u_jobs_rights_all') ." - ". rex_i18n::msg('d2u_jobs') .': '. $sql_jobs->getValue('name') .'</a>';
		if(!in_array($message, $warning)) {
			$warning[] = $message;
		}
    }

	// Categories
	for($i = 0; $i < $sql_categories->getRows(); $i++) {
		$message = '<a href="javascript:openPage(\'index.php?page=d2u_jobs/category&func=edit&entry_id='. $sql_categories->getValue('category_id') .'\')">'.
			 rex_i18n::msg('d2u_jobs_rights_all') ." - ". rex_i18n::msg('d2u_helper_category') .': '. $sql_categories->getValue('name') . '</a>';
		if(!in_array($message, $warning)) {
			$warning[] = $message;
		}
    }

	// Contacts
	for($i = 0; $i < $sql_contacts->getRows(); $i++) {
		$message = '<a href="javascript:openPage(\'index.php?page=d2u_jobs/contact&func=edit&entry_id='. $sql_contacts->getValue('contact_id') .'\')">'.
			 rex_i18n::msg('d2u_jobs_rights_all') ." - ". rex_i18n::msg('d2u_jobs_contacts') .': '. $sql_contacts->getValue('name') . '</a>';
		if(!in_array($message, $warning)) {
			$warning[] = $message;
		}
    }

	// Settings
	$addon = rex_addon::get("d2u_jobs");
	if($addon->hasConfig("logo") && $addon->getConfig("logo") == $filename) {
		$message = '<a href="javascript:openPage(\'index.php?page=d2u_jobs/settings\')">'.
			 rex_i18n::msg('d2u_jobs') ." - ". rex_i18n::msg('d2u_helper_settings') . '</a>';
		if(!in_array($message, $warning)) {
			$warning[] = $message;
		}
	}
	return $warning;
}