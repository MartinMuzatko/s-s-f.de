<?php namespace ProcessWire;?>
<? $event = $this->wire->event; ?>
<div class="event-heading" layout="row" layout-align="center center">
    <img class="event-heading__logo" src="<?=$event->logo->width(128, ["upscaling"=>false])->url?>">
    <div layout="column">
        <h1 class="event-heading__title"><?=$event->title?></h1>
        <p class="event-heading__summary"><?=$event->summary?></p>
    </div>
    <p flex="100">
        <?=strftime( '%d.%m.%Y', $event->getUnformatted('startDate'))?>
        - <?=strftime( '%d.%m.%Y', $event->getUnformatted('endDate'))?>
    </p>
</div>