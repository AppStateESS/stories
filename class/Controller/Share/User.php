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

namespace stories\Controller\Share;

use stories\Controller\RoleController;
use stories\Factory\ShareFactory;
use stories\Factory\GuestFactory;
use stories\Factory\HostFactory;
use stories\Factory\PublishFactory;
use stories\Factory\FeatureStoryFactory;
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
        // Pull guest by authkey to make sure it is legitimate
        $guestFactory = new GuestFactory;
        $guest = $guestFactory->getByAuthkey($authkey);

        // Get the share id by the guest and entry id, then delete it
        $shareId = $this->factory->getShareId($guest->id, $entryId);
        if (!$shareId) {
            throw new \Exception("No share id from guest:{$guest->id}, entry:$entryId");
        }
        $this->factory->delete($shareId);

        $publishFactory = new PublishFactory;
        $publishObj = $publishFactory->loadByShareId($shareId);

        // delete feature stories by publish id
        $featureStoryFactory = new FeatureStoryFactory;
        $featureStoryFactory->deleteByPublishId($publishObj->id);

        // unpublish the share
        $publishFactory->unpublishShare($shareId);
        return ['success' => true];
    }

    public function removeHostShareJsonCommand(Request $request)
    {
        $entryId = $request->pullGetInteger('entryId');
        $authkey = $request->pullGetString('authkey');
        $hostFactory = new HostFactory;
        $host = $hostFactory->getByAuthkey($authkey);
        $hostFactory->removeTrack($entryId, $host->id);
        return ['success' => true];
    }

    /**
     * Unsubscribes (deletes) guest from host system
     * @param Request $request
     */
    public function unsubscribeJsonCommand(Request $request)
    {
        $guestFactory = new GuestFactory;
        $authKey = $request->pullGetString('authkey');
        $guestFactory->unsubscribeByAuthkey($authKey);
        return ['success' => true];
    }
    
    public function listHtmlCommand(Request $request) {
        \Current_User::requireLogin();
    }
    
    /**
     * Allows guest and hosts to test for site to site communication.
     * @return boolean
     */
    public function testHtmlCommand() {
        return true;
    }

}
