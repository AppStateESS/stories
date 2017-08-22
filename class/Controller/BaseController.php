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

    public function htmlResponse($content)
    {
        $view = new \phpws2\View\HtmlView($content);
        $response = new \Canopy\Response($view);
        return $response;
    }

    public function jsonResponse($json)
    {
        $view = new \phpws2\View\JsonView($json);
        $response = new \Canopy\Response($view);
        return $response;
    }

    private function loadController(Request $request)
    {
        $major_controller = filter_var($request->shiftCommand(),
                FILTER_SANITIZE_STRING);

        if (empty($major_controller)) {
            \Canopy\Server::forward('./stories/');
        }

        if (empty($major_controller)) {
            throw new \stories\Exception\BadCommand;
        }

        $role_name = substr(strrchr(get_class($this->role), '\\'), 1);
        $controller_name = '\\stories\\Controller\\' . $major_controller . '\\' . $role_name;
        if (!class_exists($controller_name)) {
            throw new \stories\Exception\BadCommand($controller_name);
        }
        $this->controller = new $controller_name($this->role);
    }

    protected function loadRole()
    {
        // Only students will be able to log in. Admins
        // will meet first conditional.
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
        } catch (\Exception $e) {
            // Friendly error catch here if needed.
            throw $e;
        }
    }

    public function post(Request $request)
    {
        return $this->controller->post($request);
    }

    public function patch(Request $request)
    {
        return $this->controller->patch($request);
    }

    public function delete(Request $request)
    {
        return $this->controller->delete($request);
    }

    public function put(Request $request)
    {
        return $this->controller->put($request);
    }

    public function get(Request $request)
    {
        if ($request->isAjax()) {
            $result = $this->controller->getJson($request);
        } else {
            $result = $this->controller->getHtml($request);
        }
        return $result;
    }

    public function friendlyError(Request $request, $message = null)
    {
        $fe = new FriendlyError($this->getModule());
        if ($message) {
            $fe->setMessage($message);
        }
        return $fe->execute($request);
    }

}
