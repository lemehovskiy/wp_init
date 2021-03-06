<?php

function register_post_type_{POST_TYPE_SLUG_UNDERSCORE}() {

    register_post_type( '{POST_TYPE_SLUG}',
        array(
            'labels' => array(

                'name' => __('{POST_TYPE_NAME}', '{TEXT_DOMAIN}'),
                'singular_name' => __('{POST_TYPE_SINGULAR_NAME}', '{TEXT_DOMAIN}'),
                'add_new_item'  => __('New {POST_TYPE_SINGULAR_NAME}', '{TEXT_DOMAIN}'),
                'view_item'     => __('View {POST_TYPE_SINGULAR_NAME}', '{TEXT_DOMAIN}')
            ),
            'public' 	   => true,
            'has_archive'  => true,
            'hierarchical' => true,
            'supports' => array('title', 'author', 'page-attributes'),
            'rewrite'  => array('slug' => '{POST_TYPE_SLUG}'),
        )
    );
}

add_action( 'init', 'register_post_type_{POST_TYPE_SLUG_UNDERSCORE}' );