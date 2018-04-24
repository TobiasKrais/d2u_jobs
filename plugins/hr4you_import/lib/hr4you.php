<?php
/**
 * Class managing all HR4You stuff
 */
class hr4you {
	/**
	 * Perform HR4You XML import, calls import()
	 */
	public static function autoimport() {
		// Include mediapool functions when call is frontend call
		if (!rex::isBackend()) {
			require_once __DIR__ . '/../../../../mediapool/functions/function_rex_mediapool.php';
		}

		if(self::import()) {
			print \rex_view::success(\rex_i18n::msg('d2u_jobs_hr4you_import_success'));
		}
	}

	/**
	 * Perform HR4You XML import
	 * @return boolean TRUE if successfull
	 */
	public static function import() {
		$hr4you_xml_url = \rex_config::get('d2u_jobs', 'hr4you_xml_url', FALSE);
		if($hr4you_xml_url === FALSE) {
			print \rex_view::error(\rex_i18n::msg('d2u_jobs_hr4you_settings_failure_xml_url'));
			return FALSE;
		}
		
		$xml_stream = stream_context_create(['http' => ['header' => 'Accept: application/xml']]);
		$xml_contents = file_get_contents($hr4you_xml_url, FALSE, $xml_stream);
		if($xml_contents === FALSE) {
			print \rex_view::error(\rex_i18n::msg('d2u_jobs_hr4you_import_failure_xml_url'));
			return FALSE;
		}
		$xml_jobs = new SimpleXMLElement($xml_contents);

		self::log('***** Starting Import *****');
		// Get old stuff to be able to delete it later
		$old_jobs = \D2U_Jobs\Job::getAllHR4YouJobs();
		$old_contacts = []; // Get them later from Jobs
		$old_pictures = [];
		foreach($old_jobs as $old_job) {
			// Pictures
			if(!in_array($old_job->picture, $old_pictures)) {
				$old_pictures[$old_job->picture] = $old_job->picture;
			}
			// D2U_Jobs\Contacts
			if($old_job->contact !== FALSE && !array_key_exists($old_job->contact->contact_id, $old_contacts)) {
				$old_contacts[$old_job->contact->contact_id] = $old_job->contact;
				if(!in_array($old_job->contact->picture, $old_pictures)) {
					$old_pictures[$old_job->contact->picture] = $old_job->contact->picture;
				}
			}
		}
		
		// Get new jobs
		foreach ($xml_jobs->entry as $xml_job) {
			// Import pictures
			$job_picture_filename = "";
			if($xml_job->kopfgrafik_url != "") {
				$job_picture_pathInfo = pathinfo($xml_job->kopfgrafik_url);
				$job_picture_filename = self::getMediapoolFilename($job_picture_pathInfo['basename']);
				$job_picture = \rex_media::get($job_picture_filename);
				if($job_picture instanceof \rex_media && $job_picture->fileExists()) {
					// File already imported, unset in $old_pictures, because remaining ones will be deleted
					if(in_array($job_picture->getFileName(), $old_pictures)) {
						unset($old_pictures[$job_picture->getFileName()]);
					}
					self::log('Job picture '. $job_picture_filename .' already available in mediapool.');
				}
				else {
					// File exists only in database, but no more physically: remove it before import
					if($job_picture instanceof \rex_media) {
						try {
							\rex_mediapool_deleteMedia($job_picture->getFileName());
						}
						catch(Exception $e) {
							self::log('Picture physically not found. Error deleting media from mediapool database.');
						}
					}
					
					// Import
					$target_picture = \rex_path::media($job_picture_pathInfo['basename']);
					// Copy first
					if(copy($xml_job->kopfgrafik_url, $target_picture)) {
						chmod($target_picture, octdec(664));
// Use next line instead the one after as soon as https://github.com/redaxo/redaxo/issues/1614 is fixed
//						$sync_file_infos = \rex_mediapool_syncFile($job_picture_pathInfo['basename'], \rex_config::get('d2u_jobs', 'hr4you_media_category'), $xml_jobs->titel);
						$sync_file_infos = self::importMedia($job_picture_pathInfo['basename'], \rex_config::get('d2u_jobs', 'hr4you_media_category'), $xml_jobs->titel);
						$job_picture_filename = $sync_file_infos['filename'];
						self::log('Job picture '. $job_picture_filename .' importet into database.');
					}
				}
			}

			// Import contact
			$contact = \D2U_Jobs\Contact::getByMail($xml_job->ap_email);
			if($contact === FALSE) {
				$contact = \D2U_Jobs\Contact::factory();
				self::log('Contact '. $contact->name .' added.');
			}
			else {
				self::log('Contact '. $contact->name .' already exists.');
			}
			$contact->name = $xml_job->ap_vorname . ' ' . $xml_job->ap_nachname;
			if($xml_job->ap_telefon->__toString() != '') {
				$contact->phone = $xml_job->ap_telefon->__toString();
			}
			if($xml_job->ap_email->__toString() != '') {
				$contact->email = $xml_job->ap_email->__toString();
			}
			$contact->save();
			if(array_key_exists($contact->contact_id, $old_contacts)) {
				unset($old_contacts[$contact->contact_id]);
			}
			if($contact->picture != "" && in_array($contact->picture, $old_pictures)) {
				unset($old_pictures[$contact->picture]);
			}
			
			// Category
			$category = \D2U_Jobs\Category::getByHR4YouID($xml_job->berufskategorie_id->__toString());
			if($category === FALSE) {
				self::log('Category with HR4You ID '. $xml_job->berufskategorie_id->__toString() .' does not exist. Falback to default category.');
				$category = new \D2U_Jobs\Category(\rex_config::get('d2u_jobs', 'hr4you_default_category'), \rex_config::get('d2u_jobs', 'hr4you_default_lang'));
			}

			// Import job
			$job = \D2U_Jobs\Job::getByHR4YouID($xml_job->jobid->__toString());
			if($job == FALSE) {
				$job = \D2U_Jobs\Job::factory();
				$job->clang_id = \rex_config::get('d2u_jobs', 'hr4you_default_lang');
				$job->hr4you_job_id = $xml_job->jobid->__toString();
			}

			foreach(\rex_clang::getAll() as $clang) {
				if($clang->getCode() == $xml_job->sprachcode->__toString()) {
					$job->clang_id = $clang->getId();
					break;
				}
			}

			$job->contact = $contact;
			if(!in_array($category, $job->categories)) {
				$job->categories[$category->category_id] = $category;
			}

			$job->city = $xml_job->arbeitsort->__toString();
			$job->date = $xml_job->von_datum->__toString();
			$job->hr4you_lead_in = $xml_job->einleitung->__toString();
			$job->hr4you_url_application_form = $xml_job->url_application_form->__toString();
			$job->name = $xml_job->titel->__toString();
			$job->offer_heading = self::getHeadline($xml_job->block3_html) != '' ? self::getHeadline($xml_job->block3_html) : \Sprog\Wildcard::get('d2u_jobs_hr4you_offer_heading', \rex_config::get('d2u_jobs', 'hr4you_default_lang'));
			$job->offer_text = self::trimString(self::stripHeadline($xml_job->block3_html));
			$job->online_status = "online";
			if($job_picture_filename != "") {
				$job->picture = $job_picture_filename;
			}
			$job->profile_heading = self::getHeadline($xml_job->block2_html) != '' ? self::getHeadline($xml_job->block2_html) : \Sprog\Wildcard::get('d2u_jobs_hr4you_profile_heading', \rex_config::get('d2u_jobs', 'hr4you_default_lang'));
			$job->profile_text = self::trimString(self::stripHeadline($xml_job->block2_html));
			$job->reference_number = $xml_job->referenznummer->__toString();
			$job->tasks_heading = self::getHeadline($xml_job->block1_html) != '' ? self::getHeadline($xml_job->block1_html) : \Sprog\Wildcard::get('d2u_jobs_hr4you_tasks_heading', \rex_config::get('d2u_jobs', 'hr4you_default_lang'));
			$job->tasks_text = self::trimString(self::stripHeadline($xml_job->block1_html));
			$job->translation_needs_update = 'no';
			$job->save();

			if(array_key_exists($job->hr4you_job_id, $old_jobs)) {
				unset($old_jobs[$job->hr4you_job_id]);
				self::log('Job '. $job->name .' already exists. Updated.');
			}
			else {
				self::log('Job '. $job->name .' added.');
			}
		}

		// Delete unused old jobs
		foreach ($old_jobs as $old_job) {
			$old_job->delete(TRUE);
			self::log('Job '. $old_job->name .' deleted.');
		}
		
		// Delete unused old contacts
		foreach ($old_contacts as $old_contact) {
			$old_contact->delete();
			self::log('Contact '. $old_contact->name .' deleted.');
		}

		// Delete unused old pictures
		foreach($old_pictures as $old_picture) {
			$delete_result = \rex_mediapool_deleteMedia($old_picture);
			if($delete_result['ok'] === FALSE) {
				// File seems to be in use
				self::log('Picture '. $old_picture .' deletion requested, but is in use.');
			}
			else {
				self::log('Picture '. $old_picture .' deleted.');
			}
		}
		
		return TRUE;
	}

