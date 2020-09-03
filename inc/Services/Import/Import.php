<?php


namespace MAM\Plugin\Services\Import;

use Exception;
use SimpleXMLElement;
use MAM\Plugin\Config;
use FtpClient\FtpClient;
use FtpClient\FtpException;
use MAM\Plugin\Services\WPAPI\Endpoint;
use MAM\Plugin\Services\ServiceInterface;

class Import implements ServiceInterface
{

    /**
     * @var Endpoint
     */
    private $endpoint_api;

    /**
     * @var FtpClient
     */
    private $ftp;

    /**
     * @var FtpClient
     */
    private $download_path;

    /**
     * @var array
     */
    private $errors;



    public function __construct()
    {
        $this->ftp = new FtpClient();
        $this->endpoint_api = new Endpoint();
        $this->download_path = Config::getInstance()->plugin_path . '/downloads';
    }

    /**
     * @inheritDoc
     */
    public function register()
    {
        add_action( 'admin_notices', array($this, 'show_admin_notice') );
        add_action('mam_reaxml_import', array($this, 'run'));
        add_action( 'init', array($this, 'setup_cron_job') );

        try {
            $this->endpoint_api->add_endpoint('mam-reaxml-import')->with_template('mam-reaxml-import.php')->register_endpoints();
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
        }
    }


    /**
     * Run importer
     */
    public function run(){
        if(Config::getInstance()->ftp_host && Config::getInstance()->ftp_username && Config::getInstance()->ftp_password){
            try {
                $this->ftp->connect(Config::getInstance()->ftp_host);
            } catch (FtpException $e) {
                $this->errors[] = $e->getMessage();
            }
            try {
                $this->ftp->login(Config::getInstance()->ftp_username, Config::getInstance()->ftp_password);
            } catch (FtpException $e) {
                $this->errors[] = $e->getMessage();
            }
        }

        // donwload all the files from the server
        $this->download_files();

        // get the properties list from the downloaded files
        try {
            $properties = $this->get_listing_array();
            foreach ($properties as $property){
                $the_property = new Property($property);
                $the_property->update();
            }
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
        }
    }

    /**
     * Show admin notices
     */
    public function show_admin_notice() {
        if(!empty($this->errors)){
            foreach($this->errors as $error){
        ?>
        <div class="notice error my-acf-notice is-dismissible" >
            <p><?php echo $error; ?></p>
        </div>
        <?php
            }
        }
    }

    /**
     * Download REAXML file from host
     * @return array $xml_files_list the list of xml files
     */
    private function download_files()
    {
        $files_list = $this->ftp->scanDir();

        $xml_files_list = [];

        foreach ($files_list as $file) {
            if (strpos($file['name'], '.xml') !== false &&
                strpos($file['name'], '4389') !== false &&
                !file_exists($this->download_path . '/' . $file['name'])) {
                $this->ftp->get($this->download_path . '/' . $file['name'], $file['name'], 1);
                $xml_files_list[] = $file;
            }
        }

        return $xml_files_list;
    }

    /**
     * Setup cronjob
     */
    public static function setup_cron_job()
    {
        //Use wp_next_scheduled to check if the event is already scheduled
        $timestamp = wp_next_scheduled( 'mam_reaxml_import' );

        //If $timestamp === false schedule daily backups since it hasn't been done previously
        if( $timestamp === false ){
            //Schedule the event for right now, then to repeat daily using the hook 'update_whatToMine_api'
            wp_schedule_event( time(), 'hourly', 'mam_reaxml_import' );
        }
    }

    /**
     * Get a list of all properties in the feed
     * @return SimpleXMLElement[] listing array
     * @throws Exception
     */
    private function get_listing_array()
    {
        $res = [];
        $xml_files = scandir($this->download_path);
        foreach ($xml_files as $file) {
            if (strpos($file, '.xml') !== false &&
                strpos($file, '4389') !== false) {
                $xmlData = simplexml_load_file($this->download_path . '/' . $file);
                if($xmlData){
                    foreach ($xmlData->rental as $rental) {
                        $rental['type'] = 'rental';
                        $rental['id'] = $rental->uniqueID;
                        $res[(string)$rental['id']] = $rental;
                    }
                    foreach ($xmlData->residential as $residential) {
                        $residential['type'] = 'residential';
                        $residential['id'] = $residential->uniqueID;
                        $res[(string)$residential['id']] = $residential;
                    }
                }else{
                    echo 'Could not load '. $this->download_path . '/' . $file;
                }

            }
        }
        return $res;
    }

}