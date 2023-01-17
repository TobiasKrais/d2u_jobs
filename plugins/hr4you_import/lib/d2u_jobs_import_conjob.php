<?php
/**
 * Administrates background import cronjob for HR4You.
 */
class d2u_jobs_import_conjob extends D2U_Helper\ACronJob {
	/**
	 * Create a new instance of object
	 * @return multinewsletter_cronjob_cleanup CronJob object
	 */
	public static function factory() {
		$cronjob = new self();
		$cronjob->name = "D2U Jobs Autoimport";
		return $cronjob;
	}
	
	/**
	 * Install CronJob. Its also activated.
	 */
	public function install():void {
		$description = 'Imports jobs automatically from HR4You XML';
		$php_code = '<?php hr4you::autoimport(); ?>';
		$interval = '{\"minutes\":[0],\"hours\":[21],\"days\":\"all\",\"weekdays\":\"all\",\"months\":\"all\"}';
		self::save($description, $php_code, $interval);
	}
}