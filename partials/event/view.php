<?php namespace ProcessWire; ?>
<article class="event-view content content--padded">
    <div class="event-view__heading">
        <h1 class="event-view__title"><?=$page->title?></h1>
        <p class="event-view__summary"><?=$page->summary?></p>
    </div>
    <div class="event-view__content" layout="row">
        <div layout="row" flex="75" layout-align="space-between">
            <div flex="45" class="text--centered">
                <h2 class="text--centered heading heading--underlined"><?=__('Programm & Verpflegung')?></h2>
                <p><?=__('Wie jedes Jahr gibt es ein ausgedehntes Programm. Darunter Freiluftkino, Redneck-Pool und Burger essen.')?></p>
                <a href="" class="button button--primary"><?=__('Mehr erfahren')?></a>
            </div>
            <div flex="45" class="text--centered">
                <h2 class="text--centered heading heading--underlined"><a href="registration"><?=__('Registrierung')?></a></h2>
                <? require('registerstate.php') ?>
            </div>
            <div flex="45" class="text--centered">
                <h2 class="text--centered heading heading--underlined"><?=__('Fotos')?></h2>
                <p><?=__('Fotos und Filme vergangener UFurryA Events.')?></p>
                <img style="height: 164px;" src="https://i.imgur.com/QIga3n4.jpg" alt="" class="image--fit-cover">
            </div>
            <div flex="45" class="text--centered">
                <h2 class="text--centered heading heading--underlined"><?=__('Anfahrt')?></h2>
                <p><?=__('Details zur Location und wie Du zu uns kommst.')?></p>
                <img style="height: 140px" class="image--fit-cover" src="https://i.imgur.com/3Lgf8rS.png" alt="">
            </div>
        </div>
    </div>
    <p><?=__('Beginnt:')?> <?=$page->startDate?></p>
    <p><?=__('Endet:')?> <?=$page->endDate?></p>
    tickets noch offen

    <p>
        <a href="<?=$page->child('template=event-registrations')->url?>"><?=__('Jetzt registrieren')?></a>
    </p>
</article>
