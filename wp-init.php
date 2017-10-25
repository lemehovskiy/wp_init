<?php

$options = getopt("f:i::");


$config_json = file_get_contents("wp-init-config.json");

$config = json_decode($config_json, true);

if (isset($options['f'])) {
    $config['project_name'] = $options['f'];

    create_project_folder($config);

}
else if (isset($options['i'])) {
    dowload_wp();
    extract_wp();
    delete_wp_archive();

    remove_files($options);

    download_starter_theme();
    extract_starter_theme($config);
    delete_starter_theme_archive();

    install_plugins($config);
}

else {


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


function remove_files($config)
{
    foreach ($config['remove_files'] as $file) {
        system('rm -rf ' . $file);
    }
}



function download_starter_theme()
{
    system('curl -L -o wp-starter-theme.zip https://github.com/lemehovskiy/wp-starter-theme/archive/master.zip');
}

function extract_starter_theme($config)
{
    system('mkdir -p wp-content/themes/' . $config['project_name']);
    system('tar -xvf wp-starter-theme.zip --strip 1 --directory wp-content/themes/' . $config['project_name']);
}

function delete_starter_theme_archive()
{
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