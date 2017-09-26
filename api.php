<?php namespace ProcessWire;

use \API\Response;
use \API\Request;
use \API\Router;
use \API\Route;

function getFieldValues($page, $fields)
{
    return array_map(
        function($field) use ($page) {
            if ($field->type instanceof FieldtypeImage) {
                $image = $page->{$field->name};
                if($image->count) {
                    $image = $image instanceof Pageimages ? $image->first->httpUrl : $image->httpUrl;
                } else {
                    $image = '';
                }
                return [$field->name => $image];
            }
            if ($field->type instanceof FieldtypeRepeater) {
                $repeaterPages = $page->{$field->name};
                $repeaterFields = array_map(
                    function($repeaterPage) {
                        $fields = getFieldValues($repeaterPage, $repeaterPage->fields->getArray());
                        return call_user_func_array('array_merge', $fields);
                    },
                    $repeaterPages->getArray()
                );
                return [$field->name => $repeaterFields];
            }
            return [$field->name => htmlentities($page->{$field->name})];
        },
        $fields
    );
}

$router = new Router([

    new Route('GET', 'pages/([0-9]+)', function($path, $pageId) {
        $page = $this->pages->get("id=$pageId");
        $pages = array_map(
            function($page) {
                $data = ["template" => $page->template->name];
                $fields = getFieldValues($page, $page->fields->getArray());
                return array_merge($data, ...$fields);
            },
            $page->pageModules->getArray()
        );
        return $pages;
    }),
    new Route('GET', 'modules', function($path) {
        $modules = array_map(
            function($module) {
                return [
                    "title" => $module->title,
                    "moduleIcon" => $module->moduleIcon ? $module->moduleIcon->size(128,128)->httpUrl : '',
                    "summary" => $module->summary,
                    "associatedTemplate" => $module->associatedTemplate,
                    "templateType" => explode(' ', $this->templates->{$module->associatedTemplate}->tags),
                ];
            },
            $this->pages->get("template=page-modules")->children->getArray()
        );
        return $modules;
    }),


    new Route('POST', 'pages/([0-9]+)', function($path, $pageId) {
        $data = Request::getPayload();
        if (!$data) {
            return 'no data';
            http_response_code(415);
        }
        $page = $this->pages->get("id=$pageId");
        $contentPage = $this->pages->get('template=page-contents');
        if (!$page instanceof Page) {
            return 'no page with ID found';
            http_response_code(415);
        }
        $module = new Page();
        $module->parent = $contentPage;
        $module->of(false);
        $module->title = $page->title.'_'.$data->template;
        $validTemplates = [''];
        $module->template = $data->template;
        array_map(
            function($key, $value) use ($module) {
                //get_class($module->{$key});
                $module->{$key} = html_entity_decode($value);
            },
            array_keys((array) $data),
            (array) $data
        );
        $module->save();
        $page->of(false);
        $page->pageModules->add($module);
        $page->save();
    }),

    new Route('PUT', 'pages/([0-9]+)', function($path, $pageId) {
        $data = Request::getPayload();
        if (!is_array($data)) {
            return 'no data';
            http_response_code(415);
        }
        $page = $this->pages->get("id=$pageId");
        $contentPage = $this->pages->get('template=page-contents');
        if (!$page instanceof Page) {
            return 'no page with ID found';
            http_response_code(415);
        }
        $page->of(false);
        $pages = $page->pageModules;
        //$page->pageModules->removeAll();
        foreach ($pages as $pageModule) {
            $pageModule->delete(true);
        }
        // page deletion has to be made as a second call BEFORE creating page with same name again.
        $page->save();

        function setFieldValues($data, $page)
        {
            return array_map(
                function($key, $value) use ($page) {
                    if ($page->{$key} instanceof RepeaterPageArray) {
                        $page->save();
                        $repeaterItem = $page->{$key}->getNew();
                        //$repeaterItem->of(false);
                        array_map(function($val) use ($repeaterItem) {
                            setFieldValues($val, $repeaterItem);
                        }, $value);
                        //$repeaterItem->parent = $page;
                        $repeaterItem->save();
                        $page->{$key}->add($repeaterItem);
                        //$page->
                    } else {
                        $page->{$key} = html_entity_decode($value);
                    }
                },
                array_keys((array) $data),
                (array) $data
            );
        }

        foreach ($data as $moduleData) {
            $module = new Page();
            $module->parent = $contentPage;
            $module->of(false);
            $validTemplates = [''];
            $module->template = $moduleData->template;
            $module->title = $page->title.'_'.$moduleData->template;
            $module->save();
            $page->pageModules->add($module);
            $page->save();
            setFieldValues($moduleData, $module);
            $module->save();
        }
        $page->save();
    }),

    new Route('POST', 'test', function($path) {
        \TD::dump($this->pages->get('/')->logo->pagefiles->path());
        die;
        $x = new WireUpload('image');
        $x->setMaxFiles(1);
        $x->setOverwrite(false);
        $x->setValidExtensions(['jpg','png', 'jpeg', 'svg', 'pdf']);
        $x->setDestinationPath('/Users/martinmuzatko/dev/http/s-s-f.de/site/assets/files/');
        $y = $x->execute();
        var_dump($y);
        //var_dump($this->wire->input->post);
    }),

    new Route('GET', 'events', function($path) {
        return $this->events->listEvents();
    }),
    new Route('POST', 'events', function($path) {
        return $this->events->createEvent(Request::getPayload());
    }),

        // EVENT
        new Route('GET', 'events/([\w-]+)', function($path, $eventName) {

            return $this->events->getEvent($eventName);
        }),
        new Route('PUT', 'events/([\w-]+)', function($path, $eventName) {
            $eventData = $this->events->getEvent($eventName);
            if ($eventData) {
                $event = new \API\Event($eventData->page);
                return $event->updateEvent(Request::getPayload());
            }
            return false;
        }),

            // REGISTRATIONS
            new Route('GET', 'events/([\w-]+)/registrations', function($path, $eventName) {
                $events = new \API\Events($path);
                $event = new \API\Event($events->getEvent($eventName)->page);
                $registrations = $event->getRegistrations();
                return $registrations;
            }),
                // REGISTRATION
                new Route('POST', 'events/([\w-]+)/registrations/([\w-]+)', function($path, $eventName, $user) {
                    $user = $this->users->get("name=$user");
                    if ($user instanceof NullPage) {
                        return false;
                    }
                    $this->wire->events->get("name=$eventName")->registerUser($user, $this->input->post);
                }),
                new Route('GET', 'events/([\w-]+)/registrations/([\w-]+)', function($path, $eventName, $userName) {

                }),
                new Route('PUT', 'events/([\w-]+)/registrations/([\w-]+)', function($path, $eventName, $userName) {
                    $user = $this->users->get("name=$user");
                    if ($user instanceof NullPage) {
                        return false;
                    }
                    //$this->wire->events->get("name=$eventName")->unregisterUser($user);
                }),
                new Route('DELETE', 'events/([\w-]+)/registrations/([\w-]+)', function($path, $eventName, $userName) {
                    $user = $this->users->get("name=$user");
                    if ($user instanceof NullPage) {
                        return false;
                    }
                    $this->wire->events->get("name=$eventName")->unregisterUser($user);
                }),

            // ITEMS
            new Route('GET', 'events/([\w-]+)/items', function($path, $eventName) {

            }),
            new Route('POST', 'events/([\w-]+)/items', function($path, $eventName) {

            }),
                // ITEM
                new Route('GET', 'events/([\w-]+)/items/([\w-]+)', function($path, $eventName, $itemName) {

                }),
                new Route('PUT', 'events/([\w-]+)/items/([\w-]+)', function($path, $eventName, $itemName) {

                }),

    // USERS
    new Route('GET', 'users', function($path) {
        $nameFilter = $this->input->get->name;
		$limit = $this->input->get->limit ? $this->input->get->limit : 10;
		if ($nameFilter) {
			$users = $this->users->find("username^=$nameFilter, limit=$limit");
		} else {
            $users = $this->users->find("limit=$limit");
        }
        return array_map(function($user) {return ["username"=>$user->username, "avatar"=>$user->avatar->size(32,32)->url];}, $users->getArray());
    }),
        new Route('GET', 'users/getAvatar', function($path) {
            $name = $this->input->get->name;
            $user = $this->users->get("username|name|email=$name");
            $user = $user instanceof NullPage ? $this->users->getGuestUser() : $user;
            return $user->getAvatar(256);
        }),
        // MESSAGES
        new Route('GET', 'users/([\w-]+)/messages', function($path, $user) {
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
        // GET A SINGLE MESSAGE
        new Route('GET', 'users/([\w-]+)/messages/(\d+)', function($path, $user, $messageId) {
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
        // WRITE A MESSAGE
        new Route('POST', 'users/([\w-]+)/messages', function($path, $receiver) {
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

    new Route('GET', 'sessions', function($path) {
        $user = new \API\User();
        return $user->getActiveSessions();
    }),
    new Route('POST', 'session', function($path) {
        $user = new \API\User();
        $credentials = $user->getPayload();
        return $user->login($credentials->username, $credentials->password);
    }),
    new Route('DELETE', 'session', function($path) {
        $user = new \API\User();
        return $user->logout();
    }),
    new Route('GET', 'users/getSpecies', function($path) {
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
    } catch(\Exception $e) {
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
