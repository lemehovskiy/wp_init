<?php

function register_taxonomy_{TAXONOMY_SLUG_UNDERSCORE}()
{

    register_taxonomy('{TAXONOMY_SLUG}', array('{ASSIGN_TO_POST_TYPE}'),
    
        array(
            'labels' => array(
                'name' => __('{TAXONOMY_NAME}', '{TEXT_DOMAIN}'),
                'singular_name' => __('{TAXONOMY_SINGULAR_NAME}', '{TEXT_DOMAIN}'),
                'search_items' => __('Search {TAXONOMY_NAME}', '{TEXT_DOMAIN}'),
                'all_items' => __('All {TAXONOMY_NAME}', '{TEXT_DOMAIN}'),
                'edit_item' => __('Edit {TAXONOMY_SINGULAR_NAME}', '{TEXT_DOMAIN}'),
                'update_item' => __('Update {TAXONOMY_SINGULAR_NAME}', '{TEXT_DOMAIN}'),
                'add_new_item' => __('Add New {TAXONOMY_SINGULAR_NAME}', '{TEXT_DOMAIN}'),
                'new_item_name' => __('New {TAXONOMY_SINGULAR_NAME}', '{TEXT_DOMAIN}'),
                'menu_name' => __('{TAXONOMY_NAME}', '{TEXT_DOMAIN}')
            ),
            'hierarchical' => true,
            'rewrite' => array('slug' => '{TAXONOMY_SLUG}')
        ));
}


add_action('init', 'register_taxonomy_{TAXONOMY_SLUG_UNDERSCORE}');