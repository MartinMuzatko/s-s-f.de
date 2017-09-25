<?php namespace ProcessWire;


class Event extends Page {

	public $registration;
	public $guestlist;

	public function __construct(Template $tpl = null) {
		//throw new Exception();
		if(is_null($tpl)) {
			$this->template = $this->wire('templates')->get('event');
		}
		if(!$this->parent_id) $this->set('parent_id', $this->wire->pages->get('/events/')->id);
        parent::__construct($tpl);
	}

    public function getRegistrations($selector = '')
    {
        return $this->getRegistrationsPage()->find($selector);
    }

	public function getRegisteredUser($name)
	{
		return $this->getRegistrations("user=$name")->first;
	}

    public function getRegistrationsPage()
    {
        return $this->find("template=event-registrations")->first;
    }

	public function getPageByModule($module)
	{
		return $this->get("template=page, pageModules*=page-module-$module");
	}

	public function hasRegistrationPage()
	{
		return $this->getPageByModule('event-registration') instanceof NullPage;
	}

	public function hasGuestlistPage()
	{
		return $this->getPageByModule('event-guestlist') instanceof NullPage;
	}

	public function getItemsPage()
	{
		return $this->find("template=event-items")->first;
	}

	public function isUserRegistered($user)
	{
		return (bool) $this->getRegistrations("profile=$user")->count;
	}

    public function registerUser($user, $data = [])
    {
		$items = is_array($data->items) ? array_keys($data->items) : [];
        if (!$this->isUserRegistered($user)) {
			$attendee = new Page();
			$attendee->parent = $this->getRegistrationsPage();
			$attendee->template = $this->wire->templates->get('event-registration');
			$attendee->title = $user->name;
			array_map(
				function($item) use ($attendee) {
					$attendee->items->add($this->getItemsPage()->get("name=badge"));
				},
				$items
			);
			$attendee->profile = $user;
			$attendee->save();
			return $attendee;
		} else {
			return false;
		}
    }

	public function unregisterUser($user)
	{
		if ($this->isUserRegistered($user)) {
			$this->getRegisteredUser($user)->delete();
		}
	}

	public function isEventRunning()
	{
		$startTimestamp = $this->getUnformatted('startDate');
		$endTimestamp = $this->getUnformatted('endDate');
		$now = time();
		return $now > $startTimestamp && $now < $endTimestamp;
	}

	public function isEventOver()
	{
		return time() > $this->getUnformatted('endDate');
	}

    public function isRegistrationOpen()
    {
        $registrations = $this->getRegistrationsPage();
        $startTimestamp = $registrations->getUnformatted('startDate');
        $endTimestamp = $registrations->getUnformatted('endDate');
        $isRegOpen = $registrations->isRegistrationOpen;
        $now = time();
        if (strlen($endTimestamp)) {
            return $now > $startTimestamp && $now < $endTimestamp;
        } elseif ($startTimestamp) {
            return $now > $startTimestamp;
        } else {
            return (bool) $isRegOpen;
        }
    }

    public function isRegistrationClosed()
    {
        return !$this->isRegistrationOpen();
    }

    public function getTicketsLeft()
    {
        return $this->ticketLimit - $this->getRegistrations()->count();
    }

    public function updateEvent($data)
    {
        if ($data && $this->user->hasPermission('event-manage')) {
            $this->of(false);
            $this->setFields($this, $data);
            if (property_exists($data,'title')) {
                $this->title = $this->sanitizer->text($data->title);
                $this->name = $this->sanitizer->pageName($data->title);
            }
            $this->save();
            return true;
        }
        return false;
    }

	public function addItem($data)
	{
		$item = new Page();
		$item->parent = $this->getItemsPage();
		$item->template = $this->wire->templates->get('event-item');
		$item->title = $this->wire->sanitizer->text($data->name);
		$item->sellPrice = $this->wire->sanitizer->text($data->sellPrice);
		$item->buyPrice = $this->wire->sanitizer->text($data->buyPrice);
		$item->save();
	}

	public function getItems()
	{
		return $this->getItemsPage()->children;
	}

}
