<?php

// remove module specific media manager type
$sql = rex_sql::factory();
$sql->setQuery('DELETE FROM '. \rex::getTablePrefix() ."yform_email_template WHERE name LIKE 'd2u_jobs_%'");
