<?php get_header(); ?>
<?php
/**
 * Init variables
 */
$permalink = get_the_permalink();
$images = [];
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

if (have_rows('features')) {
    while (have_rows('features')) {
        the_row();
        $features[] = get_sub_field('feature');
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

$imagesCount = count($images);
$remainingImages = $imagesCount - 3;
?>

    <main id="content">
        <div class="container">
            <div class="archive-property-header archive-single-property-header">
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
                            <h4><?php echo $streetNumber; ?> <?php echo $street; ?></h4>
                            <h2><?php echo $suburb; ?></h2>
                        </div>
                        <hr/>
                        <div class="mam-property-item-footer">
                            <div class="price">
                                <p><b><?php echo $priceText; ?></b></p>
                                <p><a href="https://www.1form.com/au/tenant/application/start/" target="_blank"><img src="https://1form.com/buttons/default.png" /></a> </p>
                            </div>
                            <div class="property-info">
                                <span class="property-info-item"><i class="fas fa-bed"></i> <?php echo $beds; ?></span>
                                <span class="property-info-item"><i class="fas fa-bath"></i> <?php echo $baths; ?></span>
                                <span class="property-info-item"><i class="fas fa-car"></i> <?php echo $cars; ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="mam-property-item-inner mam-property-item-inner-single">
                        <div class="mam-property-content">
                            <h4 class="text text-big big"><?php _e('Property Dates and Times'); ?></h4>
                        </div>
                        <hr/>
                        <div class="mam-property-item-footer">
                            <p class="text text-big big"><?php _e('Rental Available Date'); ?></p>
                            <div class="mam-property-excerpt">
                                <?php
                                foreach ($inspectionTimes as $time) {
                                    echo '<p><i class="fas fa-calendar-alt"></i> ' . $time . '</p>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="mam-property-item-inner mam-property-item-inner-single">
                        <div class="mam-property-content">
                            <h4><?php _e('Property Features'); ?></h4>
                        </div>
                        <hr/>
                        <div class="mam-property-content">
                            <p><?php _e('Property ID:'); ?><?php echo $propertyID; ?></p>
                        </div>
                        <hr/>
                        <?php if($bond){ ?>
                            <div class="mam-property-content">
                                <p><?php _e('Bond'); ?>
                                    <br/>
                                    $<?php echo number_format($bond); ?></p>
                            </div>
                            <hr/>
                        <?php } ?>
                        <?php if($landDetails || $buildingDetails){ ?>
                            <div class="mam-property-content">
                                <div class="row">
                                    <?php if($landDetails){ ?>
                                    <div class="col-md-6">
                                        <p><?php _e('Land Size'); ?>
                                            <br/>
                                            <?php echo ($landDetails); ?> m<sup>2</sup></p>
                                    </div>
                                    <?php } ?>
                                    <?php if($buildingDetails){ ?>
                                    <div class="col-md-6">
                                        <p><?php _e('Floor Area'); ?>
                                            <br/>
                                            <?php echo ($buildingDetails); ?> m<sup>2</sup></p>
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
                            <?php foreach ($externallink as $link){ ?>
                                <p><a href="<?php echo $link[1]; ?>" target="_blank"><i class="fas fa-external-link-alt"></i> <?php echo $link[0]; ?></a></p>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="mam-property-item-inner mam-property-item-inner-single">
                        <div class="mam-property-content">
                            <h4><?php _e('Make an Enquiry'); ?></h4>
                        </div>
                        <hr/>
                        <div class="mam-property-item-footer">
                            <p><a href="tel:0732939100"><i class="fas fa-phone-alt"></i> 07 3293 9100</a></p>
                            <p><a href="#enquire" data-fancybox data-src="#enquire" class="btn btn-primary"><?php _e('Make an Enquiry'); ?></a></p>
                            <div style="display: none;">
                                <div id="enquire">
                                    <h2><?php _e('Property Enquiry'); ?></h2>
                                    <hr />
                                    <h4><?php _e('Property Address:'); ?><?php echo $streetNumber; ?> <?php echo $street; ?> <?php echo $suburb; ?></h4>
                                    <?php echo do_shortcode('[ninja_form id=3]'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div id="map"></div>
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
    <script>
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
            geocoder.geocode({'address': address}, function(results, status) {
                if (status === 'OK') {
                    map.setCenter(results[0].geometry.location);
                    var marker = new google.maps.Marker({
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