<?php namespace ProcessWire; ?>
<?
if($input->post->method == 'DELETE') {
    // $event->unregisterUser($user);
}
if($input->post->method == 'POST') {
    $attendee = $event->registerUser($user, $input->post);
}
$this->wire('attendee', $event->getRegisteredUser($user));
$attendee = $this->wire->attendee;
?>
<? if($input->post->method == 'DELETE'): ?>
    <ssf-dialog visible size="small">
        <p>
            Schade, dass Du nicht dabei bist!
            Wir haben dich vom <?=$event->title?> abgemeldet.
        </p>
        <p>
            Solltest Du bereits bezahlt haben, kontaktiere uns.
        </p>
        <p><a href="" class="button button--primary">Event-staff kontaktieren</a></p>

    </ssf-dialog>
<? endif ?>
<? if($user->isLoggedin()): ?>
    <? if($event->isUserRegistered($user)): ?>
        <? if($event->isRegistrationOpen()): ?>
            <? if($attendee->attendeeStatus->title == 'new'): ?>
                <div class="notification notification--success">
                    <h2>Du hast dich für das Event <strong><?=$event->title?></strong> registriert</h2>
                    <p>Deine Registrierung wird von uns manuell geprüft. Sobald Du im Zahlungseingang landest, geben wir dir Bescheid.</p>
                    <p>Deine Con fee beträgt <strong><?=$event->getAttendeePaymentSum($user)?> €</strong>. Du erhältst die Bankdaten, sobald wir dich freigeschalten haben.</p>
                </div>
            <? elseif($attendee->attendeeStatus->title == 'pending'): ?>
                <div class="notification notification--success">
                    <h2>Deine Registrierung wurde bearbeitet. Wir erwarten Deine Zahlung</h2>
                    <p>Bitte überweise den Betrag von <strong><?=$event->getAttendeePaymentSum($user)?> €</strong> auf folgendes Konto:</p>
                    <p>
                        IBAN:<strong><?=$event->bankIBAN?></strong><br>
                        BIC:<strong><?=$event->bankBIC?></strong><br>
                    </p>
                    <p>
                        Empfänger: <br>
                        <strong><?=$event->bankAccountName?></strong>
                    </p>
                    <!-- <?=$attendee->paymentMethod->title?> -->
                    <p>
                        Als Betreff gib bitte das Event und Deinen Nick an: <strong><?=$event->title?> - <?=$attendee->profile->username?></strong>
                    </p>
                    <p>
                        Adresse für Auslandsüberweisungen:<br>
                        <strong><?=$event->bankAccountName?></strong><br>
                        <strong><?=$event->bankAccountAddress?></strong>
                    </p>
                </div>
            <? elseif($attendee->attendeeStatus->title == 'accepted'): ?>
                <div class="notification notification--success">
                    <h2>Juhu! Du bist dabei</h2>
                    <p>Wir haben deine Zahlung von <strong><?=$event->getAttendeePaymentSum($user)?> €</strong> erhalten und erwaten dich auf dem Event</p>
                </div>
            <? elseif($attendee->attendeeStatus->title == 'waiting'): ?>
                <div class="notification notification--warning">
                    <h2>Warteliste</h2>
                    <p>Leider ist das Event im Augenblick voll. Sobald ein Platz frei wird, wirst Du automatisch benachrichtigt. Du musst nichts weiter unternehmen.</p>
                </div>
            <? elseif($attendee->attendeeStatus->title == 'signedoff'): ?>
                <div class="notification notification--error">
                    <h2>Du hast dich vom Event abgemeldet</h2>
                    <p>Schade, dass Du beim <strong><?=$event->title?></strong> nicht dabei sein kannst.</p>
                    <p>Bei Fragen melde dich bitte an den Event-staff.</p>
                </div>
            <? elseif($attendee->attendeeStatus->title == 'dismissed'): ?>
                <div class="notification notification--error">
                    <h2>Du wurdest von der Teilnahme am Event ausgeschlossen.</h2>
                    <p>Bei Fragen melde dich bitte an den Event-staff.</p>
                </div>
            <? endif; ?>
            <div class="card card--light content--padded">
                <p>Du hast dich am: <strong><?=date("d.m.Y H:m", $attendee->created)?> registriert</strong></p>
                <? if($attendee->items->count): ?>
                    <h3>Hier sind Deine ausgewählten Optionen:</h3>
                    <div layout="row">
                        <? foreach($attendee->items as $item): ?>
                            <div flex="30" flex-gt-sm="20" flex-gt-md="15" class=" content--margin icon-card icon-card--small">
                                <span class="icon-card__title"><?=$item->title?></span>
                                <img class="icon-card__image" src="<?=$item->image->first->size(128,128)->url?>">
                            </div>
                        <? endforeach; ?>
                    </div>
                <? endif; ?>
                <? if($attendee->attendeeRoles->count): ?>
                    <h3>Du nimmst beim Event teil als:</h3>
                    <div layout="row">
                        <? foreach($attendee->attendeeRoles as $attendeeRole): ?>
                            <div flex="30" flex-gt-sm="20" flex-gt-md="15" class=" content--margin icon-card icon-card--small">
                                <span class="icon-card__title"><?=$attendeeRole->title?></span>
                                <img class="icon-card__image" src="<?=$attendeeRole->image->first->size(128,128)->url?>">
                            </div>
                        <? endforeach; ?>
                    </div>
                <? endif; ?>
            </div>
            <!-- <form action="" method="POST">
                <input type="hidden" name="method" value="DELETE">
                <input class="button button--primary" type="submit" value="Vom Event abmelden">
            </form> -->
        <? else: ?>
            <div class="notification notification--error">
                <h2>Die Registrierung ist leider schon geschlossen</h2>
                <p>Du kannst keine Details mehr bearbeiten.</p>
            </div>
        <? endif ?>
    <? else: ?>
        <? if($user->isEligibleForEventRegistration()): ?>
            <? if($event->isUserOldEnoughAtEventStartDate($user)): ?>
                <? if($event->isRegistrationOpen()): ?>
                    <?
                        $itemsJson = json_encode(array_map(
                            function($item) {
                                return [
                                    'title' => $item->title,
                                    'image' => $item->image->first->url,
                                    'name' => $item->name,
                                    'summary' => $item->summary,
                                    'sellPrice' => $item->sellPrice,
                                    'isFreeForMembers' => $item->isFreeForMembers,
                                    'isPreSelected' => $item->isPreSelected,
                                    'isMembersOnly' => $item->isMembersOnly
                                ];
                            },
                            $event->getItems()->getArray()
                        ));
                        
                        $rolesJson = json_encode(array_map(
                            function($item) {
                                return [
                                    'title' => $item->title,
                                    'name' => $item->name,
                                    'image' => $item->image->first->url,
                                    'summary' => $item->summary
                                ];
                            },
                            $event->getRoles()->getArray()
                        ));

                        $sponsorlevelsJson = json_encode(array_map(
                            function($item) {
                                return [
                                    'title' => $item->title,
                                    'name' => $item->name,
                                    'text' => $item->text,
                                    'image' => $item->image->first->url,
                                    'buyPrice' => $item->buyPrice,
                                    'items' => array_map(
                                        function($item) {
                                            return $item->name;
                                        },
                                        $item->items->getArray()
                                    )
                                ];
                            },
                            $event->getSponsorlevels()->getArray()
                        ));
                    ?>
                    <script>
                        var items = <?=$itemsJson?>;
                        var roles = <?=$rolesJson?>;
                        var sponsorlevels = <?=$sponsorlevelsJson?>;
                    </script>
                    <div class="card card--light" layout="row">
                        <div flex="100" flex-gt-md="40">
                            <div class="icon-card">
                                <span ref="labels" class="icon-card__title">Gast</span>
                                <img class="icon-card__image" src="<?=$config->urls->templates?>dist/images/attendee.svg">
                            </div>
                        </div>
                        <div class="content--padded" flex="100" flex-gt-md="60">
                            <?php
                                $profile = $user;
                                require('partials/user/user-info.php');
                            ?>
                            <?=$profile->street?> in <?=$profile->zip?> <?=$profile->city?>, <?=$profile->country->title?>
                        </div>
                    </div>
                    <event-register terms="<?=$event->terms->url?>" event-name="<?=$event->name?>" event-title="<?=$event->title?>" items={items} roles={roles} ticket-price="<?=$event->sellPrice?>">
                    </event-register>
                <? else: ?>
                    <div class="notification notification--error">
                        <h2>Die Registrierung ist leider schon geschlossen</h2>
                        <p>Hier kannst Du aber die Gasteliste sehen.</p>
                    </div>
                <? endif ?>
            <? else: ?>
                <div class="notification notification--error">
                    <p>
                        Du bist leider noch nicht die erforderlichen <strong><?=$event->getRegistrationsPage()->minimumAge?> Jahre</strong> zum Zeitpunkt des Events.
                    </p>
                    <p>
                        Wenn Du Fragen zu diesem Thema hast, informiert dich das Event-team gerne.
                    </p>
                    <a href="mailto:<?=$event->email?>" class="button button--primary">Event-Team Kontaktieren</a>
                </div>
            <? endif ?>
        <? else: ?>
            <div class="notification notification--error">
                <p>Es fehlen noch ein paar Account Informationen um Events beitreten zu koennen.</p>
                <p>
                    Uns fehlen folgende Daten:
                </p>
                <ul>
                    <? foreach($user->getMissingRequiredFields() as $field):?>
                        <li>
                            <?=$field?>
                        </li>
                    <? endforeach;?>
                </ul>
                <p><a href="<?=$pages->get('/users/')->url.'/'.$user->name.'/edit'?>">Bearbeite dein Profil</a></p>
            </div>
        <? endif ?>
    <? endif ?>
<? else: ?>
    <div class="notification notification--warning">
        <p>Du benötigst einen Account, um am Event teilzunehmen</p>
        <p><a class="button button--primary" href="<?=$pages->get("/users/register")->url?>?redirect=<?=$options['originalPage']->id?>">Jetzt registrieren</a></p>
        <p>Hast Du bereits einen Account? <a href="<?=$pages->get("/users/login")->url?>?redirect=<?=$options['originalPage']->id?>">Jetzt einloggen</a></p>
    </div>
<? endif; ?>
