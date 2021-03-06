<?php


namespace MAM\Plugin\Services\Import;


use WP_Query;
use SimpleXMLElement;

class Property
{

    /**
     * @var int
     */
    public $property_id;

    /**
     * @var string
     */
    public $rent_period_week;

    /**
     * @var string
     */
    public $rent_period_monthly;

    /**
     * @var int
     */
    public $bond;

    /**
     * @var string
     */
    public $dateAvailable;

    /**
     * @var int
     */
    public $agentID;

    /**
     * @var int
     */
    public $uniqueID;

    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $status;

    /**
     * @var string
     */
    public $publishDate;

    /**
     * @var string
     */
    public $authority;

    /**
     * @var string
     */
    public $underOffer;

    /**
     * @var string
     */
    public $isHomeLandPackage;

    /**
     * @var string
     */
    public $priceView;

    /**
     * @var string
     */
    public $address;

    /**
     * @var string
     */
    public $site;

    /**
     * @var string
     */
    public $subnumber;

    /**
     * @var string
     */
    public $lotnumber;

    /**
     * @var string
     */
    public $streetnumber;

    /**
     * @var string
     */
    public $street;

    /**
     * @var string
     */
    public $suburb;

    /**
     * @var string
     */
    public $state;

    /**
     * @var string
     */
    public $postcode;

    /**
     * @var string
     */
    public $country;

    /**
     * @var string
     */
    public $category;

    /**
     * @var string
     */
    public $headline;

    /**
     * @var string
     */
    public $description;

    /**
     * @var string
     */
    public $landDetails;

    /**
     * @var string
     */
    public $buildingDetails;

    /**
     * @var array
     */
    public $inspections;

    /**
     * @var array
     */
    public $externalLinks;

    /**
     * @var array
     */
    public $images;

    /**
     * @var array
     */
    public $features;

    /**
     * @var array
     */
    public $agents;

    /**
     * @var string
     */
    public $newconstruction;

    /**
     * @var string
     */
    public $floorplan;

    /**
     * @var string
     */
    public $miniweb;

    /**
     * @var string
     */
    public $original_data;

    /**
     * @var string
     */
    public $videolink;

    /**
     * @var int
     */
    public $bed;

    /**
     * @var int
     */
    public $car;

    /**
     * @var int
     */
    public $bath;

    /**
     * @var int
     */
    public $post_id;

    /**
     * @var string
     */
    public $post_title;

    /**
     * @var int
     */
    public $thumbnail_id;


