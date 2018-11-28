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
    /**
     * @var \stories\Factory\HostFactory
     */
    protected $factory;
    
    public function postCommand(Request $request)
    {
        $this->factory->create($request);
        return ['success' => true];
    }

    public function testJsonCommand(Request $request)
    {
        return ['duplicate' => (bool) $this->factory->getByUrl($request->pullGetString('url')),
            'successfulContact' => $this->factory->testUrl($request->pullGetString('url'))];
    }

    public function putCommand(Request $request)
    {
        $host = $this->factory->load($this->id);
        $host->authkey = $request->pullPutString('authkey');
        $this->factory->save($host);
        return ['success' => true];
    }

    /**
     * Delete host, but notify host first. If host connection fails, continue
     * with deletion.
     * 
     * @param Request $request
     * @return boolean
     * @throws \Exception
     */
    public function deleteCommand(Request $request)
    {
        try {
            $this->factory->removeShareFromHost($this->id);
        } catch (\Exception $e) {
            $this->factory->delete($this->id);
            throw $e;
        }
        $this->factory->delete($this->id);
        $json = ['success' => true];
        return $json;
    }

    public function submitPutCommand(Request $request)
    {
        $json = $this->factory->submit($this->id, $request);
        return $json;
    }

}
