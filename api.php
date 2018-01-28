<?php namespace ProcessWire;


use \API\Response;
use \API\Resource;
use \API\Request;
use \API\Router;
use \API\Route;

function getFieldValues($page, $fields) {
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
                $data = [
                    "template" => $page->template->name,
                    "id" => $page->id,
                ];
                $fields = getFieldValues($page, $page->fields->getArray());
                return array_merge($data, ...$fields);
            },
            $page->pageModules->getArray()
        );
        return $pages;
    }),
    new Route('GET', 'countries', function() {
        return $this->fields->get('name=country')->getInputfield($this->user)->options;
    }),
    new Route('GET', 'modules', function($path) {
        $modules = array_map(
            function($module) {
                return [
                    "title" => $module->title,
                    "moduleIcon" => $module->moduleIcon ? $module->moduleIcon->size(128,128)->url : '',
                    "placeholderImage" => $module->placeholderImage ? $module->placeholderImage->height(512)->url : '',
                    "summary" => $module->summary,
                    "associatedTemplate" => $module->associatedTemplate,
                    "templateType" => explode(' ', $this->templates->{$module->associatedTemplate}->tags),
                ];
            },
            $this->pages->get("template=page-modules")->children->getArray()
        );
        return $modules;
    }),
    new Route('GET', 'permissions', function($path) {
        $name = $this->input->get->name;
        $this->input->whitelist('name', $name);
        $permissions = array_map(
            function($permission) {
                return [
                    "name" => $permission->name,
                    "title" => $permission->title,
                ];
            },
            $this->permissions->find("name*=$name")->getArray()
        );
        return $permissions;
    }),
    new Route('GET', 'roles', function() {
        $roles = array_map(
            function($role) {
                return [
                    "title" => $role->title,
                    "summary" => $role->summary,
                    "image" => $role->image->first->url,
                ];
            },
            $this->pages->get('/events/resources/roles')->children->getArray()
        );
        return $roles;
    }),
    new Route('GET', 'notifications', function() {
        $notifications = array_map(
            function($notification) {
                return [
                    "title" => $notification->title,
                    "text" => $notification->text,
                    "trigger" => $notification->trigger->title
                ];
            },
            $this->pages->get('/events/resources/notifications')->children->getArray()
        );
        return $notifications;
    }),
    new Route('GET', 'items', function() {
        $items = array_map(
            function($item) {
                return [
                    "title" => $item->title,
                    "summary" => $item->summary,
                    "included" => $item->included,
                    "buyPrice" => $item->buyPrice,
                    "sellPrice" => $item->sellPrice,
                    "image" => $item->image->first->url,
                ];
            },
            $this->pages->get('/events/resources/items')->children->getArray()
        );
        return $items;
    }),
    new Route('GET', 'templates', function() {
        $templates = array_map(
            function($template) {
                return [
                    "title" => $template->title,
                    "summary" => $template->summary,
                    "image" => $template->image->first->url,
                    "pageModules" => array_map(
                        function($pagemodule) {
                            return array_merge(
                                call_user_func_array(
                                    'array_merge',
                                    getFieldValues($pagemodule, $pagemodule->fields->getArray())
                                ),
                                ["template" => $pagemodule->template->name]
                            );
                        },
                        $template->pageModules->getArray()
                    )
                ];
            },
            $this->pages->get('/events/resources/templates')->children->getArray()
        );
        return $templates;
    }),

    new Route('PUT', 'pages/([0-9]+)', function($path, $pageId) {
        $data = Request::getPayload();
        if (!is_array($data)) {
            return 'no data';
            http_response_code(415);
        }
        $page = $this->pages->get("id=$pageId");
        if (!$page instanceof Page) {
            http_response_code(415);
            return 'no page with ID found';
        }
        setPageModules($page, $data, $this->pages->get('template=page-contents'));
    }),



    new Route('GET', 'events', function($path) {
        return $this->events->listEvents();
    }),
    new Route('POST', 'events', function($path) {
        return $this->events->createEvent(Request::getPayload());
    }),

        // EVENT
        new Route('GET', 'events/([\w-]+)', function($path, $eventName) {
            return $this->events->get($eventName);
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
                
            new Route('GET', 'events/([\w-]+)/helpers', function($path, $eventName) {
                $event = $this->events->get("name=$eventName");
                $helpers = array_map(
                    function($helper) {
                        return [
                            "permissions" => array_map(
                                function($permission){
                                    return [
                                        "name" => $permission->name,
                                        "title" => $permission->title,
                                    ];
                                },
                                $helper->permissions->getArray()
                            ),
                            "profile" => [
                                "name" => $helper->profile->name,
                                "username" => $helper->profile->username,
                                "avatar" => $helper->profile->getAvatar(),
                            ],
                        ];
                    },
                    $event->getHelpers()->getArray()
                );
                return $helpers;
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
        return array_map(
            function($user) {
                return [
                    "username" => $user->username,
                    "name" => $user->name,
                    "avatar" => $user->avatar->size(32,32)->url,
                ];
            },
            $users->getArray()
        );
    }),
        new Route('GET', 'users/getAvatar', function($path) {
            $name = $this->input->get->name;
            $user = $this->users->get("username|name|email=$name");
            $user = $user instanceof NullPage ? $this->users->getGuestUser() : $user;
            return $user->getAvatar(256);
        }),

        new Route('POST', 'users/([\w-]+)/avatar', function($path, $username) {
            $user = $this->users->get("name=$username");
            $upload = new WireUpload('file');
            $upload->setMaxFiles(1);
            $upload->setOverwrite(true);
            $upload->setValidExtensions(['jpg','png', 'jpeg', 'svg', 'gif']);
            $upload->setDestinationPath($user->avatar->pagefiles->path);
            $upload->setAllowAjax(true);
            // $user->filesManager->createPath();
            // delete before uploading
            $user->of(false);
            $user->avatar->deleteAll();
            $user->save();
            $files = $upload->execute();
            if (count($files)) {
                // use first file
                $user->of(false);
                $user->avatar = $files[0];
                $user->save();
            } else {
                http_response_code(415);
            }
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
            $receiver = $this->users->get("name=$receiver");
            if (!$receiver instanceof NullPage) {
                $message = new WireData();
                $message->setArray([
                    'title' => $data->title,
                    'text' => $data->text,
                    'sender' => $data->sender,
                    'read' => $data->read,
                ]);
                return $receiver->sendMessage($message);
            }
            return false;
        }),

    new Route('GET', 'sessions', function($path) {
        $user = new \API\User();
        return $user->getActiveSessions();
    }),
    new Route('GET', 'users/getSpecies', function($path) {
        $nameFilter = $this->input->get->name;
		$limit = $this->input->get->limit ? $this->input->get->limit : 10;
		if ($nameFilter) {
			$species = $this->pages->get('/resources/species')->children("title^=$nameFilter, limit=$limit");
		} else {
            $species = $this->pages->get('/resources/species')->children("limit=$limit");
        }
        return array_map(
            function($specie) {
                return [
                    "title" => $specie->title
                ];
            },
            $species->getArray()
        );

    }),
]);
$data = true;
try {
    // If user is not logged in, attempt to login
    if (!$user->isLoggedIn()) {
        if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
            $session->login($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);
        }
    }
    $data = $router->execute($input->urlSegmentStr);
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