	/**
	 * Isolates headline from text
	 * @param string $string String potenially containing headline
	 * @return string headline text without tags
	 */
	private static function getHeadline($string) {
		if ($string == '') {
			return '';
		}

		$doc = new DOMDocument();
		$doc->loadHTML($string);

		foreach($doc->getElementsByTagName(\rex_config::get('d2u_jobs', 'hr4you_headline_tag')) as $item) {
			return utf8_decode($item->textContent);
		}

		return '';
	}

	/**
	 * Get mediapool new filename by old filename
	 * @param string $old_filename Old media filename before import into mediapool
	 * @return string filename used in mediapool, if not found, empty string is returned
	 */
	private static function getMediapoolFilename($old_filename) {
		$query = "SELECT filename FROM `". \rex::getTablePrefix() ."media` "
			. "WHERE originalname = '". $old_filename ."'";
		$result = \rex_sql::factory();
		$result->setQuery($query);

		if($result->getRows() > 0) {
			return $result->getValue("filename");
		}
		
		return "";
	}
	
	/**
	 * Removes headline from text
	 * @param string $string String with text potentially containing headline
	 * @return string text without headline
	 */
	private static function stripHeadline($string) {
		$headline = self::getHeadline($string);

		$h_tag = \rex_config::get('d2u_jobs', 'hr4you_headline_tag');
		return str_replace('<' . $h_tag . '>' . $headline . '</' . $h_tag . '>', '', $string);
	}

