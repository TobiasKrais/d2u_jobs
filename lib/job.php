<?php
/**
 * Job details
 */
class Job {
	/**
	 * @var int Database job ID
	 */
	var $job_id = 0;
	
	/**
	 * @var int Redaxo language ID
	 */
	var $clang_id = 0;
	
	/**
	 * @var string Reference number
	 */
	var $reference_number = "";
	
	/**
	 * @var string date (Format YYYY-MM-DD).
	 */
	var $date = "";
	
	/**
	 * @var string City or region
	 */
	var $city = "";
	
	/**
	 * @var string Picture name
	 */
	var $picture = "";
	
	/**
	 * @var string Online status "online" or "offline".
	 */
	var $online_status = "offline";
	
	/**
	 * @var JobCategory[] Array with categories, job belongs to
	 */
	var $categories = [];
	
	/**
	 * @var JobContact Contact person responsible for the job
	 */
	var $contact = FALSE;
	
	/**
	 * @var string job offer name
	 */
	var $name = "";

	/**
	 * @var string HR4YOU introduction / lead-in test
	 */
	var $hr4you_lead_in = "";

	/**
	 * @var string HR4YOU application form URL
	 */
	var $hr4you_url_application_form = "";
	
	/**
	 * @var string Heading tasks
	 */
	var $tasks_heading = "";
	
	/**
	 * @var string Tasks details
	 */
	var $tasks_text = "";
	
	/**
	 * @var string Heading person profile
	 */
	var $profile_heading = "";
	
	/**
	 * @var string Profile details
	 */
	var $profile_text = "";
	
	/**
	 * @var string Heading offer
	 */
	var $offer_heading = "";
	
	/**
	 * @var string Offer details
	 */
	var $offer_text = "";
	
	/**
	 * @var string "yes" if translation needs update
	 */
	var $translation_needs_update = "delete";
	
	/**
	 * @var string URL 
	 */
	private $url = "";
	
	/**
	 * Constructor
	 * @param int $job_id Job ID.
	 * @param int $clang_id Redaxo language ID
	 */
	 public function __construct($job_id, $clang_id) {
		$this->clang_id = $clang_id;
		$query = "SELECT * FROM ". rex::getTablePrefix() ."d2u_jobs_jobs AS jobs "
				."LEFT JOIN ". rex::getTablePrefix() ."d2u_jobs_jobs_lang AS lang "
					."ON jobs.job_id = lang.job_id "
				."WHERE jobs.job_id = ". $job_id ." "
					."AND clang_id = ". $clang_id;
		$result = rex_sql::factory();
		$result->setQuery($query);

		if ($result->getRows() > 0) {
			$this->job_id = $result->getValue("job_id");
			$this->reference_number =$result->getValue("reference_number");
			$this->date = $result->getValue("date");
			$this->city = $result->getValue("city");
			$this->picture = $result->getValue("picture") != "" ? $result->getValue("picture") : rex_url::addonAssets('d2u_jobs', 'noavatar.jpg');
			$this->contact = new JobContact($result->getValue("contact_id"));
			$category_ids = preg_grep('/^\s*$/s', explode("|", $result->getValue("category_ids")), PREG_GREP_INVERT);
			foreach ($category_ids as $category_id) {
				$this->categories[$category_id] = new JobCategory($category_id, $this->clang_id);
			}
			$this->online_status = $result->getValue("online_status");
			$this->name = $result->getValue("name");

			$this->tasks_heading = $result->getValue("tasks_heading");
			$this->tasks_text = htmlspecialchars_decode($result->getValue("tasks_text"));
			$this->profile_heading = $result->getValue("profile_heading");
			$this->profile_text =  htmlspecialchars_decode($result->getValue("profile_text"));
			$this->offer_heading = $result->getValue("offer_heading");
			$this->offer_text = htmlspecialchars_decode($result->getValue("offer_text"));
			$this->translation_needs_update = $result->getValue("offer_text");

			if(rex_plugin::get('d2u_jobs', 'hr4you_import')->isAvailable()) {
				$this->hr4you_lead_in = $result->getValue("hr4you_lead_in");
				$this->hr4you_url_application_form = $result->getValue("hr4you_url_application_form");
			}
		}
	}
	
	/**
	 * Changes the online status of this object
	 */
	public function changeStatus() {
		if($this->online_status == "online") {
			if($this->job_id > 0) {
				$query = "UPDATE ". rex::getTablePrefix() ."d2u_jobs_jobs "
					."SET online_status = 'offline' "
					."WHERE job_id = ". $this->job_id;
				$result = rex_sql::factory();
				$result->setQuery($query);
			}
			$this->online_status = "offline";
		}
		else {
			if($this->job_id > 0) {
				$query = "UPDATE ". rex::getTablePrefix() ."d2u_jobs_jobs "
					."SET online_status = 'online' "
					."WHERE job_id = ". $this->job_id;
				$result = rex_sql::factory();
				$result->setQuery($query);
			}
			$this->online_status = "online";			
		}
	}
	
