<?php
namespace D2U_Jobs;

/**
 * Job contact class
 */
class Contact {
	/**
	 * @var int Database ID
	 */
	var $contact_id = 0;
	
	/**
	 * @var string Name
	 */
	var $name = "";
	
	/**
	 * @var string Picture
	 */
	var $picture = "";
	
	/**
	 * @var string Phone number
	 */
	var $phone = "";
	
	/**
	 * @var string E-Mail address
	 */
	var $email = "";
	
	/**
	 * Constructor.
	 * @param int $contact_id Contact ID.
	 */
	public function __construct($contact_id = 0) {
		if($contact_id > 0) {
			$query = "SELECT * FROM ". \rex::getTablePrefix() ."d2u_jobs_contacts "
					."WHERE contact_id = ". $contact_id;
			$result = \rex_sql::factory();
			$result->setQuery($query);
			$num_rows = $result->getRows();

			if ($num_rows > 0) {
				$this->contact_id = $result->getValue("contact_id");
				$this->name = $result->getValue("name");
				$this->picture = $result->getValue("picture");
				$this->phone = $result->getValue("phone");
				$this->email = $result->getValue("email");
			}
		}
		else {
			return $this;
		}
	}
		
	/**
	 * Deletes the object.
	 */
	public function delete() {
		$query = "DELETE FROM ". \rex::getTablePrefix() ."d2u_jobs_contacts "
				."WHERE contact_id = ". $this->contact_id;
		$result = \rex_sql::factory();
		$result->setQuery($query);
	}

	/**
	 * Create an empty object instance.
	 * @return Contact empty new object
	 */
	 public static function factory() {
		 return new Contact();
	}
	
	/**
	 * Get all contacts.
	 * @return Contact[] Array with Contact objects.
	 */
	public static function getAll() {
		$query = "SELECT contact_id FROM ". \rex::getTablePrefix() ."d2u_jobs_contacts ORDER BY name";
		$result = \rex_sql::factory();
		$result->setQuery($query);
		
		$contacts = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$contacts[$result->getValue("contact_id")] = new Contact($result->getValue("contact_id"));
			$result->next();
		}
		return $contacts;
	}
	
	/**
	 * Get contact by e-mail address.
	 * @param string $email E-Mail address
	 */
	public static function getByMail($email) {
		$query = "SELECT contact_id FROM ". \rex::getTablePrefix() ."d2u_jobs_contacts "
				."WHERE email = '". $email ."'";
		$result = \rex_sql::factory();
		$result->setQuery($query);
		$num_rows = $result->getRows();

		if ($num_rows > 0) {
			return new Contact($result->getValue("contact_id"));
		}
		else {
			return FALSE;
		}
	}

	/**
	 * Gets the jobs of the contact.
	 * @return Job[] Jobs
	 */
	public function getJobs() {
		$clang_id = \rex_config::get("d2u_helper", "default_lang");
		$query = "SELECT jobs.job_id FROM ". \rex::getTablePrefix() ."d2u_jobs_jobs AS jobs "
			."LEFT JOIN ". \rex::getTablePrefix() ."d2u_jobs_jobs_lang AS lang "
				."ON jobs.job_id = lang.job_id AND lang.clang_id = ". $clang_id ." "
			."WHERE contact_id = ". $this->contact_id ." ";
		$query .= 'ORDER BY name ASC';
		$result = \rex_sql::factory();
		$result->setQuery($query);
		
		$jobs = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$jobs[] = new Job($result->getValue("job_id"), $clang_id);
			$result->next();
		}
		return $jobs;
	}
	
	/**
	 * Updates or inserts the object into database.
	 * @return in error code if error occurs
	 */
	public function save() {
		$error = FALSE;

		$pre_save_contact = new Contact($this->contact_id);

		if($this->contact_id == 0 || $pre_save_contact != $this) {
			$query = \rex::getTablePrefix() ."d2u_jobs_contacts SET "
					."email = '". $this->email ."', "
					."name = '". $this->name ."', "
					."phone = '". $this->phone ."', "
					."picture = '". (strpos($this->picture, "noavatar.jpg") !== FALSE ? '' : $this->picture) ."' ";

			if($this->contact_id == 0) {
				$query = "INSERT INTO ". $query;
			}
			else {
				$query = "UPDATE ". $query ." WHERE contact_id = ". $this->contact_id;
			}

			$result = \rex_sql::factory();
			$result->setQuery($query);
			if($this->contact_id == 0) {
				$this->contact_id = $result->getLastId();
				$error = $result->hasError();
			}
		}
		
		return $error;
	}
}