<?php
/**
 * Administrates background import cronjob for HR4You.
 */
class d2u_jobs_import_conjob {
	/**
	 * @var string Name of CronJob
	 */
	 static $CRONJOB_NAME = "D2U Jobs Autoimport";
	
	/**
	 * Delete cron job.
	 */
	public static function delete() {
		if(\rex_addon::get('cronjob')->isAvailable()) {
			$query = "DELETE FROM `". rex::getTablePrefix() ."cronjob` WHERE `name` = '". self::$CRONJOB_NAME ."'";
			$sql = rex_sql::factory();
			$sql->setQuery($query);
		}
	}

	/**
	 * Install and activate cron job.
	 */
	public static function install() {
		if(\rex_addon::get('cronjob')->isAvailable()) {
			$query = "INSERT INTO `". rex::getTablePrefix() ."cronjob` (`name`, `description`, `type`, `parameters`, `interval`, `nexttime`, `environment`, `execution_moment`, `execution_start`, `status`, `createdate`, `createuser`) VALUES "
				."('". self::$CRONJOB_NAME ."', 'Imports jobs automatically from HR4You XML', 'rex_cronjob_phpcode', '{\"rex_cronjob_phpcode_code\":\"<?php hr4you::autoimport(); ?>\"}', '{\"minutes\":[0],\"hours\":[21],\"days\":\"all\",\"weekdays\":\"all\",\"months\":\"all\"}', '". date("Y-m-d H:i:s", strtotime("+5 min")) ."', '|frontend|backend|', 0, '1970-01-01 01:00:00', 1, '". date("Y-m-d H:i:s") ."', 'd2u_jobs');";
			$sql = rex_sql::factory();
			$sql->setQuery($query);
		}
	}

	/**
	 * Checks if  cron job is installed.
	 * @return boolean TRUE if Cronjob is installed, otherwise FALSE.
	 */
	public static function isInstalled() {
		if(\rex_addon::get('cronjob')->isAvailable()) {
			$query = "SELECT `name` FROM `". rex::getTablePrefix() ."cronjob` WHERE `name` = '". self::$CRONJOB_NAME ."'";
			$sql = rex_sql::factory();
			$sql->setQuery($query);
			if($sql->getRows() > 0) {
				return TRUE;
			}
			else {
				return FALSE;
			}
		}
	}
}