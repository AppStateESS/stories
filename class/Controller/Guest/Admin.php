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

class Admin extends RoleController
{

    /**
     * @var stories\Factory\GuestFactory
     */
    protected $factory;

    /**
     * @var stories\View\GuestView
     */
    protected $view;

    protected function loadFactory()
    {
        $this->factory = new Factory;
    }
    
    protected function loadView()
    {
        $this->View = new View;
    }

}
