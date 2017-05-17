<?php namespace ProcessWire;?>
<?$profiles = $users->find("*")?>
<? foreach ($profiles as $profile): ?>
    <? if ($profile->avatar->count): ?>
        <img src="<?=$profile->avatar->first->size(32,32)->url?>" alt="<?=$profile->name?>">
    <? endif ?>
    <a href="<?=$this->config->urls->root?>user/<?=$profile->name?>"><?=$profile->name?></a> here since <?=$profile->created?> <br>
<? endforeach ?>
