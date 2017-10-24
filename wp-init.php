<?php


$init_settings = array(
    'project_name' => 'test_theme'
);


$base_themes = array(
    'wp-content/themes/twentyfifteen',
    'wp-content/themes/twentyseventeen',
    'wp-content/themes/twentysixteen'
);

$base_plugins = array(
    'wp-content/plugins/akismet',
    'wp-content/plugins/hello.php'
);


function dowload_wp()
{
    system('curl -L -o latest.zip https://wordpress.org/latest.zip');

}


function extract_wp()
{
    system('tar -xvf latest.zip --strip 1');
}

function delete_wp_archive()
{
    system('rm latest.zip');
}


function delete_base_themes($base_themes)
{
    foreach ($base_themes as $theme) {
        system('rm -rf ' . $theme);
    }
}


function delete_base_plugins($base_plugins)
{
    foreach ($base_plugins as $plugin) {
        system('rm -rf ' . $plugin);
    }
}

function download_starter_theme()
{
    system('curl -L -o wp-starter-theme.zip https://github.com/lemehovskiy/wp-starter-theme/archive/master.zip');
}

function extract_starter_theme($init_settings)
{
    system('mkdir -p wp-content/themes/' . $init_settings['project_name']);
    system('tar -xvf wp-starter-theme.zip --strip 1 --directory wp-content/themes/' . $init_settings['project_name']);
}

function delete_starter_theme_archive()
{
    system('rm wp-starter-theme.zip');
}


dowload_wp();
extract_wp();
delete_wp_archive();
delete_base_themes($base_themes);
delete_base_plugins($base_plugins);


download_starter_theme();
extract_starter_theme($init_settings);
delete_starter_theme_archive();