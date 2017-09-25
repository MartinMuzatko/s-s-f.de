<article class="event">
    <h1 class="event__heading"><?=$page->title?></h1>
    <p class="event__summary"><?=$page->summary?></p>
    <div class="content" layout="row">
        <div layout="row" flex="75" layout-align="space-between">
            <div flex="45" class="text--centered">
                <h2 class="text--centered heading heading--underlined">Programm & Verpflegung</h2>
                <p>Wie jedes Jahr gibt es ein ausgedehntes Programm. Darunter Freiluftkino, Redneck-Pool und Burger essen.</p>
                <a href="" class="button button--primary">Mehr erfahren</a>
            </div>
            <div flex="45" class="text--centered">
                <h2 class="text--centered heading heading--underlined"><a href="registration">Registrierung</a></h2>
                <? require('registerstate.php') ?>
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
            </div>
        </div>
    </div>
    <p>Beginnt: <?=$page->startDate?></p>
    <p>Endet: <?=$page->endDate?></p>
    tickets noch offen

    <p>
        <a href="<?=$page->child('template=event-registrations')->url?>">Jetzt registrieren</a>
    </p>
</article>
