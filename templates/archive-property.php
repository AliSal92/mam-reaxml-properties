<?php get_header(); ?>
    <main id="content">
        <div class="archive-property-header">
            <h1><?php _e('PROPERTIES'); ?></h1>
            <h3><?php _e('For Sale/Rent in Thailand'); ?></h3>
            <div class="customer-search-form">
                <div class="container">
                    <h2><?php _e('FIND A PROPERTY'); ?></h2>
                    <?php echo do_shortcode('[mam-property-form]'); ?>
                </div>
            </div>
        </div>
        <?php echo do_shortcode('[mam-property-listing]'); ?>
    </main>
<?php get_sidebar(); ?>
<?php get_footer(); ?>