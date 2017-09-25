<?php namespace ProcessWire;?>
<event-create>
    <yield to="heading">
        <?=$page->title?>
    </yield>
    <yield to="accept">
        <?=$page->text?>
    </yield>
    <yield to="accept-button">
        Verstanden
    </yield>
</event-create>
