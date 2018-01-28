<?php namespace ProcessWire;
    $event = $this->wire->event;
    $registrations = $event->getRegistrations();
    $registrationsPage = $event->getRegistrationsPage();
    $attendeeStatus = 'accepted';
?>
<div class="content content--padded">
    <div class="card card--light" layout="column" layout-align="center center">
        <p><?=$registrations->find("attendeeStatus.title=$attendeeStatus")->count?> von <?=$event->ticketLimit?> Gäste beim Event</p>
        <ssf-gauge min="0" max="<?=$event->ticketLimit?>" value="<?=$registrations->find("attendeeStatus.title=$attendeeStatus")->count?>"></ssf-gauge>
    </div>
    <div layout="row">
        <? foreach($registrations as $registration): ?>
            <? if($registration->attendeeStatus->title == $attendeeStatus): ?>
                <?=$registration->render(['mode'=>'guestlist'])?>
            <? endif; ?>
        <? endforeach ?>
    </div>
</div>
