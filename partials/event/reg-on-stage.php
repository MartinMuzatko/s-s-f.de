<?php namespace ProcessWire;
    if (!$event->userCan('event-user-registration-onstage')) {
        $session->redirect($pages->get('name=403')->url);
    }
?>
<manage-on-stage event="<?=$event->name?>"></manage-on-stage>