<?php
$readmePath = rex_path::addon('d2u_jobs', 'README.md');
$readmeContent = rex_file::get($readmePath);
if(null !== $readmeContent) {
    echo rex_markdown::factory()->parse($readmeContent);
}