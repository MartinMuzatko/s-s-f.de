<?php namespace API;

use \ProcessWire\Wire;

/**
 *
 */
class Resource extends Wire
{
    public $path;

    function __construct($path)
    {
        $this->path = $path;
        $this->config = $this->wire('config');
        $this->pages = $this->wire('pages');
        $this->modules = $this->wire('modules');
        $this->page = $this->pages->get('/'.$path);

    }

    public function getConnections($page)
    {
        if ($page instanceof \ProcessWire\NullPage) {
            throw new \Exception('$page is not of type Page');
        }
        $connections = [
            'self' => [
                $page->id => $this->getApiUrl($page->httpUrl)
            ],
            'parent' => [
                $page->parent->id => $this->getApiUrl($page->parent->httpUrl)
            ],
            'children' => []
        ];
        foreach ($page->children as $key => $child) {
            $connections['children'][$child->id] = $this->getApiUrl($child->httpUrl);
        }
        return $connections;
    }

    public function getDefaultFields($page)
    {
        $fields = [];
        $additionalFields = [
            'created' => $page->created,
            'published' => $page->published,
            'modified' => $page->modified,
            'createdUser' => $page->createdUser->name,
            'modifiedUser' => $page->modifiedUser->name,
            'parent' => (string) $page->parent,
            'template' => $page->template->name
        ];
        foreach ($page->fields as $field) {
            $fields[$field->name] = htmlentities($page->{$field->name});
        }
        foreach ($additionalFields as $field => $value) {
            $fields[$field] = $value;
        }
        //$fields = $additionalFields;
        return $fields;
    }

    public function createForm($value='')
    {
        # code...
    }

    public function getActiveSessions()
    {
        $sessionHandler = $this->modules->get('SessionHandlerDB');
        $sessions = array_map(
            function($session) {
                $session['user_name'] = $this->users->get($session['user_id'])->name;
                return $session;
            },
            $sessionHandler->getSessions(60*10)
        );
        return array_filter($sessions, function($session) {return $session['user_name'] != 'guest';});
    }

    public function setFields($page, $fields)
    {
        if ($page instanceof \ProcessWire\Page) {

            $page->of(false);
            foreach ($fields as $field => $value) {
                $page->$field = $value;
            }
            $page->save();
            return true;
        }
        return false;
    }

    protected function getApiUrl($link)
    {
        $index = strpos($link, $this->config->urls->root);
        $pageUrl = substr($link, $index+strlen($this->config->urls->root));
        return $this->joinPaths($this->pages->get('/')->httpUrl, 'api', $pageUrl);
    }

    public function getPayload()
    {
        return json_decode(file_get_contents('php://input'));
    }

    private function joinPaths() {
        $args = func_get_args();
        $paths = array();
        foreach ($args as $arg) {
            $paths = array_merge($paths, (array)$arg);
        }
        $paths = array_map(create_function('$p', 'return trim($p, "/");'), $paths);
        $paths = array_filter($paths);
        return join('/', $paths);
    }
}
