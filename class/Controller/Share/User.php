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

use stories\Controller\RoleController;
use stories\Factory\ShareFactory;
use Canopy\Request;

class User extends RoleController
{

    /**
     *
     * @var stories\Factory\ShareFactory
     */
    protected $factory;

    protected function loadFactory()
    {
        $this->factory = new ShareFactory;
    }

    protected function loadView()
    {
        
    }

    public function submitJsonCommand(Request $request)
    {
        $json = $this->factory->shareRequest($request);
        return $json;
    }

}
