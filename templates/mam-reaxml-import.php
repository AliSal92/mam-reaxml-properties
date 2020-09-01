<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use MAM\Plugin\Services\Import\Import;

$import = new Import();

//$import->download_files();

$import->unpublish_properties();

$properties = $import->get_listings_array();

foreach ($properties as $property){
    if($import->property_exists((string)$property['id'])){
        $import->update_property($property);
    }else{
        $import->add_property($property);
    }
}
