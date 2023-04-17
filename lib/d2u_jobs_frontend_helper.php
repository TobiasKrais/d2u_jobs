<?php

use D2U_Jobs\Category;
use D2U_Jobs\Job;

/**
 * @api
 * Offers helper functions for frontend.
 */
class d2u_jobs_frontend_helper
{
    /**
     * Returns alternate URLs. Key is Redaxo language id, value is URL.
     * @return string[] alternate URLs
     */
    public static function getAlternateURLs()
    {
        $alternate_URLs = [];

        // Prepare objects first for sorting in correct order
        $url_namespace = d2u_addon_frontend_helper::getUrlNamespace();
        $url_id = d2u_addon_frontend_helper::getUrlId();

        if (filter_input(INPUT_GET, 'job_id', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]) > 0 || 'job_id' === $url_namespace) {
            $job_id = (int) filter_input(INPUT_GET, 'job_id', FILTER_VALIDATE_INT);
            if (\rex_addon::get('url')->isAvailable() && $url_id > 0) {
                $job_id = $url_id;
            }
            foreach (rex_clang::getAllIds(true) as $this_lang_key) {
                $lang_job = new D2U_Jobs\Job($job_id, $this_lang_key);
                if ('delete' !== $lang_job->translation_needs_update) {
                    $alternate_URLs[$this_lang_key] = $lang_job->getUrl();
                }
            }
        } elseif (filter_input(INPUT_GET, 'job_category_id', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]) > 0 || 'job_category_id' === $url_namespace) {
            $category_id = (int) filter_input(INPUT_GET, 'job_category_id', FILTER_VALIDATE_INT);
            if (\rex_addon::get('url')->isAvailable() && $url_id > 0) {
                $category_id = $url_id;
            }
            foreach (rex_clang::getAllIds(true) as $this_lang_key) {
                $lang_category = new D2U_Jobs\Category($category_id, $this_lang_key);
                if ('delete' !== $lang_category->translation_needs_update) {
                    $alternate_URLs[$this_lang_key] = $lang_category->getUrl();
                }
            }
        }

        return $alternate_URLs;
    }

    /**
     * Returns breadcrumbs. Not from article path, but only part from this addon.
     * @return string[] Breadcrumb elements
     */
    public static function getBreadcrumbs()
    {
        $breadcrumbs = [];

        // Prepare objects first for sorting in correct order
        $url_namespace = d2u_addon_frontend_helper::getUrlNamespace();
        $url_id = d2u_addon_frontend_helper::getUrlId();

        $category = false;
        $job = false;
        if (filter_input(INPUT_GET, 'job_id', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]) > 0 || 'job_id' === $url_namespace) {
            $job_id = (int) filter_input(INPUT_GET, 'job_id', FILTER_VALIDATE_INT);
            if (\rex_addon::get('url')->isAvailable() && $url_id > 0) {
                $job_id = $url_id;
            }
            $target_clang = filter_input(INPUT_GET, 'target_clang', FILTER_VALIDATE_INT) > 0 ? filter_input(INPUT_GET, 'target_clang', FILTER_VALIDATE_INT) : rex_clang::getCurrentId();
            $job = new \D2U_Jobs\Job($job_id, $target_clang);
            foreach (array_keys($job->categories) as $job_category_id) {
                // Do not take the category object due to target_clang my differ
                $category = new D2U_Jobs\Category($job_category_id, rex_clang::getCurrentId());
                break;
            }
        } elseif (filter_input(INPUT_GET, 'job_category_id', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]) > 0 || 'job_category_id' === $url_namespace) {
            $category_id = (int) filter_input(INPUT_GET, 'job_category_id', FILTER_VALIDATE_INT);
            if (\rex_addon::get('url')->isAvailable() && $url_id > 0) {
                $category_id = $url_id;
            }
            $category = new D2U_Jobs\Category($category_id, rex_clang::getCurrentId());
        }

        // Breadcrumbs
        if ($category instanceof Category && $category->name !== '') {
            $breadcrumbs[] = '<a href="' . $category->getUrl() . '">' . $category->name . '</a>';
        }
        if ($job instanceof Job && $job->name !== '') {
            $job_url = $job->clang_id === rex_clang::getCurrentId() ? $job->getUrl() : rex_getUrl('', '', ['job_id' => $job->job_id, 'target_clang' => $job->clang_id]);
            $breadcrumbs[] = '<a href="' . $job_url . '">' . $job->name . '</a>';
        }

        return $breadcrumbs;
    }
}
