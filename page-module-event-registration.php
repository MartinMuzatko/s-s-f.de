<?php namespace ProcessWire;
    $event = $this->wire->event;
    $attendee = $this->wire->attendee;
?>
<div class="content content--padded">
    <? if($event->isEventRunning()): ?>
        <p>Das Event läuft gerade. Sieh dir an wer teilnimmt.</p>
        <a href="" class="button button--secondary">Gästeliste</a>
    <? elseif($event->isEventOver()): ?>
        <p>Das Event ist bereits gelaufen. Schau dir an, wer dabei war.</p>
        <a href="" class="button button--secondary">Gästeliste</a>
    <? elseif($event->isRegistrationOpen() && !$event->isEventOver()):?>
        <p>Es sind bereits <strong><?=$event->getRegistrations()->count?></strong> von <strong><?=$event->ticketLimit?></strong> Plätzen reserviert.</p>
        <progress value="<?=str_replace(',','.',100*($event->getRegistrations()->count/$event->ticketLimit))?>" max="100"></progress><br>
        <? if($event->getRegistrationsPage()->endDate): ?>Die Registrierung schließt am <?=$event->getRegistrationsPage()->endDate?><?endif?>
        <? require('./partials/event/register.php')?>
    <? elseif(!$event->isRegistrationOpen() && !$event->isEventOver()): ?>
        <ssf-countdown to="<?=$event->getRegistrationsPage()->getUnformatted('startDate')?>000">
            <yield to="before">
                <p>
                    Die Registrierung ist noch nicht offen.
                    <? if($event->getRegistrationsPage()->startDate): ?>Sie offnet am <?=$event->getRegistrationsPage()->startDate?><?endif?>
                </p>
            </yield>
            <yield to="after">
                <div hidden ref="after">
                    Du kannst dich nun Registrieren.
                    <? require('./partials/event/register.php')?>
                </div>
            </yield>
        </ssf-countdown>
    <? endif ?>
</div>
