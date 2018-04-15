<?php namespace ProcessWire;
    $event = $page;
    $this->wire('event', $event);
    $event->guestlist = $event->getPageByModule('event-guestlist');
    $event->registration = $event->getPageByModule('event-registration');
    if($input->urlSegment == 'reg.csv') {
        require('./partials/event/csv.php');
    }
?>

<div class="content content--padded">
    <?if($user->hasPermission('event-user-manage')):?>
    <div class="actions actions--centered" style="margin:1em;">
        <a href="<?=$page->url?>" class="button button--primary">Event Ansicht</a>
        <? if($event->userCan('event-user-manage')):?>
            <a href="<?=$page->url?>edit" class="button button--primary">Event bearbeiten</a>
        <? endif ?>
        <? if($event->userCan('event-user-registration-onstage')):?>
            <a href="<?=$page->url?>reg-on-stage" class="button button--primary">On-Stage Registrierung</a>
        <? endif ?>
        <? if($event->userCan('event-user-registration-edit')):?>
            <a href="<?=$page->url?>manage-registrations" class="button button--primary">Registrierungen bearbeiten</a>
        <? endif ?>
    </div>
    <?endif?>
</div>

<? if($input->urlSegment == 'edit'):?>
    <event-update canpublish={<?=(string)$user->hasPermission('event-publish')?>} event-name="<?=$page->name?>"></event-update>
<? elseif($input->urlSegment == 'reg-on-stage'):?>
    <? require('./partials/event/reg-on-stage.php')?>
<? elseif($input->urlSegment == 'manage-registrations'):?>
    <? require('./partials/event/manage-registrations.php')?>
<? else: ?>
    <?if($user->hasPermission('page-edit') && !isset($input->get->edit)):?>
        <a href="<?=$page->url?>?edit" class="button button--primary">Seite bearbeiten</a>
    <?endif?>
    <? if($user->hasPermission('page-edit') && isset($input->get->edit)): ?>
        <page-editor
            page-modules="<?=($page->parents("(template=event), (name=templates)")->count || $page->template->name == 'event') ? 'module event' : 'module'?>" page-title="<?=$page->title?>"
            page-id="<?=$page->id?>"></page-editor>
    <? else: ?>
        <? foreach($page->pageModules as $pageModule):?>
            <?=$pageModule->render()?>
        <? endforeach ?>
    <? endif ?>
<? endif; ?>
