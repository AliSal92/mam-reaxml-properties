<?php

global $a;
$the_query = apply_filters('mam-property-filtered-posts', $_GET);

?>
<?php if ($the_query->have_posts()) { ?>
    <div class="mam-properties-list">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <label for="sort_properties" style="display: none;"></label>
                    <select id="sort_properties" onchange="if (this.value) window.location.href=this.value" class="pull-right form-control">
                        <option value="<?php echo add_sort_to_current_url(''); ?>" <?php if (isset($_GET['sort']) && $_GET['sort'] == 'new-old') {
                            echo 'selected';
                        }; ?> >Date (Newest - Oldest)
                        </option>
                        <option value="<?php echo add_sort_to_current_url('old-new'); ?>" <?php if (isset($_GET['sort']) && $_GET['sort'] == 'old-new') {
                            echo 'selected';
                        }; ?>>Date (Oldest - Newest)
                        </option>
                        <option value="<?php echo add_sort_to_current_url('low-high'); ?>" <?php if (isset($_GET['sort']) && $_GET['sort'] == 'low-high') {
                            echo 'selected';
                        }; ?>>Price (Low - High)
                        </option>
                        <option value="<?php echo add_sort_to_current_url('high-low'); ?>" <?php if (isset($_GET['sort']) && $_GET['sort'] == 'high-low') {
                            echo 'selected';
                        }; ?>>Price (High - Low)
                        </option>
                    </select>
                    <h2>
                        <?php echo $the_query->post_count; ?>
                        <?php if ($a['type'] == 'for-rent') {
                            _e('RESIDENTIAL PROPERTIES FOUND FOR RENT');
                        } else {
                            _e('RESIDENTIAL PROPERTIES FOUND FOR SALE');
                        } ?>
                    </h2>
                </div>
                <?php while ($the_query->have_posts()) : $the_query->the_post(); ?>
                    <?php
                    // init variables
                    $permalink = get_the_permalink();
                    $image = 'https://via.placeholder.com/1920x600';
                    $streetNumber = get_field('streetnumber');
                    $street = get_field('street');
                    $suburb = get_field('suburb');
                    $inspectionTimes = [];
                    $priceText = get_field('priceView');
                    $beds = get_field('bed');
                    $baths = get_field('bath');
                    $cars = get_field('car');

                    if (have_rows('images')) {
                        while (have_rows('images')) {
                            the_row();
                            $image = get_sub_field('image');
                            break;
                        }
                    }

                    if (have_rows('inspectiontimes')) {
                        while (have_rows('inspectiontimes')) {
                            the_row();
                            $inspectionTimes[] = get_sub_field('option');
                        }
                    }

                    ?>
                    <div class="col-md-4 mam-property-item">
                        <div class="mam-property-item-inner">
                            <a href="<?php echo get_the_permalink(); ?>" class="mam-property-link"></a>
                            <div class="mam-property-featured-image">
                                <img src="<?php echo $image; ?>" alt="<?php the_title(); ?>" width="100%" height="auto"/>
                                <div class="hover-effect"></div>
                            </div>
                            <div class="mam-property-content">
                                <h4><?php echo $streetNumber; ?>&nbsp;<?php echo $street; ?></h4>
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

function add_sort_to_current_url($param)
{
    $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $url_parts = parse_url($actual_link);
    if (isset($url_parts['query'])) {
        parse_str($url_parts['query'], $params);
    } else {
        $params = array();
    }
    $params['sort'] = $param;
    $url_parts['query'] = http_build_query($params);
    return $url_parts['scheme'] . '://' . $url_parts['host'] . $url_parts['path'] . '?' . $url_parts['query'];
}

?>
