<?php namespace ProcessWire;

use \API\Users as UserRegister;

$registration = new UserRegister();
$errors = [];
if($user->isLoggedIn()) {
    $session->redirect($pages->get('/')->url.'users/'.$user->name);
}
if(count($input->post->getArray())) {
    $session->CSRF->validate();
    $errors = $registration->registerUser($input->post);
}
?>
<script>
    var CSRFToken = '<?=$session->CSRF->getTokenName()?>'
    var CSRFValue = '<?=$session->CSRF->getTokenValue()?>'
    var postdata = <?=json_encode($input->post->getArray())?>
</script>
<? if(count($errors)): ?>
    <div class="notification notification--error">
        <h4>Deine Registrierung hat leider nicht geklappt</h4>
        <p>Folgende Punkte müssen noch angepasst werden:</p>
        <ul>
            <? foreach($errors as $error): ?>
                <li><?=$error?></li>
            <? endforeach; ?>
        </ul>
    </div>
<? endif ?>
<div class="content--padded">
    <user-register postdata="{postdata}"></user-register>
</div>
