<?php namespace ProcessWire; ?>
<? foreach($events->getOwnEvents() as $event):?>
    <?=$event->userCan('event-registration-onstage')?>
    <?=$event->httpUrl.'reg-on-stage'?>
<? endforeach; ?>
<? if($event->userCan('event-registration-onstage')):?>
    Reg on stage!
<? endif; ?>

<? foreach($event->getRegistrations() as $registration):?>
    <?=$registration->profile->username?>
    <?=$registration->paid?>
    <?=$registration->items?>
<? endforeach; ?>