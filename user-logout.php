<?php namespace ProcessWire;
$session->logout();
$session->redirect($pages->get('/')->url);
