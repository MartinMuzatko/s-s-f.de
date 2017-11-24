<?php namespace ProcessWire;

class Events extends PagesType {

    /**
     * Construct the Events manager for the given parent and template
     *
     * @param Template|int|string|array $templates Template object or array of template objects, names or IDs
     * @param int|Page|array $parents Parent ID or array of parent IDs (may also be Page or array of Page objects)
     */
    public function __construct(ProcessWire $wire, $templates = array(), $parents = array()) {
        parent::__construct($wire, $templates, $parents);

        // Make sure we always include the event template and /events/ parent page
        $this->addTemplates("event");
        $this->addParents($this->pages->get("/events/"));
        $this->setPageClass('Event');
    }

    public function getOpenEvents()
    {
        $pages = $this->getEvents()->getArray();
        $pages = array_filter($pages, function($page) {
            return $page->isRegistrationOpen();
        } );
        $events = new PageArray();
        $events->add($pages);
        return $events;
    }

    public function getEvents()
    {
        return parent::find('template=event, status!=hidden');
    }

    public function getOwnEvents()
    {
        $user = $this->wire('user');
        return $this->getEvents()->find("createdUser=$user");
    }

    /**
     * Return the API variable used for managing pages of this type
     *
     * #pw-internal
     *
     * @return Pages|PagesType
     *
     */
    public function getPagesManager() {
        return $this->wire('events');
    }

    /**
     * Custom method to find by date range
     *
     * @param int $start timestamp
     * @param int $end timestamp
     * @param string|null $additionalSelector (optional)
     * @return PageArray
     */
    public function findInDateRange($start, $end, $additionalSelector = null){
        // Build selector
        $selector = "endDate>=$start, (endDate<=$end), (startDate<=$end)";
        if($additionalSelector) $selector .= ", " . $additionalSelector;

        // Search only the available events with the selector
        return $this->find($selector);
    }

    public function updateEvent($page, $data)
    {
        $page->of(false);
        $page->title = $this->sanitizer->text($data->title);
        $page->name = str_replace('.', '', $this->sanitizer->pageName($data->title));
        $page->summary = $this->sanitizer->text($data->summary);
        $page->city = $this->sanitizer->text($data->city);
        $page->street = $this->sanitizer->text($data->street);
        $page->zip = $this->sanitizer->text($data->zip);
        //$event->country = $this->sanitizer->text($data->country);
        $page->ticketLimit = $this->sanitizer->text($data->ticketLimit);
        $page->sellPrice = $this->sanitizer->text($data->sellPrice);
        $page->startDate = $this->sanitizer->text($data->startDate);
        $page->endDate = $this->sanitizer->text($data->endDate);
        $page->save();
    }

    public function createEvent($data)
    {
        $template = $this->pages->get('/events/resources/template');
        if ($data) {
            $events = $this->pages->get('/events');
            $pageName = $this->sanitizer->pageName($data->title);
            if ($events->find("name=$pageName")->count) {
                http_response_code(409);
                return false;
            }
            try {
                $template->of(false);
                $event = $this->pages->clone($template, $events);
                $this->updateEvent($event, $data);
                if (property_exists($data, 'roles')) {
                    array_map(
                        function($role) use ($event) {
                            $event->addRole($role);
                        },
                        $data->roles
                    );
                }
                if (property_exists($data, 'items')) {
                    array_map(
                        function($item) use ($event) {
                            $event->addItem($item);
                        },
                        $data->items
                    );
                }
                if (property_exists($data, 'helpers')) {
                    array_map(
                        function($helper) use ($event) {
                            $event->addHelper($helper);
                        },
                        $data->helpers
                    );
                }
                if (property_exists($data, 'sponsorlevels')) {
                    array_map(
                        function($sponsorlevel) use ($event) {
                            $event->addSponsorlevel($sponsorlevel);
                        },
                        $data->sponsorlevels
                    );
                }
                if (property_exists($data, 'pages')) {
                    array_map(
                        function($page) use ($event) {
                            $event->addPage($page);
                        },
                        $data->pages
                    );
                }
                http_response_code(201);
                return $event->url;
            } catch (Exception $e) {
                return $e->getMessage();
            }
        }
    }
}
