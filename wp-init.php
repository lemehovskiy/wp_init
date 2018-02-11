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


define("THEME_DIRECTORY", 'wp-content/themes/' . $config['project_slug'] . '-theme');
define("PROJECT_SLUG_UNDERSCORE",  str_replace('-', '_', $config['project_slug']));


if (isset($options['init'])) {
    $config['project_slug'] = $options['init'];

    create_project_folder($config);

} else if ($config['project_slug'] != null) {
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

        create_style_file($config);

//        todo
//        download_theme_assets($config['download_theme_assets']);

        create_db($config);

        if (isset($options['destroy'])) {
            remove_wp_init();
            git_init();
        }
    } else if (isset($options['destroy'])) {
        remove_wp_init();
        git_init();
    } else {

    }
}

else {

}


function download_theme_assets($assets){
    downloader($assets);
}

function downloader($assets)
{

    foreach ($assets as $asset) {

        $full_path_to = str_replace("{THEME_DIRECTORY}", THEME_DIRECTORY, $asset['path_to']);

        $full_dir_from = dirname($asset['path_from']);
        $full_dir_to = dirname($full_path_to);

        //download
        system('curl -L -o asset.zip ' . $asset['download_url']);

        //create temp folder
        system('mkdir -p downloads_temp');

        //extract zip
        system('tar -xvf asset.zip -C downloads_temp ' . $asset['path_from']);

        //create destination path
        system('mkdir -p ' . $full_dir_to);

        //copy files
        system('cp -r downloads_temp/' . $full_dir_from . '/ ' . $full_dir_to);

        //remove temp folder, archive
        remove_files(array(
            'downloads_temp',
            'asset.zip'
        ));
    }
}



function create_file_by_sample($settings){

    $dirname = dirname($settings['create_file']);

    if (!is_dir($dirname))
    {
        mkdir($dirname, 0755, true);
    }

    $sample_file = file_get_contents($settings['sample_file']);

    $sample_file_replaced_fields = str_replace($settings['search_field'], $settings['replace_field'], $sample_file);

    $create_file = fopen($settings['create_file'], 'w');
    fwrite($create_file, $sample_file_replaced_fields);

}

function create_db($config){
    $db = mysqli_connect('localhost', $config['db_user'], $config['db_password'], null, '8889', '/Applications/MAMP/tmp/mysql/mysql.sock') or die('Error connecting to MySQL server.');

    $query = 'CREATE DATABASE ' . PROJECT_SLUG_UNDERSCORE;

    mysqli_query($db, $query);

    mysqli_close($db);
}

function create_folder($path){
    if (!is_dir($path)) {
        mkdir($path, 0777, true);
    }
}


function create_style_file($config){

    $project_name = $config['project_slug'];

    if ($config['project_name'] != null) {
        $project_name = $config['project_name'];
    }

    $searchF = array(
        "{THEME_NAME}",
        "{AUTHOR}",
        "{DESCRIPTION}",
        "{TEXT_DOMAIN}"
    );

    $replaceW = array(
        $project_name,
        $project_name,
        $config['project_description'],
        $config['project_slug'] . '-theme',
    );

    create_file_by_sample(array(
        'sample_file' => "wp-init-src/other/style.css",
        'create_file' => THEME_DIRECTORY . '/' . "style.css",
        'search_field' => $searchF,
        'replace_field' => $replaceW
    ));

}

function create_wp_config($config){

    exec('wget https://api.wordpress.org/secret-key/1.1/salt/ -q -O -', $secret_keys);

    $table_prefix = 'wp_' . PROJECT_SLUG_UNDERSCORE . '_' . substr(uniqid(), -5)  . '_';

    $searchF = array(
        "database_name_here",
        "username_here",
        "password_here",
        "{SECRET_KEYS}",
        "{TABLE_PREFIX}"
    );

    $replaceW = array(
        PROJECT_SLUG_UNDERSCORE,
        $config['db_user'],
        $config['db_password'],
        implode("\n", $secret_keys),
        $table_prefix
    );

    create_file_by_sample(array(
        'sample_file' => "wp-init-src/core/wp-config-sample.php",
        'create_file' => "wp-config.php",
        'search_field' => $searchF,
        'replace_field' => $replaceW
    ));

    create_file_by_sample(array(
        'sample_file' => "wp-init-src/core/wp-config-sample.php",
        'create_file' => "wp-config-example.php",
        'search_field' => $searchF,
        'replace_field' => $replaceW
    ));
}


function git_init(){
    system('git init');
    system('git add .');
    system('git commit -m "init"');
}

