<?php


namespace MAM\Plugin\Services\PostType;


use MAM\Plugin\Config;
use MAM\Plugin\Services\ServiceInterface;

class Property implements ServiceInterface
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

        add_action('init', array($this, 'init_property_post_type'), 0);
        add_filter('single_template', array($this, 'init_property_template'));
        add_filter('template_include', array($this, 'archive_template'));
        add_filter('mam-property-filtered-posts', array($this, 'filtered_posts'));
        add_action('acf/init', array($this, 'add_property_custom_fields'));
        add_shortcode('mam-property-listing', [$this, 'mam_property_listing']);
    }

    /**
     * init property post type info (to be called by wordpress)
     */
    public static function init_property_post_type()
    {
        $labels = array(
            'name' => _x('Properties', 'Post Type General Name'),
            'singular_name' => _x('Property', 'Post Type Singular Name'),
            'menu_name' => __('Properties'),
            'name_admin_bar' => __('Property'),
            'archives' => __('Item Archives'),
            'attributes' => __('Item Attributes'),
            'parent_item_colon' => __('Parent Property:'),
            'all_items' => __('All Properties'),
            'add_new_item' => __('Add New Property'),
            'add_new' => __('Add New'),
            'new_item' => __('New Property'),
            'edit_item' => __('Edit Property'),
            'update_item' => __('Update Property'),
            'view_item' => __('View Property'),
            'view_items' => __('View Properties'),
            'search_items' => __('Search Property'),
            'not_found' => __('Not found'),
            'not_found_in_trash' => __('Not found in Trash'),
            'featured_image' => __('Featured Image'),
            'set_featured_image' => __('Set featured image'),
            'remove_featured_image' => __('Remove featured image'),
            'use_featured_image' => __('Use as featured image'),
            'insert_into_item' => __('Insert into'),
            'uploaded_to_this_item' => __('Uploaded to this Property'),
            'items_list' => __('Items list'),
            'items_list_navigation' => __('Items list navigation'),
            'filter_items_list' => __('Filter Properties list'),
        );
        $args = array(
            'label' => __('Property'),
            'description' => __('Property post type by MAM Properties'),
            'labels' => $labels,
            'supports' => array('title', 'editor', 'thumbnail', 'revisions', 'excerpt', 'custom-fields'),
            'hierarchical' => false,
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'menu_icon' => 'dashicons-building',
            'menu_position' => 5,
            'show_in_admin_bar' => true,
            'show_in_nav_menus' => true,
            'can_export' => true,
            'has_archive' => true,
            'exclude_from_search' => false,
            'publicly_queryable' => true,
            'capability_type' => 'page',
        );
        register_post_type('property', $args);
    }

    /**
     * init post type template file single-property.php
     */
    function init_property_template($template)
    {
        global $post;
        if ('property' == $post->post_type) {
            $theme_files = array('single-property.php', 'mam-property/single-property.php');
            $exists_in_theme = locate_template($theme_files, false);
            if ($exists_in_theme != '') {
                return $exists_in_theme;
            } else {
                return $this->plugin_path . 'templates/single-property.php';
            }
        }
        return $template;
    }

    /**
     * add property archive template
     */
    public function archive_template($template)
    {
        if (is_post_type_archive('property')) {
            $theme_files = array('archive-property.php', 'mam-property/archive-property.php');
            $exists_in_theme = locate_template($theme_files, false);
            if ($exists_in_theme != '') {
                return $exists_in_theme;
            } else {
                return $this->plugin_path . 'templates/archive-property.php';
            }
        }
        return $template;
    }

    /**
     * add property post type custom fields (using ACF Pro)
     */
    public function add_property_custom_fields()
    {
        // TODO ACF Fields
        return false;
    }


    /**
     * [mam-property-listing] function
     */
    public function mam_property_listing($atts)
    {
        global $a;
        $a = shortcode_atts(array(
            'type' => 'for-sale'
        ), $atts);

        $theme_files = array('mam-property-listing.php', 'mam-property/mam-property-listing.php');
        $exists_in_theme = locate_template($theme_files, false);

        ob_start();
        if ($exists_in_theme != '') {
            /** @noinspection PhpIncludeInspection */
            include $exists_in_theme;
        } else {
            // nope, load the content
            include $this->plugin_path . 'templates/mam-property-listing.php';
        }
        return ob_get_clean();
    }

    /**
     * Get the properties filtered
     */
    public function filtered_posts($getData)
    {
        global $a;

        $type = 'rental';
        if (isset($a['type'])) {
            if ($a['type'] == 'for-sale') {
                $type = 'residential';
            }
        }

        $sort = '';
        if (isset($getData['sort'])) {
            $sort = $getData['sort'];
        }


        $suburb = '';
        if (isset($getData['suburb'])) {
            $suburb = $getData['suburb'];
        }


        $price_from = '';
        if (isset($getData['price_from'])) {
            $price_from = $getData['price_from'];
        }


        $price_to = '';
        if (isset($getData['price_to'])) {
            $price_to = $getData['price_to'];
        }


        $bed = '1';
        if (isset($getData['bed'])) {
            $bed = $getData['bed'];
        }


        $bath = '1';
        if (isset($getData['bath'])) {
            $bath = $getData['bath'];
        }


        $car = '1';
        if (isset($getData['car'])) {
            $car = $getData['car'];
        }

        $meta_query = [];
        $meta_query['relation'] = 'AND';

        if ($suburb != '') {
            $meta_query[] = [
                'key' => 'suburb',
                'value' => $suburb,
                'compare' => '='
            ];
        }

        if ($price_from != '') {
            $meta_query[] = [
                'key' => 'rent_period_week',
                'value' => $price_from,
                'compare' => '>='
            ];
        }

        if ($price_to != '') {
            $meta_query[] = [
                'key' => 'rent_period_week',
                'value' => $price_to,
                'compare' => '<='
            ];
        }

        if ($bed != '') {
            $meta_query[] = [
                'key' => 'bed',
                'value' => $bed,
                'compare' => '>='
            ];
        }

        if ($bath != '') {
            $meta_query[] = [
                'key' => 'bath',
                'value' => $bath,
                'compare' => '>='
            ];
        }

        if ($car != '') {
            $meta_query[] = [
                'key' => 'car',
                'value' => $car,
                'compare' => '>='
            ];
        }

        if ($type != '') {
            $meta_query[] = [
                'key' => 'type',
                'value' => $type,
                'compare' => '='
            ];
        }

        // args
        $args = array(
            'numberposts' => -1,
            'post_type' => 'property',
            'meta_query' => $meta_query
        );

        // query
        return new \WP_Query($args);
    }
}