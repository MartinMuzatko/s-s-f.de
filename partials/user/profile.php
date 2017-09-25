<?php
    if ($profile->isInvisible) {
        $session->redirect($pages->get('name=404')->url);
    }
    $userLink = $this->config->urls->root.'users/'.$profile->name;
    $profileImage = $profile->avatar->size(128,128);
?>
<div class="profile-info">
    <div class="profile-info__column">
        <div layout="row" layout-nowrap>
            <h1><?=$profile->username?></h1>
            <img flex-end class="avatar avatar--big avatar--round" src="<?=$profileImage->url?>" alt="Profilbild von <?=$profile->username?>">
        </div>
        <div class="actions">
            <? if($profile->name == $user->name || $user->hasRole('superuser')):?>
                <a href="<?=$profile->editUrl?>">Profil bearbeiten</a>
            <? endif?>
        </div>
        <?php
            $events = $user->getAttendedEvents();
            foreach ($events as $event) {
                echo $event->title;
            }
        ?>
        <?=$profile->renderFields()?>
    </div>
    <div class="profile-info__column">
        <event-list-short heading="Du nimmst teil an diesen Events"></event-list-short>
        <event-list-short heading="Du warst bei diesen Events dabei"></event-list-short>
        <event-list-short heading="Diese Events empfehlen wir dir"></event-list-short>
    </div>
</div>
