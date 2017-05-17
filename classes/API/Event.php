<?php namespace API;

use \ProcessWire;

/**
 *
 */
class Event extends Resource
{
    public $event;

    public function __construct($page)
    {
        if ($page instanceof ProcessWire\Page) {
            $this->event = $page;
            $this->registrations = $page->child('template=event-registrations');
            $this->tickets = $page->child('template=event-tickets');
            $this->items = $page->child('template=event-items');
        } else {
            throw new \Exception('Event is not of type Page');
        }
    }

    public function isRegistrationOpen()
    {
        $startTimestamp = $this->registrations->getUnformatted('startDate');
        $endTimestamp = $this->registrations->getUnformatted('endDate');
        $isRegOpen = $this->registrations->isRegistrationOpen;
        $now = time();
        if (strlen($endTimestamp)) {
            return $now > $startTimestamp && $now < $endTimestamp;
        } elseif ($startTimestamp) {
            return $now > $startTimestamp;
        } else {
            return $isRegOpen;
        }
    }

    public function isRegistrationClosed()
    {
        return !$this->isRegistrationOpen();
    }

    public function getTicketsLeft()
    {
        return $this->event->ticketLimit - $this->getRegistrations()->count;
    }

    public function getRegistrations()
    {
        //return $this->registrations->children;
        $registrationsPage = $this->event->get('template=event-registrations');
        //$this->getDefaultFields($eventPage);
        $registrations = [];
        foreach ($registrationsPage->children() as $registration) {
            $registrations[$registration->profile->name] = $this->getDefaultFields($registration);
        }
        $data = [
            "page" => $registrationsPage,
            "connections" => $this->getConnections($registrationsPage),
            "registrations" => $registrations
        ];
        return array_merge($data, $this->getDefaultFields($registrationsPage));
        //return $data;
    }

    public function updateEvent($data)
    {
        if ($data && $this->user->hasPermission('event-manage')) {
            $this->event->of(false);
            $this->setFields($this->event, $data);
            if (property_exists($data,'title')) {
                $this->event->title = $this->sanitizer->text($data->title);
                $this->event->name = $this->sanitizer->pageName($data->title);
            }
            $this->event->save();
            return true;
        }
        return false;
    }

}
