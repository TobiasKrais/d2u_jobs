<?php

if (rex::isBackend() && is_object(rex::getUser())) {
    rex_perm::register('d2u_jobs[hr4you]', rex_i18n::msg('d2u_jobs_hr4you_rights'));
}
