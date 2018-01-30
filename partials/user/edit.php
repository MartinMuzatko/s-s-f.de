<?php namespace ProcessWire;
    use \API\Users as UserRegister;

    $registration = new UserRegister();
    $errors = [];
    if (!$isMe && !$isAdmin) {
        $session->redirect($pages->get('name=404')->url);
    }
    $updated = false;
    if(count($input->post->getArray())) {
        $session->CSRF->validate();
        $errors = $registration->updateUser($input->post, $profile);
        $updated = true;
    }
    $userData = array_merge(...getFieldValues($profile, $profile->fields->getArray(), false));
    $userData['pass'] = '';
?>
<script>
    var CSRFToken = '<?=$session->CSRF->getTokenName()?>'
    var CSRFValue = '<?=$session->CSRF->getTokenValue()?>'
    var postdata = <?=json_encode($userData)?>
    </script>
<div class="content--padded">
    <? if($updated && !count($errors)): ?>
        <div class="notification notification--success">
            <h4><?=__('Dein Profil wurde aktualisiert!')?></h4>
        </div>
    <? endif ?>
    <? if(count($errors)): ?>
        <div class="notification notification--error">
            <h4><?=__('Die Änderung deines Profils hat leider nicht geklappt.')?></h4>
            <p><?=__('Folgende Punkte müssen noch angepasst werden:')?></p>
            <ul>
                <? foreach($errors as $error): ?>
                    <li><?=$error?></li>
                <? endforeach; ?>
            </ul>
        </div>
    <? endif ?>
    <user-edit user="<?=$username?>" postdata="{postdata}"></user-edit>
</div>
