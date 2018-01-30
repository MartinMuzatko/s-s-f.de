<?php namespace ProcessWire;
    $event = $this->wire->event;
    $attendee = $this->wire->attendee;
?>

<div class="content content--padded">
    <? if($event->isEventRunning()): ?>
        <div layout="column" layout-align="center center">
            <p><?=__('Das Event läuft gerade. Sieh dir an wer teilnimmt.')?></p>
            <a href="" class="button button--secondary"><?=__('Gästeliste')?></a>
        </div>
    <? elseif($event->isEventOver()): ?>
        <div layout="column" layout-align="center center">
            <p><?=__('Das Event ist bereits vorbei. Aber das nächste ist sicher nicht fern.')?></p>
        </div>
        <? if($event->isUserRegistered($user)): ?>
            <div layout="row" layout-align="center">
                <div flex="100" flex-gt-sm="80" flex-gt-md="70" flex-gt-lg="60" class="card card--light content--padded">
                    <h2><?=__('Wie hat es dir bei uns gefallen?')?></h2>
                    <?=__('Dem Veranstalter des Events ist es wichtig einen guten Überblick über die Zufriedenheit der Besucher zu erlangen.')?>
                    <ssf-rating></ssf-rating>
                    <a href="" class="button button--primary"><?=__('Meinung Absenden')?></a>
                </div>
            </div>
        <? endif ?>
        <? elseif($event->isRegistrationOpen() && !$event->isEventOver()):?>
        <div layout="column" layout-align="center center" class="content--padded">
            <p>Es sind bereits <strong><?=$event->getRegistrations()->count?></strong> von <strong><?=$event->ticketLimit?></strong> Plätzen reserviert.</p>
            <progress value="<?=str_replace(',','.',100*($event->getRegistrations()->count/$event->ticketLimit))?>" max="100"></progress><br>
            <? if($event->getRegistrationsPage()->endDate): ?>Die Registrierung schließt am <?=$event->getRegistrationsPage()->endDate?><?endif?>
        </div>
        <h3><?=__('Du registrierst dich mit diesen Daten:')?></h3>
        <? require('./partials/event/register.php')?>
    <? elseif(!$event->isRegistrationOpen() && !$event->isEventOver()): ?>
        <ssf-countdown to="<?=$event->getRegistrationsPage()->getUnformatted('startDate')?>000">
            <yield to="before">
                <p>
                    <?=__('Die Registrierung ist momentan geschlossen.')?>
                    <? if($event->getRegistrationsPage()->startDate): ?>Sie öffnet am <?=$event->getRegistrationsPage()->startDate?><?endif?>
                </p>
            </yield>
            <yield to="after">
                <div hidden ref="after">
                    <?=__('Du kannst dich nun Registrieren.')?>
                    <? require('./partials/event/register.php')?>
                </div>
            </yield>
        </ssf-countdown>
    <? endif ?>
</div>
