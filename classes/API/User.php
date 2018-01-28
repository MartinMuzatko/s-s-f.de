<?php namespace API;

use \ProcessWire;

/**
 *
 */
class User extends Resource
{

    function __construct($username = '')
    {
        $this->users = $this->wire('users');
        $this->currentUser = $this->wire('user');
        $this->session = $this->wire('session');
        $user = $this->users->get($username);
        if ($user instanceof ProcessWire\User) {
            $this->user = $user;
        }
    }

    public function isValidPassword($password)
    {
        $pass = $this->fields->get('pass')->getInputField($this->user);
        // clear errors
        $pass->getErrors(true);
        $pass->___processInput(new \ProcessWire\WireInputData(
            [
                'pass' => $password,
                '_pass' => $password
            ]
        ));
        $pass->isValidPassword($password);
        return $pass->getErrors();
    }

    public function login($username, $password)
    {
        $this->session->login($username, $password);
    }

    public function logout()
    {
        return $this->session->logout();
    }

	public function setCreated($user, $created)
	{
		if ($user instanceof \ProcessWire\User) {
			$sql = "UPDATE `pages` SET `created` = '".$created."' WHERE `name` = '".$user->name."';";
			$update = $this->wire('db')->query($sql);
		}
	}

	public function setModified($user, $modified)
	{
		if ($user instanceof \ProcessWire\User) {
			$sql = "UPDATE `pages` SET `modified` = '".$modified."' WHERE `name` = '".$user->name."';";
			$update = $this->wire('db')->query($sql);
		}
	}
}
