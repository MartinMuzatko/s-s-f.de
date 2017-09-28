<?php namespace ProcessWire; ?>
<ssf-carousel>
    <? foreach($page->slides as $slide): ?>
        <div class="carousel__item">
            <img src="<?=$slide->image->first->size(800,400)->url?>" alt="" class="carousel__image">
            <div class="carousel__content" layout="row" layout-align="start end">
                <div flex="66">

                    <?=$slide->text?>
                </div>
            </div>
        </div>
    <? endforeach; ?>
</ssf-carousel>
<div class="content content--padded" layout="row" layout-align="space-between">
	<div flex="45">
		<h2 class="heading text-or"><span>Über uns</span></h2>
		<p>Wir sind ein kleines Projekt welches den Furries im Süden Deutschlands die Möglichkeit bietet, ihre Veranstaltungen wie Meets, Cons und Walks zu koordinieren und zu präsentieren.</p>

		<p>Möchtest Du ein Treffen organisieren und brauchst Hilfe? Dann greifen Wir Dir gerne unter die Arme! Du musst kein Vereinsmitglied sein um bei uns mitmachen zu können! Jeder ist herzlich dazu eingeladen unsere Plattform zu nutzen. Wir bieten Dir ein Eventmanagemant/Reg-System, ein Portal zum Helfer suchen und wir machen Dein Event auf den gängigen Social Media-Plattformen publik.</p>

		<p>Dafür bieten wir Dir hier eine Infrastruktur an. Noch befindet sich einiges im Auf- sowie im Umbau. Wir sind fast täglich in unserer Freizeit dabei das Portal zu pflegen. </p>

		<p>Wir freuen uns schon darauf Dir und Deinem Event bei Seite stehen zu können!</p>

		<p>~Die Südstaaten Furs</p>
	</div>
	<div flex="45">
		<h2 class="heading text-or"><span>Events</span></h2>
		<event-list-short heading="Events die in Kürze starten"></event-list-short>
	</div>
</div>
