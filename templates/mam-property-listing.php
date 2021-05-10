<?php

global $a;
$the_query = apply_filters('mam-property-filtered-posts', $_GET);

?>
<?php if ($the_query->have_posts()) { ?>
    <div class="mam-properties-list">
        <div class="container">
            <div class="row">
                <?php if($a['show_title'] == 'yes'){ ?>
                <div class="col-md-12">
                    <label for="sort_properties" style="display: none;"></label>
                    <select id="sort_properties" onchange="if (this.value) window.location.href=this.value" class="pull-right form-control">
                        <option value="<?php echo apply_filters('mam-property-sort-current-url', 'new-old'); ?>" <?php if (isset($_GET['sort']) && $_GET['sort'] == 'new-old') {
                            echo 'selected';
                        }; ?> >Date (Newest - Oldest)
                        </option>
                        <option value="<?php echo apply_filters('mam-property-sort-current-url', 'old-new'); ?>" <?php if (isset($_GET['sort']) && $_GET['sort'] == 'old-new') {
                            echo 'selected';
                        }; ?>>Date (Oldest - Newest)
                        </option>
                    </select>
                    <h1>
                        <?php echo $the_query->found_posts; ?>
                        <?php if ($a['type'] == 'for-rent') {
                            _e('Properties For Rent');
                        } else if ($a['type'] == 'for-sale') {
                            _e('Properties For Sale');
                        } else if ($a['type'] == 'leased') {
                            _e('Properties Recently Leased');
                        } else if ($a['type'] == 'sold') {
                            _e('Properties Recently Sold');
                        }
                        ?>
                    </h1>
                </div>
                <?php } ?>
                <?php while ($the_query->have_posts()) : $the_query->the_post(); ?>
                    <?php
                    // init variables
                    $permalink = get_the_permalink();
                    $status = get_field('status');
                    $image = 'https://via.placeholder.com/1920x600';
                    $subnumber = get_field('subnumber');
                    if($subnumber){
                        $subnumber = $subnumber . ' / ';
                    }
                    $lotnumber = get_field('lotnumber');
                    $streetNumber = get_field('streetnumber');
                    $streetNumber = get_field('streetnumber');
                    $street = get_field('street');
                    $suburb = get_field('suburb');
                    $inspectionTimes = [];
                    $priceText = get_field('priceView');
                    $beds = get_field('bed');
                    $baths = get_field('bath');
                    $cars = get_field('car');

                    $image = wp_get_attachment_image_src(get_post_thumbnail_id(), 'medium');

                    if (have_rows('inspectiontimes')) {
                        while (have_rows('inspectiontimes')) {
                            the_row();
                            $inspectionTimes[] = get_sub_field('option');
                        }
                    }

                    ?>
                    <div class="col-md-4 mam-property-item mam-property-item-<?php echo get_the_ID(); ?>  mam-property-item-<?php echo $status;?>">
                        <div class="mam-property-item-inner">
                            <a href="<?php echo get_the_permalink(); ?>" class="mam-property-link"></a>
                            <div class="mam-property-featured-image">
                                <img src="<?php echo $image[0]; ?>" alt="<?php the_title(); ?>" width="100%" height="auto"/>
                                <div class="hover-effect"></div>
                            </div>
                            <div class="mam-property-content">
                                <h4><?php echo $subnumber; ?> <?php echo $lotnumber; ?> <?php echo $streetNumber; ?> <?php echo $street; ?></h4>
                                <h2><?php echo $suburb; ?></h2>
                                <div class="mam-property-excerpt">
                                    <?php
                                    foreach ($inspectionTimes as $time) {
                                        echo '<p><i class="fas fa-calendar-alt"></i> ' . $time . '</p>';
                                    }
                                    ?>
                                </div>
                            </div>
                            <hr/>
                            <div class="mam-property-item-footer">
                                <div class="price">
                                    <b><?php echo $priceText; ?></b>
                                </div>
                                <div class="property-info">
                                        <span class="property-info-item">
                                            <i class="fas fa-bed"></i> <?php echo $beds; ?>
                                        </span>
                                    <span class="property-info-item">
                                            <i class="fas fa-bath"></i> <?php echo $baths; ?>
                                        </span>
                                    <span class="property-info-item">
                                            <i class="fas fa-car"></i> <?php echo $cars; ?>
                                        </span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>

                <div class="pagination">
                    <?php
                    $big = 999999999;
                    echo paginate_links(array(
                        'base' => str_replace($big, '%#%', get_pagenum_link($big)),
                        'format' => '?paged=%#%',
                        'current' => max(1, get_query_var('paged')),
                        'total' => $the_query->max_num_pages
                    ));
                    ?>
                </div>

            </div>
        </div>
    </div>
    <?php

} else {
    ?>
    <h2>Sorry, We couldn't find properties match your search options.</h2>
<?php
}
wp_reset_query();
?>
