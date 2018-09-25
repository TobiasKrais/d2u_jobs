<?php
/**
 * Offers helper functions for frontend
 */
class d2u_jobs_frontend_helper {
	/**
	 * Returns alternate URLs. Key is Redaxo language id, value is URL
	 * @return string[] alternate URLs
	 */
	public static function getAlternateURLs() {
		$alternate_URLs = [];

		// Prepare objects first for sorting in correct order
		$urlParamKey = "";
		if(\rex_addon::get("url")->isAvailable()) {
			$url_data = UrlGenerator::getData();
			$urlParamKey = isset($url_data->urlParamKey) ? $url_data->urlParamKey : "";
		}		
		
		if(filter_input(INPUT_GET, 'job_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || (rex_addon::get("url")->isAvailable() && isset($url_data->urlParamKey) && $url_data->urlParamKey === "job_id")) {
			$job_id = filter_input(INPUT_GET, 'job_id', FILTER_VALIDATE_INT);
			if(\rex_addon::get("url")->isAvailable() && UrlGenerator::getId() > 0) {
				$job_id = UrlGenerator::getId();
			}
			foreach(rex_clang::getAllIds(TRUE) as $this_lang_key) {
				$lang_job = new D2U_Jobs\Job($job_id, $this_lang_key);
				if($lang_job->translation_needs_update != "delete") {
					$alternate_URLs[$this_lang_key] = $lang_job->getURL();
				}
			}
		}
		else if(filter_input(INPUT_GET, 'job_category_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || (rex_addon::get("url")->isAvailable() && isset($url_data->urlParamKey) && $url_data->urlParamKey === "job_category_id")) {
			$category_id = filter_input(INPUT_GET, 'job_category_id', FILTER_VALIDATE_INT);
			if(\rex_addon::get("url")->isAvailable() && UrlGenerator::getId() > 0) {
				$category_id = UrlGenerator::getId();
			}
			foreach(rex_clang::getAllIds(TRUE) as $this_lang_key) {
				$lang_category = new D2U_Jobs\Category($category_id, $this_lang_key);
				if($lang_category->translation_needs_update != "delete") {
					$alternate_URLs[$this_lang_key] = $lang_category->getURL();
				}
			}
		}
		
		return $alternate_URLs;
	}

	/**
	 * Returns breadcrumbs. Not from article path, but only part from this addon.
	 * @return string[] Breadcrumb elements
	 */
	public static function getBreadcrumbs() {
		$breadcrumbs = [];

		// Prepare objects first for sorting in correct order
		$category = FALSE;
		$job = FALSE;
		$url_data = [];
		if(\rex_addon::get("url")->isAvailable()) {
			$url_data = UrlGenerator::getData();
		}
		if(filter_input(INPUT_GET, 'job_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || (rex_addon::get("url")->isAvailable() && isset($url_data->urlParamKey) && $url_data->urlParamKey === "job_id")) {
			$job_id = filter_input(INPUT_GET, 'job_id', FILTER_VALIDATE_INT);
			if(\rex_addon::get("url")->isAvailable() && UrlGenerator::getId() > 0) {
				$job_id = UrlGenerator::getId();
			}
			$job = new D2U_Jobs\Job($job_id, rex_clang::getCurrentId());
			foreach($job->categories as $category) {
				$category = $category;
				break;
			}
		}
		else if(filter_input(INPUT_GET, 'job_category_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || (rex_addon::get("url")->isAvailable() && isset($url_data->urlParamKey) && $url_data->urlParamKey === "job_category_id")) {
			$category_id = filter_input(INPUT_GET, 'job_category_id', FILTER_VALIDATE_INT);
			if(\rex_addon::get("url")->isAvailable() && UrlGenerator::getId() > 0) {
				$category_id = UrlGenerator::getId();
			}
			$category = new D2U_Jobs\Category($category_id, rex_clang::getCurrentId());
		}

		// Breadcrumbs
		if($category !== FALSE) {
			$breadcrumbs[] = '<a href="' . $category->getUrl() . '">' . $category->name . '</a>';
		}
		if($job !== FALSE) {
			$breadcrumbs[] = '<a href="' . $job->getUrl() . '">' . $job->name . '</a>';
		}
		
		return $breadcrumbs;
	}
	
	/**
	 * Returns breadcrumbs. Not from article path, but only part from this addon.
	 * @return string[] Breadcrumb elements
	 */
	public static function getMetaTags() {
		$meta_tags = "";

		// Prepare objects first for sorting in correct order
		$urlParamKey = "";
		if(\rex_addon::get("url")->isAvailable()) {
			$url_data = UrlGenerator::getData();
			$urlParamKey = isset($url_data->urlParamKey) ? $url_data->urlParamKey : "";
		}

		// Job
		if(filter_input(INPUT_GET, 'job_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || (rex_addon::get("url")->isAvailable() && isset($url_data->urlParamKey) && $url_data->urlParamKey === "job_id")) {
			$job_id = filter_input(INPUT_GET, 'job_id', FILTER_VALIDATE_INT);
			if(\rex_addon::get("url")->isAvailable() && UrlGenerator::getId() > 0) {
				$job_id = UrlGenerator::getId();
			}
			$job = new D2U_Jobs\Job($job_id, rex_clang::getCurrentId());
			$meta_tags .= $job->getMetaAlternateHreflangTags();
			$meta_tags .= $job->getCanonicalTag() . PHP_EOL;
			$meta_tags .= $job->getMetaDescriptionTag() . PHP_EOL;
			$meta_tags .= $job->getTitleTag() . PHP_EOL;
		}
		// Category
		else if(filter_input(INPUT_GET, 'job_category_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || (rex_addon::get("url")->isAvailable() && isset($url_data->urlParamKey) && $url_data->urlParamKey === "job_category_id")) {
			$category_id = filter_input(INPUT_GET, 'job_category_id', FILTER_VALIDATE_INT);
			if(\rex_addon::get("url")->isAvailable() && UrlGenerator::getId() > 0) {
				$category_id = UrlGenerator::getId();
			}
			$category = new D2U_Jobs\Category($category_id, rex_clang::getCurrentId());
			$meta_tags .= $category->getMetaAlternateHreflangTags();
			$meta_tags .= $category->getCanonicalTag() . PHP_EOL;
			$meta_tags .= $category->getMetaDescriptionTag() . PHP_EOL;
			$meta_tags .= $category->getTitleTag() . PHP_EOL;
		}

		return $meta_tags;
	}
}