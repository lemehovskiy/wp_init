<?php

$options = getopt("f:i::c::");


$config_json = file_get_contents("wp-init-config.json");

$config = json_decode($config_json, true);

if (isset($options['f'])) {
    $config['project_name'] = $options['f'];

    create_project_folder($config);

}
else if (isset($options['i'])) {

    dowload_wp_core();

    remove_core_files($config);

    download_starter_theme($config);

    install_plugins($config);

    if (isset($options['c'])) {
        remove_wp_init();
    }
}

else if (isset($options['c'])) {
    remove_wp_init();
}

else {
    create_post_types($config);
    create_taxonomies($config);
}


function create_taxonomies($config){

    foreach ($config['taxonomies'] as $taxonomy){

        $taxonomy_slug_underscore = str_replace('-', '_', $taxonomy['taxonomy_slug']);

        $searchF  = array(
            '{TAXONOMY_SLUG}',
            '{TAXONOMY_NAME}',
            '{TAXONOMY_SINGULAR_NAME}',
            '{ASSIGN_TO_POST_TYPE}',
            '{TAXONOMY_SLUG_UNDERSCORE}'
        );

        $replaceW = array(
            $taxonomy['taxonomy_slug'],
            $taxonomy['taxonomy_name'],
            $taxonomy['taxonomy_singular_name'],
            $taxonomy['assign_to_post_type'],
            $taxonomy_slug_underscore
        );

        $layout_file = file_get_contents("wp-init-src/core/register_taxonomy.php");

        $layout_file = str_replace($searchF, $replaceW, $layout_file);

        $taxonomy_file = fopen('wp-content/themes/wp-test-project-theme/core/taxonomies/register_taxonomy_'. $taxonomy_slug_underscore  .'.php', 'w');

        fwrite($taxonomy_file, $layout_file);

    }

}

function create_post_types($config){

    foreach ($config['post_types'] as $post_type){

        $post_type_slug_underscore = str_replace('-', '_', $post_type['post_type_slug']);

        $searchF  = array(
            '{POST_TYPE_SLUG}',
            '{POST_TYPE_NAME}',
            '{POST_TYPE_SINGULAR_NAME}',
            '{POST_TYPE_SLUG_UNDERSCORE}'
        );

        $replaceW = array(
            $post_type['post_type_slug'],
            $post_type['post_type_name'],
            $post_type['post_type_singular_name'],
            $post_type_slug_underscore
        );

        $layout_file = file_get_contents("wp-init-src/core/register_post_type.php");

        $layout_file = str_replace($searchF, $replaceW, $layout_file);

        $post_type_file = fopen('wp-content/themes/wp-test-project-theme/core/post_types/register_post_type_'. $post_type_slug_underscore  .'.php', 'w');

        fwrite($post_type_file, $layout_file);

    }

}

function install_plugins($config){
    //copy local plugins
    foreach ($config['local-plugins'] as $plugin) {
        if ($plugin['install']) {
            system('cp -r ' . $plugin['path'] .' '. 'wp-content/plugins');
        }
    }

    //download remote plugins
    foreach ($config['remote-plugins'] as $plugin) {
        if ($plugin['install']) {

            //download
            system('curl -L -o remote-plugin.zip ' . $plugin['url']);

            //extract and remove
            system('tar -xvf remote-plugin.zip --directory wp-content/plugins && rm remote-plugin.zip');

        }
    }
}


function dowload_wp_core()
{
    //download
    system('curl -L -o latest.zip https://wordpress.org/latest.zip');

    //extract
    system('tar -xvf latest.zip --strip 1');

    //remove archive
    system('rm latest.zip');
}


function remove_core_files($config)
{
    foreach ($config['remove_files'] as $file) {
        system('rm -rf ' . $file);
    }
}



function download_starter_theme($config)
{
    //download
    system('curl -L -o wp-starter-theme.zip https://github.com/lemehovskiy/wp-starter-theme/archive/master.zip');

    //extract
    system('mkdir -p wp-content/themes/' . $config['project_name'] .'-theme');
    system('tar -xvf wp-starter-theme.zip --strip 1 --directory wp-content/themes/' . $config['project_name'] .'-theme');

    //remove archive
    system('rm wp-starter-theme.zip');

}


function create_project_folder($config)
{

    $path = '../' . $config['project_name'];

    if (is_dir($path)) {
        throw new \RuntimeException(sprintf('Unable to create the %s directory', $path));
    } else {

        //create project folder
        system('mkdir -p ' . $path);

        //create config file
        $fp = fopen($path . '/wp-init-config.json', 'w');
        fwrite($fp, json_encode($config, JSON_PRETTY_PRINT));
        fclose($fp);

        //copy src files
        system('cp -r wp-init-src '. $path);

        //copy init file
        system('cp -r wp-init.php '. $path);
    }

}

function remove_wp_init(){
    system('rm -rf wp-init.php');
    system('rm -rf wp-init-src');
    system('rm -rf wp-init-config.json');
}