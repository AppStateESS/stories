<?php

/**
 * This is the Host controller. All administrative views and requests for share
 * host information are handled from here. The Host factory will pull guest 
 * information. Likewise the Guest factory pulls host information.
 * 
 * 
 * MIT License
 * Copyright (c) 2018 Electronic Student Services @ Appalachian State University
 * 
 * See LICENSE file in root directory for copyright and distribution permissions.
 * 
 * @author Matthew McNaney <mcnaneym@appstate.edu>
 * @license https://opensource.org/licenses/MIT
 */

namespace stories\Controller\Host;

use Canopy\Request;
use stories\Factory\HostFactory;
use stories\Factory\GuestFactory;
use stories\View\HostView as View;
use stories\Controller\RoleController;

class Admin extends RoleController
{

    /**
     * @var stories\Factory\HostFactory
     */
    protected $hostFactory;
    
    /**
     * @var stories\Factory\GuestFactory
     */
    protected $guestFactory;

    /**
     * @var stories\View\HostView
     */
    protected $view;

    protected function loadFactory()
    {
        $this->guestFactory = new GuestFactory;
        $this->hostFactory = new HostFactory;
    }
    
    protected function loadView()
    {
        $this->view = new View;
    }
    
    public function listHtmlCommand(Request $request)
    {
        return $this->view->scriptView('ShareHost');
    }
    
    public function listJsonCommand(Request $request)
    {
        $data = [];
        $data['currentGuests'] = $this->guestFactory->getCurrentGuests();
        $data['guestRequests'] = $this->guestFactory->getGuestRequests();
        return $data;
    }

}
