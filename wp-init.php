<?php


function dowload_wp()
{
    $url = "https://wordpress.org/latest.zip";

    $path = 'filename.zip'; //address of local file
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $con = curl_exec($ch);
    curl_close($ch);
    file_put_contents($path, $con);
}


function extract_wp()
{
    system('tar -xvf filename.zip --strip 1');
}


function delete_base_themes()
{
    system('export GLOBIGNORE=wp-content/themes/index.php');
    system('rm -rf wp-content/themes/*');
    system('export GLOBIGNORE=');
}


function delete_base_plugins()
{
    system('GLOBIGNORE=*/index.php');
    system('rm -rf wp-content/plugins/*');
    system('unset GLOBIGNORE');
}


//dowload_wp();
extract_wp();
//delete_base_themes();
//delete_base_plugins();