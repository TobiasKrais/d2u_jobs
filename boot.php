<?php

if (rex::isBackend() && is_object(rex::getUser())) {
    rex_perm::register('d2u_jobs[]', rex_i18n::msg('d2u_jobs_rights_all'));
}