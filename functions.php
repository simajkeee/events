<?php
add_action( 'wp_enqueue_scripts', function() {
    wp_enqueue_script("bootstrap-custom", get_template_directory_uri() . "/assets/bootstrap/js/bootstrap.js", array("jQuery"), "1.0.0", true );
    wp_enqueue_style("bootstrap-custom", get_template_directory_uri() . "/assets/bootstrap/css/bootstrap.css");
} );
add_action( 'init', function() {
    $labels = array(
        'name' => _x( 'Events tags', 'taxonomy general name' ),
        'singular_name' => _x( 'Event tag', 'taxonomy singular name' ),
        'search_items' =>  __( 'Search Events tags' ),
        'all_items' => __( 'All Events tags' ),
        'parent_item' => __( 'Parent Event tag' ),
        'parent_item_colon' => __( 'Parent Event tag:' ),
        'edit_item' => __( 'Edit Event tag' ),
        'update_item' => __( 'Update Event tag' ),
        'add_new_item' => __( 'Add New Event tag' ),
        'new_item_name' => __( 'New Event tag Name' ),
        'menu_name' => __( 'Events tags' ),
    );
    register_taxonomy('event', array( 'events' ), array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'show_in_rest' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array( 'slug' => 'subject' ),
    ));
}, 0 );
add_action( 'init', function() {
    register_post_type( 'events',
        array(
            'labels' => array(
                'name' => __( 'Events' ),
                'singular_name' => __( 'Event' )
            ),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'Events'),
            'show_in_rest' => true,
        )
    );
} );
function isTermChild($term,$taxonomy) {
    return !empty( get_ancestors( $term->term_id, $taxonomy ) );
}
function dateSortEventTaxonomyPosts( $a, $b ) {
    $aDates = get_fields( $a->ID );
    $bDates = get_fields( $b->ID );
    return strtotime( $aDates["events_start"] ) - strtotime( $bDates["events_start"] );
}