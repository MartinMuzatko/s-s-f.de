<?php namespace ProcessWire; ?>
<form action="" method="post">
    <input type="text" name="username" value="">
    <input type="password" name="password" value="">
    <input type="submit" value="Login">
</form>

<? if($user->isLoggedin()): ?>
    <a href="<?=$pages->get('/user/logout')->url?>">Logout <?=$user->name?></a>
<? else: ?>
    <a href="<?=$pages->get('/user/login')->url?>">Login</a>
<? endif; ?>
<?php
?>
<? if (count($input->post()->getArray())): ?>
    <?php
        $loginUser = $session->login($input->post->username, $input->post->password);
        if (!$loginUser instanceof User) {
            $loginUser = $session->login($input->post->username, $input->post->password);
        }


    ?>
    <? if($loginUser instanceof User): ?>
        logged in! Hello <?=$loginUser->name?>
    <? endif; ?>
    <? var_dump($loginUser); ?>
<? endif; ?>
