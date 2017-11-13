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

	public function getAttendeesPerDay($filter = '')
	{
		$attendees = $this->getRegistrations($filter.', sort=created')->getArray();
	    $dates = [];
	    foreach ($attendees as $attendee) {
	        $dates[date('d.m.Y', $attendee->created)][] = $attendee->profile->username;
	    }
	    $registration = $this->getRegistrationsPage();
	    $startDate = $registration->getUnformatted('startDate');
	    $endDate = $registration->getUnformatted('endDate');
	    $period = new \DatePeriod(
	        (new \DateTime())->setTimestamp($startDate),
	        new \DateInterval('P1D'),
	        (new \DateTime())->setTimestamp($endDate)
	    );
	    $dateRange = [];
	    foreach ($period as $date) {
	        $dateRange[$date->format('d.m.Y')] = count($dates[$date->format('d.m.Y')]);
	    }
		return $dateRange;
	}

	public function getAddress()
	{
		//Format: Ostendstraße 20, 70190 Stuttgart, Germany
		return $this->street.', '.$this->zip.' '.$this->city.', '.$this->country->name;
	}

    public function getRegistrations($selector = '')
    {
        return $this->getRegistrationsPage()->find($selector);
    }

	public function getRegisteredUser($name)
	{
		return $this->getRegistrations("user=$name")->first;
	}
	
	public function getHelpersPage()
    {
        return $this->find("template=event-helpers")->first;
	}
	
	public function getHelpers($selector = '*')
    {
        return $this->getHelpersPage()->find($selector);
	}
	
	public function getSponsorlevelsPage()
	{
	    return $this->find("template=event-sponsorlevels")->first;
	}

	public function getSponsorlevels($selector = '*')
	{
	    return $this->getSponsorlevelsPage()->find($selector);
	}
	
	public function userCan($permission)
	{
		$user = $this->wire('user');
		if ($user->hasPermission('event-admin')) {
			return true;
		}
		$helper = $this->getHelpers("profile=$user")->first;
		return $helper ? $helper->permissions->has($permission) : false;
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

	public function isRegistrationOver()
	{
		$registrations = $this->getRegistrationsPage();
		$endTimestamp = $registrations->getUnformatted('endDate');
		if (strlen($endTimestamp)) {
			return time() > $endTimestamp;
		} else {
			return (bool) $registrations->isRegistrationOpen;
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
        if ($data && $this->user->hasPermission('event-user-manage')) {
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

	public function addHelper($data)
	{
		$item = new Page();
		$item->parent = $this->getHelpersPage();
		$item->template = $this->wire->templates->get('event-helper');
		$item->title = $this->wire->sanitizer->text($data->user->name);
		$username = $this->wire->sanitizer->username($data->user->name);
		$item->profile = $this->wire->users->get("name=$username");
		array_map(
			function($permission) use ($item) {
				if ($permission->active) {
					$name = $permission->name;
					$perm = $this->wire('permissions')->get("name=$name");
					$item->permissions->add($perm);
				}
			},
			(array) $data->permissions
		);
		$item->save();
	}

	public function getItems()
	{
		return $this->getItemsPage()->children;
	}

}
