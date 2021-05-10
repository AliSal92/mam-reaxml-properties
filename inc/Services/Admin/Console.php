<?php

namespace MAM\Plugin\Services\Admin;


use MAM\Plugin\Services\ServiceInterface;

class Console implements ServiceInterface
{
    /**
     * @inheritDoc
     */
    public function register()
    {
        add_action( 'plugins_loaded', [$this, 'add_option_page']);
        add_action( 'plugins_loaded', [$this, 'add_custom_fields']);
    }


    public static function add_option_page() {
        // Register the option page using ACF
        if ( function_exists( 'acf_add_options_page' ) ) {
            // parent page
            acf_add_options_page(array(
                'page_title' 	=> 'MAM Console Import',
                'menu_title'	=> 'MAM Console Import',
                'menu_slug' 	=> 'mam',
                'capability'	=> 'read',
                'redirect'		=> true
            ));

            // child page
            acf_add_options_sub_page(array(
                'page_title' 	=> 'MAM Console Import',
                'menu_title'	=> 'MAM Console Import',
                'menu_slug'  => 'mam-stripe',
                'capability'	=> 'read',
                'parent_slug'	=> 'mam'
            ));

        }
    }

    public static function add_custom_fields() {
        if( function_exists('acf_add_local_field_group') ){
            acf_add_local_field_group(array(
                'key' => 'group_5f4c673164cdd',
                'title' => 'FTP Access to console reaxml files',
                'fields' => array(
                    array(
                        'key' => 'field_5f4c677c40094',
                        'label' => 'Host',
                        'name' => 'host',
                        'type' => 'text',
                        'instructions' => 'Domain name or IP address',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'maxlength' => '',
                    ),
                    array(
                        'key' => 'field_5f4c67a240095',
                        'label' => 'Username',
                        'name' => 'username',
                        'type' => 'text',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'maxlength' => '',
                    ),
                    array(
                        'key' => 'field_5f4c67ad40096',
                        'label' => 'Password',
                        'name' => 'password',
                        'type' => 'password',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                    ),
                    array(
                        'key' => 'field_5f4c67b840097',
                        'label' => 'Path',
                        'name' => 'path',
                        'type' => 'text',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'maxlength' => '',
                    ),
                    array(
                        'key' => 'field_6098c475d1a32',
                        'label' => 'Import Now',
                        'name' => '',
                        'type' => 'message',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'message' => '<a href="https://affinityproperty.net.au/mam-reaxml-import/" target="_blank" class="button button-primary">Import Now</a>',
                        'new_lines' => '',
                        'esc_html' => 0,
                    ),
                ),
                'location' => array(
                    array(
                        array(
                            'param' => 'options_page',
                            'operator' => '==',
                            'value' => 'mam-stripe',
                        ),
                    ),
                ),
                'menu_order' => 0,
                'position' => 'normal',
                'style' => 'default',
                'label_placement' => 'top',
                'instruction_placement' => 'label',
                'hide_on_screen' => '',
                'active' => true,
                'description' => '',
            ));
        }
    }

}