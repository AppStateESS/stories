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

namespace stories\Controller\Guest;

use Canopy\Request;
use stories\Factory\GuestFactory as Factory;
use stories\View\GuestView as View;
use stories\Controller\RoleController;

class Admin extends User
{

    public function acceptPutCommand(Request $request)
    {
        $this->factory->acceptRequest($this->id);
        return ['success' => true];
    }

    public function deleteCommand(Request $request)
    {
        $this->factory->delete($this->id);
        return ['success' => true];
    }
    
    public function denyPutCommand(Request $request)
    {
        $this->factory->denyRequest($this->id);
        return ['success'=>true];
    }

}
