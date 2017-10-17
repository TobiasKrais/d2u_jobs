<?php
// Job Categories
print 'Anzuzeigende Stellenkategorie auswählen: ';
$select = new rex_select(); 
$select->setName('VALUE[1]');
$select->setSize(1);

// Daten
$select->addOption("Alle", 0);
foreach(D2U_Jobs\Category::getAll(rex_clang::getCurrentId()) as $category)  {
  $select->addOption($category->name, $category->category_id); 
}
$select->setSelected("REX_VALUE[1]");
echo $select->show();
print "<br>";
?>

<p><br />Alle weiteren Änderungen bitte im <a href="index.php?page=d2u_jobs">D2U Stellenmarkt</a> Addon vornehmen.</p>