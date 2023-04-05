<?php

namespace D2U_Jobs;

use rex;
use rex_sql;

/**
 * Job contact class.
 */
class Contact
{
    /** @var int Database ID */
    public $contact_id = 0;

    /** @var string Name */
    public $name = '';

    /** @var string Picture */
    public $picture = '';

    /** @var string Phone number */
    public $phone = '';

    /** @var string E-Mail address */
    public $email = '';

    /**
     * Constructor.
     * @param int $contact_id contact ID
     */
    public function __construct($contact_id = 0)
    {
        if ($contact_id > 0) {
            $query = 'SELECT * FROM '. rex::getTablePrefix() .'d2u_jobs_contacts '
                    .'WHERE contact_id = '. $contact_id;
            $result = rex_sql::factory();
            $result->setQuery($query);
            $num_rows = $result->getRows();

            if ($num_rows > 0) {
                $this->contact_id = $result->getValue('contact_id');
                $this->name = stripslashes($result->getValue('name'));
                $this->picture = $result->getValue('picture');
                $this->phone = $result->getValue('phone');
                $this->email = $result->getValue('email');
            }
        } else {
            return $this;
        }
    }

    /**
     * Deletes the object.
     */
    public function delete(): void
    {
        $query = 'DELETE FROM '. rex::getTablePrefix() .'d2u_jobs_contacts '
                .'WHERE contact_id = '. $this->contact_id;
        $result = rex_sql::factory();
        $result->setQuery($query);
    }

     /**
      * Create an empty object instance.
      * @return Contact empty new object
      */
     public static function factory()
     {
         return new self();
     }

    /**
     * Get all contacts.
     * @return Contact[] array with Contact objects
     */
    public static function getAll()
    {
        $query = 'SELECT contact_id FROM '. rex::getTablePrefix() .'d2u_jobs_contacts ORDER BY name';
        $result = rex_sql::factory();
        $result->setQuery($query);

        $contacts = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $contacts[$result->getValue('contact_id')] = new self((int) $result->getValue('contact_id'));
            $result->next();
        }
        return $contacts;
    }

    /**
     * Get contact by e-mail address.
     * @param string $email E-Mail address
     */
    public static function getByMail($email)
    {
        $query = 'SELECT contact_id FROM '. rex::getTablePrefix() .'d2u_jobs_contacts '
                ."WHERE email = '". $email ."'";
        $result = rex_sql::factory();
        $result->setQuery($query);
        $num_rows = $result->getRows();

        if ($num_rows > 0) {
            return new self((int) $result->getValue('contact_id'));
        }

        return false;

    }

    /**
     * Gets the jobs of the contact.
     * @return Job[] Jobs
     */
    public function getJobs()
    {
        $clang_id = (int) rex_config::get('d2u_helper', 'default_lang');
        $query = 'SELECT jobs.job_id FROM '. rex::getTablePrefix() .'d2u_jobs_jobs AS jobs '
            .'LEFT JOIN '. rex::getTablePrefix() .'d2u_jobs_jobs_lang AS lang '
                .'ON jobs.job_id = lang.job_id AND lang.clang_id = '. $clang_id .' '
            .'WHERE contact_id = '. $this->contact_id .' ';
        $query .= 'ORDER BY name ASC';
        $result = rex_sql::factory();
        $result->setQuery($query);

        $jobs = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $jobs[] = new Job($result->getValue('job_id'), $clang_id);
            $result->next();
        }
        return $jobs;
    }

    /**
     * Updates or inserts the object into database.
     * @return in error code if error occurs
     */
    public function save()
    {
        $error = false;

        $pre_save_contact = new self($this->contact_id);

        if (0 === $this->contact_id || $pre_save_contact != $this) {
            $query = rex::getTablePrefix() .'d2u_jobs_contacts SET '
                    ."email = '". $this->email ."', "
                    ."name = '". addslashes($this->name) ."', "
                    ."phone = '". $this->phone ."', "
                    ."picture = '". (str_contains($this->picture, 'noavatar.jpg') ? '' : $this->picture) ."' ";

            if (0 === $this->contact_id) {
                $query = 'INSERT INTO '. $query;
            } else {
                $query = 'UPDATE '. $query .' WHERE contact_id = '. $this->contact_id;
            }

            $result = rex_sql::factory();
            $result->setQuery($query);
            if (0 === $this->contact_id) {
                $this->contact_id = (int) $result->getLastId();
                $error = $result->hasError();
            }
        }

        return $error;
    }
}
