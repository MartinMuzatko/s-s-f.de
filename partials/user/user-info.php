<?php namespace ProcessWire; ?>
<div layout="row" layout-nowrap>
    <div>
        <h1><?=$profile->username?></h1>
        <div>
            <strong>Spezies:</strong>
            <?=$profile->species->title?>
        </div>
        <div>
            <strong>Registriert seit:</strong>
            <?=strftime('%d.%m.%Y', $profile->created)?>
        </div>
        <div><strong>Land:</strong> <?=$profile->country->title?></div>
    </div>
    <img flex-end class="avatar avatar--big avatar--round" src="<?=$profile->getAvatar(128)?>" alt="Profilbild von <?=$profile->username?>">
</div>