<?php

function register_taxonomy_{TAXONOMY_SLUG_UNDERSCORE}()
{

    register_taxonomy('{TAXONOMY_SLUG}', array('{ASSIGN_TO_POST_TYPE}'),
    
        array(
            'labels' => array(
                'name' => __('{TAXONOMY_NAME}'),
                'singular_name' => __('{TAXONOMY_SINGULAR_NAME}'),
                'search_items' => __('Search {TAXONOMY_NAME}'),
                'all_items' => __('All {TAXONOMY_NAME}'),
                'edit_item' => __('Edit {TAXONOMY_SINGULAR_NAME}'),
                'update_item' => __('Update {TAXONOMY_SINGULAR_NAME}'),
                'add_new_item' => __('Add New {TAXONOMY_SINGULAR_NAME}'),
                'new_item_name' => __('New {TAXONOMY_SINGULAR_NAME}'),
                'menu_name' => __('{TAXONOMY_NAME}')
            ),
            'hierarchical' => true,
            'rewrite' => array('slug' => '{TAXONOMY_SLUG}')
        ));
}


add_action('init', 'register_taxonomy_{TAXONOMY_SLUG_UNDERSCORE}');