	/**
	 * Synchronisiert die Datei $physical_filename des Mediafolders in den
	 * Medienpool.
	 *
	 * @param string $physical_filename
	 * @param int    $rex_file_category
	 * @param string $title
	 *
	 * @return bool|array
	 */
	private static function importMedia($physical_filename, $rex_file_category, $title) {
		$abs_file = \rex_path::media($physical_filename);

		if (!file_exists($abs_file)) {
			return FALSE;
		}

		$filesize = filesize($abs_file);
		$filetype = "";
		if (function_exists('mime_content_type')) {
			$filetype = mime_content_type($abs_file);
		}
		else if (function_exists('finfo_open')) {
			$finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
			$filetype = finfo_file($finfo, $abs_file);
		}

		$FILE = [];
		$FILE['name'] = $physical_filename;
		$FILE['size'] = $filesize;
		$FILE['type'] = $filetype;

		$FILEINFOS = [];
		$FILEINFOS['title'] = $title;

		// Check file category exists
		$gc = rex_sql::factory();
		$gc->setQuery('SELECT * FROM ' . \rex::getTablePrefix() . 'media_category WHERE id=' . $rex_file_category);
		if ($gc->getRows() != 1) {
			$rex_file_category = 0;
		}

		$FILENAME = $FILE['name'];
		$FILESIZE = $FILE['size'];
		$FILETYPE = $FILE['type'];
		$NFILENAME = \rex_mediapool_filename($FILENAME, TRUE);
		$message = [];

		// ----- alter/neuer filename
		$srcFile = \rex_path::media($FILENAME);
		$dstFile = \rex_path::media($NFILENAME);

		$success = true;
		// Filesync
		if (!@rename($srcFile, $dstFile)) {
			$message[] = \rex_i18n::msg('pool_file_movefailed');
			$success = false;
		}

		if ($success) {
			@chmod($dstFile, \rex::getFilePerm());

			// get width height
			$size = @getimagesize($dstFile);

			if ($FILETYPE == '' && isset($size['mime'])) {
				$FILETYPE = $size['mime'];
			}

			$FILESQL = \rex_sql::factory();
			$FILESQL->setTable(\rex::getTablePrefix() . 'media');
			$FILESQL->setValue('filetype', $FILETYPE);
			$FILESQL->setValue('title', $FILEINFOS['title']);
			$FILESQL->setValue('filename', $NFILENAME);
			$FILESQL->setValue('originalname', $FILENAME);
			$FILESQL->setValue('filesize', $FILESIZE);

			if ($size) {
				$FILESQL->setValue('width', $size[0]);
				$FILESQL->setValue('height', $size[1]);
			}

			$FILESQL->setValue('category_id', $rex_file_category);
			$FILESQL->setDateTimeValue('createdate', time());
			$FILESQL->setDateTimeValue('createuser', null);
			$FILESQL->setDateTimeValue('updatedate', time());
			$FILESQL->setDateTimeValue('updateuser', null);
			$FILESQL->insert();

			if ($NFILENAME != $FILENAME) {
				$message[] = \rex_i18n::rawMsg('pool_file_renamed', $FILENAME, $NFILENAME);
			}

			\rex_media_cache::deleteList($rex_file_category);
		}

		$RETURN['title'] = $FILEINFOS['title'];
		$RETURN['type'] = $FILETYPE;
		$RETURN['msg'] = implode('<br />', $message);
		// Aus BC gruenden hier mit int 1/0
		$RETURN['ok'] = $success ? 1 : 0;
		$RETURN['filename'] = $NFILENAME;
		$RETURN['old_filename'] = $FILENAME;

		if (isset($size)) {
			$RETURN['width'] = $size[0];
			$RETURN['height'] = $size[1];
		}

		// ----- EXTENSION POINT
		if ($success) {
			\rex_extension::registerPoint(new \rex_extension_point('MEDIA_ADDED', '', $RETURN));
		}

		return $RETURN;
	}
	
