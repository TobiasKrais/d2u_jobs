<?php
/**
 * Job category class
 */
class JobCategory {
	/**
	 * @var int Database ID
	 */
	var $category_id = 0;
	
	/**
	 * @var int Redaxo language ID
	 */
	var $clang_id = 0;
	
	/**
	 * @var string Name
	 */
	var $name = "";
	
	/**
	 * @var string Picture
	 */
	var $picture = "";

	/**
	 * @var int Sort Priority
	 */
	var $priority = 0;

	/**
	 * @var string "yes" if translation needs update
	 */
	var $translation_needs_update = "delete";

	/**
	 * @var string URL
	 */
	var $url = "";
	
	/**
	 * Constructor
	 * @param int $category_id Category ID.
	 * @param int $clang_id Redaxo language ID.
	 */
	 public function __construct($category_id, $clang_id) {
		$query = "SELECT * FROM ". rex::getTablePrefix() ."d2u_jobs_categories_lang AS lang "
				."LEFT JOIN ". rex::getTablePrefix() ."d2u_jobs_categories AS categories "
					."ON lang.category_id = categories.category_id "
				."WHERE lang.category_id = ". $category_id ." "
					."AND clang_id = ". $clang_id;
		$result = rex_sql::factory();
		$result->setQuery($query);

		if ($result->getRows() > 0) {
			$this->category_id = $result->getValue("category_id");
			if($result->getValue("clang_id") != "") {
				$this->clang_id = $result->getValue("clang_id");
			}
			$this->name =$result->getValue("name");
			$this->picture = $result->getValue("picture");
			$this->priority = $result->getValue("priority");
			$this->translation_needs_update = $result->getValue("translation_needs_update");
		}
	}
	
	/**
	 * Deletes the object in all languages.
	 * @param int $delete_all If TRUE, all translations and main object are deleted. If 
	 * FALSE, only this translation will be deleted.
	 */
	public function delete($delete_all = TRUE) {
		$query_lang = "DELETE FROM ". rex::getTablePrefix() ."d2u_jobs_categories_lang "
			."WHERE category_id = ". $this->category_id
			. ($delete_all ? '' : ' AND clang_id = '. $this->clang_id) ;
		$result_lang = rex_sql::factory();
		$result_lang->setQuery($query_lang);
		
		// If no more lang objects are available, delete
		$query_main = "SELECT * FROM ". rex::getTablePrefix() ."d2u_jobs_categories_lang "
			."WHERE category_id = ". $this->category_id;
		$result_main = rex_sql::factory();
		$result_main->setQuery($query_main);
		if($result_main->getRows() == 0) {
			$query = "DELETE FROM ". rex::getTablePrefix() ."d2u_jobs_categories "
				."WHERE category_id = ". $this->category_id;
			$result = rex_sql::factory();
			$result->setQuery($query);
		}
	}
	