	/**
	 * Deletes the object in all languages.
	 * @param int $delete_all If TRUE, all translations and main object are deleted. If 
	 * FALSE, only this translation will be deleted.
	 */
	public function delete($delete_all = TRUE) {
		$query_lang = "DELETE FROM ". rex::getTablePrefix() ."d2u_jobs_jobs_lang "
			."WHERE job_id = ". $this->job_id
			. ($delete_all ? '' : ' AND clang_id = '. $this->clang_id) ;
		$result_lang = rex_sql::factory();
		$result_lang->setQuery($query_lang);
		
		// If no more lang objects are available, delete
		$query_main = "SELECT * FROM ". rex::getTablePrefix() ."d2u_jobs_jobs_lang "
			."WHERE job_id = ". $this->job_id;
		$result_main = rex_sql::factory();
		$result_main->setQuery($query_main);
		if($result_main->getRows() == 0) {
			$query = "DELETE FROM ". rex::getTablePrefix() ."d2u_jobs_jobs "
				."WHERE job_id = ". $this->job_id;
			$result = rex_sql::factory();
			$result->setQuery($query);
		}
	}
	
	/**
	 * Get all jobs
	 * @param int $clang_id Redaxo language ID
	 * @param int $category_id JobCategory ID if only jobs of that category should be imported.
	 * @param boolean $online_only If only online jobs should be returned TRUE, otherwise FALSE
	 */
	public static function getAll($clang_id, $category_id = 0, $online_only = TRUE) {
		$query = "SELECT lang.job_id FROM ". rex::getTablePrefix() ."d2u_jobs_jobs_lang AS lang "
				."LEFT JOIN ". rex::getTablePrefix() ."d2u_jobs_jobs AS jobs "
					."ON lang.job_id = jobs.job_id "
				."WHERE clang_id = ". $clang_id;
		if($online_only) {
			$query .= " AND status = 'online'";
		}
		if($category_id > 0) {
			$query .= " AND category_ids LIKE '%|". $category_id ."|%'";
		}
		$query .= " ORDER BY date DESC";

		$result = rex_sql::factory();
		$result->setQuery($query);
		
		$jobs = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$jobs[] = new Job($result->getValue('job_id'), $clang_id);
			$result->next();
		}
		
		return $jobs;
	}
	
	/**
	 * Returns the URL of this object.
	 * @param string $including_domain TRUE if Domain name should be included
	 * @return string URL
	 */
	public function getURL($including_domain = FALSE) {
		if($this->url == "") {
			$parameterArray = [];
			$parameterArray['job_id'] = $this->job_id;
			$this->url = rex_getUrl(rex_config::get('d2u_jobs', 'article_id'), $this->clang_id, $parameterArray, "&");
		}

		if($including_domain) {
			return str_replace(rex::getServer(). '/', rex::getServer(), rex::getServer() . $this->url) ;
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
		$pre_save_job = new Job($this->job_id, $this->clang_id);

		if($this->job_id == 0 || $pre_save_job != $this) {
			$query = rex::getTablePrefix() ."d2u_jobs_jobs SET "
					."reference_number = ". $this->reference_number .", "
					."category_ids = '|". implode("|", array_keys($this->categories)) ."|', "
					."date = '". $this->date ."', "
					."city = '". $this->city ."', "
					."picture = '". $this->picture ."', "
					."online_status = '". $this->online_status ."', "
					."contact_id = '". $this->contact->contact_id ."' ";
			if(rex_plugin::get("d2u_jobs", "hr4you_import")->isAvailable()) {
				$query .= ", hr4you_lead_in = '". $this->hr4you_lead_in ."' "
						.", hr4you_url_application_form = '". $this->hr4you_url_application_form ."'";
			}

			if($this->job_id == 0) {
				$query = "INSERT INTO ". $query;
			}
			else {
				$query = "UPDATE ". $query ." WHERE job_id = ". $this->job_id;
			}

			$result = rex_sql::factory();
			$result->setQuery($query);
			if($this->job_id == 0) {
				$this->job_id = $result->getLastId();
				$error = $result->hasError();
			}
		}
	
		if($error !== FALSE) {
			// Save the language specific part
			$pre_save_job = new Job($this->job_id, $this->clang_id);
			if($pre_save_job != $this) {
				$query = "REPLACE INTO ". rex::getTablePrefix() ."d2u_jobs_jobs_lang SET "
						."job_id = '". $this->job_id ."', "
						."clang_id = '". $this->clang_id ."', "
						."name = '". $this->name ."', "
						."tasks_heading = '". $this->tasks_heading ."', "
						."tasks_text = '". addslashes(htmlspecialchars($this->tasks_text)) ."', "
						."profile_heading = '". $this->profile_heading ."', "
						."profile_text = '". addslashes(htmlspecialchars($this->profile_text)) ."', "
						."offer_heading = '". $this->offer_heading ."', "
						."offer_text = '". addslashes(htmlspecialchars($this->offer_text)) ."', "
						."translation_needs_update = '". $this->translation_needs_update ."', "
						."updatedate = ". time() .", "
						."updateuser = '". rex::getUser()->getLogin() ."' ";
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
}