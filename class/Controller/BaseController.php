<?php

/**
 * MIT License
 * Copyright (c) 2017 Electronic Student Services @ Appalachian State University
 * 
 * See LICENSE file in root directory for copyright and distribution permissions.
 * 
 * @author Matthew McNaney <mcnaneym@appstate.edu>
 * @license https://opensource.org/licenses/MIT
 */

namespace stories\Controller;

use Canopy\Request;

class BaseController extends \phpws2\Http\Controller
{

    protected $role;
    protected $controller;

    public function __construct($module, $request)
    {
        parent::__construct($module);
        $this->loadRole();
        $this->loadController($request, $this->role);
    }

    private function loadController(Request $request)
    {
        $major_controller = filter_var($request->shiftCommand(),
                FILTER_SANITIZE_STRING);

        if (empty($major_controller)) {
            throw new \stories\Exception\BadCommand('Missing controller name');
        }
        
        $role_name = substr(strrchr(get_class($this->role), '\\'), 1);
        $controller_name = '\\stories\\Controller\\' . $major_controller . '\\' . $role_name;
        if (!class_exists($controller_name)) {
            $entryFactory = new \stories\Factory\EntryFactory;
            $entry = $entryFactory->getByUrlTitle($major_controller);
            if (!empty($entry)) {
                $request->setUrl($entry->id);
                $request->buildCommands();
                $controller_name = '\\stories\\Controller\\Entry\\' . $role_name;
            } else {
                throw new \stories\Exception\BadCommand($controller_name);
            }
        }
        $this->controller = new $controller_name($this->role);
    }

    protected function loadRole()
    {
        $user_id = \Current_User::getId();
        if (\Current_User::allow('stories')) {
            $this->role = new \stories\Role\Admin($user_id);
        } else {
            $this->role = new \stories\Role\User;
        }
    }

    public function execute(Request $request)
    {
        try {
            return parent::execute($request);
        } catch (\stories\Exception\PrivilegeMissing $e) {
            \Current_User::requireLogin();
        } catch (\Exception $e) {
            // Friendly error catch here if needed.
            $friendly = new FriendlyError($this->getModule());
            if (STORIES_FRIENDLY_ERROR) {
                return $friendly->execute($request);
            } else {
                throw $e;
            }
        }
    }
    
    public function post(Request $request)
    {
        return $this->controller->changeResponse($request);
    }

    public function patch(Request $request)
    {
        return $this->controller->changeResponse($request);
    }

    public function delete(Request $request)
    {
        return $this->controller->changeResponse($request);
    }

    public function put(Request $request)
    {
        return $this->controller->changeResponse($request);
    }

    public function get(Request $request)
    {
        if ($request->isAjax() || (bool)$request->pullGetBoolean('json', true)) {
            $result = $this->controller->getJson($request);
        } else {
            $result = $this->controller->getHtml($request);
        }
        return $result;
    }

}
