<?php namespace ProcessWire;
header('Access-Control-Allow-Origin: *');
/* Allowed request method */
header("Access-Control-Allow-Methods: *");
/* Allowed custom header */
header("Access-Control-Allow-Headers: *");

include('vendor/autoload.php');

//$x = new \API\User('aaa');

$homepage = $pages->get('/');
$assets = $config->urls->templates;
$author = $homepage->createdUser;

include('_func.php');


if ($input->get->json) {
    include('json.php');
    die;
}

ob_start();
