<?php namespace ProcessWire; ?>
<p>Beginnt: <?=$page->startDate?></p>
<p>Endet: <?=$page->endDate?></p>
<?=$page->ticketLimit - count($page->get('template=event-registrations')->children)?>/<?=$page->ticketLimit?>
tickets noch offen

<p>
    <a href="<?=$page->child('template=event-registrations')->url?>">Jetzt registrieren</a>
</p>

<?php
    $event = new \API\Event($page);
    var_dump($event->getTicketsLeft());

?>
<? /*

isRegistrationOpen
isRegistrationClosed
getTicketsLeft
getRegistrations
*/
