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

namespace stories\Factory;
use phpws2\Database;
use stories\Factory\GuestFactory;
use stories\Resource\ShareResource as Resource;
use stories\Resource\GuestResource;

class ShareFactory extends BaseFactory
{

    public function build(array $data = null)
    {
        $resource = new Resource();
        if ($data) {
            $resource->setVars($data);
        }
        return $resource;
    }
    
    public function create(GuestResource $guest, int $entryId)
    {
        $share = $this->build();
        $share->guestId = $guest->id;
        $share->entryId = $entryId;
        $share->url = $guest->url . 'stories/Entry/' . $entryId;
        $share->approved = 0;
        $share->stampSubmit();
        self::saveResource($share);
    }
    
        
    public function listing(int $approved=1)
    {
        $db = Database::getDB();
        $tbl = $db->addTable('storiesshare');
        $tbl->addFieldConditional('approved', $approved);
        $tbl->addOrderBy('submitDate');
        $result = $db->select();
        if (empty($result)) {
            return null;
        }
        foreach ($result as $share) {
            $shareData = $this->pullShareData($share['id']);
            $share['fullUrl'] = $share['url'] . '/' . $shareData->urlTitle;
            unset($shareData->id);
            $listing[] = get_object_vars($shareData) + $share;
        }
        return $listing;
    }
    
    public function pullShareData($shareId)
    {
        $share = $this->load($shareId);
        $guestFactory = new GuestFactory;
        $guest = $guestFactory->load($share->guestId);
        $url = $share->url . '/?json=1';
        $json = file_get_contents($url);
        $jsonObject = json_decode($json);
        $jsonObject->siteName = $guest->siteName;
        $jsonObject->siteUrl = $guest->url;
        $jsonObject->thumbnail = $jsonObject->siteUrl . $jsonObject->thumbnail;
        return $jsonObject;
    }
    
    public function approve($shareId)
    {
        $share = $this->load($shareId);
        $share->approved = true;
        self::saveResource($share);
    }
    
    public function deny($shareId)
    {
        $share = $this->load($shareId);
        self::deleteResource($share);
    }
    
}
