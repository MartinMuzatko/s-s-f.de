<?php namespace ProcessWire;
    if (count($input->post()->getArray())) {
        if (strstr($input->post->username, '@')) {
            $username = $users->get("email=".$input->post->username);
        } else {
            $username = $input->post->username;
        }
        $loginUser = $session->login($username, $input->post->password);
        if($loginUser instanceof User) {
            $session->redirect($pages->get('/')->url.'users/'.$loginUser->name);
        } else {
            $error = 1;
        }
    }
    if($user->isLoggedin()) {
        $session->redirect($pages->get('/')->url.'users/'.$user->name);
    }
?>
<form action="" method="POST" layout="column" layout-align="center center">
    <div class="fieldset">
        <p>
            <a href="<?=$pages->get('/users/register')->url?>">Hast du noch keinen Account? Jetzt Registrieren.</a>
        </p>
        <user-login>
            <div layout="column" layout-align="center center">
                <img ref="avatar" class="avatar avatar--big avatar--round" src="<?=$user->getAvatar()?>" alt="">
            </div>
            <? if(isset($error)): ?>
                <div class="notification notification--error">
                    Der User existiert nicht oder das Passwort ist falsch.<br> Wenn du das Passwort vergessen hast, <a href="<?=$pages->get('/users/forgot-password')->url.'?name='?>">kannst du es wiederherstellen</a>.
                </div>
            <? endif ?>
            <label class="field-group" for="username">
                <span class="field-group__label">Nick / E-Mail</span>
                <input ref="username" class="field-group__input" type="text" name="username" required autocomplete autofocus value="<?=$input->post->username?>">
            </label>
            <label class="field-group">
                <span class="field-group__label">Passwort</span>
                <input class="field-group__input" type="password" name="password" autocomplete required value="">
            </label>
            <input class="button button--primary button--block button--big" type="submit" value="Login">
            <p>
                <a href="<?=$pages->get('/users/forgot-password')->url?>">Passwort vergessen?</a>
            </p>
            <div>
                <div class="text-or"><span>Oder</span></div>
                <div class="g-signin2" data-onsuccess="googleSignIn"></div>
            </div>
        </user-login>
    </div>
</form>
