<?php namespace ProcessWire; ?>
<div layout="row" layout-nowrap>
    <div>
        <h1><?=$profile->username?></h1>
        <div>
            <strong><?=__('Spezies:')?></strong>
            <?=$profile->species->title?>
        </div>
        <div>
            <strong><?=__('Registriert seit:')?></strong>
            <?=strftime('%d.%m.%Y', $profile->created)?>
        </div>
    </div>
    <img flex-end class="avatar avatar--big avatar--round" src="<?=$profile->getAvatar(128)?>" alt="Profilbild von <?=$profile->username?>">
</div>
