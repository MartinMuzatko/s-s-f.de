<?php namespace ProcessWire;

use \API\Events;
use \API\Response;
use \API\Event;
use \API\Request;
use \API\Router;
use \API\Route;

$router = new Router([

    // EVENTS

    new Route('GET', 'events', function($path){
        $events = new Events($path);
        return $events->listEvents();
    }),
    new Route('POST', 'events', function($path){
        $x = Request::getPayload();
        // var_dump($x);
        die;
        $events = new Events($path);
        return $events->createEvent();
    }),

        // EVENT
        new Route('GET', 'events/([\w-]+)', function($path, $eventName){
            $events = new Events($path);
            return $events->getEvent($eventName);
        }),
        new Route('PUT', 'events/([\w-]+)', function($path, $eventName){
            $events = new Events($path);
            $eventData = $events->getEvent($eventName);
            if ($eventData) {
                $event = new Event($eventData->page);
                return $event->updateEvent(Request::getPayload());
            }
            return false;
        }),

            // REGISTRATIONS
            new Route('GET', 'events/([\w-]+)/registrations', function($path, $eventName){
                $events = new Events($path);
                $event = new Event($events->getEvent($eventName)->page);
                $registrations = $event->getRegistrations();
                return $registrations;
            }),
            new Route('POST', 'events/([\w-]+)/registrations', function($path, $eventName){
            }),
            new Route('PUT', 'events/([\w-]+)/registrations', function($path, $eventName){
            }),
                // REGISTRATION
                new Route('GET', 'events/([\w-]+)/registrations/([\w-]+)', function($path, $eventName, $userName){

                }),
                new Route('PUT', 'events/([\w-]+)/registrations/([\w-]+)', function($path, $eventName, $userName){

                }),

            // ITEMS
            new Route('GET', 'events/([\w-]+)/items', function($path, $eventName){

            }),
            new Route('POST', 'events/([\w-]+)/items', function($path, $eventName){

            }),
                // ITEM
                new Route('GET', 'events/([\w-]+)/items/([\w-]+)', function($path, $eventName, $itemName){

                }),
                new Route('PUT', 'events/([\w-]+)/items/([\w-]+)', function($path, $eventName, $itemName){

                }),

            // TICKETS
            new Route('GET', 'events/([\w-]+)/tickets', function($path, $eventName){
                $events = new Events($path);
                $event = new Event($events->getEvent($event)->page);
                $registrations = $event->getRegistrations();
                return $registrations;
            }),
            new Route('POST', 'events/([\w-]+)/tickets', function($path, $eventName){

            }),
                // TICKET
                new Route('GET', 'events/([\w-]+)/tickets/([\w-]+)', function($path, $eventName, $ticketName){

                }),
                new Route('PUT', 'events/([\w-]+)/tickets/([\w-]+)', function($path, $eventName, $ticketName){

                }),

    // USERS
    new Route('GET', 'users', function($path){
        $users = new \API\Users();
        return $users->getUsers();

    }),
        // MESSAGES
        new Route('GET', 'users/([\w-]+)/messages', function($path, $user){
            //$user = $this->user;
            $mode = $this->input->get('mode');
            $user = $this->users->get("name=$user");
            // permissions check!
            $currentUser = $this->user;
            if ($mode == 'inbox') {
                $messages = $user->getMessages();
            } elseif ($mode == 'outbox') {
                $messages = $user->getSentMessages();
            } else {
                http_response_code(415);
                return ;
            }
            return array_map(
                function($message) {
                    return [
                        "id" => $message->id,
                        "title" => $message->title,
                        "text" => $message->text,
                        "sender" => $message->sender->name,
                        "receiver" => $message->receiver->name,
                        "created" => (int) $message->created.'000',
                        "read" => $message->read
                    ];
                },
                $messages->getArray()
            );
        }),
        new Route('GET', 'users/([\w-]+)/messages/(\d+)', function($path, $user, $messageId){
            $user = $this->users->get("name=$user");
            $message = $user->getMessageById($messageId);
            $message->of(false);
            $message->read = 1;
            $message->save();
            return [
                "id" => $message->id,
                "title" => $message->title,
                "text" => $message->text,
                "sender" => $message->sender->name,
                "receiver" => $message->receiver->name,
                "created" => (int) $message->created.'000',
                "read" => $message->read
            ];
        }),
        new Route('POST', 'users/([\w-]+)/messages', function($path, $receiver){
            //$user = $this->user;
            $resource = new \API\Resource($path);
            $data = $resource->getPayload();
            $sender = $this->user;
            try {
                $message = new Page();
                $message->parent = $this->pages->get('template=messages');
                $message->template = 'message';
                $message->title = $this->sanitizer->text($data->title);
                $message->text = $this->sanitizer->textarea(
                    $data->text,
                    ["allowableTags" => "<h2><h3><h4><h5><h6><pre><code><small><del><br><hr><cite><abbr><p><a><b><strong><i><em><u><sup><sub><ul><ol><li><dd><dl><dt><table><tr><td><tbody><thead><th><blockquote><q>"]
                );
                $message->receiver = $this->sanitizer->username($receiver);
                if (property_exists($data, 'sender')) {
                    $message->sender = $this->sanitizer->username($data->sender);
                } else {
                    $message->sender = $this->sanitizer->username($this->user->name);
                }
                $message->save();
                return $message->id;
            } catch (\Exception $e) {
                return false;
            }
        }),

    new Route('GET', 'sessions', function($path){
        $user = new \API\User();
        return $user->getActiveSessions();
    }),
    new Route('POST', 'session', function($path){
        $user = new \API\User();
        $credentials = $user->getPayload();
        return $user->login($credentials->username, $credentials->password);
    }),
    new Route('DELETE', 'session', function($path){
        $user = new \API\User();
        return $user->logout();
    }),
    new Route('GET', 'users/getSpecies', function($path){
        $users = new \API\Users();
        return $users->getAllSpecies();

    }),
]);
$data = true;


try {
    $data = $router->execute($input->urlSegmentStr);
    try {
        if (!$user->isLoggedIn()) {
            if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
                if (!$session->login($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'])) {
                    throw new \Exception('wrong credentials');
                }
            } else {
                throw new \Exception('not logged in');
            }
        }
    } finally {
        header('WWW-Authenticate: Basic realm="SüdStaaten Furs"');
        http_response_code(401);
    }
} catch (\Exception $e) {
    $data = false;
    if ($config->debug) {
        $data = [
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTrace()
        ];
    }
} finally {
    Response::setContentTypeJSON();
    echo json_encode($data);
}
die;

//example string:
// GET events
// GET events/ufurrya
// GET events/ufurrya/registrations
// $pages->get('/events/ufurrya/registrations')
// Events->getEvent('ufurrya')->getRegistration('misan')
/*  Resources
        Events
            GET - list
            POST - create
            Event
                GET - show
                PUT - update
                Registrations
                    GET - list
                    POST - create
                    Registration
                        GET - show
                        PUT - update


                Items
                Tickets
        Users


*/
// Events->get('ufurrya')
// GET events/ufurrya/registrations/misan
// POST events
// POST events/ufurrya/registrations
// POST events/ufurrya/tickets
// GET sessions - list active sessions
// POST sessions
// DELETE sessions
