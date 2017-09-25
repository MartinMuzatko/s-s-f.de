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
