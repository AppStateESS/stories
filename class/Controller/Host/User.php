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

namespace stories\Controller\Host;

use Canopy\Request;
use stories\Factory\HostFactory;
use stories\Factory\GuestFactory;
use stories\View\HostView as View;
use stories\Controller\RoleController;

class User extends RoleController
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

    public function shareHtmlCommand(Request $request)
    {
        $this->forceAjax = true;
        $result = $this->hostFactory->shareRequest($request);
        return $result;
        //echo json_encode($result);exit;
    }

}
