<?php
/*
Plugin Name: Clean post cache button (MU)
Version: 0.1.0
Description: Add a post row action to clean the post from the object cache.
Author: Viktor Szépe
Plugin URI: https://github.com/szepeviktor/wordpress-plugin-construction
GitHub Plugin URI: https://github.com/szepeviktor/wordpress-plugin-construction
*/

add_filter( 'post_row_actions', function ( $actions, $post ) {
    $post_list_url = admin_url( add_query_arg( array(
        'post_type' => $post->post_type,
        'clean_post' => $post->ID,
    ), 'edit.php' ) );

    $actions['clean_post'] = sprintf( '<a class="clean" href="%s">%s</a>',
        wp_nonce_url( $post_list_url, 'clean_post' ),
        __( 'Clean', 'clean-post' )
    );

    return $actions;
}, 11, 2 );

add_action( 'admin_init', function () {
    if ( ! isset( $_GET['clean_post'] ) ) {
        return;
    }

    check_admin_referer( 'clean_post' );

    // Clean
    $post_id = (int) $_GET['clean_post'];
    if ( false !== get_post_status( $post_id ) ) {
        clean_post_cache( $post_id );
    }

    add_action( 'admin_notices', function () {
        echo '<div class="notice notice-success is-dismissible"><p>Post cleaned from Object Cache.</p></div>';
    } );
} );
