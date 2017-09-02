<?php namespace ProcessWire;?>
<h1><?=$page->title?></h1>
<? if ($this->modules->isInstalled('ProcessForgotPassword')): ?>
    <?=$this->modules->get("ProcessForgotPassword")->execute()?>
<?endif?>
