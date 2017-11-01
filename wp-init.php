<?php

$shortopts = "";

$longopts = array(
    "init:",
    "install::",
    "destroy::"
);

$options = getopt($shortopts, $longopts);

$config_json = file_get_contents("wp-init-config.json");

$config = json_decode($config_json, true);


define("THEME_DIRECTORY", 'wp-content/themes/' . $config['project_name'] . '-theme');
define("PROJECT_NAME_UNDERSCORE",  str_replace('-', '_', $config['project_name']));


if (isset($options['init'])) {
    $config['project_name'] = $options['init'];

    create_project_folder($config);

} else if ($config['project_name'] != null) {
    if (isset($options['install'])) {

        dowload_wp_core();

        remove_files($config['remove_wp_core_files']);

        download_starter_theme($config);

        install_plugins($config);

        create_post_types($config);
        create_taxonomies($config);

        remove_starter_theme_files($config);

        create_gitignore($config);

        create_flexible_templates($config);
        create_flexible_template_sections_files($config);

        create_wp_config($config);



        git_init($config);

        if (isset($options['destroy'])) {
            remove_wp_init();
        }
    } else if (isset($options['destroy'])) {
        remove_wp_init();
    } else {

    }
}


function create_folder($path){
    if (!is_dir($path)) {
        mkdir($path, 0777, true);
    }
}

function create_wp_config($config){

    exec('wget https://api.wordpress.org/secret-key/1.1/salt/ -q -O -', $secret_keys);

    $table_prefix = 'wp_' . PROJECT_NAME_UNDERSCORE . '_' . substr(uniqid(), -5)  . '_';

    $searchF = array(
        "database_name_here",
        "username_here",
        "password_here",
        "{SECRET_KEYS}",
        "{TABLE_PREFIX}"
    );

    $replaceW = array(
        $config['project_name'],
        $config['db_user'],
        $config['db_password'],
        implode("\n", $secret_keys),
        $table_prefix
    );

    $layout_file = file_get_contents("wp-init-src/core/wp-config-sample.php");

    $layout_file = str_replace($searchF, $replaceW, $layout_file);

    //create config
    $post_type_file = fopen('wp-config.php', 'w');
    fwrite($post_type_file, $layout_file);

    //create config example
    $post_type_file = fopen('wp-config-example.php', 'w');
    fwrite($post_type_file, $layout_file);
}


function git_init($config){
    system('git init');
    system('git add .');
    system('git commit -m "init"');
    system('git remote add origin '. $config['project_name']);
}

function create_flexible_template_sections_files($config)
{

    foreach ($config['flexible_templates'] as $template) {

        $section_style_files_include_str = '';

        //create style folder
        $style_folder_path = THEME_DIRECTORY . '/src/css/' . $template['slug'];
        create_folder($style_folder_path);

        //create template folder
        $template_folder_path = THEME_DIRECTORY . '/template_parts/' . $template['slug'];
        create_folder($template_folder_path);

        //create template style file
        $template_style_file = fopen($style_folder_path . '/' . $template['slug'] . '.scss', 'w');

        //include template style file to main style
        $main_style_file = fopen(THEME_DIRECTORY . '/src/css/style.scss', 'a');
        fwrite($main_style_file, "\n". '@import "' . $template['slug'] . '/' . $template['slug'] .'.scss";');
        fclose($main_style_file);


        foreach ($template['sections'] as $section) {

            $section_class = 'section-' . str_replace('_', '-', $section);

            $searchF = array(
                '{SECTION_CLASS}'
            );

            $replaceW = array(
                $section_class
            );


            //create section templates
            $sample_file = file_get_contents("wp-init-src/templates/section_sample.php");

            $sample_file = str_replace($searchF, $replaceW, $sample_file);

            $file = fopen($template_folder_path . '/section_' . $section . '.php', 'w');

            fwrite($file, $sample_file);

            //create style files
            $sample_file = file_get_contents("wp-init-src/sass/section_sample.scss");

            $sample_file = str_replace($searchF, $replaceW, $sample_file);

            $file = fopen($style_folder_path . '/section_' . $section . '.scss', 'w');

            fwrite($file, $sample_file);

            //create include style files string
            $section_style_files_include_str .= '@import "section_'. $section . '.scss";' . "\n";

        }

        //include sections to template style file
        fwrite($template_style_file, $section_style_files_include_str);


    }
}


