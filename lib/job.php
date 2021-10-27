<?php
namespace D2U_Jobs;

/**
 * Job details
 */
class Job implements \D2U_Helper\ITranslationHelper {
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
	 * @var string Reference number
	 */
	var $internal_name = "";
	
	/**
	 * @var string date (Format YYYY-MM-DD).
	 */
	var $date = "";
	
	/**
	 * @var string City or region
	 */
	var $city = "";
	
	/**
	 * @var string Zip code
	 */
	var $zip_code = "";
	
	/**
	 * @var string Country code
	 */
	var $country_code = "DE";
	
	/**
	 * @var string Picture name
	 */
	var $picture = "";
	
	/**
	 * @var string Online status "online" or "offline".
	 */
	var $online_status = "offline";
	
	/**
	 * @var Category[] Array with categories, job belongs to
	 */
	var $categories = [];
	
	/**
	 * @var Contact Contact person responsible for the job
	 */
	var $contact = FALSE;
	
	/**
	 * @var string job offer name
	 */
	var $name = "";

	/**
	 * @var string job type. According to Google structured data for jobs, types
	 * can be "FULL_TIME", "PART_TIME", "CONTRACTOR", "TEMPORARY", "INTERN",
	 * "VOLUNTEER", "PER_DIEM", "OTHER"
	 */
	var $type = "FULL_TIME";

	/**
	 * @var string HR4YOU job ID
	 */
	var $hr4you_job_id = 0;

	/**
	 * @var string HR4YOU introduction / lead-in test
	 */
	var $hr4you_lead_in = "";

	/**
	 * @var string HR4YOU application form URL
	 */
	var $hr4you_url_application_form = "";
	
