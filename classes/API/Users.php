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
        $existingUser = $this->users->get($this->sanitizer->username($username));
        return $existingUser instanceof ProcessWire\User;
    }
    
    public function getUser($username)
    {
        if ($this->doesUserExist($username)) {
            return new User($this->users->get($this->sanitizer->username($username)));
        }
    }

    public function updateUser(ProcessWire\WireInputData $submission, $user) {
        $errors = $this->validateUser($submission);
        if (count($errors)) {
            return $errors;
        } else {
            $this->setUserFields($user, $submission);
            return [];
        }
    }

    public function setUserFields($user, $data, $create = false) {
        $user->of(false);
        $user->email = $this->sanitizer->text($data->email);
        $user->species = $this->sanitizer->text($data->species);
        $user->firstname = $this->sanitizer->text($data->firstname);
        $user->lastname = $this->sanitizer->text($data->lastname);
        $user->pass = $this->sanitizer->text($data->password);
        $user->email = $this->sanitizer->text($data->email);
        $user->street = $this->sanitizer->text($data->street);
        $user->street = $this->sanitizer->text($data->street);
        $user->isInvisible = !isset($data->isInvisible) ? true : false; // We have to invert it, since we display it as isVisible
        $user->isQueryable = isset($data->isQueryable) ? true : false;
        $user->isSubscribedOfficial = isset($data->isSubscribedOfficial) ? true : false;
        $user->isSubscribedAll = isset($data->isSubscribedAll) ? true : false;
        $species = $this->sanitizer->text($data->species);
        $speciesPage = $this->pages->get('/resources/species');
        $specieExists = $speciesPage->get("title=$species");
        if ($specieExists instanceof \ProcessWire\NullPage) {
            $specie = new \ProcessWire\Page;
            $specie->parent = $speciesPage;
            $specie->template = 'user-specie';
            $specie->title = $species;
            $specie->save();
        }
        $user->species = $this->sanitizer->text($data->species);
        $user->zip = $this->sanitizer->text($data->zip);
        $user->city = $this->sanitizer->text($data->city);
        $user->country = $this->sanitizer->text($data->country);
        if ($create) {
            $user->birthdate = $this->sanitizer->text($data->birthdate);
        }
        $user->save();
        return $user;
    }
    
    private function createUser($data)
    {
        $username = $this->sanitizer->text($data->username);
        $newUser = $this->users->add($this->sanitizer->pageName($data->username));
        $newUser->username = $username;
        $newUser->addRole('user');
        $newUser->language = $this->languages->get("deutsch");
        $this->setUserFields($newUser, $data, true);
        // defaults
        $newUser->isInvisible = false;
        $newUser->isQueryable = true;
        $newUser->isSubscribedOfficial = false;
        $newUser->isSubscribedAll = true;
        $newUser->hasReadPrivacyPolicy = true;
        $newUser->save();

        // Send notification
        $context = new ProcessWire\WireData();
        $context->setArray([
            'user' => $newUser
        ]);
        array_map(
            function($notification) use ($newUser, $context) {
                \ProcessWire\sendNotification($notification, $newUser, $context);
            },
            $this->wire->pages->get("/resources/notifications")->find("trigger=UserCreated")->getArray()
        );
        // Redirect to redirect page or user profile
        $this->session->login($newUser->name, $data->password);
        $redirectPage = $this->pages->get($this->input->get->redirect);
        if($redirectPage instanceof \ProcessWire\NullPage) {
            $this->session->redirect($this->pages->get('/')->url.'users/'.$newUser->name);
        } else {
            $this->session->redirect($redirectPage->url);
        }
    }

    public function doesUserWithSameMailExist($email)
    {
        return $this->users->get("email=$email") instanceOf ProcessWire\User;
    }

	public function isUsernameAllowed($username)
	{
		$blacklist = [
			'/^page\d+$/i',
			'/^delete$/i',
			'/^login$/i',
			'/^logout$/i',
			'/^register$/i',
		];
		foreach ($blacklist as $regex) {
			if(preg_match($regex, $username)) {
				return false;
			}
		}
		return true;
    }
    
    public function validateUser(ProcessWire\WireInputData $submission) {
        $errors = [];
        $form = $this->modules->get("InputfieldForm");
        $form->attr("id+name",'subscribe-form');
    
        if (!count($submission)) {
            $form->error('no submission data');
            return;
        }

        $fields = $this->templates->get('user')->fields;

        foreach ($submission as $key => $value) {
            $field = $fields->{$key};
            if ($field instanceof ProcessWire\Field) {
                $field = $field->getInputfield($this->user);
                $form->append($field);
            }
        }
        $form->processInput($submission);
        if ($submission->username && \ProcessWire\Wire('user')->name == 'guest') {
            if (strlen($submission->username) && $this->doesUserExist($submission->username)) {
                $form->username->error("Der Benutzer $submission->username existiert bereits");
            }
            if (strlen($submission->username) && !$this->isUsernameAllowed($submission->username)) {
                $form->username->error("Der Benutzername $submission->username ist nicht erlaubt.");
            }
            if (strlen($submission->email) && $this->doesUserWithSameMailExist($submission->email)) {
                $form->email->error("Die Email $submission->email wird bereits verwendet");
            }
        }
        
        if ($form->pass) {
            if ($submission->pass == $submission->email) {
                $form->pass->error('Please pick another password than your email');
            }
            if ($submission->pass == $submission->username) {
                $form->pass->error('Please pick another password than your username');
            }
        }
        return $form->getErrors();
    }

    public function registerUser(ProcessWire\WireInputData $submission)
    {
        $errors = $this->validateUser($submission);
        if (count($errors)) {
            return $errors;
        } else {
            $this->createUser($submission);
        }
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
