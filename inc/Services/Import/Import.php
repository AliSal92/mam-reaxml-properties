<?php


namespace MAM\Plugin\Services\Import;

use SimpleXMLElement;
use WP_Query;
use Exception;
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
        $this->endpoint_api->add_endpoint('mam-reaxml-import')->with_template('mam-reaxml-import.php')->register_endpoints();

        try {
            $this->ftp->connect(Config::getInstance()->ftp_host);
        } catch (FtpException $e) {
            echo $e->getMessage();
            die();
        }
        try {
            $this->ftp->login(Config::getInstance()->ftp_username, Config::getInstance()->ftp_password);
        } catch (FtpException $e) {
            echo $e->getMessage();
            die();
        }

    }


    /**
     * Run importer
     */
    public function run(){
        // donwload all the files from the server
        $this->download_files();

        // unpublish all properpies
        $this->unpublish_properties();

        // get the properties list from the downloaded files
        $properties = $this->get_listings_array();

        // import and publish all the properties
        foreach ($properties as $property){
            if($this->property_exists((string)$property['id'])){
                $this->update_property($property);
            }else{
                $this->add_property($property);
            }
        }
    }

    /**
     * Download REAXML file from host
     */
    public function download_files()
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
     * Get a list of all properties in the feed
     * @return SimpleXMLElement[] listing array
     * @throws Exception
     */
    public function get_listings_array()
    {
        $res = [];
        $xml_files = scandir($this->download_path);
        foreach ($xml_files as $file) {
            if (strpos($file, '.xml') !== false &&
                strpos($file, '4389') !== false) {
                $xmlData = simplexml_load_file($this->download_path . '/' . $file);
                foreach ($xmlData->rental as $rental) {
                    if ((string)$rental['status'] != 'current') {
                        continue;
                    }
                    $rental['type'] = 'rental';
                    $rental['id'] = $rental->uniqueID;
                    $res[(string)$rental['id']] = $rental;
                }
                foreach ($xmlData->residential as $residential) {
                    if ((string)$residential['status'] != 'current') {
                        continue;
                    }
                    $residential['type'] = 'residential';
                    $residential['id'] = $residential->uniqueID;
                    $res[(string)$residential['id']] = $residential;
                }

            }
        }
        return $res;
    }


    /**
     * Check if property id exists
     *
     * @param $property_id string the property id to check
     *
     * @return bool true if the property exists
     */
    public function property_exists($property_id)
    {
        $meta_query = [];

        if ($property_id != '') {
            $meta_query[] = [
                'key' => 'uniqueID',
                'value' => $property_id,
                'compare' => '='
            ];
        } else {
            return false;
        }

        // args
        $args = array(
            'numberposts' => -1,
            'post_type' => 'property',
            'meta_query' => $meta_query
        );

        $query = new WP_Query($args);
        return $query->have_posts();
    }


    /**
     * Get the property post id by propert id
     *
     * @param $property_id string the property id to check
     *
     * @return int|bool the property id or false
     */
    public function property_post_id($property_id)
    {

        wp_reset_query();

        $meta_query = [];

        if ($property_id != '') {
            $meta_query[] = [
                'key' => 'uniqueID',
                'value' => $property_id,
                'compare' => '='
            ];
        } else {
            return false;
        }

        // args
        $args = array(
            'numberposts' => -1,
            'post_type' => 'property',
            'meta_query' => $meta_query
        );

        $query = new WP_Query($args);
        while ($query->have_posts()) {
            $query->the_post();
            return get_the_ID();
        }
        return false;
    }


    /**
     * Unpublish all properties
     *
     * @return bool true on success
     */
    public function unpublish_properties()
    {
        wp_reset_query();
        // args
        $args = array(
            'numberposts' => -1,
            'post_type' => 'property'
        );

        $_query = new WP_Query($args);
        while ($_query->have_posts()) {
            $_query->the_post();
            global $post;
            $post->post_status = 'private';
            wp_update_post($post);
        }
        return true;
    }

    /**
     * Update property custom fields
     *
     * @param $property SimpleXMLElement the property to update
     * @param $post_id int existing post id
     *
     * @return bool true if the property updated
     */
    public function update_property($property, $post_id = null)
    {
        $property_id = (string)$property['id'];

        if(!$post_id){
            $post_id = $this->property_post_id($property_id);
        }

        wp_reset_query();

        $meta_query = [];
        if ($property_id != '') {
            $meta_query[] = [
                'key' => 'uniqueID',
                'value' => $property_id,
                'compare' => '='
            ];
        }
        // args
        $args = array(
            'numberposts' => -1,
            'post_type' => 'property',
            'meta_query' => $meta_query
        );
        $query = new WP_Query($args);
        while ($query->have_posts()) {
            $query->the_post();
            global $post;
            $post->post_status = 'publish';
            wp_update_post($post);
        }

        if (isset($property['type']) && (string)$property['type'] == 'rental') {
            update_field('rent_period_week', (string)$property->children()->rent[0], $post_id);
            update_field('rent_period_monthly', (string)$property->children()->rent[1], $post_id);
            update_field('bond', (string)$property->bond, $post_id);
            update_field('dateAvailable', (string)$property->children()->dateAvailable, $post_id);
        }
        update_field('agentID', (string)$property->agentID, $post_id);
        update_field('uniqueID', (string)$property->uniqueID, $post_id);
        update_field('type', (string)$property['type'], $post_id);
        update_field('status', (string)$property['status'], $post_id);
        update_field('authority', (string)$property->authority['value'], $post_id);
        update_field('underOffer', (string)$property->underOffer['value'], $post_id);
        update_field('isHomeLandPackage', (string)$property->isHomeLandPackage['value'], $post_id);
        update_field('priceView', (string)$property->priceView, $post_id);
        update_field('address', (string)$property->address['display'], $post_id);
        update_field('site', (string)$property->address->site, $post_id);
        update_field('subnumber', (string)$property->address->subNumber, $post_id);
        update_field('lotnumber', (string)$property->address->lotNumber, $post_id);
        update_field('streetnumber', (string)$property->address->streetNumber, $post_id);
        update_field('street', (string)$property->address->street, $post_id);
        update_field('suburb', (string)$property->address->suburb, $post_id);
        update_field('state', (string)$property->address->state, $post_id);
        update_field('postcode', (string)$property->address->postcode, $post_id);
        update_field('country', (string)$property->address->country, $post_id);
        update_field('category', (string)$property->category['name'], $post_id);
        update_field('headline', (string)$property->headline, $post_id);
        update_field('description', (string)$property->description, $post_id);
        update_field('landDetails', (string)$property->landDetails->area, $post_id);
        update_field('buildingDetails', (string)$property->buildingDetails->area, $post_id);

        $inspections = [];
        foreach ($property->inspectionTimes->inspection as $inspectionTime) {
            if (trim((string)$inspectionTime)) {
                $inspection = [
                    'option' => (string)$inspectionTime
                ];
                $inspections[] = $inspection;
            }
        }
        delete_post_meta($post_id, 'inspectiontimes');
        foreach ($inspections as $inspection) {
            add_row('inspectiontimes', $inspection);
        }

        $externalLinks = [];
        if ((string)$property->videoLink['href']) {
            $externalLink = [
                'text' => 'Video',
                'link' => (string)$property->videoLink['href']
            ];
            $externalLinks[] = $externalLink;
        }
        if ((string)$property->objects->floorplan['url']) {
            $externalLink = [
                'text' => 'Floorplan',
                'link' => (string)$property->objects->floorplan['url']
            ];
            $externalLinks[] = $externalLink;
        }

        delete_post_meta($post_id, 'externallink');
        foreach ($externalLinks as $externalLink) {
            add_row('externallink', $externalLink);
        }
        update_field('videolink', (string)$property->videoLink['href'], $post_id);


        $images = [];
        foreach ($property->images[0] as $image) {
            if ((string)$image['url']) {
                $image = [
                    'image' => (string)$image['url']
                ];
                $images[] = $image;
            }
        }

        delete_post_meta($post_id, 'images');
        foreach ($images as $image) {
            add_row('images', $image);
        }

        update_field('newconstruction', (string)$property->newConstruction, $post_id);
        update_field('floorplan', (string)$property->objects->floorplan['url'], $post_id);
        update_field('miniweb', (string)$property->miniweb->uri, $post_id);
        update_field('last_updated', date('Y-m-d H:i:s'), $post_id);
        update_field('original_data', $property->asXML(), $post_id);
        update_field('bed', (string)$property->features->bedrooms, $post_id);
        update_field('car', (string)$property->features->garages, $post_id);
        update_field('bath', (string)$property->features->bathrooms, $post_id);


        $features = $this->get_features_array($property->features);
        delete_post_meta($post_id, 'features');
        foreach ($features as $feature) {
            add_row('features', $feature);
        }
        return $post_id;
    }

    /**
     * Return array of readable features
     * @param $features Properties Features from REAXML
     * @return array list of features
     */
    private function get_features_array($features)
    {
        $_features = [];

        if ($features->splitSystemAirCon != '0') {
            $_feature = [
                'feature' => 'Split System AirCon'
            ];
            $_features[] = $_feature;
        }
        if ($features->shed != '0') {
            $_feature = [
                'feature' => 'Shed'
            ];
            $_features[] = $_feature;
        }
        if ($features->workshop != '0') {
            $_feature = [
                'feature' => 'Workshop'
            ];
            $_features[] = $_feature;
        }
        if ($features->insideSpa != '0') {
            $_feature = [
                'feature' => 'Inside Spa'
            ];
            $_features[] = $_feature;
        }
        if ($features->tennisCourt != '0') {
            $_feature = [
                'feature' => 'Tennis Court'
            ];
            $_features[] = $_feature;
        }
        if ($features->balcony != '0') {
            $_feature = [
                'feature' => 'Balcony'
            ];
            $_features[] = $_feature;
        }
        if ($features->ductedHeating != '0') {
            $_feature = [
                'feature' => 'Ducted Heating'
            ];
            $_features[] = $_feature;
        }
        if ($features->openSpaces != '0') {
            $_feature = [
                'feature' => 'Open Spaces'
            ];
            $_features[] = $_feature;
        }
        if ($features->hydronicHeating != '0') {
            $_feature = [
                'feature' => 'Hydronic Heating'
            ];
            $_features[] = $_feature;
        }
        if ($features->courtyard != '0') {
            $_feature = [
                'feature' => 'Courtyard'
            ];
            $_features[] = $_feature;
        }
        if ($features->reverseCycleAirCon != '0') {
            $_feature = [
                'feature' => 'Reverse Cycle AirCon'
            ];
            $_features[] = $_feature;
        }
        if ($features->builtInRobes != '0') {
            $_feature = [
                'feature' => 'BuiltIn Robes'
            ];
            $_features[] = $_feature;
        }
        if ($features->broadband != '0') {
            $_feature = [
                'feature' => 'Broadband'
            ];
            $_features[] = $_feature;
        }
        if ($features->rumpusRoom != '0') {
            $_feature = [
                'feature' => 'Rumpus Room'
            ];
            $_features[] = $_feature;
        }
        if ($features->bathrooms != '0') {
            $_feature = [
                'feature' => 'Bathrooms'
            ];
            $_features[] = $_feature;
        }
        if ($features->evaporativeCooling != '0') {
            $_feature = [
                'feature' => 'Evaporative Cooling'
            ];
            $_features[] = $_feature;
        }
        if ($features->outdoorEnt != '0') {
            $_feature = [
                'feature' => 'Outdoor Ent'
            ];
            $_features[] = $_feature;
        }
        if ($features->garages != '0') {
            $_feature = [
                'feature' => 'Garages'
            ];
            $_features[] = $_feature;
        }
        if ($features->dishwasher != '0') {
            $_feature = [
                'feature' => 'Dishwasher'
            ];
            $_features[] = $_feature;
        }
        if ($features->payTV != '0') {
            $_feature = [
                'feature' => 'Pay TV'
            ];
            $_features[] = $_feature;
        }
        if ($features->heating != '0') {
            $_feature = [
                'feature' => 'Heating'
            ];
            $_features[] = $_feature;
        }
        if ($features->fullyFenced != '0') {
            $_feature = [
                'feature' => 'Fully Fenced'
            ];
            $_features[] = $_feature;
        }
        if ($features->poolAboveGround != '0') {
            $_feature = [
                'feature' => 'Pool Above Ground'
            ];
            $_features[] = $_feature;
        }
        if ($features->vacuumSystem != '0') {
            $_feature = [
                'feature' => 'Vacuum System'
            ];
            $_features[] = $_feature;
        }
        if ($features->remoteGarage != '0') {
            $_feature = [
                'feature' => 'Remote Garage'
            ];
            $_features[] = $_feature;
        }
        if ($features->toilets != '0') {
            $_feature = [
                'feature' => 'Toilets'
            ];
            $_features[] = $_feature;
        }
        if ($features->gasHeating != '0') {
            $_feature = [
                'feature' => 'Gas Heating'
            ];
            $_features[] = $_feature;
        }
        if ($features->livingAreas != '0') {
            $_feature = [
                'feature' => 'living Areas'
            ];
            $_features[] = $_feature;
        }
        if ($features->deck != '0') {
            $_feature = [
                'feature' => 'Deck'
            ];
            $_features[] = $_feature;
        }
        if ($features->study != '0') {
            $_feature = [
                'feature' => 'Study'
            ];
            $_features[] = $_feature;
        }
        if ($features->intercom != '0') {
            $_feature = [
                'feature' => 'Intercom'
            ];
            $_features[] = $_feature;
        }
        if ($features->alarmSystem != '0') {
            $_feature = [
                'feature' => 'Alarm System'
            ];
            $_features[] = $_feature;
        }
        if ($features->poolInGround != '0') {
            $_feature = [
                'feature' => 'Pool In Ground'
            ];
            $_features[] = $_feature;
        }
        if ($features->openFirePlace != '0') {
            $_feature = [
                'feature' => 'Open Fire Place'
            ];
            $_features[] = $_feature;
        }
        if ($features->splitSystemHeating != '0') {
            $_feature = [
                'feature' => 'Split Syste mHeating'
            ];
            $_features[] = $_feature;
        }
        if ($features->carports != '0') {
            $_feature = [
                'feature' => 'Carports'
            ];
            $_features[] = $_feature;
        }
        if ($features->floorboards != '0') {
            $_feature = [
                'feature' => 'Floor Boards'
            ];
            $_features[] = $_feature;
        }
        if ($features->outsideSpa != '0') {
            $_feature = [
                'feature' => 'Outside Spa'
            ];
            $_features[] = $_feature;
        }
        if ($features->ensuite != '0') {
            $_feature = [
                'feature' => 'Ensuite'
            ];
            $_features[] = $_feature;
        }
        if ($features->ductedCooling != '0') {
            $_feature = [
                'feature' => 'Ducted Cooling'
            ];
            $_features[] = $_feature;
        }
        if ($features->bedrooms != '0') {
            $_feature = [
                'feature' => 'Bedrooms'
            ];
            $_features[] = $_feature;
        }
        if ($features->gym != '0') {
            $_feature = [
                'feature' => 'Gym'
            ];
            $_features[] = $_feature;
        }
        if ($features->airConditioning != '0') {
            $_feature = [
                'feature' => 'Air Conditioning'
            ];
            $_features[] = $_feature;
        }
        if ($features->secureParking != '0') {
            $_feature = [
                'feature' => 'Secure Parking'
            ];
            $_features[] = $_feature;
        }
        if ($features->otherFeatures != '0') {
            $_feature = [
                'feature' => (string)$features->otherFeatures
            ];
            $_features[] = $_feature;
        }

        return $_features;
    }


    /**
     * Add property and it's custom fields
     *
     * @param $property SimpleXMLElement the property to add
     *
     * @return bool true if the property updated
     */
    public function add_property($property)
    {
        $id = wp_insert_post(array(
            'post_title'=> (string)$property->address->streetNumber . ' ' . (string)$property->address->street . ' ' . (string)$property->address->suburb,
            'post_type'=>'property',
            'post_status' => 'publish',
        ));
        return $this->update_property($property, $id);
    }

}