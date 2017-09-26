<?php namespace ProcessWire;
    $event = $page;
    $event->guestlist = $event->getPageByModule('event-guestlist');
    $event->registration = $event->getPageByModule('event-registration');
?>
<?if($user->hasPermission('event-manage')):?>
    <a href="<?=$page->url?>" class="button button--primary">Event Ansicht</a>
    <a href="<?=$page->url?>edit" class="button button--primary">Event bearbeiten</a>
    <a href="<?=$page->url?>manage" class="button button--primary">Event managen</a>
	<a href="<?=$page->url?>manage-registrations" class="button button--primary">Registrierungen bearbeiten</a>
<?endif?>

<? if($input->urlSegment == 'edit'):?>
    <event-update event-name="<?=$page->name?>"></event-update>
<? elseif($input->urlSegment == 'manage'):?>
    <? require('./partials/event/manage.php')?>
<? elseif($input->urlSegment == 'manage-registrations'):?>
    <? require('./partials/event/manage-registrations.php')?>
<? else: ?>
    <? require('./partials/event/view.php')?>
<? endif; ?>
