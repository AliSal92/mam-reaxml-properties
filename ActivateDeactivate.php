<?php
/**
 * @package mam-reaxml-properties
 */

namespace Mam\SalesBoard\Base;


use MAM\Plugin\Services\Import\Import;

class ActivateDeactivate {

    /**
     * Flush Rewrite rules
     */
    public static function activate(){
        flush_rewrite_rules();
        Import::setup_cron_job();
    }

    /**
     * Flush Rewrite rules
     */
    public static function deactivate(){
        flush_rewrite_rules();
        Import::unset_cron_job();
    }

}