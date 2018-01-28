<?php namespace ProcessWire;


class Event extends Page {

	public $registration;
	public $guestlist;

	public function __construct(Template $tpl = null) {
		//throw new Exception();
		if(is_null($tpl)) {
			$this->template = $this->wire->templates->get('event');
		}
		if(!$this->parent_id) $this->set('parent_id', $this->wire->pages->get('/events/')->id);
        parent::__construct($tpl);
	}

	public function getAttendeesPerDay($filter = '', $timeField = 'created')
	{
		$attendees = $this->getRegistrations($filter.', sort=created')->getArray();
	    $dates = [];
	    foreach ($attendees as $attendee) {
	        $dates[date('d.m.Y', $attendee->{$timeField})][] = $attendee->profile->username;
	    }
	    $registration = $this->getRegistrationsPage();
	    $startDate = $registration->getUnformatted('startDate');
		$endDate = $registration->getUnformatted('endDate');
		if (time() < $endDate) {
			$endDate = time();
		}
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
	
	public function userCan($permission)
	{
		$user = $this->wire->user;
		if ($user->hasPermission('event-admin')) {
			return true;
		}
		$helper = $this->getHelpers("profile=$user")->first;
		return $helper ? $helper->permissions->has($permission) : false;
	}

	public function getPageByModule($module)
	{
		return $this->get("template=page, include=all, pageModules*=page-module-$module");
	}

	public function hasRegistrationPage()
	{
		return $this->getPageByModule('event-registration') instanceof Page;
	}

	public function hasGuestlistPage()
	{
		return $this->getPageByModule('event-guestlist') instanceof Page;
	}

	public function getItemsPage()
	{
		return $this->find("template=event-items")->first;
	}
	
	public function getRolesPage()
	{
		return $this->find("template=event-roles")->first;
	}

	public function isUserRegistered($user)
	{
		return $this->getRegisteredUser($user) instanceof Page;
	}

    public function registerUser($user, $data = [])
    {
		// $user = $this->users->find('')->getRandom();
		$items = is_array($data->items) ? array_keys($data->items) : [];
		$roles = is_array($data->roles) ? array_keys($data->roles) : [];
        if (!$this->isUserRegistered($user)) {
			$attendee = new Page();
			$attendee->parent = $this->getRegistrationsPage();
			$attendee->template = $this->wire->templates->get('event-registration');
			$attendee->title = $user->name;
			$attendee->donation = min(max(floatval($data->donation), 0), 10000);
			$attendee->comment = $this->wire->sanitizer->text($data->comment);
			$attendee->isInvisible = !$data->isVisible;
			array_map(
				function($item) use ($attendee) {
					$attendee->items->add($this->getItemsPage()->get("name=$item"));
				},
				$items
			);
			array_map(
				function($role) use ($attendee) {
					$attendee->attendeeRoles->add($this->getRolesPage()->get("name=$role"));
				},
				$roles
			);
			$attendee->profile = $user;
			// if ($user->name != 'misan') {
				$attendee->save();
			// }
			// Send notification if there is a hook
			$attendee->profile->conFee = $this->getAttendeePaymentSum($attendee->profile);
			$context = new WireData();
			$context->setArray([
				'event' => $this,
				'user' => $attendee->profile
			]);
			$eventsystemUser = \ProcessWire\Wire('users')->get('name=eventsystem');
			array_map(
				function($notification) use ($attendee, $context, $eventsystemUser) {
					sendNotification($notification, $attendee->profile, $context);
					// send copy to eventsystem
					sendNotification($notification, $eventsystemUser, $context);
				},
				$this->getNotifications("trigger=UserCreatedInEvent")->getArray()
			);

			return $attendee;
		} else {
			return false;
		}
	}

	public function setAttendeeState($user, $status)
	{
		if ($this->isUserRegistered($user)) {
			$attendee = $this->getRegisteredUser($user);
			if($attendee->attendeeStatus->title != $status) {
				$attendee->of(false);
				$attendee->attendeeStatus = $status;
				if ($status == 'accepted') {
					$attendee->paid = time();
				}
				$attendee->save();

				$triggerStatus = ucfirst($status);
				$attendee->profile->conFee = $this->getAttendeePaymentSum($attendee->profile);
				$context = new WireData();
				$context->setArray([
					'event' => $this,
					'user' => $attendee->profile
				]);
				array_map(
					function($notification) use ($attendee, $context) {
						sendNotification($notification, $attendee->profile, $context);
					},
					$this->getNotifications("trigger=statusChanged$triggerStatus")->getArray()
				);
				return true;
			}
		}
		return false;
	}

	public function isUserOldEnoughAtEventStartDate($user)
	{
		$minAge = $this->getRegistrationsPage()->minimumAge;
    	return $user->birthdate < strtotime("-$minAge year", $this->getUnformatted('startDate'));
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
		if ($this->isEventOver()) return false;
        $registrations = $this->getRegistrationsPage();
        $startTimestamp = $registrations->getUnformatted('startDate');
        $endTimestamp = $registrations->getUnformatted('endDate');
        $isRegOpen = $registrations->isRegistrationOpen;
        $now = time();
        if ((bool)$isRegOpen) {
			return true;
		} elseif (strlen($endTimestamp)) {
			return $now > $startTimestamp && $now < $endTimestamp;
        } elseif ($startTimestamp) {
			return $now > $startTimestamp;
        } else {
			return false;
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
	

	public function getRegistrationsPage()
	{
		return $this->find("template=event-registrations")->first;
	}

    public function getRegistrations($selector = '')
    {
        return $this->getRegistrationsPage()->find($selector);
    }

	public function getRegisteredUser($name)
	{
		return $this->getRegistrations("profile=$name")->first;
	}
	
	public function getAttendeePaymentSum($name) {
		$attendee = $this->getRegisteredUser($name);
		$sum = array_sum([
			array_sum(
				array_map(function($item) {
					return $item->sellPrice;
				}, $attendee->items->getArray())
			),
			$attendee->donation,
			$this->sellPrice
		]);
		$sponsorlevel = $this->getSponsorLevelForMoney($attendee->donation);
		if ($sponsorlevel) {
			$sum = $sum - array_sum(
				array_map(function($item) {
					return $item->sellPrice;
				}, $this->getSponsorLevelForMoney($attendee->donation)->items->getArray())
			);
		}
		return $sum;
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

	public function getSponsorLevelForMoney($money) {
		return $this->getSponsorlevels("sort=-buyPrice, buyPrice<=$money")->first();
	}

	public function getSponsorlevels($selector = '*')
	{
	    return $this->getSponsorlevelsPage()->find($selector);
	}

	public function getNotificationsPage()
	{
		return $this->find("template=notifications")->first;
	}

	public function getNotifications($selector = '*')
	{
		return $this->getNotificationsPage()->find($selector);
	}

	public function addItem($data)
	{
		$item = new Page();
		$item->parent = $this->getItemsPage();
		$item->template = $this->wire->templates->get('event-item');
		$item->title = $this->wire->sanitizer->text($data->title);
		$item->save();
		$item->sellPrice = $this->wire->sanitizer->text($data->sellPrice);
		$item->buyPrice = $this->wire->sanitizer->text($data->buyPrice);
		$item->image->add('http://'.$this->wire->config->httpHost.$data->image);
		$item->save();
	}
	
	public function addRole($data)
	{
		$role = new Page();
		$role->parent = $this->getRolesPage();
		$role->template = $this->wire->templates->get('event-role');
		$role->title = $this->wire->sanitizer->text($data->title);
		$role->save();
		$role->summary = $this->wire->sanitizer->text($data->summary);
		$role->image->add('http://'.$this->wire->config->httpHost.$data->image);
		$role->save();
	}
	
	public function addSponsorlevel($data)
	{
		$sponsorlevel = new Page();
		$sponsorlevel->parent = $this->getSponsorlevelsPage();
		$sponsorlevel->template = $this->wire->templates->get('event-sponsorlevel');
		$sponsorlevel->title = $this->wire->sanitizer->text($data->name);
		$sponsorlevel->minPrice = $this->wire->sanitizer->text($data->minPrice);
		$sponsorlevel->summary = $this->wire->sanitizer->text($data->summary);
		// $item->image = $this->wire->sanitizer->text($data->image);
		$sponsorlevel->save();
	}

	public function addHelper($data)
	{
		$helper = new Page();
		$helper->parent = $this->getHelpersPage();
		$helper->template = $this->wire->templates->get('event-helper');
		$helper->title = $this->wire->sanitizer->text($data->user->name);
		$username = $this->wire->sanitizer->username($data->user->name);
		$helper->profile = $this->wire->users->get("name=$username");
		array_map(
			function($permission) use ($helper) {
				if ($permission->active) {
					$name = $permission->name;
					$perm = $this->wire->permissions->get("name=$name");
					$helper->permissions->add($perm);
				}
			},
			(array) $data->permissions
		);
		$helper->save();
	}
	
	public function addPage($data)
	{
		$page = new Page();
		$page->parent = $this;
		$page->template = $this->wire->templates->get('page');
		$page->title = $this->wire->sanitizer->text($data->title);
        setPageModules($page, $data->pageModules);
		$page->save();
	}

	public function addNotification($data)
	{
		$notification = new Page();
		$notification->parent = $this->getNotificationsPage();
		$notification->template = $this->wire->templates->get('notification');
		$notification->title = $this->wire->sanitizer->text($data->title);
		$notification->text = $this->wire->sanitizer->text($data->text);
		$notification->trigger = $this->wire->sanitizer->text($data->trigger);
		$notification->save();
	}

	public function getItems()
	{
		return $this->getItemsPage()->children;
	}
	
	public function getRoles()
	{
		return $this->getRolesPage()->children;
	}

}
