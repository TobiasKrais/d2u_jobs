<?php
/**
 * Class managing modules published by www.design-to-use.de
 *
 * @author Tobias Krais
 */
class D2UJobsModules {
	/**
	 * Get modules offered by this addon.
	 * @return D2UModule[] Modules offered by this addon
	 */
	public static function getModules() {
		$modules = [];
		$modules[] = new D2UModule("23-1",
			"D2U Jobs - Stellen",
			1);
		$modules[] = new D2UModule("23-2",
			"D2U Jobs - Kategorien",
			1);
		return $modules;
	}
}