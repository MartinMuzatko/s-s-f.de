<?php namespace ProcessWire;
header('Access-Control-Allow-Origin: *');
/* Allowed request method */
header("Access-Control-Allow-Methods: *");
/* Allowed custom header */
header("Access-Control-Allow-Headers: *");

include('vendor/autoload.php');

$homepage = $pages->get('/');
$assets = $config->urls->templates;
$author = $homepage->createdUser;
$favicon = $homepage->logo->size(128,128)->httpUrl;

include('_func.php');


if ($input->get->json) {
    include('json.php');
    die;
}

ob_start();
