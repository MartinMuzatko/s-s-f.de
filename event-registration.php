<?php namespace ProcessWire;?>
<? if($options['mode'] == 'guestlist' && $page->profile): ?>
    <user-profile-card flex="100" flex-gt-sm="50" flex-gt-md="33" flex-gt-lg="25"
        joined="<?=date('Y-m-d', $page->profile->created)?>"
        link="<?=$this->config->urls->root?>users/<?=$page->profile->name?>"
        club-number="<?=$page->profile->clubMemberID?>"
        username="<?=$page->profile->username?>"
        country="<?=$page->profile->country->title?>"
        avatar="<?=$page->profile->getAvatar()?>"></user-profile-card>
<? elseif(false):?>

<? endif; ?>