function create_flexible_template_sections_files($config)
{

    foreach ($config['flexible_templates'] as $template) {

        $section_style_files_include_str = '';

        //create style folder
        $style_folder_path = THEME_DIRECTORY . '/src/sass/' . $template['slug'];
        create_folder($style_folder_path);

        //create template folder
        $template_folder_path = THEME_DIRECTORY . '/template_parts/' . $template['slug'];
        create_folder($template_folder_path);

        //create template style file
        $template_style_file = fopen($style_folder_path . '/' . $template['slug'] . '.scss', 'w');

        //include template style file to main style
        $main_style_file = fopen(THEME_DIRECTORY . '/src/sass/style.scss', 'a');
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

            create_file_by_sample(array(
                'sample_file' => "wp-init-src/templates/section_sample.php",
                'create_file' => $template_folder_path . '/section_' . $section . '.php',
                'search_field' => $searchF,
                'replace_field' => $replaceW
            ));

            //create style files

            create_file_by_sample(array(
                'sample_file' => "wp-init-src/sass/section_sample.scss",
                'create_file' => $style_folder_path . '/section_' . $section . '.scss',
                'search_field' => $searchF,
                'replace_field' => $replaceW
            ));

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


        create_file_by_sample(array(
            'sample_file' => "wp-init-src/templates/flexible_template.php",
            'create_file' => THEME_DIRECTORY . '/' . $template['slug'] . '.php',
            'search_field' => $searchF,
            'replace_field' => $replaceW
        ));

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
        $taxonomy_slug_underscore = str_replace('-', '_', $taxonomy['slug']);

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
        $taxonomy_slug_underscore = str_replace('-', '_', $post_type['slug']);

        $post_type_path_string .= "\n" . 'include("post_types/register_post_type_' . $taxonomy_slug_underscore . '.php");';

    }

    $file = file_get_contents(THEME_DIRECTORY . "/core/core.php");
    $file = str_replace('// POST TYPES', $post_type_path_string, $file);
    file_put_contents(THEME_DIRECTORY . "/core/core.php", $file);

}

function create_taxonomies($config)
{

    if (!isset($config['taxonomies'])) {
        return;
    }

    foreach ($config['taxonomies'] as $taxonomy) {

        $taxonomy_slug_underscore = str_replace('-', '_', $taxonomy['slug']);

        $searchF = array(
            '{TAXONOMY_SLUG}',
            '{TAXONOMY_NAME}',
            '{TAXONOMY_SINGULAR_NAME}',
            '{ASSIGN_TO_POST_TYPE}',
            '{TAXONOMY_SLUG_UNDERSCORE}',
            '{TEXT_DOMAIN}'
        );

        $replaceW = array(
            $taxonomy['slug'],
            $taxonomy['name'],
            $taxonomy['singular_name'],
            $taxonomy['assign_to_post_type'],
            $taxonomy_slug_underscore,
            $config['project_slug'] . '-theme'
            
        );


        create_file_by_sample(array(
            'sample_file' => "wp-init-src/core/register_taxonomy.php",
            'create_file' => THEME_DIRECTORY . '/core/taxonomies/register_taxonomy_' . $taxonomy_slug_underscore . '.php',
            'search_field' => $searchF,
            'replace_field' => $replaceW
        ));

    }

    include_taxonomies_to_core($config);

}

function create_post_types($config)
{

    if (!isset($config['post_types'])) {
        return;
    }
    
    foreach ($config['post_types'] as $post_type) {

        $post_type_slug_underscore = str_replace('-', '_', $post_type['slug']);

        $searchF = array(
            '{POST_TYPE_SLUG}',
            '{POST_TYPE_NAME}',
            '{POST_TYPE_SINGULAR_NAME}',
            '{POST_TYPE_SLUG_UNDERSCORE}',
            '{TEXT_DOMAIN}'
        );

        $replaceW = array(
            $post_type['slug'],
            $post_type['name'],
            $post_type['singular_name'],
            $post_type_slug_underscore,
            $config['project_slug'] . '-theme'
        );

        create_file_by_sample(array(
            'sample_file' => "wp-init-src/core/register_post_type.php",
            'create_file' => THEME_DIRECTORY . '/core/post_types/register_post_type_' . $post_type_slug_underscore . '.php',
            'search_field' => $searchF,
            'replace_field' => $replaceW
        ));

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

    $theme_url = 'https://github.com/lemehovskiy/wp-starter-theme/archive/master.zip';

    if ($config['build_system'] == 'gulp') {
        $theme_url = 'https://github.com/lemehovskiy/wp-starter-theme/archive/gulp-version.zip';
    }

    //download
    system('curl -L -o wp-starter-theme.zip ' . $theme_url);

    //extract
    system('mkdir -p ' . THEME_DIRECTORY);
    system('tar -xvf wp-starter-theme.zip --strip 1 --directory ' . THEME_DIRECTORY);

    //remove archive
    system('rm wp-starter-theme.zip');

}


function create_project_folder($config)
{

    $path = '../' . $config['project_slug'];

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