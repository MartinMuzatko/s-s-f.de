<? if($event->isEventRunning()): ?>
    <p>Das Event läuft gerade. Sieh dir an wer teilnimmt.</p>
    <a href="<?=$event->guestlist->url?>" class="button button--secondary">Gästeliste</a>
<? elseif($event->isEventOver()): ?>
    <p>Das Event ist bereits gelaufen. Schau dir an, wer dabei war.</p>
    <a href="<?=$event->guestlist->url?>" class="button button--secondary">Gästeliste</a>
<? elseif($event->isRegistrationOpen() && !$event->isEventOver()):?>
    <p>Es sind bereits <strong><?=$event->getRegistrations()->count?></strong> von <strong><?=$event->ticketLimit?></strong> Plätzen reserviert.</p>
    <progress value="<?=100*($event->getRegistrations()->count/$event->ticketLimit)?>" max="100">30%</progress><br>
    <? if($event->getRegistrationsPage()->endDate): ?>Die Registrierung schließt am <?=$event->getRegistrationsPage()->endDate?><?endif?>
    <div layout="row" layout-align="space-around">
        <a href="<?=$event->guestlist->url?>" class="button button--secondary">Gästeliste</a>
        <a href="<?=$event->registration->url?>" class="button button--primary">Jetzt Registrieren</a>
    </div>
<? elseif(!$event->isRegistrationOpen() && !$event->isEventOver()): ?>
    <ssf-countdown to="<?=$event->getRegistrationsPage()->getUnformatted('startDate')?>000">
        <yield to="before">
            <p>
                Die Registrierung ist noch nicht offen.
                <? if($event->getRegistrationsPage()->startDate): ?>Sie offnet am <?=$event->getRegistrationsPage()->startDate?><?endif?>
            </p>
        </yield>
        <yield to="after">
            <p hidden ref="after">
                Du kannst dich nun Registrieren.
                <a href="<?=$event->registration->url?>" class="button button--primary">Jetzt Registrieren</a>
            </p>
        </yield>
    </ssf-countdown>
<? endif ?>
