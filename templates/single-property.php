<?php get_header(); ?>
<?php
/**
 * Init variables
 */
$permalink = get_the_permalink();
$status = get_field('status');
$type = get_field('type');
$images = [];
$subnumber = get_field('subnumber');
$lotnumber = get_field('lotnumber');
$streetNumber = get_field('streetnumber');
$street = get_field('street');
$suburb = get_field('suburb');
$state = get_field('state');
$postcode = get_field('postcode');
$country = get_field('country');
$inspectionTimes = [];
$priceText = get_field('priceView');
$beds = get_field('bed');
$baths = get_field('bath');
$cars = get_field('car');
$propertyID = get_field('uniqueID');
$bond = get_field('bond');
$landDetails = get_field('landDetails');
$buildingDetails = get_field('buildingDetails');
$features = [];
$headline = get_field('headline');
$description = get_field('description');
$externallink = [];
$agents = [];

if (have_rows('images')) {
    while (have_rows('images')) {
        the_row();
        $images[] = get_sub_field('image');
    }
}

if (have_rows('inspectiontimes')) {
    while (have_rows('inspectiontimes')) {
        the_row();
        $inspectionTimes[] = get_sub_field('option');
    }
}

$features = [];
if (have_rows('features')) {
    while (have_rows('features')) {
        the_row();
        $_feature = explode(',', get_sub_field('feature'));
        if(is_array($_feature)){
            foreach ($_feature as $feature){
                $features[] = $feature;
            }
        }else{
            $features[] = $_feature;
        }
    }
}

if (have_rows('externallink')) {
    while (have_rows('externallink')) {
        the_row();
        $item = [];
        $item[0] = get_sub_field('text');
        $item[1] = get_sub_field('link');
        $externallink[] = $item;
    }
}

if (have_rows('agents')) {
    while (have_rows('agents')) {
        the_row();
        $item = [];
        $item[0] = get_sub_field('name');
        $item[1] = get_sub_field('phone');
        $item[2] = get_sub_field('phone2');
        $item[3] = get_sub_field('email');
        $agents[] = $item;
    }
}


