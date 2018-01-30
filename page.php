<?php namespace ProcessWire;?>
<? if($event instanceof Event): ?>
    <a aria-role="menu" href="<?=$event->url?>" class="button button--subtle">&laquo; Zurück zur Hauptseite von <?=$event->title?></a>
<? endif; ?>
<!-- $options['originalPage'] -->
<?if($user->hasPermission('page-edit') && !isset($input->get->edit)):?>
    <a href="<?=$page->url?>?edit" class="button button--primary">Seite bearbeiten</a>
<?endif?>
<? if($user->hasPermission('page-edit') && isset($input->get->edit)): ?>
    <p>
        Seite zuletzt bearbeitet am <strong><?=date('d.m.Y G:i:s', $page->modified)?></strong> von <strong><?=$users->get("id=$page->modifiedUser")->username?></strong>
    </p>
    <page-editor
        page-modules="<?=$availableModules?>" page-title="<?=$page->title?>"
        page-id="<?=$page->id?>"></page-editor>
<? else: ?>
    <header class="content--padded">
        <h1><?=$page->title?></h1>
    </header>
    <? foreach($page->pageModules as $pageModule):?>
        <?=$pageModule->render(["originalPage" => $page])?>
    <? endforeach ?>
<? endif ?>
