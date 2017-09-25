<?php namespace ProcessWire;
    $event = $page->parent('template=event');
    if (!$event instanceof NullPage) {
        $this->wire('event', $event);
        $this->wire('attendee', $event->getRegisteredUser($user));
    }
?>
<?if($user->hasPermission('page-edit') && !isset($input->get->edit)):?>
    <a href="<?=$page->url?>?edit" class="button button--primary">Seite bearbeiten</a>
<?endif?>
<? if($user->hasPermission('page-edit') && isset($input->get->edit)): ?>
    <page-editor
        page-modules="<?=($page->parent->template->name == 'event' || $page->template->name == 'event') ? 'module event' : 'module'?>" page-title="<?=$page->title?>"
        page-id="<?=$page->id?>"></page-editor>
<? else: ?>
    <? foreach($page->pageModules as $pageModule):?>
        <?=$pageModule->render()?>
    <? endforeach ?>
<? endif ?>