if (have_rows('agents_pics', 'option')) {
    while (have_rows('agents_pics', 'option')) {
        the_row();
        $name = get_sub_field('name');
        $image = get_sub_field('image');
        foreach ($agents as $key => $agent){
            if($agent[0] == $name){
                $agent[4] = $image;
            }
            $agents[$key] = $agent;
        }
    }
}
$imagesCount = count($images);
$remainingImages = $imagesCount - 3;
?>

    <main id="content">
        <div class="container">
            <div class="archive-property-header archive-single-property-header archive-single-property-header-<?php echo $status;?>">
                <div class="row">
                    <?php if ($imagesCount == 1) { ?>
                        <div class="col-md-12">
                            <a href="<?php echo $images[0]; ?>" class="property-gallery-image" data-fancybox="property"><img src="<?php echo $images[0]; ?>" alt="<?php the_title(); ?>" width="100%" height="auto"/></a>
                        </div>
                    <?php } else if ($imagesCount == 2) { ?>
                        <div class="col-md-6">
                            <a href="<?php echo $images[0]; ?>" class="property-gallery-image" data-fancybox="property"><img src="<?php echo $images[0]; ?>" alt="<?php the_title(); ?>" width="100%" height="auto"/></a>
                        </div>
                        <div class="col-md-6">
                            <a href="<?php echo $images[1]; ?>" class="property-gallery-image" data-fancybox="property"><img src="<?php echo $images[1]; ?>" alt="<?php the_title(); ?>" width="100%" height="auto"/></a>
                        </div>
                    <?php } else if ($imagesCount >= 3) { ?>
                        <div class="col-md-9">
                            <a href="<?php echo $images[0]; ?>" class="property-gallery-image" data-fancybox="property">
                                <img src="<?php echo $images[0]; ?>" alt="<?php the_title(); ?>" width="100%" height="auto"/>
                                <span class="btn btn-default images-count"><i class="fas fa-image"></i> <?php echo $imagesCount; ?> <?php _e('Images'); ?></span>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="<?php echo $images[1]; ?>" class="property-gallery-image" data-fancybox="property">
                                <img src="<?php echo $images[1]; ?>" alt="<?php the_title(); ?>" width="100%" height="auto"/>
                            </a>
                            <a href="<?php echo $images[2]; ?>" class="property-gallery-image property-gallery-counter" data-fancybox="property">
                                <img src="<?php echo $images[2]; ?>" alt="<?php the_title(); ?>" width="100%" height="auto"/>
                                <?php if ($remainingImages > 0) { ?>
                                    <span class="image-counter">
                                        <span class="image-counter-inner">+<?php echo $remainingImages; ?></span>
                                    </span>
                                <?php } ?>
                            </a>
                            <div style="display: none;">
                                <?php $count = 0;
                                foreach ($images as $image) {
                                    $count = $count + 1;
                                    if ($count == 1 || $count == 2 || $count == 3) {
                                        continue;
                                    } ?>
                                    <a href="<?php echo $image; ?>" class="property-gallery-image" data-fancybox="property">
                                        <img src="<?php echo $image; ?>" alt="<?php the_title(); ?>" width="100%" height="auto"/>
                                    </a>
                                <?php } ?>
                            </div>
                        </div>

                    <?php } ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="mam-property-item-inner mam-property-item-inner-single">
                        <div class="mam-property-content">
                            <h1 style="font-size: 22px;"><?php echo $subnumber; ?> <?php echo $lotnumber; ?> <?php echo $streetNumber; ?> <?php echo $street; ?></h1>
                            <h2><?php echo $suburb; ?></h2>
                        </div>
                        <hr/>
                        <div class="mam-property-item-footer">
                            <?php if ($status == 'sold' || $status == 'leased') { ?>
                            <?php } else { ?>
                                <div class="price">
                                    <p><b><?php echo $priceText; ?></b></p>
                                    <?php if ($type != 'residential') { ?>
                                    <p><a href="https://www.1form.com/au/tenant/application/start/" target="_blank"><img alt="apply-link" src="https://1form.com/buttons/default.png"/></a></p>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                            <div class="property-info">
                                <span class="property-info-item"><i class="fas fa-bed"></i> <?php echo $beds; ?></span>
                                <span class="property-info-item"><i class="fas fa-bath"></i> <?php echo $baths; ?></span>
                                <span class="property-info-item"><i class="fas fa-car"></i> <?php echo $cars; ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <?php if (!empty($inspectionTimes)) { ?>
                        <div class="mam-property-item-inner mam-property-item-inner-single">
                            <div class="mam-property-content">
                                <h4 class="text text-big big"><?php _e('Property Dates and Times'); ?></h4>
                            </div>
                            <hr/>
                            <div class="mam-property-item-footer">
                                <?php if ($type != 'residential') { ?>
                                    <p class="text text-big big"><?php _e('Rental Available Date'); ?></p>
                                <?php }else{ ?>
                                    <p class="text text-big big"><?php _e('Available Date'); ?></p>
                                <?php } ?>
                                <div class="mam-property-excerpt">
                                    <?php
                                    foreach ($inspectionTimes as $time) {
                                        echo '<p><i class="fas fa-calendar-alt"></i> ' . $time . '</p>';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                    <div class="mam-property-item-inner mam-property-item-inner-single">
                        <div class="mam-property-content">
                            <h4><?php _e('Property Features'); ?></h4>
                        </div>
                        <hr/>
                        <div class="mam-property-content">
                            <p><?php _e('Property ID:'); ?><?php echo $propertyID; ?></p>
                        </div>
                        <hr/>
                        <?php if ($bond) { ?>
                            <div class="mam-property-content">
                                <p><?php _e('Bond'); ?>
                                    <br/>
                                    $<?php echo number_format($bond); ?></p>
                            </div>
                            <hr/>
                        <?php } ?>
                        <?php if ($landDetails || $buildingDetails) { ?>
                            <div class="mam-property-content">
                                <div class="row">
                                    <?php if ($landDetails) { ?>
                                        <div class="col-md-6">
                                            <p><?php _e('Land Size'); ?>
                                                <br/>
                                                <?php echo($landDetails); ?> m<sup>2</sup></p>
                                        </div>
                                    <?php } ?>
                                    <?php if ($buildingDetails) { ?>
                                        <div class="col-md-6">
                                            <p><?php _e('Floor Area'); ?>
                                                <br/>
                                                <?php echo($buildingDetails); ?> m<sup>2</sup></p>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                            <hr/>
                        <?php } ?>
                        <div class="mam-property-item-footer">
                            <div class="mam-property-features">
                                <?php
                                foreach ($features as $feature) {
                                    echo '<p>' . $feature . '</p>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="mam-property-item-inner mam-property-item-inner-single">
                        <div class="mam-property-content">
                            <h4><?php echo $headline; ?></h4>
                        </div>
                        <hr/>
                        <div class="mam-property-item-footer">
                            <div class="description"><?php echo $description; ?></div>
                        </div>
                    </div>

                </div>

                <div class="col-md-4">
                    <div class="mam-property-item-inner mam-property-item-inner-single">
                        <div class="mam-property-content">
                            <h4><?php _e('Property Tools'); ?></h4>
                        </div>
                        <hr/>
                        <div class="mam-property-item-footer property-tools">
                            <p><a href="javascript:window.print();"><i class="fas fa-file-image"></i> <?php _e('Print Brochure'); ?></a></p>
                            <?php foreach ($externallink as $link) { ?>
                                <p><a href="<?php echo $link[1]; ?>" data-fancybox target="_blank"><i class="fas fa-external-link-alt"></i> <?php echo $link[0]; ?></a></p>
                            <?php } ?>
                        </div>
                    </div>

                    <?php if ($status == 'sold' || $status == 'leased') { ?>
                        <div class="mam-property-item-inner mam-property-item-inner-single">
                            <div class="mam-property-content">
                                <h4><?php echo strtoupper($status); ?></h4>
                            </div>
                            <hr/>
                        </div>
                    <?php } else { ?>
                        <?php if (!empty($agents)) { ?>
                            <?php foreach ($agents as $agent) { ?>
                                <div class="mam-property-item-inner mam-property-item-inner-single">
                                    <div class="mam-property-content">
                                        <h4><?php echo $agent[0]; ?></h4>
                                    </div>
                                    <hr/>
                                    <div class="mam-property-item-footer">
                                        <?php if ($agent[4]){ ?>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="agent-image"><img src="<?php echo $agent[4]; ?>" class="agent-image" width="100%" height="auto"/></div>
                                            </div>
                                            <div class="col-md-8"><?php } ?>
                                                <p>
                                                    <a href="tel:<?php echo $agent[1]; ?>"><i class="fas fa-phone-alt"></i> <?php echo $agent[1]; ?></a>
                                                    <?php if ($agent[2]) { ?>
                                                        <br/><a href="tel:<?php echo $agent[2]; ?>"><i class="fas fa-mobile-alt"></i> <?php echo $agent[2]; ?></a>
                                                    <?php } ?>
                                                    <?php if ($agent[3]) { ?>
                                                        <br/><a href="mailto:<?php echo $agent[3]; ?>"><i class="far fa-envelope"></i> <?php echo $agent[3]; ?></a>
                                                    <?php } ?>
                                                </p>

                                                <?php if ($agent[4]){ ?></div>
                                        </div><?php } ?>
                                        <p><a href="#enquire" data-fancybox data-src="#enquire" class="btn btn-primary"><?php _e('Make an Enquiry'); ?></a></p>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php } else { ?>
                            <div class="mam-property-item-inner mam-property-item-inner-single">
                                <div class="mam-property-content">
                                    <h4><?php _e('Make an Enquiry'); ?></h4>
                                </div>
                                <hr/>
                                <div class="mam-property-item-footer">

                                    <p><a href="tel:0732939100"><i class="fas fa-phone-alt"></i> 07 3293 9100</a></p>
                                    <p><a href="#enquire" data-fancybox data-src="#enquire" class="btn btn-primary"><?php _e('Make an Enquiry'); ?></a></p>
                                </div>
                            </div>
                        <?php } ?>
                    <?php } ?>


                    <div style="display: none;">
                        <div id="enquire">
                            <h2><?php _e('Property Enquiry'); ?></h2>
                            <hr/>
                            <h4><?php _e('Property Address: '); ?><?php echo $streetNumber; ?>&nbsp;<?php echo $street; ?>,&nbsp;<?php echo $suburb; ?></h4>
                            <?php //echo do_shortcode('[gravityform id="5" title="false" description="false" ajax="true"]'); ?>
							<?php gravity_form(5, $display_title = false, $display_description = false, $display_inactive = false, $field_values = array('property_address' => $streetNumber . ' ' . $street . ', ' . $suburb), $ajax = true); ?>
                        </div>
                    </div>
                </div>

               <div class="col-md-12">
           
                </div>


            </div>
        </div>
    </main>

    <style type="text/css">
        #map {
            width: 100%;
            height: 400px;
            border: #ccc solid 1px;
            margin: 20px 0;
        }

        #map {
            max-width: inherit !important;
        }
    </style>
    <script >
        var geocoder;
        var map;
        var address = "<?php echo $streetNumber; ?> <?php echo $street; ?> <?php echo $suburb; ?><?php echo $state; ?><?php echo $postcode; ?><?php echo $country; ?>";

        function initMap() {
            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 16,
                center: {lat: -34.397, lng: 150.644}
            });
            geocoder = new google.maps.Geocoder();
            codeAddress(geocoder, map);
        }

        function codeAddress(geocoder, map) {
            geocoder.geocode({'address': address}, function (results, status) {
                if (status === 'OK') {
                    map.setCenter(results[0].geometry.location);
                    new google.maps.Marker({
                        map: map,
                        position: results[0].geometry.location
                    });
                } else {
                    console.log('Geocode was not successful for the following reason: ' + status);
                }
            });
        }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC4OF3GAWlsSAMvAwsjoPqSvg516FGXIYw&callback=initMap"></script>

<?php get_footer(); ?>