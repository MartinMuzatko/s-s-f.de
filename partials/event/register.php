<?php namespace ProcessWire; ?>
<?
if($input->post->method == 'DELETE') {
    $event->unregisterUser($user);
}
if($input->post->method == 'POST') {
    $attendee = $event->registerUser($user, $input->post);
    $this->wire('attendee', $attendee);
}
?>
<? if($input->post->method == 'DELETE'): ?>
    <ssf-dialog visible size="small">
        <p>
            Schade, dass du nicht dabei bist!
            Wir haben dich vom <?=$event->title?> abgemeldet.
        </p>
        <p>
            Solltest du bereits bezahlt haben, kontaktiere uns.
        </p>
        <p><a href="" class="button button--primary">Event-staff kontaktieren</a></p>

    </ssf-dialog>
<? endif ?>
<? if($user->isLoggedin()): ?>
    <? if($event->isUserRegistered($user)): ?>
        <? if($event->isRegistrationOpen()): ?>
            <div class="notification notification--success">
                <h2>Du bist bereits registriert</h2>
                <p>Hier kannst du deine Details einsehen und bearbeiten.</p>
            </div>
            <p>Bezahlstatus: <?=$attendee->paid?></p>
            <p>Du hast dich registriert am: <?=date("d.m.Y H:m", $attendee->created)?></p>
            <form action="" method="POST">
                <input type="hidden" name="method" value="DELETE">
                <input class="button button--primary" type="submit" value="Vom Event abmelden">
            </form>
        <? else: ?>
            <div class="notification notification--error">
                <h2>Die Registrierung ist leider schon geschlossen</h2>
                <p>Du kannst keine Details mehr bearbeiten.</p>
            </div>
        <? endif ?>
    <? else: ?>
        <? if($user->isEligibleForEventRegistration()): ?>
            <? if($event->isRegistrationOpen()): ?>
                <?
                    $itemsJson = json_encode(array_map(
                        function($item) {
                            return [
                                "title"=>$item->title,
                                "name"=>$item->name,
                                "sellPrice"=>$item->sellPrice
                            ];
                        },
                        $event->getItems()->getArray()
                    ));
                ?>
                <script>
                    var items = <?=$itemsJson?>
                </script>
                <event-register event-name="<?=$event->name?>" event-title="<?=$event->title?>" items={items} ticket-price="<?=$event->sellPrice?>">
                </event-register>
            <? else: ?>
                <div class="notification notification--error">
                    <h2>Die Registrierung ist leider schon geschlossen</h2>
                    <p>Hier kannst du aber die Gasteliste sehen.</p>
                </div>
            <? endif ?>
            <? //if($event->): ?>
            <?
                //$event->registerUser($user);
            ?>
        <? else: ?>
            <div class="notification notification--error">
                <p>Es fehlen noch ein paar Account Informationen um Events beitreten zu koennen.</p>
                <p>
                    Uns fehlen folgende Daten: <? var_dump($user->getMissingRequiredFields()); ?>
                </p>
                <p><a href="<?=$pages->get('/users/')->url.'/'.$user->name?>">Bearbeite dein Profil</a></p>
            </div>
        <? endif ?>
    <? endif ?>
<? else: ?>
    <div class="notification notification--error">
        <p>Du benoetigst einen Account um am Event teilzunehmen</p>
        <p><a class="button button--primary" href="<?=$pages->get('/users/register')->url?>">Jetzt registrieren</a></p>
        <p>Hast du bereits einen Account? <a href="<?=$pages->get('/users/login')->url?>">Jetzt einloggen</a></p>
    </div>
<? endif; ?>