    /**
     * Property constructor.
     * @param $property  SimpleXMLElement
     */
    public function __construct($property)
    {
        $this->property_id = (int)$property['id'];

        $this->rent_period_week = '';
        $this->rent_period_monthly = '';
        $this->bond = '';
        if (isset($property['type']) && (string)$property['type'] == 'rental') {
            $this->rent_period_week = (string)$property->children()->rent[0];
            $this->rent_period_monthly = (string)$property->children()->rent[1];
            $this->bond = (string)$property->bond;
        }

        $this->dateAvailable = (string)$property->children()->dateAvailable;
        $this->agentID = (string)$property->agentID;
        $this->uniqueID = (string)$property->uniqueID;
        $this->type = (string)$property['type'];
        $this->status = (string)$property['status'];
        $this->publishDate = (string)$property['modTime'];
        if($this->status == "sold"){
            $this->publishDate = (string)$property->soldDetails->soldDate;
        }
        $this->authority = (string)$property->authority['value'];
        $this->underOffer = (string)$property->underOffer['value'];
        $this->isHomeLandPackage = (string)$property->isHomeLandPackage['value'];
        $this->priceView = (string)$property->priceView;
        $this->address = (string)$property->address['display'];
        $this->site = (string)$property->address->site;
        $this->subnumber = (string)$property->address->subNumber;
        $this->lotnumber = (string)$property->address->lotNumber;
        $this->streetnumber = (string)$property->address->streetNumber;
        $this->street = (string)$property->address->street;
        $this->suburb = (string)$property->address->suburb;
        $this->state = (string)$property->address->state;
        $this->postcode = (string)$property->address->postcode;
        $this->country = (string)$property->address->country;
        $this->category = (string)$property->category['name'];
        $this->headline = (string)$property->headline;
        $this->description = (string)$property->description;
        $this->landDetails = (string)$property->landDetails->area;
        $this->buildingDetails = (string)$property->buildingDetails->area;
        $this->videolinkvideolink = (string)$property->videoLink['href'];
        $this->newconstruction = (string)$property->newConstruction;
        $this->floorplan = (string)$property->objects->floorplan['url'];
        $this->miniweb = (string)$property->miniweb->uri;
        $this->original_data = $property->asXML();
        $this->bed = (int)$property->features->bedrooms;
        $this->car = (int)$property->features->garages;
        $this->bath = (int)$property->features->bathrooms;

        $this->inspections = [];
        foreach ($property->inspectionTimes->inspection as $inspectionTime) {
            if (trim((string)$inspectionTime)) {
                $inspection = [
                    'option' => (string)$inspectionTime
                ];
                $this->inspections[] = $inspection;
            }
        }

        $this->externalLinks = [];
        $video = (string)$property->videoLink['href'];
        if ($video) {
            $externalLink = [
                'text' => 'Video',
                'link' => $video
            ];
            $this->externalLinks[] = $externalLink;
        }
        if ((string)$property->objects->floorplan['url']) {
            $externalLink = [
                'text' => 'Floorplan',
                'link' => (string)$property->objects->floorplan['url']
            ];
            $this->externalLinks[] = $externalLink;
        }

        $this->images = [];
        foreach ($property->images[0] as $image) {
            if ((string)$image['url']) {
                $image = [
                    'image' => (string)$image['url']
                ];
                $this->images[] = $image;
            }
        }

        $this->agents = [];
        foreach ($property->listingAgent as $agent) {
            if(trim((string)$agent->name)){
                $_agent = [
                    'name' => (string)$agent->name,
                    'phone' => (string)$agent->telephone[0],
                    'phone2' => (string)$agent->telephone[1],
                    'email' => (string)$agent->email,
                ];
                $this->agents[] = $_agent;
            }
        }

        $this->features = $this->get_features_array($property->features);
        $this->post_id = $this->property_post_id();
        $this->thumbnail_id = $this->get_thumbnail_id();
        $this->post_title = $this->site . ' ' . $this->subnumber . ' ' . $this->lotnumber . ' ' . $this->streetnumber . ' ' . $this->street;
    }

    /**
     * Update the property if exist insert and update the property if it's not exist
     *
     * @return int $post_id the post id
     */
    public function update()
    {

        $post_id = $this->post_id;
        if (!$post_id) {
            $post_id = wp_insert_post(array(
                'post_title' => $this->post_title,
                'post_type' => 'property',
                'post_status' => 'publish',
            ));
        }

        if (isset($this->type) && $this->type == 'rental') {
            update_field('rent_period_week', $this->rent_period_week, $post_id);
            update_field('rent_period_monthly', $this->rent_period_monthly, $post_id);
            update_field('bond', $this->bond, $post_id);
            update_field('dateAvailable', $this->dateAvailable, $post_id);
        }
        update_field('agentID', $this->agentID, $post_id);
        update_field('uniqueID', $this->uniqueID, $post_id);
        update_field('type', $this->type, $post_id);
        update_field('status', $this->status, $post_id);
        wp_update_post(
            array (
                'ID'            => $post_id, // ID of the post to update
                'post_date'     => $this->publishDate
            )
        );
        update_field('authority', $this->authority, $post_id);
        update_field('underOffer', $this->underOffer, $post_id);
        update_field('isHomeLandPackage', $this->isHomeLandPackage, $post_id);
        update_field('priceView', $this->priceView, $post_id);
        update_field('address', $this->address, $post_id);
        update_field('site', $this->site, $post_id);
        update_field('subnumber', $this->subnumber, $post_id);
        update_field('lotnumber', $this->lotnumber, $post_id);
        update_field('streetnumber', $this->streetnumber, $post_id);
        update_field('street', $this->street, $post_id);
        update_field('suburb', $this->suburb, $post_id);
        update_field('state', $this->state, $post_id);
        update_field('postcode', $this->postcode, $post_id);
        update_field('country', $this->country, $post_id);
        update_field('category', $this->category, $post_id);
        update_field('headline', $this->headline, $post_id);
        update_field('description', $this->description, $post_id);
        update_field('landDetails', $this->landDetails, $post_id);
        update_field('buildingDetails', $this->buildingDetails, $post_id);
        update_field('videolink', $this->videolink, $post_id);
        update_field('newconstruction', $this->newconstruction, $post_id);
        update_field('floorplan', $this->floorplan, $post_id);
        update_field('miniweb', $this->miniweb, $post_id);
        update_field('last_updated', date('Y-m-d H:i:s'), $post_id);
        update_field('original_data', $this->original_data, $post_id);
        update_field('bed', $this->bed, $post_id);
        update_field('car', $this->car, $post_id);
        update_field('bath', $this->bath, $post_id);

        delete_post_meta($post_id, 'inspectiontimes');
        foreach ($this->inspections as $inspection) {
            add_row('inspectiontimes', $inspection);
        }

        delete_post_meta($post_id, 'externallink');
        foreach ($this->externalLinks as $externalLink) {
            add_row('externallink', $externalLink);
        }

        delete_post_meta($post_id, 'images');
        foreach ($this->images as $image) {
            add_row('images', $image);
        }

        delete_post_meta($post_id, 'features');
        foreach ($this->features as $feature) {
            add_row('features', $feature);
        }

        delete_post_meta($post_id, 'agents');
        foreach ($this->agents as $agent) {
            add_row('agents', $agent);
        }

        set_post_thumbnail( $this->post_id, $this->thumbnail_id );

        return $post_id;
    }

