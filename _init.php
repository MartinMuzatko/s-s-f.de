<?php namespace ProcessWire;
// header('Access-Control-Allow-Origin: *');
// /* Allowed request method */
// header("Access-Control-Allow-Methods: *");
// /* Allowed custom header */
// header("Access-Control-Allow-Headers: *");

include('vendor/autoload.php');

$eventsystemUser = $users->get('name=eventsystem');

$homepage = $pages->get('/');
$isHomepage = $homepage->id == $page->id;
$assets = $config->urls->templates;
$author = $homepage->createdUser;
$favicon = $homepage->logo->size(128,128)->httpUrl;
$events = new Events($this->wire);
$this->wire('events', $events, true);

$event = $page->is('event') ? $page : $page->parents('template=event')->get('template=event');
$isEvent = (bool) $event;
$isEventHome = $isEvent ? $event->id == $page->id : false;
if ($page->parents("template=event|event-list")->count) {
    $availableModules = 'module event';
} elseif ($page->parents("name=verein")) {
    $availableModules = 'module club';
} else {
    $availableModules = 'module';
}
// $event = $pages->find('template=event')->first; // use this to edit the templates for events


if($event instanceof Event) {
    $event->terms = $event->getPageByModule('event-terms');
    $this->wire('event', $event);
    $this->wire('attendee', $event->getRegisteredUser($user));
}

include('_func.php');