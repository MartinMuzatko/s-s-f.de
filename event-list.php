<?php namespace ProcessWire; ?>
<? foreach ($page->children as $event): ?>
    <h2>
        <a href="<?=$event->url?>">
            <?=$event->title?>
        </a>
    </h2>
    <p>
        <?=$event->ticketLimit - count($event->get('template=event-registrations')->children)?>/<?=$event->ticketLimit?>
        Tickets noch frei
    </p>
    <hr>
<? endforeach; ?>
