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
    /**
     * @var stories\Factory\ShareFactory
     */
    protected $factory;
    
    /**
     * @var stories\View\ShareView
     */
    protected $view;
    
    public function loadFactory()
    {
        $this->factory = new Factory;
    }
    
    public function loadView(){
        $this->view = new View;
    }
    
    public function listHtmlCommand(Request $request)
    {
        return $this->view->scriptView('Share');
    }
}
