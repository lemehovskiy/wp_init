<?php

function register_post_type_{POST_TYPE_SLUG}() {

    register_post_type( '{POST_TYPE_SLUG}',
        array(
            'labels' => array(

                'name' => __( '{POST_TYPE_NAME}' ),
                'singular_name' => __( '{POST_TYPE_SINGULAR_NAME}' ),
                'add_new_item'  => __( 'New {POST_TYPE_SINGULAR_NAME}'  ),
                'view_item'     => __( 'View {POST_TYPE_SINGULAR_NAME}' )
            ),
            'public' 	   => true,
            'has_archive'  => true,
            'hierarchical' => true,
            'supports' => array('title', 'author', 'page-attributes'),
            'rewrite'  => array('slug' => '{POST_TYPE_SLUG}'),
        )
    );
}

add_action( 'init', 'register_post_type_{POST_TYPE_SLUG}' );