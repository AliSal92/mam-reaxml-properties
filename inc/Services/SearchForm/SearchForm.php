<?php


namespace MAM\Plugin\Services\SearchForm;


use MAM\Plugin\Config;
use MAM\Plugin\Services\ServiceInterface;

class SearchForm implements ServiceInterface
{
    /**
     * @var string the plugin path
     */
    private $plugin_path;

    /**
     * @inheritDoc
     */
    public function register()
    {
        // set the plugin_path
        $this->plugin_path = Config::getInstance()->plugin_path;

        add_filter('mam-property-form-data', [$this, 'get_form_data']);
        add_shortcode('mam-property-form-rent', [$this, 'mam_property_form_rent']);
        add_shortcode('mam-property-form-buy', [$this, 'mam_property_form_buy']);
    }

    public function mam_property_form_rent()
    {
        $theme_files = array('mam-search-form-rent.php', 'mam-property/mam-search-form-rent.php');
        $exists_in_theme = locate_template($theme_files, false);

        ob_start();
        if ($exists_in_theme != '') {
            /** @noinspection PhpIncludeInspection */
            include $exists_in_theme;
        } else {
            // nope, load the content
            include $this->plugin_path . 'templates/mam-search-form-rent.php';
        }
        return ob_get_clean();
    }

    public function mam_property_form_buy()
    {
        $theme_files = array('mam-search-form-buy.php', 'mam-property/mam-search-form-buy.php');
        $exists_in_theme = locate_template($theme_files, false);

        ob_start();
        if ($exists_in_theme != '') {
            /** @noinspection PhpIncludeInspection */
            include $exists_in_theme;
        } else {
            // nope, load the content
            include $this->plugin_path . 'templates/mam-search-form-buy.php';
        }
        return ob_get_clean();
    }

    public function get_form_data($type = 'rent')
    {
        // init data
        $data = [];

        if($type == 'rent'){
            // init static options
            $data['price-from'] = [
                '50' => '$ 50',
                '100' => '$100',
                '150' => '$150',
                '200' => '$200',
                '250' => '$250',
                '300' => '$300',
                '350' => '$350',
                '400' => '$400',
                '450' => '$450'
            ];
        }

        if($type == 'buy'){
            // init static options
            $data['price-from'] = [
                '50000' => '$ 50,000',
                '100000' => '$100,000',
                '150000' => '$150,000',
                '200000' => '$200,000',
                '250000' => '$250,000',
                '300000' => '$300,000',
                '350000' => '$350,000',
                '400000' => '$400,000',
                '450000' => '$450,000',
                '500000' => '$500,000',
                '550000' => '$550,000',
                '600000' => '$600,000',
                '650000' => '$650,000',
                '700000' => '$700,000',
                '750000' => '$750,000',
                '800000' => '$800,000',
                '850000' => '$850,000',
                '900000' => '$900,000',
                '950000' => '$950,000',
                '1000000' => '$1,000,000',
                '1250000' => '$1,250,000',
                '1500000' => '$1,500,000',
                '1750000' => '$1,750,000',
                '2000000' => '$2,000,000',
                '2250000' => '$2,250,000',
                '2500000' => '$2,500,000',
                '2750000' => '$2,750,000',
                '3000000' => '$3,000,000'
            ];
        }

        $data['bed'] = [
            '1' => 'Bed',
            '2' => 'Bed: 2+',
            '3' => 'Bed: 3+',
            '4' => 'Bed: 4+',
            '5' => 'Bed: 5+',
            '6' => 'Bed: 6+'
        ];

        $data['bath'] = [
            '1' => 'Bath',
            '2' => 'Bath: 2+',
            '3' => 'Bath: 3+',
            '4' => 'Bath: 4+',
            '5' => 'Bath: 5+',
            '6' => 'Bath: 6+'
        ];
        $data['car'] = [
            '1' => 'Car',
            '2' => 'Car: 2+',
            '3' => 'Car: 3+',
            '4' => 'Car: 4+',
            '5' => 'Car: 5+',
            '6' => 'Car: 6+'
        ];
        $data['type'] = [
            'Studio Apartment' => 'Studio Apartment',
            'Apartment' => 'Apartment',
            'Unit' => 'Unit',
            'Townhouse' => 'Townhouse',
            'House' => 'House',
            'Villa' => 'Villa',
            'Duplex' => 'Duplex',
            'Multiple Houses' => 'Multiple Houses',
            'Semi Detached' => 'Semi Detached',
            'Development Site' => 'Development Site',
            'Holiday Rental' => 'Holiday Rental',
            'House And Land Package' => 'House And Land Package',
            'New Land Subdivision' => 'New Land Subdivision',
            'Apartment Block' => 'Apartment Block',
            'Vacant Land' => 'Vacant Land',
            'Section Res' => 'Section Res',
            'Retail/dwelling' => 'Retail/dwelling',
            'Acreage' => 'Acreage',
            'Retirement Living' => 'Retirement Living',
            'Lifestyle' => 'Lifestyle'
        ];

        // init dynamic options from posts
        $data['suburb'] = [];

        // WP_Query arguments
        $args = array(
            'post_type' => array('property'),
            'nopaging' => true,
            'posts_per_page' => '9999',
        );
        $suburbA = [];
        // The Query
        $query = new \WP_Query($args);

        // The Loop
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $suburb = get_field('suburb');
                if (!in_array($suburb, $suburbA)) {
                    $suburbA[] = $suburb;
                }
            }
        }

        // Restore original Post Data
        wp_reset_postdata();
        sort($suburbA);

        $data['suburb'] = $suburbA;
        return $data;
    }
}