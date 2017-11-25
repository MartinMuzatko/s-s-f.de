<?php namespace ProcessWire;?>
<div class="content content--padded">
    <h2>Gästeliste</h2>
    <?
        $event = $this->wire->event;
        $registrations = $event->getRegistrations();
        $registrationsPage = $event->getRegistrationsPage();
        $attendeeStatus = 'accepted';
    ?>
    <?=$event->ticketLimit?>
    <?=$registrations->find("attendeeStatus.title=$attendeeStatus")->count?>
    <ssf-gauge min="0" max="<?=$event->ticketLimit?>" value="<?=$registrations->find("attendeeStatus.title=$attendeeStatus")->count?>"></ssf-gauge>
    
    <? foreach($registrations as $registration): ?>
        <? if($registration->attendeeStatus->title == $attendeeStatus): ?>
            <?=$registration->render(['mode'=>'guestlist'])?>
        <? endif; ?>
    <? endforeach ?>
</div>
