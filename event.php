<?php namespace ProcessWire;
    $event = $page;
    $event->guestlist = $event->getPageByModule('event-guestlist');
    $event->registration = $event->getPageByModule('event-registration');
?>
<div class="content content--padded">
    <?if($user->hasPermission('event-manage')):?>
    <div class="actions actions--centered" style="margin:1em;">
        <a href="<?=$page->url?>" class="button button--primary">Event Ansicht</a>
        <a href="<?=$page->url?>edit" class="button button--primary">Event bearbeiten</a>
        <a href="<?=$page->url?>manage" class="button button--primary">Event managen</a>
        <a href="<?=$page->url?>manage-registrations" class="button button--primary">Registrierungen bearbeiten</a>
    </div>
    <?endif?>
</div>

<? if($input->urlSegment == 'edit'):?>
    <event-update event-name="<?=$page->name?>"></event-update>
<? elseif($input->urlSegment == 'manage'):?>
    <? require('./partials/event/manage.php')?>
<? elseif($input->urlSegment == 'manage-registrations'):?>
    <? require('./partials/event/manage-registrations.php')?>
<? else: ?>
    <? require('./partials/event/view.php')?>
<? endif; ?>
