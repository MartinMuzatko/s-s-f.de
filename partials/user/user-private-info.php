<?php namespace ProcessWire; ?>
<div><strong><?=__('Vorname:')?></strong> <?=$profile->firstname?></div>
<div><strong><?=__('Nachname:')?></strong> <?=$profile->lastname?></div>
<div><strong><?=__('Straße:')?></strong> <?=$profile->street?></div>
<div><strong><?=__('Ort:')?></strong> <?=$profile->city?></div>
<div><strong><?=__('PLZ:')?></strong> <?=$profile->zip?></div>
<div><strong><?=__('Land:')?></strong> <?=$profile->country->title?></div>
<div><strong><?=__('Geburtsdatum:')?></strong> <?=$profile->birthdate?></div>
<div><strong><?=__('Email:')?></strong> <?=$profile->email?></div>
