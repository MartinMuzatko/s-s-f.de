<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

try {
    if (!$user->isLoggedIn()) {
        if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
            if (!$session->login($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'])) {
                throw new Exception;
            }
        } else {
            throw new Exception;
        }
    }
} catch (Exception $e) {
    header('WWW-Authenticate: Basic realm="Happy-CSS"');
    http_response_code(401);
    die;
}


$method = $_SERVER['REQUEST_METHOD'];
//$json = ['message'=>'NONE'];
//$request = explode("/", substr(@$_SERVER['PATH_INFO'], 1));


switch ($method) {
    case 'POST':
        $pageFields = json_decode(file_get_contents('php://input'));


        // if page exists:
        // http_response_code(409); - conflict

        // create new page, if not already there
        //$p = new Page();
        // content-text
        // content-code
        // content-cta
        // content-media
        // content-preview
        // $p->template = 'content-';
        // $p->parent = $page;
        // $p->score_name = $scoreName;
        // $p->score = $score;
        //$p->of(false); // turns off output formatting
        //$p->title = 'score';
        //$p->save();

        http_response_code(201);
        $json = ['id'=>$id];
    break;
    case 'PUT':
        if($user->hasPermission('page-edit', $page)) {
            $pageFields = json_decode(file_get_contents('php://input'));
            $page->of(false);
            // foreach ($pageFields as $field => $value) {
            //     echo $field . " " . $value."\n";
            //     $page->$field = $value;
            //     //$json[$field] = [$page->${field}];
            //
            // }
            $page->set('published','1486297829');
            $page->published = '1486297829';
            $page->save();
            die;

            $json = ['message'=>'RESOURCE UPDATED'];
            http_response_code(201);
        } else {
            $json = ['message'=>'NOACCESS'];
        }
    break;
    case 'GET':
        $json = [];
        $connections = [];
        foreach ($page->children as $key => $child) {
            $connections[$child->id] = $child->httpUrl;
        }
        $additionalFields = [
            'created' => $page->created.'000',
            'published' => $page->published.'000',
            'modified' => $page->modified.'000',
            'createdUser' => $page->createdUser->name,
            'modifiedUser' => $page->modifiedUser->name,
            'parent' => (string) $page->parent,
            'template' => $page->template->name
        ];
        $json['children'] = $connections;
        foreach ($page->fields as $field) {
            $json[$field->name] = [
                'value' => htmlentities($page->{$field->name}),
                'field' => (array) $page->{$field->name}
            ];
        }
        foreach ($additionalFields as $field => $value) {
            $json[$field] = [
                'value' => $value
            ];
        }
    break;
    default:
        //handle_error($request);
    break;
}

// ob_clean();
// access from somewhere else than the origin?
// header('Access-Control-Allow-Origin: https://martinmuzatko.github.io');
// header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE,OPTIONS");
// header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
// http_response_code(200);

// only set content-type when successfull, otherwise we set html so we can still read errors WHILE developing
header("Content-Type: application/json");
echo json_encode($json);
