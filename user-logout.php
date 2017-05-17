<?php namespace ProcessWire;?>

<? if($user->isLoggedIn()): ?>
    Bye <?=$user->name?>
<? endif;?>
<? $session->logout(); ?>