	/**
	 * Removes not allowed tags and other stuff from string.
	 * @param string $string String to be prepared
	 * @return string Prepared string
	 */
	private static function trimString($string) {
		$string = strip_tags($string, '<ul></ul><b></b><i></i><strong></strong><li></li><br><br /><p></p>');
		$string = trim(preg_replace('/\t+/', '', $string));
		$string = str_replace(['&nbsp;', '&crarr;'], ' ', $string);
		$string = preg_replace("/\s+/", " ", $string); 
		return str_replace(["\r", "\n"], '', $string);
	}
	
	/**
	 * Logs message
	 * @param string $message Message to be logged
	 */
	private static function log($message) {
		$log = file_exists(rex_path::addonCache('d2u_jobs', 'hr4you_import_log.txt')) ? file_get_contents(rex_path::addonCache('d2u_jobs', 'hr4you_import_log.txt')) : "";
		
		$log .= PHP_EOL. date('d.m.Y H:i:s', time()) .": ". $message;

		// Write to log
		if(!is_dir(rex_path::addonCache('d2u_jobs'))) {
			mkdir(rex_path::addonCache('d2u_jobs'), 0755, TRUE);
		}
		file_put_contents(rex_path::addonCache('d2u_jobs', 'hr4you_import_log.txt'), $log);
	}
}