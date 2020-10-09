<div class="row">
	<div class="col-xs-4">
		Anzuzeigende Stellenkategorie
	</div>
	<div class="col-xs-8">
		<?php
			// Job Categories
			$select = new rex_select(); 
			$select->setName('VALUE[1]');
			$select->setAttribute('class', 'form-control');
			$select->setSize(1);

			// Daten
			$select->addOption("Alle", 0);
			foreach(D2U_Jobs\Category::getAll(rex_clang::getCurrentId()) as $category)  {
				$select->addOption($category->name, $category->category_id); 
			}
			$select->setSelected("REX_VALUE[1]");
			echo $select->show();
		?>
	</div>
</div>
<div class="row">
	<div class="col-xs-12">&nbsp;</div>
</div>
<div class="row">
	<div class="col-xs-4">
		<input type="checkbox" name="REX_INPUT_VALUE[2]" value="true" <?php echo "REX_VALUE[2]" == 'true' ? ' checked="checked"' : ''; ?> style="float: right;" />
	</div>
	<div class="col-xs-8">
		Allgemeiner Bewerbungshinweis unterhalb der Stellenanzeige verbergen.<br />
	</div>
</div>
<div class="row">
	<div class="col-xs-12">&nbsp;</div>
</div>
<div class="row">
	<div class="col-xs-4">
		<input type="checkbox" name="REX_INPUT_VALUE[3]" value="true" <?php echo "REX_VALUE[3]" == 'true' ? ' checked="checked"' : ''; ?> style="float: right;" />
	</div>
	<div class="col-xs-8">
		Stellen im JSON-LD Format ausgeben, damit Stellensuchmaschinen (z.B. Google Jobs) die Stelle anzeigen können.<br />
	</div>
</div>
<div class="row">
	<div class="col-xs-12">&nbsp;</div>
</div>
<div class="row">
	<div class="col-xs-12">
		Alle weiteren Änderungen bitte im <a href="index.php?page=d2u_jobs">D2U Stellenmarkt</a> Addon vornehmen.
	</div>
</div>