	/**
	 * Get all categories.
	 * @param int $clang_id Redaxo clang id.
	 * @return JobCategory[] Array with JobCategory objects.
	 */
	public static function getAll($clang_id) {
		$query = "SELECT lang.category_id FROM ". rex::getTablePrefix() ."d2u_jobs_categories_lang AS lang "
			."LEFT JOIN ". rex::getTablePrefix() ."d2u_jobs_categories AS categories "
				."ON lang.category_id = categories.category_id "
			."WHERE clang_id = ". $clang_id ." ";
		$query .= 'ORDER BY name';
		$result = rex_sql::factory();
		$result->setQuery($query);
		
		$categories = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$categories[$result->getValue("category_id")] = new JobCategory($result->getValue("category_id"), $clang_id);
			$result->next();
		}
		return $categories;
	}
	
	/**
	 * Get the <link rel="canonical"> tag for page header.
	 * @return Complete tag.
	 */
	public function getCanonicalTag() {
		return '<link rel="canonical" href="'. $this->getURL() .'">';
	}
	
	/**
	 * Get the <title> tag for page header.
	 * @return Complete title tag.
	 */
	public function getTitleTag() {
		return '<title>'. $this->name .' / '. rex::getServerName() .'</title>';
	}
		
	/**
	 * Gets the jobs of the category.
	 * @param boolean $only_online Show only online jobs
	 * @return Job[] Jobs in this category
	 */
	public function getJobs($only_online = FALSE) {
		$query = "SELECT lang.job_id FROM ". rex::getTablePrefix() ."d2u_jobs_jobs_lang AS lang "
			."LEFT JOIN ". rex::getTablePrefix() ."d2u_jobs_jobs AS jobs "
					."ON lang.job_id = jobs.job_id "
			."WHERE category_id = ". $this->category_id ." AND clang_id = ". $this->clang_id ." ";
		if($only_online) {
			$query .= "AND online_status = 'online' ";
		}
		$query .= 'ORDER BY name ASC';
		$result = rex_sql::factory();
		$result->setQuery($query);
		
		$jobs = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$jobs[] = new Job($result->getValue("job_id"), $this->clang_id);
			$result->next();
		}
		return $jobs;
	}

	/**
	 * Get the <meta rel="alternate" hreflang=""> tags for page header.
	 * @return Complete tags.
	 */
	public function getMetaAlternateHreflangTags() {
		$hreflang_tags = "";
		foreach(rex_clang::getAll() as $rex_clang) {
			if($rex_clang->getId() == $this->clang_id && $this->translation_needs_update != "delete") {
				$hreflang_tags .= '<link rel="alternate" type="text/html" hreflang="'. $rex_clang->getCode() .'" href="'. $this->getURL() .'" title="'. str_replace('"', '', $this->name) .'">';
			}
			else {
				$category = new JobCategory($this->category_id, $rex_clang->getId());
				if($category->translation_needs_update != "delete") {
					$hreflang_tags .= '<link rel="alternate" type="text/html" hreflang="'. $rex_clang->getCode() .'" href="'. $category->getURL() .'" title="'. str_replace('"', '', $category->name) .'">';
				}
			}
		}
		return $hreflang_tags;
	}
	
	/**
	 * Get the <meta name="description"> tag for page header.
	 * @return Complete tag.
	 */
	public function getMetaDescriptionTag() {
		return '<meta name="description" content="'. $this->name .'">';
	}
	
	/*
	 * Returns the URL of this object.
	 * @param string $including_domain TRUE if Domain name should be included
	 * @return string URL
	 */
	public function getURL($including_domain = FALSE) {
		if($this->url == "") {
				
			$parameterArray = [];
			$parameterArray['category_id'] = $this->category_id;
			
			$this->url = rex_getUrl(rex_config::get('d2u_jobs', 'article_id'), $this->clang_id, $parameterArray, "&");
		}

		if($including_domain) {
			return str_replace(rex::getServer(). '/', rex::getServer(), rex::getServer() . $this->url);
		}
		else {
			return $this->url;
		}
	}
	
	/**
	 * Updates or inserts the object into database.
	 * @return boolean TRUE if successful
	 */
	public function save() {
		$error = 0;

		// Save the not language specific part
		$pre_save_category = new JobCategory($this->category_id, $this->clang_id);
	
		// save priority, but only if new or changed
		if($this->priority != $pre_save_category->priority || $this->category_id == 0) {
			$this->setPriority();
		}

		if($this->category_id == 0 || $pre_save_category != $this) {
			$query = rex::getTablePrefix() ."d2u_jobs_categories SET "
					."priority = ". $this->priority .", "
					."picture = '". $this->picture ."' ";

			if($this->category_id == 0) {
				$query = "INSERT INTO ". $query;
			}
			else {
				$query = "UPDATE ". $query ." WHERE category_id = ". $this->category_id;
			}
			$result = rex_sql::factory();
			$result->setQuery($query);
			if($this->category_id == 0) {
				$this->category_id = $result->getLastId();
				$error = $result->hasError();
			}
		}
		
		if($error == 0) {
			// Save the language specific part
			$pre_save_category = new JobCategory($this->category_id, $this->clang_id);
			if($pre_save_category != $this) {
				$query = "REPLACE INTO ". rex::getTablePrefix() ."d2u_jobs_categories_lang SET "
						."category_id = '". $this->category_id ."', "
						."clang_id = '". $this->clang_id ."', "
						."name = '". $this->name ."', "
						."translation_needs_update = '". $this->translation_needs_update ."' ";

				$result = rex_sql::factory();
				$result->setQuery($query);
				$error = $result->hasError();
			}
		}
		
		// Update URLs
		if(rex_addon::get("url")->isAvailable()) {
			UrlGenerator::generatePathFile([]);
		}
		
		return $error;
	}
	
	/**
	 * Reassigns priority to all Categories in database.
	 */
	private function setPriority() {
		// Pull prios from database
		$query = "SELECT category_id, priority FROM ". rex::getTablePrefix() ."d2u_jobs_categories "
			."WHERE category_id <> ". $this->category_id ." ORDER BY priority";
		$result = rex_sql::factory();
		$result->setQuery($query);
		
		// When priority is too small, set at beginning
		if($this->priority <= 0) {
			$this->priority = 1;
		}
		
		// When prio is too high, simply add at end 
		if($this->priority > $result->getRows()) {
			$this->priority = $result->getRows() + 1;
		}

		$categories = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$categories[$result->getValue("priority")] = $result->getValue("category_id");
			$result->next();
		}
		array_splice($categories, ($this->priority - 1), 0, [$this->category_id]);

		// Save all prios
		foreach($categories as $prio => $category_id) {
			$query = "UPDATE ". rex::getTablePrefix() ."d2u_jobs_categories "
					."SET priority = ". ($prio + 1) ." " // +1 because array_splice recounts at zero
					."WHERE category_id = ". $category_id;
			$result = rex_sql::factory();
			$result->setQuery($query);
		}
	}
}