	/**
	 * @var string prolog text
	 */
	var $prolog = "";

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
		if($job_id > 0) {
			$query = "SELECT * FROM ". \rex::getTablePrefix() ."d2u_jobs_jobs AS jobs "
					."LEFT JOIN ". \rex::getTablePrefix() ."d2u_jobs_jobs_lang AS lang "
						."ON jobs.job_id = lang.job_id "
					."WHERE jobs.job_id = ". $job_id ." "
						."AND clang_id = ". $clang_id;
			$result = \rex_sql::factory();
			$result->setQuery($query);

			if ($result->getRows() > 0) {
				$this->job_id = $result->getValue("job_id");
				$this->reference_number = $result->getValue("reference_number");
				$this->date = $result->getValue("date");
				$this->city = $result->getValue("city");
				$this->zip_code = $result->getValue("zip_code");
				$this->country_code = $result->getValue("country_code") ?? $this->country_code;
				$this->picture = $result->getValue("picture");
				$this->contact = new Contact($result->getValue("contact_id"));
				$category_ids = preg_grep('/^\s*$/s', explode("|", $result->getValue("category_ids")), PREG_GREP_INVERT);
				foreach ($category_ids as $category_id) {
					$this->categories[$category_id] = new Category($category_id, $this->clang_id);
				}
				$this->online_status = $result->getValue("online_status");
				$this->internal_name = stripslashes($result->getValue("internal_name"));
				$this->name = stripslashes($result->getValue("name"));

				$this->prolog = stripslashes($result->getValue("prolog"));
				$this->tasks_heading = stripslashes($result->getValue("tasks_heading"));
				$this->tasks_text = stripslashes(htmlspecialchars_decode($result->getValue("tasks_text")));
				$this->profile_heading = stripslashes($result->getValue("profile_heading"));
				$this->profile_text = stripslashes(htmlspecialchars_decode($result->getValue("profile_text")));
				$this->offer_heading = stripslashes($result->getValue("offer_heading"));
				$this->offer_text = stripslashes(htmlspecialchars_decode($result->getValue("offer_text")));
				$this->translation_needs_update = $result->getValue("translation_needs_update");
				$this->type = $result->getValue("type");

				if(\rex_plugin::get('d2u_jobs', 'hr4you_import')->isAvailable()) {
					$this->hr4you_job_id = $result->getValue("hr4you_job_id") > 0 ? $result->getValue("hr4you_job_id") : 0;
					$this->hr4you_lead_in = $result->getValue("hr4you_lead_in");
					$this->hr4you_url_application_form = $result->getValue("hr4you_url_application_form");
				}
			}
		}
	}
	
	/**
	 * Changes the online status of this object
	 */
	public function changeStatus() {
		if($this->online_status == "online") {
			if($this->job_id > 0) {
				$query = "UPDATE ". \rex::getTablePrefix() ."d2u_jobs_jobs "
					."SET online_status = 'offline' "
					."WHERE job_id = ". $this->job_id;
				$result = \rex_sql::factory();
				$result->setQuery($query);
			}
			$this->online_status = "offline";
		}
		else {
			if($this->job_id > 0) {
				$query = "UPDATE ". \rex::getTablePrefix() ."d2u_jobs_jobs "
					."SET online_status = 'online' "
					."WHERE job_id = ". $this->job_id;
				$result = \rex_sql::factory();
				$result->setQuery($query);
			}
			$this->online_status = "online";			
		}
		
		// Don't forget to regenerate URL cache to make online machine available
		if(\rex_addon::get("url")->isAvailable()) {
			\d2u_addon_backend_helper::generateUrlCache("job_id");
			\d2u_addon_backend_helper::generateUrlCache("job_category_id");
		}
	}
	
	/**
	 * Deletes the object in all languages.
	 * @param int $delete_all If TRUE, all translations and main object are deleted. If 
	 * FALSE, only this translation will be deleted.
	 */
	public function delete($delete_all = TRUE) {
		$query_lang = "DELETE FROM ". \rex::getTablePrefix() ."d2u_jobs_jobs_lang "
			."WHERE job_id = ". $this->job_id
			. ($delete_all ? '' : ' AND clang_id = '. $this->clang_id) ;
		$result_lang = \rex_sql::factory();
		$result_lang->setQuery($query_lang);
		
		// If no more lang objects are available, delete
		$query_main = "SELECT * FROM ". \rex::getTablePrefix() ."d2u_jobs_jobs_lang "
			."WHERE job_id = ". $this->job_id;
		$result_main = \rex_sql::factory();
		$result_main->setQuery($query_main);
		if($result_main->getRows() == 0) {
			$query = "DELETE FROM ". \rex::getTablePrefix() ."d2u_jobs_jobs "
				."WHERE job_id = ". $this->job_id;
			$result = \rex_sql::factory();
			$result->setQuery($query);
		}
	}

	/**
	 * Create an empty object instance.
	 * @return Job empty new object
	 */
	 public static function factory() {
		 return new Job(0, 0);
	}

	/**
	 * Get all jobs
	 * @param int $clang_id Redaxo language ID
	 * @param int $category_id Category ID if only jobs of that category should be returned.
	 * @param boolean $online_only If only online jobs should be returned TRUE, otherwise FALSE
	 * @return Job[] Array with jobs
	 */
	public static function getAll($clang_id, $category_id = 0, $online_only = TRUE) {
		$query = "SELECT lang.job_id FROM ". \rex::getTablePrefix() ."d2u_jobs_jobs_lang AS lang "
				."LEFT JOIN ". \rex::getTablePrefix() ."d2u_jobs_jobs AS jobs "
					."ON lang.job_id = jobs.job_id "
				."WHERE clang_id = ". $clang_id;
		if($online_only) {
			$query .= " AND online_status = 'online'";
		}
		if($category_id > 0) {
			$query .= " AND category_ids LIKE '%|". $category_id ."|%'";
		}
		$query .= " ORDER BY date DESC";

		$result = \rex_sql::factory();
		$result->setQuery($query);
		
		$jobs = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$jobs[$result->getValue('job_id')] = new Job($result->getValue('job_id'), $clang_id);
			$result->next();
		}
		
		return $jobs;
	}

	/**
	 * Get all jobs
	 * @param int $preferred_clang_id Preferred Redaxo language ID
	 * @param int $category_id Category ID if only jobs of that category should be returned.
	 * @param boolean $online_only If only online jobs should be returned TRUE, otherwise FALSE
	 * @return Job[] Array with jobs
	 */
	public static function getAllIgnoreLanguage($preferred_clang_id, $category_id = 0, $online_only = TRUE) {
		$query = "SELECT lang.job_id, clang_id FROM ". \rex::getTablePrefix() ."d2u_jobs_jobs_lang AS lang "
				."LEFT JOIN ". \rex::getTablePrefix() ."d2u_jobs_jobs AS jobs "
					."ON lang.job_id = jobs.job_id";
		$where = [];
		if($online_only) {
			$where[] = " online_status = 'online'";
		}
		if($category_id > 0) {
			$where[] = " category_ids LIKE '%|". $category_id ."|%'";
		}
		$query .= ($where ? ' WHERE '. implode(' AND', $where) : ''). " ORDER BY date DESC";

		$result = \rex_sql::factory();
		$result->setQuery($query);
		
		$jobs = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			if(!isset($jobs[$result->getValue('job_id')]) || (isset($jobs[$result->getValue('job_id')]) && $result->getValue('clang_id') == $preferred_clang_id)) {
				$job = new self($result->getValue('job_id'), $result->getValue('clang_id'));
				if($job->job_id > 0) {
					$jobs[$result->getValue('job_id')] = $job;
				}
			}
			$result->next();
		}
		
		return $jobs;
	}

	/**
	 * Get all country codes that are used in jobs
	 * @param boolean $ignore_offline If only online jobs should be returned TRUE, otherwise FALSE
	 * @return Job[] Array with jobs
	 */
	public static function getAllCountryCodes($ignore_offline = true) {
		$query = "SELECT country_code FROM ". \rex::getTablePrefix() ."d2u_jobs_jobs ";
		if($ignore_offline) {
			$query .= "WHERE online_status = 'online'";
		}
		$query .= " GROUP BY country_code ORDER BY country_code DESC";

		$result = \rex_sql::factory();
		$result->setQuery($query);
		
		$country_codes = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$country_codes[] = $result->getValue('country_code');
			$result->next();
		}
		
		return $country_codes;
	}

	/**
	 * Get all jobs for a country
	 * @param int $clang_id Redaxo language ID
	 * @param string $country_code 2 digit country code
	 * @param boolean $online_only If only online jobs should be returned TRUE, otherwise FALSE
	 * @return Job[] Array with jobs
	 */
	public static function getByCountryCode($clang_id, $country_code, $online_only = TRUE) {
		$query = "SELECT lang.job_id FROM ". \rex::getTablePrefix() ."d2u_jobs_jobs_lang AS lang "
				."LEFT JOIN ". \rex::getTablePrefix() ."d2u_jobs_jobs AS jobs "
					."ON lang.job_id = jobs.job_id "
				."WHERE clang_id = ". $clang_id;
		if($online_only) {
			$query .= " AND online_status = 'online'";
		}
		if($country_code) {
			$query .= " AND country_code = '". $country_code ."'";
		}
		$query .= " ORDER BY date DESC";

		$result = \rex_sql::factory();
		$result->setQuery($query);
		
		$jobs = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$jobs[$result->getValue('job_id')] = new Job($result->getValue('job_id'), $clang_id);
			$result->next();
		}
		
		return $jobs;
	}

	/**
	 * Get all jobs for a country ignoring the language
	 * @param int $preferred_clang_id Redaxo language ID
	 * @param string $country_code 2 digit country code
	 * @param boolean $online_only If only online jobs should be returned TRUE, otherwise FALSE
	 * @return Job[] Array with jobs
	 */
	public static function getByCountryCodeIgnoreLanguage($preferred_clang_id, $country_code, $online_only = TRUE) {
		$query = "SELECT lang.job_id, clang_id FROM ". \rex::getTablePrefix() ."d2u_jobs_jobs_lang AS lang "
				."LEFT JOIN ". \rex::getTablePrefix() ."d2u_jobs_jobs AS jobs "
					."ON lang.job_id = jobs.job_id";
		$where = [];
		if($country_code) {
			$where[] = " country_code = '". $country_code ."'";
		}
		if($online_only) {
			$where[] = " online_status = 'online'";
		}
		$query .= ($where ? ' WHERE '. implode(' AND', $where) : ''). " ORDER BY date DESC";

		$result = \rex_sql::factory();
		$result->setQuery($query);
		
		$jobs = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			if(!isset($jobs[$result->getValue('job_id')]) || (isset($jobs[$result->getValue('job_id')]) && $result->getValue('clang_id') == $preferred_clang_id)) {
				$job = new self($result->getValue('job_id'), $result->getValue('clang_id'));
				if($job->job_id > 0) {
					$jobs[$result->getValue('job_id')] = $job;
				}
			}
			$result->next();
		}
		
		return $jobs;
	}

	/**
	 * Get job as structured data JSON LD code.
	 * @return string JSON LD code containing job data including script tag
	 */
	public function getJsonLdCode() {
		$json_job ='<script type="application/ld+json">'. PHP_EOL
			.'{'.PHP_EOL
				.'"@context" : "https://schema.org/",'. PHP_EOL
				.'"@type" : "JobPosting",'. PHP_EOL
				.'"title" : "'. addslashes($this->name) .'",'. PHP_EOL
				.'"description" : "'. addslashes($this->tasks_text) .'",'. PHP_EOL
				.'"datePosted" : "'. $this->date .'",'. PHP_EOL
//				.'"validThrough" : "'. date( "Y-m-d", strtotime( $this->date. " +2 month" ) ) .'T00:00",'. PHP_EOL
				.'"employmentType" : "'. ($this->type ?? 'FULL_TIME') .'",'. PHP_EOL
				.'"hiringOrganization" : {'. PHP_EOL
					.'"@type" : "Organization",'. PHP_EOL
					.'"name" : "'. addslashes(\rex_config::get('d2u_jobs', 'company_name', '')) .'",'. PHP_EOL
					.'"sameAs" : "'. (\rex_addon::get('yrewrite')->isAvailable() ? \rex_yrewrite::getCurrentDomain()->getUrl() : rex::getServer()) .'",'. PHP_EOL
					.'"logo" : "'. rtrim((\rex_addon::get('yrewrite')->isAvailable() ? \rex_yrewrite::getCurrentDomain()->getUrl() : rex::getServer()), "/") . \rex_url::media(\rex_config::get('d2u_jobs', 'logo', '')) .'"'. PHP_EOL
				.'},'. PHP_EOL
				.'"jobLocation": {'. PHP_EOL
					.'"@type": "Place",'. PHP_EOL
					.'"address": {'. PHP_EOL
						.'"@type": "PostalAddress",'. PHP_EOL
//						.'"streetAddress": "1600 Amphitheatre Pkwy, ",'. PHP_EOL
						.'"addressLocality": "'. $this->city .'"'. PHP_EOL
						.($this->zip_code != "" ? ', "postalCode": "'. $this->zip_code .'"'. PHP_EOL : "")
						.($this->country_code != "" ? ', "addressCountry": "'. $this->country_code .'"'. PHP_EOL : "")
					.'}'. PHP_EOL
				.'}'. PHP_EOL
			.'}'. PHP_EOL
		.'</script>';
		return $json_job;
	}

	/**
	 * Get object by HR4You ID
	 * @param int $hr4you_id HR4You ID
	 * @return Job Job object, if available, otherwise FALSE
	 */
	public static function getByHR4YouID($hr4you_id) {
		if(\rex_plugin::get('d2u_jobs', 'hr4you_import')->isAvailable()) {
			$query = "SELECT job_id FROM ". \rex::getTablePrefix() ."d2u_jobs_jobs "
					."WHERE hr4you_job_id = ". $hr4you_id;
			$result = \rex_sql::factory();
			$result->setQuery($query);

			if($result->getRows() > 0) {
				return new Job($result->getValue("job_id"), \rex_config::get('d2u_jobs', 'hr4you_default_lang'));
			}
		}
		return FALSE;
	}

	/**
	 * Get all jobs imported by HR4You Plugin
	 * @return Job[] Array with jobs
	 */
	public static function getAllHR4YouJobs() {
		$query = "SELECT job_id, hr4you_job_id FROM ". \rex::getTablePrefix() ."d2u_jobs_jobs "
				."WHERE hr4you_job_id > 0";
		$result = \rex_sql::factory();
		$result->setQuery($query);
		
		$jobs = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$jobs[$result->getValue('hr4you_job_id')] = new Job($result->getValue('job_id'), \rex_config::get('d2u_jobs', 'hr4you_default_lang'));
			$result->next();
		}
		
		return $jobs;
	}

	/**
	 * Get objects concerning translation updates
	 * @param int $clang_id Redaxo language ID
	 * @param string $type 'update' or 'missing'
	 * @return Job[] Array with Job objects.
	 */
	public static function getTranslationHelperObjects($clang_id, $type) {
		$query = 'SELECT job_id FROM '. \rex::getTablePrefix() .'d2u_jobs_jobs_lang '
				."WHERE clang_id = ". $clang_id ." AND translation_needs_update = 'yes' "
				.'ORDER BY name';
		if($type == 'missing') {
			$query = 'SELECT main.job_id FROM '. \rex::getTablePrefix() .'d2u_jobs_jobs AS main '
					.'LEFT JOIN '. \rex::getTablePrefix() .'d2u_jobs_jobs_lang AS target_lang '
						.'ON main.job_id = target_lang.job_id AND target_lang.clang_id = '. $clang_id .' '
					.'LEFT JOIN '. \rex::getTablePrefix() .'d2u_jobs_jobs_lang AS default_lang '
						.'ON main.job_id = default_lang.job_id AND default_lang.clang_id = '. \rex_config::get('d2u_helper', 'default_lang') .' '
					."WHERE target_lang.job_id IS NULL "
					.'ORDER BY default_lang.name';
			$clang_id = \rex_config::get('d2u_helper', 'default_lang');
		}
		$result = \rex_sql::factory();
		$result->setQuery($query);

		$objects = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$job = new Job($result->getValue("job_id"), $clang_id);
			// HR4YOU Import may have an other language than default language
			if($job->job_id == 0 && \rex_plugin::get("d2u_jobs", "hr4you_import")->isAvailable()) {
				$job = new Job($result->getValue("job_id"), \rex_config::get('d2u_jobs', 'hr4you_default_lang'));
			}
			$objects[] = $job;
			$result->next();
		}
		
		return $objects;
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
			$this->url = \rex_getUrl(\rex_config::get('d2u_jobs', 'article_id'), $this->clang_id, $parameterArray, "&");
		}

		if($including_domain) {
			if(\rex_addon::get('yrewrite') && \rex_addon::get('yrewrite')->isAvailable())  {
				return str_replace(\rex_yrewrite::getCurrentDomain()->getUrl() .'/', \rex_yrewrite::getCurrentDomain()->getUrl(), \rex_yrewrite::getCurrentDomain()->getUrl() . $this->url);
			}
			else {
				return str_replace(\rex::getServer(). '/', \rex::getServer(), \rex::getServer() . $this->url);
			}
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
		$error = FALSE;

		// Save the not language specific part
		$pre_save_job = new Job($this->job_id, $this->clang_id);

		if($this->job_id == 0 || $pre_save_job != $this) {
			$query = \rex::getTablePrefix() ."d2u_jobs_jobs SET "
					."reference_number = '". $this->reference_number ."', "
					."category_ids = '|". implode("|", array_keys($this->categories)) ."|', "
					."contact_id = ". $this->contact->contact_id .", "
					."date = '". $this->date ."', "
					."city = '". $this->city ."', "
					."zip_code = '". $this->zip_code ."', "
					."country_code = '". $this->country_code ."', "
					."picture = '". $this->picture ."', "
					."internal_name = '". addslashes($this->internal_name) ."', "
					."online_status = '". $this->online_status ."', "
					."type = '". $this->type ."' ";
			if(\rex_plugin::get("d2u_jobs", "hr4you_import")->isAvailable()) {
				$query .= ", hr4you_job_id = ". $this->hr4you_job_id .", "
						."hr4you_url_application_form = '". $this->hr4you_url_application_form ."'";
			}

			if($this->job_id == 0) {
				$query = "INSERT INTO ". $query;
			}
			else {
				$query = "UPDATE ". $query ." WHERE job_id = ". $this->job_id;
			}

			$result = \rex_sql::factory();
			$result->setQuery($query);
			if($this->job_id == 0) {
				$this->job_id = $result->getLastId();
				$error = $result->hasError();
			}
		}

		$regenerate_urls = false;
		if(!$error) {
			// Save the language specific part
			$pre_save_job = new Job($this->job_id, $this->clang_id);
			if($pre_save_job != $this) {
				$query = "REPLACE INTO ". \rex::getTablePrefix() ."d2u_jobs_jobs_lang SET "
						."job_id = '". $this->job_id ."', "
						."clang_id = '". $this->clang_id ."', "
						."name = '". addslashes($this->name) ."', "
						."prolog = '". addslashes($this->prolog) ."', "
						."tasks_heading = '". addslashes($this->tasks_heading) ."', "
						."tasks_text = '". addslashes(htmlspecialchars($this->tasks_text)) ."', "
						."profile_heading = '". addslashes($this->profile_heading) ."', "
						."profile_text = '". addslashes(htmlspecialchars($this->profile_text)) ."', "
						."offer_heading = '". addslashes($this->offer_heading) ."', "
						."offer_text = '". addslashes(htmlspecialchars($this->offer_text)) ."', "
						."translation_needs_update = '". $this->translation_needs_update ."', "
						."updatedate = CURRENT_TIMESTAMP ";
				if(\rex::getUser()) {
					$query .= ", updateuser = '". \rex::getUser()->getLogin() ."' ";
				}
				else if(\rex_plugin::get("d2u_jobs", "hr4you_import")->isAvailable()) {
					$query .= ", hr4you_lead_in = '". $this->hr4you_lead_in ."' "
							. ", updateuser = 'hr4you_autoimport' ";
				}
				$result = \rex_sql::factory();
				$result->setQuery($query);
				$error = $result->hasError();
				
				if(!$error && $pre_save_object->name != $this->name) {
					$regenerate_urls = true;
				}
			}
		}

		// Update URLs
		if($regenerate_urls) {
			\d2u_addon_backend_helper::generateUrlCache('job_id');
		}
		
		return $error;
	}
}