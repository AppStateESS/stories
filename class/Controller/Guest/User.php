<?php

/**
 * MIT License
 * Copyright (c) 2019 Electronic Student Services @ Appalachian State University
 * 
 * See LICENSE file in root directory for copyright and distribution permissions.
 * 
 * @author Matthew McNaney <mcnaneym@appstate.edu>
 * @license https://opensource.org/licenses/MIT
 */

namespace stories\Controller\Guest;

use Canopy\Request;
use stories\Factory\HostFactory;
use stories\Factory\GuestFactory;
use stories\View\GuestView as View;
use stories\Controller\RoleController;

class User extends RoleController
{
    protected function loadFactory()
    {
        $this->factory = new GuestFactory;
    }
    
    protected function loadView()
    {
        $this->view = new View;
    }
    
    public function requestHtmlCommand(Request $request)
    {
        $siteName = $request->pullGetString('siteName');
        $url = $request->pullGetString('url');
        $email = $request->pullGetString('email');
        return $this->factory->requestShare($siteName, $url, $email);
    }
    
    public function requestErrorHtmlCommand(Request $request)
    {
        return $this->view->requestError();
    }
    
    public function requestAcceptedHtmlCommand()
    {
        return $this->view->requestAccepted();
    }
    
}
