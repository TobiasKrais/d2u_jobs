<?php
// Update language replacements
d2u_jobs_lang_helper::factory()->install();

// Update modules
if(class_exists(D2UModuleManager)) {
	$modules = [];
	$modules[] = new D2UModule("23-1",
		"D2U Jobs Addon - Hauptausgabe",
		1);

	$d2u_module_manager = new D2UModuleManager($modules, "", "d2u_jobs");
	$d2u_module_manager->autoupdate();
}

// Insert frontend translations
d2u_jobs_lang_helper::factory()->install();