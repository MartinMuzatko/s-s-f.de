<?php namespace ProcessWire;?>
<?php
    if (!$isMe && !$isAdmin && $isInvisible) {
        $session->redirect($pages->get('name=404')->url);
    }
    $userLink = $this->config->urls->root.'users/'.$profile->name;
?>
<div class="profile-info">
    <div class="profile-info__column content content--padded">
        <? if($isMe || $isAdmin):?>
            <a class="button button--primary" href="<?=$config->urls->root?>users/<?=$user->name?>/edit"><?=__('Profil bearbeiten')?></a>
        <? endif?>
        <? if(!$isMe):?>
            <a class="button button--primary" href=""><?=__('Nachricht senden')?></a>
        <? endif?>
        <? require('user-info.php'); ?>
        <? if($isMe || $isAdmin):?>
        <p class="notification notification--warning">Folgende Daten sind <strong>privat</strong>. Nur Du und der Eventmanager können diese Daten einsehen</p>
            <? require('user-private-info.php'); ?>
        <? endif?>
        <?php
            $upcomingEvents = $profile->getUpcomingEvents();
            $attendedEvents = $profile->getAttendedEvents();
            $recommendedEvents = $profile->getRecommendedEvents();
        ?>
    </div>
    <div class="profile-info__column content--padded">
        <? if($isMe): ?>
            <h2><?=__('Deine Events')?></h2>
        <? endif; ?>
        <? if(($upcomingEvents->count + $attendedEvents->count == 0) && $isMe && $recommendedEvents->count): ?>
            <p class="notification notification--warning">
                <?=__('Du warst noch bei keinem Event dabei? Wage den ersten Schritt und werde Teil eines unserer kommenden Events.')?>
            </p>
            <event-list-short heading="Unsere Empfehlung">
                <? foreach ($recommendedEvents as $event): ?>
                    <event-list-item logo="<?=$event->logo->width(128, ["upscaling"=>false])->url?>" title="<?=$event->title?>" date="<?=strftime('%d.%m.%Y', $event->getUnformatted('startDate'))?>" link="<?=$event->url?>"></event-list-item>
                <? endforeach; ?>
            </event-list-short>
        <? endif; ?>
        <? if($upcomingEvents->count): ?>
            <event-list-short heading="<?=$isMe?'Du nimmst teil an diesen Events':'Du kannst mich hier antreffen'?>">
                <? foreach ($upcomingEvents as $event): ?>
                    <?
                        $attendee = $event->getRegisteredUser($profile);
                    ?>
                    <event-list-item
                        logo="<?=$event->logo->width(128, ["upscaling"=>false])->url?>"
                        title="<?=$event->title?>"
                        date="<?=strftime('%d.%m.%Y', $event->getUnformatted('startDate'))?>"
                        link="<?=$event->url?>">
                        <? if($isMe || $isAdmin):?>
                            <div layout="row" layout-align="space-between center" class="event-preview__division bg--primary-dark">
                                <div layout="row" layout-align="start center">
                                    <span class="status-bubble group__item bg--<?=getAttendeeStatusColor($attendee->attendeeStatus->title)?> bg--<?=getAttendeeStatusColor($attendee->attendeeStatus->title)?>--shadow"></span>
                                    <span class="group__item">
                                        <?=getAttendeeStatusLabel($attendee->attendeeStatus->title)?>
                                    </span>
                                </div>
                                <? if($event->hasRegistrationPage()): ?>
                                    <a href="<?=$event->getPageByModule('event-registration')->url?>" class="group__item button button--primary"><?=__('Registrierdaten')?></a>
                                <? endif ?>
                            </div>
                        <? endif;?>
                    </event-list-item>
                <? endforeach; ?>
            </event-list-short>
        <? endif; ?>
        <? if($attendedEvents->count): ?>
            <event-list-short heading="<?=$isMe?'Du warst bei diesen Events dabei':'Bei diesen Events war ich bereits'?>">
                <? foreach ($attendedEvents as $event): ?>
                    <event-list-item logo="<?=$event->logo->width(128, ["upscaling"=>false])->url?>" title="<?=$event->title?>" date="<?=strftime('%d.%m.%Y', $event->getUnformatted('startDate'))?>" link="<?=$event->url?>"></event-list-item>
                <? endforeach; ?>
            </event-list-short>
        <? endif; ?>
    </div>
</div>
