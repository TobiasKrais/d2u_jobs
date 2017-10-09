<?php
/**
 * Class managing modules published by www.design-to-use.de
 *
 * @author Tobias Krais
 */
class D2UNewsModules {
	/**
	 * Get modules offered by this addon.
	 * @return D2UModule[] Modules offered by this addon
	 */
	public static function getModules() {
		$modules = [];
		$modules[] = new D2UModule("40-1",
			"D2U News - Ausgabe News",
			1);
		$modules[] = new D2UModule("40-2",
			"D2U News - Ausgabe Messen",
			1);
		$modules[] = new D2UModule("40-3",
			"D2U News - Ausgabe News und Messen",
			1);
		return $modules;
	}
}