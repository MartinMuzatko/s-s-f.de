<?php namespace ProcessWire;?>
<div class="event-view__content" layout="row">
    <div layout="row" flex="75" layout-align="space-between">
        <? foreach($this->wire->event->children('template=page') as $page): ?>
            <div flex="45" class="text--centered">
                <h2 class="text--centered heading heading--underlined">
                    <a href="<?=$page->url?>"><?=$page->title?></a>
                </h2>
                <p><?=$page->summary?></p>
                <a href="" class="button button--subtle">Mehr erfahren</a>
            </div>
        <? endforeach; ?>
        <!-- <div flex="45" class="text--centered">
            <h2 class="text--centered heading heading--underlined"><a href="registration">Registrierung</a></h2>
        </div>
        <div flex="45" class="text--centered">
            <h2 class="text--centered heading heading--underlined">Fotos</h2>
            <p>Fotos und Filme vergangener UFurryA Events.</p>
            <img style="height: 164px;" src="https://i.imgur.com/QIga3n4.jpg" alt="" class="image--fit-cover">
        </div>
        <div flex="45" class="text--centered">
            <h2 class="text--centered heading heading--underlined">Anfahrt</h2>
            <p>Details zur Location und wie du zu uns kommst.</p>
            <img style="height: 140px" class="image--fit-cover" src="https://i.imgur.com/3Lgf8rS.png" alt="">
        </div> -->
    </div>
</div>