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
use stories\Factory\ShareFactory;
use stories\View\HostView as View;
use stories\Controller\RoleController;

class Admin extends User
{

    public function listHtmlCommand(Request $request)
    {
        $vars['siteName'] = \Layout::getPageTitle(true);
        $vars['url'] = \Canopy\Server::getSiteUrl();
        return $this->view->scriptView('ShareHost', true, $vars);
    }

    public function listJsonCommand(Request $request)
    {
        $shareFactory = new ShareFactory;
        $data = [];
        $data['currentGuests'] = $this->guestFactory->getCurrentGuests();
        $data['guestRequests'] = $this->guestFactory->getGuestRequests();
        $data['hosts'] = $this->hostFactory->getHosts();
        $data['inaccessible'] = $shareFactory->getInaccessible();
        return $data;
    }

    public function postCommand(Request $request)
    {
        $this->hostFactory->create($request);
        return ['success' => true];
    }

    public function existsJsonCommand(Request $request)
    {
        return ['duplicate' => (bool) $this->hostFactory->getByUrl($request->pullGetString('url'))];
    }

    public function putCommand(Request $request)
    {
        $host = $this->hostFactory->load($this->id);
        $host->authkey = $request->pullPutString('authkey');
        $this->hostFactory->save($host);
        return ['success' => true];
    }

    public function sharePutCommand(Request $request)
    {
        $json = $this->hostFactory->share($this->id, $request);
        return $json;
    }

    public function deleteCommand(Request $request)
    {
        $this->hostFactory->delete($this->id);
        $json = ['success'=> true];
        return $json;
    }

}