function create_flexible_templates($config)
{

    foreach ($config['flexible_templates'] as $template) {

        $searchF = array(
            '{TEMPLATE_NAME}',
            '{FLEXIBLE_FIELD_SLUG}',
        );

        $replaceW = array(
            $template['name'],
            $template['slug']
        );

        $sample_file = file_get_contents("wp-init-src/templates/flexible_template.php");

        $sample_file = str_replace($searchF, $replaceW, $sample_file);

        $file = fopen(THEME_DIRECTORY . '/' . $template['slug'] . '.php', 'w');

        fwrite($file, $sample_file);

    }
}


function create_gitignore($config)
{

    $gitignore_string = "";

    $rules_counter = 0;

    foreach ($config['gitignore'] as $rule) {

        if ($rules_counter++ == 0) {
            $gitignore_string .= $rule;
        } else {
            $gitignore_string .= "\n" . $rule;
        }

    }

    //create config file
    $fp = fopen(".gitignore", 'w');
    fwrite($fp, $gitignore_string);
    fclose($fp);


}


function remove_starter_theme_files($config)
{

    $full_paths = array();

    foreach ($config['remove_starter_theme_files'] as $file) {
        $full_paths[] = THEME_DIRECTORY . '/' . $file;
    }

    remove_files($full_paths);
}


function include_taxonomies_to_core($config)
{

    $taxonomy_path_string = "// TAXONOMIES";

    foreach ($config['taxonomies'] as $taxonomy) {
        $taxonomy_slug_underscore = str_replace('-', '_', $taxonomy['taxonomy_slug']);

        $taxonomy_path_string .= "\n" . 'include("taxonomies/register_taxonomy_' . $taxonomy_slug_underscore . '.php");';

    }

    $file = file_get_contents(THEME_DIRECTORY . "/core/core.php");
    $file = str_replace('// TAXONOMIES', $taxonomy_path_string, $file);
    file_put_contents(THEME_DIRECTORY . "/core/core.php", $file);

}

function include_post_types_to_core($config)
{

    $post_type_path_string = "// POST TYPES";

    foreach ($config['post_types'] as $post_type) {
        $taxonomy_slug_underscore = str_replace('-', '_', $post_type['post_type_slug']);

        $post_type_path_string .= "\n" . 'include("post_types/register_post_type_' . $taxonomy_slug_underscore . '.php");';

    }

    $file = file_get_contents(THEME_DIRECTORY . "/core/core.php");
    $file = str_replace('// POST TYPES', $post_type_path_string, $file);
    file_put_contents(THEME_DIRECTORY . "/core/core.php", $file);

}

function create_taxonomies($config)
{

    foreach ($config['taxonomies'] as $taxonomy) {

        $taxonomy_slug_underscore = str_replace('-', '_', $taxonomy['taxonomy_slug']);

        $searchF = array(
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

        $taxonomy_file = fopen(THEME_DIRECTORY . '/core/taxonomies/register_taxonomy_' . $taxonomy_slug_underscore . '.php', 'w');

        fwrite($taxonomy_file, $layout_file);

    }

    include_taxonomies_to_core($config);

}

function create_post_types($config)
{

    foreach ($config['post_types'] as $post_type) {

        $post_type_slug_underscore = str_replace('-', '_', $post_type['post_type_slug']);

        $searchF = array(
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

        $post_type_file = fopen(THEME_DIRECTORY . '/core/post_types/register_post_type_' . $post_type_slug_underscore . '.php', 'w');

        fwrite($post_type_file, $layout_file);

    }

    include_post_types_to_core($config);

}

function install_plugins($config)
{
    //copy local plugins
    foreach ($config['local-plugins'] as $plugin) {
        if ($plugin['install']) {
            system('cp -r ' . $plugin['path'] . ' ' . 'wp-content/plugins');
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


function remove_files($files)
{
    foreach ($files as $file) {
        system('rm -rf ' . $file);
    }
}


function download_starter_theme($config)
{
    //download
    system('curl -L -o wp-starter-theme.zip https://github.com/lemehovskiy/wp-starter-theme/archive/master.zip');

    //extract
    system('mkdir -p ' . THEME_DIRECTORY);
    system('tar -xvf wp-starter-theme.zip --strip 1 --directory ' . THEME_DIRECTORY);

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
        system('cp -r wp-init-src ' . $path);

        //copy init file
        system('cp -r wp-init.php ' . $path);
    }

}

function remove_wp_init()
{
    remove_files(array(
        'wp-init.php',
        'wp-init-src',
        'wp-init-config.json'
    ));
}