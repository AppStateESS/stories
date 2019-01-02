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
use stories\Factory\GuestFactory;
use stories\Factory\HostFactory;
use stories\View\ShareView as View;
use stories\Controller\RoleController;

class Admin extends User
{

    /**
     * @var /stories\Factory\ShareFactory
     */
    protected $factory;

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
        return ['success' => true];
    }

    protected function denyPutCommand(Request $request)
    {
        $this->factory->deny($this->id);
        return ['success' => true];
    }

    public function listUnapprovedJsonCommand(Request $request)
    {
        return ['listing' => $this->factory->listing(0)];
    }

    /**
     * Delete the share off the host.
     * $this->id is the share id.
     * @param Request $request
     */
    public function deleteCommand(Request $request)
    {
        try {
            $this->factory->removeFromGuest($this->id);
        } catch (\Exception $e) {
            $this->factory->delete($this->id);
            throw $e;
        }
        $this->factory->delete($this->id);

        return ['success' => true];
    }

    /**
     * Sends a request to a host to remove a share.
     * @param Request $request
     * @return array
     */
    public function removeHostPutCommand(Request $request)
    {
        $this->factory->removeFromHost($request);
        return ['success' => true];
    }

    public function listHtmlCommand(Request $request)
    {
        $vars['siteName'] = \Layout::getPageTitle(true);
        $vars['url'] = \Canopy\Server::getSiteUrl();
        return $this->view->scriptView('ShareAdmin', true, $vars);
    }

    public function listJsonCommand(Request $request)
    {
        $data = [];
        $guestFactory = new GuestFactory;
        $hostFactory = new HostFactory;
        $data['currentGuests'] = $guestFactory->getCurrentGuests();
        $data['guestRequests'] = $guestFactory->getGuestRequests();
        $data['hosts'] = $hostFactory->getHosts();
        $data['inaccessible'] = $this->factory->getInaccessible();
        return $data;
    }

    public function guestListingHtmlCommand(Request $request)
    {
        return $this->view->scriptView('GuestListing', true,
                        ['guestId' => $request->pullGetInteger('guestId')]);
    }

    public function guestListingJsonCommand(Request $request)
    {
        $guestId = $request->pullGetInteger('guestId');
        $guestFactory = new GuestFactory;
        $guest = $guestFactory->load($guestId);
        return ['listing' => $this->factory->getSharesByGuestId($guestId), 'guest' => $guest->getStringVars()];
    }

}
