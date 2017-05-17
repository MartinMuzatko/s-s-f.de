<?php namespace ProcessWire; ?>



<script>
var postdata = <?=json_encode($input->post->getArray())?>
</script>
<user-register postdata="{postdata}"></user-register>

<!-- <form action="" method="post">
    <label for="username">username</label>
    <input type="text" required name="username" value="<?=$input->post->username?>">
    <label for="species">species</label>
    <input type="text" required name="species" value="<?=$input->post->species?>">
    <label for="firstname">firstname</label>
    <input type="text" required name="firstname" value="<?=$input->post->firstname?>">
    <label for="lastname">lastname</label>
    <input type="text" required name="lastname" value="<?=$input->post->lastname?>">
    <label for="password">password</label>
    <input type="password" required name="password" value="<?=$input->post->password?>">
    <label for="password_repeat">password_repeat</label>
    <input type="password" required name="password_repeat" value="<?=$input->post->password_repeat?>">
    <label for="email">email</label>
    <input type="email" name="email" value="<?=$input->post->email?>">
    <div layout="row">
        <label for="street">street</label>
        <input type="text" required name="street" value="<?=$input->post->street?>">
        <label for="zip">zip</label>
        <input type="text" required name="zip" value="<?=$input->post->zip?>">
        <label for="city">city</label>
        <input type="text" required name="city" value="<?=$input->post->city?>">
        <label for="country">country</label>
        <input type="text" required name="country" value="<?=$input->post->country?>">
    </div>
    <label for="birthday">birthday</label>
    <input type="date" name="birthday" value="<?=$input->post->birthday?>">

    <input type="submit" value="Register">
</form> -->
