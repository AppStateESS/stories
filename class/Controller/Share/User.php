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
use stories\Factory\GuestFactory;
use stories\Factory\HostFactory;
use stories\Factory\PublishFactory;
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

    /**
     * Removes a host's share by request of a guest.
     * Get the share id by getting the guest by the authkey, then delete the share.
     * After the share is deleted, unpublish it.
     * 
     * @param Request $request
     */
    public function removeGuestShareJsonCommand(Request $request)
    {
        $entryId = $request->pullGetInteger('entryId');
        $authkey = $request->pullGetString('authkey');
        $guestFactory = new GuestFactory;
        $guest = $guestFactory->getByAuthkey($authkey);
        $shareId = $this->factory->getShareId($guest->id, $entryId);
        $this->factory->delete($shareId);
        $publishFactory = new PublishFactory;
        $publishFactory->unpublishShare($shareId);
        return ['success'=>true];
    }

    public function removeHostShareJsonCommand(Request $request)
    {
        $entryId = $request->pullGetInteger('entryId');
        $authkey = $request->pullGetString('authkey');
        $hostFactory = new HostFactory;
        $host = $hostFactory->getByAuthkey($authkey);
        $hostFactory->removeTrack($entryId, $host->id);
        return ['success'=>true];
    }

}
