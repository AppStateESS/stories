<?php

/**
 * MIT License
 * Copyright (c) 2018 Electronic Student Services @ Appalachian State University
 * 
 * See LICENSE file in root directory for copyright and distribution permissions.
 * 
 * @author Matthew McNaney <mcnaneym@appstate.edu>
 * @license https://opensource.org/licenses/MIT
 */

namespace stories\Controller\Share;

use Canopy\Request;
use stories\Factory\ShareFactory as Factory;
use stories\View\ShareView as View;
use stories\Controller\RoleController;

class Admin extends RoleController
{

    protected function loadFactory()
    {
        $this->factory = new Factory;
    }

    protected function loadView()
    {
        $this->view = new View;
    }

    protected function approvePutCommand(Request $request)
    {
        $this->factory->approve($this->id);
        return ['success'=>true];
    }

    protected function denyPutCommand(Request $request)
    {
        $this->factory->deny($this->id);
        return ['success'=>true];
    }

    public function listUnapprovedJsonCommand(Request $request)
    {
        return ['listing' => $this->factory->listing(0)];
    }

}
