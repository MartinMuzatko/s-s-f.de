<?php namespace ProcessWire;
if(!$user->hasPermission('event-user-manage')) {
    $session->redirect($pages->get('name=403')->url);
} ?>
<event-create canpublish={<?=(string)$user->hasPermission('event-publish')?>}>
    <yield to="heading">
        <?=$page->title?>
    </yield>
    <yield to="accept">
        <?=$page->text?>
    </yield>
    <yield to="accept-button">
        <?=__('Verstanden')?>
    </yield>
</event-create>
