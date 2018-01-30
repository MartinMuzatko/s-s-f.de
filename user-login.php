<?php namespace ProcessWire;
    if (count($input->post()->getArray())) {
        if (strstr($input->post->username, '@')) {
            $username = $users->get("email=".$input->post->username);
        } else {
            $username = $input->post->username;
        }
        $loginUser = $session->login($username, $input->post->password);
        if($loginUser instanceof User) {
            $redirectPage = $pages->get($input->get->redirect);
            if(!$redirectPage instanceof NullPage) {
                $session->redirect($redirectPage->url);
            } else {
                $session->redirect($pages->get('/')->url.'users/'.$loginUser->name);
            }
        } else {
            $error = 1;
        }
    }
    if($user->isLoggedin()) {
        $session->redirect($pages->get('/')->url.'users/'.$user->name);
    }
?>
<form action="" method="POST" class="content--padded content--margin" layout="row" layout-align="center center">
    <div class="fieldset" flex="90" flex-gt-sm="70" flex-gt-md="60" flex-gt-lg="45">
        <p>
            <a href="<?=$pages->get('/users/register')->url?>"><?=__('Hast Du noch keinen Account? Jetzt Registrieren.')?></a>
        </p>
        <p class="notification notification--warning"><?=__('Aus Sicherheitsgründen, haben wir alle bisherigen Accounts der alten SSF Website entfernt. Wir bitten Dich einen neuen Account anzulegen.')?></p>
        <user-login>
            <div layout="column" layout-align="center center">
                <img ref="avatar" class="avatar avatar--big avatar--round" src="<?=$user->getAvatar()?>" alt="">
            </div>
            <? if(isset($error)): ?>
                <div class="notification notification--error">
                    <?=__('Der User existiert nicht oder das Passwort ist falsch.')?><br>
                    <?=sprintf(__('Wenn Du das Passwort vergessen hast, <a href="$s">kannst Du es wiederherstellen</a>.'), $pages->get('/users/forgot-password')->url)?>
                </div>
            <? endif ?>
            <label class="field-group" for="username">
                <span class="field-group__label field-group__label--dark">Nick / E-Mail</span>
                <input ref="username" class="field-group__input" type="text" name="username" required autocomplete autofocus value="<?=$input->post->username?>">
            </label>
            <label class="field-group">
                <span class="field-group__label field-group__label--dark">Passwort</span>
                <input class="field-group__input" type="password" name="password" autocomplete required value="">
            </label>
            <input class="button button--primary button--block button--big" type="submit" value="Login">
            <p>
                <a href="<?=$pages->get('/users/forgot-password')->url?>">Passwort vergessen?</a>
            </p>
            <!-- <div>
                <div class="text-or"><span>Oder</span></div>
                <div class="g-signin2" data-onsuccess="googleSignIn"></div>
            </div> -->
        </user-login>
    </div>
</form>
