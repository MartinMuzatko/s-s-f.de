<?php namespace API;

use \ProcessWire;

/**
 *
 */
class Users extends Resource
{

    function __construct()
    {

    }

    public function doesUserExist($username) {
        $existingUser = $this->users->get($username);
        return $existingUser instanceof ProcessWire\User;
    }
    
    public function getUser($username)
    {
        if ($this->doesUserExist($username)) {
            return new User($this->users->get($username));
        }
    }

    public function getAllSpecies()
    {
        $users = $this->users->find('template=user');
        $species = [];
        foreach ($users as $user) {
            if ($user->species->title) {
                array_push($species, $user->species->title);
            }
        }
        return array_values(array_unique($species));
    }

    private function createUser($data)
    {
        $newUser = $this->users->add($data->username);
        $newUser->email = $email;
        $newUser->species = $species;
        $newUser->firstname = $firstname;
        $newUser->lastname = $lastname;
        $newUser->password = $password;
        $newUser->email = $email;
        $newUser->street = $street;
        $newUser->zip = $zip;
        $newUser->city = $city;
        $newUser->country = $country;
        $newUser->birthday = $birthday;
        $newUser->save();
        $session->login($username, $password);
    }

    public function doesUserWithSameMailExist($email)
    {
        return $this->users->get("email=$email") instanceOf ProcessWire\User;
    }

	public function isUsernameAllowed($username)
	{
		$blacklist = [
			'/^page\d+$/gi',
			'/^delete$/gi',
			'/^login$/gi',
			'/^logout$/gi',
			'/^register$/gi',
		];
		foreach ($blacklist as $regex) {
			if(preg_match($regex, $username)) {
				return false;
			}
		}
		return true;
	}

    public function registerUser(ProcessWire\WireInputData $submission)
    {
        $fields = $this->templates->get('user')->fields;
        $errors = [];

        $form = $this->modules->get("InputfieldForm");
        $form->attr("id+name",'subscribe-form');

        if (!count($submission)) {
            $form->error('no submission data');
            return;
        }
        foreach ($submission as $key => $value) {
            $field = $fields->{$key};
            if ($field instanceof ProcessWire\Field) {
                $field = $field->getInputfield($this->user);
                $form->append($field);
            }
        }
        $form->processInput($submission);

        if (strlen($submission->username) && $this->doesUserExist($submission->username)) {
            $form->username->error("User $submission->username already exists");
        }
		if (strlen($submission->username) && $this->isUsernameAllowed($submission->username)) {
            $form->username->error("Username $submission->username is blacklisted");
        }
        if (strlen($submission->email) && $this->doesUserWithSameMailExist($submission->email)) {
            $form->email->error("Email $submission->email already used");
        }
        if ($submission->pass != $submission->_pass) {
            $form->pass->error('Password not same');
        }
        if ($submission->pass == $submission->email) {
            $form->pass->error('Please pick another password than your email');
        }
        if ($submission->pass == $submission->username) {
            $form->pass->error('Please pick another password than your username');
        }
        $x = $form->getErrors();
        var_dump($x);
        //var_dump(get_class_methods($form));
        //var_dump($form->firstname);


    }

    public function getPostData()
    {

        $data = new ProcessWire\WireInputData([
            'email' => $this->input->post->email,
            'username' => $this->input->post->username,
            'species' => $this->input->post->species,
            'firstname' => $this->input->post->firstname,
            'lastname' => $this->input->post->lastname,
            'pass' => $this->input->post->pass,
            '_pass' => $this->input->post->_pass,
            'email' => $this->input->post->email,
            'street' => $this->input->post->street,
            'zip' => $this->input->post->zip,
            'city' => $this->input->post->city,
            'country' => $this->input->post->country,
            'birthday' => $this->input->post->birthday
        ]);

        $token = $this->session->CSRF->getTokenName();
        $data->$token = $this->session->CSRF->getTokenValue();

        $post = $this->input->post;
        $post->setArray(array_merge($data->getArray(), $post->getArray()));
        return $post;
    }
}
