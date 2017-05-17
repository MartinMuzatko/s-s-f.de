<?php namespace API;

use \ProcessWire;

/**
 *
 */
class Events extends Resource
{

    const COLLECTION = '/events';

    function __construct($path)
    {
        parent::__construct($path);
        $this->events = $this->pages->get(self::COLLECTION);
    }

    public function listEvents($selector = '')
    {
        $events = $this->events->children($selector);
        return [
            "connections" => $this->getConnections($this->events),
            "data" => $this->getDefaultFields($this->events),
        ];
    }

    public function getEvent($event)
    {
        $eventPage = $this->events->child("title|name|id=$event");
        if ($eventPage instanceof ProcessWire\NullPage) {
            throw new \Exception('is not of type Page');
        }
        $data = [
            "page" => $eventPage,
            "ticketsBooked" => $eventPage->find('template=event-registration')->count,
            "registrations" => $this->getConnections($eventPage->get('template=event-registrations')),
            "connections" => $this->getConnections($eventPage),
            "data" => $this->getDefaultFields($eventPage),
        ];
        return (object) $data;
        //return $data;

    }

    public function createEvent()
    {
        $template = $this->pages->get('/events/resources/template');
        $template->of(false);
        $payload = $this->getPayload();
        if ($payload) {
            $events = $this->pages->get('/events');
            $pageName = $this->sanitizer->pageName($payload->title);
            if ($events->find("name=$pageName")->count) {
                http_response_code(409);
                return false;
            }
            try {
                $event = $this->pages->clone($template, $events);
                $event->of(false);
                $event->title = $this->sanitizer->text($payload->title);
                $event->name = $this->sanitizer->pageName($payload->title);
                $this->setFields($event, $payload);
                $event->save();
                http_response_code(201);
                return true;
            } catch (Exception $e) {
                return $e->getMessage();
            }
        }
    }
}
