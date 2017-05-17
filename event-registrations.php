<?php namespace ProcessWire; ?>
<ol>
<? foreach ($page->children as $registration): ?>
    <li><?=$registration->title?></li>
    <?=$registration->profile->renderFields()?>
<? endforeach;?>
</ol>
<? if(count($page->children("title=$user->name"))): ?>
    Already registered!
<? else: ?>
    <form action="" method="post">
        <label for="badge">Badge:</label>
        <input type="hidden" name="badge" value="0">
        <input type="checkbox" name="badge" value="1">
        <input type="submit" name="" value="registrieren">
    </form>
    <? if (count($input->post()->getArray())): ?>
        <?php
        print_r($input->post->getArray());
        $registration = new Page();
        $registration->parent = $page;
        $registration->template = 'event-registration';
        $registration->title = $user->name;
        $registration->profile = $user;
        $registration->save();
        // $loginUser = $session->login($input->post->username, $input->post->password);
        // $loginUser->name;
        ?>
        submitted! <?=$user->name?>
    <? endif; ?>
<? endif; ?>
