<?php namespace ProcessWire;
    $username = $input->urlSegmentStr;
    $profile = $users->get($username);
?>
<? if ($profile instanceof User): ?>
        <?=$profile->renderFields()?>
<? else: ?>
    <?$profiles = $users->find("name|email|firstname|lastname*=$username")?>
    <? if ($profiles->count): ?>
        <? foreach ($profiles as $profile): ?>
            <a href="<?=$this->config->urls->root?>user/<?=$profile->name?>"><?=$profile->name?></a> <br>
        <? endforeach ?>
    <? else: ?>
        user not found
    <? endif ?>
<? endif ?>
