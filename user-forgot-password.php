<?php namespace ProcessWire;?>
<div class="content content--padded">
    <h1><?=$page->title?></h1>
    <? if ($this->modules->isInstalled('ProcessForgotPassword')): ?>
        <?=$this->modules->get("ProcessForgotPassword")->execute()?>
    <?endif?>
</div>
