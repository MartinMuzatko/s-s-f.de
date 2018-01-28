<?php namespace ProcessWire;
if (!$isMe && !$isAdmin) {
    $session->redirect($pages->get('name=404')->url);
}
?>
<div class="content--padded">
    <user-messages></user-messages>
</div>