    /**
     * Check if property id exists
     *
     * @return int the property id if the property exists
     */
    public function property_post_id()
    {
        $meta_query = [];

        if ($this->property_id != '') {
            $meta_query[] = [
                'key' => 'uniqueID',
                'value' => $this->property_id,
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
        return 0;
    }

    /**
     * Check if has thumbnail ready if not download and set the thumbnail
     *
     * @return int the property thumbnail id
     */
    public function get_thumbnail_id()
    {
        // if has thumbnail skip
        if(has_post_thumbnail($this->post_id)){
            return get_post_thumbnail_id( $this->post_id );
        }

        // the image url from console
        $main_image_url = $this->images[0]['image'];
        // check if the image exist
        if(!$this->url_exists($main_image_url)){
            return 0;
        }
        if(!$main_image_url){
            return 0;
        }

        // get the image name
        $main_image_name = basename($this->http_strip_query_param($main_image_url));

        // get wordpress upload dir
        $wordpress_upload_dir = wp_upload_dir();

        // init the local image file
        $new_file_path = $wordpress_upload_dir['path'] . '/' . $main_image_name;

        // download the file
        if(is_file($main_image_url)) {
            copy($main_image_url, $new_file_path);
        } else {
            $options = array(
                CURLOPT_FILE    => fopen($new_file_path, 'w'),
                CURLOPT_TIMEOUT =>  120, // set this to 8 hours so we dont timeout on big files
                CURLOPT_URL     => $main_image_url
            );

            $ch = curl_init();
            curl_setopt_array($ch, $options);
            curl_exec($ch);
            curl_close($ch);
        }

        /* wp_insert_attachment */
        $filetype = wp_check_filetype(basename($new_file_path), null);

        $attachment = array(
            'post_mime_type' => $filetype['type'],
            'post_title' => sanitize_file_name(basename($new_file_path)),
            'post_content' => '',
            'post_status' => 'inherit'
        );

        // So here we attach image to its parent's post ID from above
        $attach_id = wp_insert_attachment($attachment, $new_file_path, $this->post_id);

        // Attachment has its ID too "$attach_id"
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $attach_data = wp_generate_attachment_metadata($attach_id, $main_image_name);
        wp_update_attachment_metadata($attach_id, $attach_data);

        return $attach_id;
    }

    /**
     * Return array of readable features
     * @param $features SimpleXMLElement Features from REAXML
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
     * Remove the url parameters
     *
     * @param $url string the url with parameters
     * @return string the url without parameters
     */
    private function http_strip_query_param($url)
    {
        if($url){
            $pieces = parse_url($url);
            if (!$pieces['query']) {
                return $url;
            }

            $query = [];
            $pieces['query'] = http_build_query($query);
            return 'https://' . $pieces['host'] . $pieces['path'];
            //return http_build_url($pieces);
        }
    }

    /**
     * Check if the URL exists
     *
     * @param $url the URL
     * @return bool true if the URL exists false if not
     */
    private function url_exists($url) {
        return curl_init($url) !== false;
    }
}