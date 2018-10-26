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
use stories\Factory\PublishFactory;
use stories\Resource\ShareResource as Resource;
use stories\Resource\GuestResource;
use Canopy\Request;

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

    public function listing(int $approved = 1)
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
            if (isset($shareData->error)) {
                $listing[] = $shareData;
                continue;
            }
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

        $error = new \stdClass();
        $error->id = $share->id;
        $error->error = true;
        $error->url = $share->url;
        $error->siteName = $guest->siteName;
        $error->siteUrl = $guest->url;
        $url = $share->url . '/?json=1';
        try {
            $json = file_get_contents($url);
        } catch (\Exception $e) {
            return $error;
        }
        $jsonObject = json_decode($json);
        if (!is_object($jsonObject)) {
            return $error;
        }
        if (isset($jsonObject->error)) {
            return $jsonObject;
        }
        $jsonObject->url = $share->url . '/' . $jsonObject->urlTitle;
        $jsonObject->siteName = $guest->siteName;
        $jsonObject->siteUrl = $guest->url;
        $jsonObject->thumbnail = $jsonObject->siteUrl . $jsonObject->thumbnail;
        return $jsonObject;
    }

    public function approve(int $shareId)
    {
        $share = $this->load($shareId);
        $share->approved = true;
        $share->stampPublish();
        self::saveResource($share);
        $publishFactory = new PublishFactory;
        $publishFactory->publishShare($shareId, time());
    }

    public function deny(int $shareId)
    {
        $share = $this->load($shareId);
        self::deleteResource($share);
    }

    public function shareRequest(Request $request)
    {
        $authkey = $request->pullGetString('authkey');
        $entryId = $request->pullGetInteger('entryId');
        $guestFactory = new GuestFactory;
        $guest = $guestFactory->getByAuthkey($authkey);
        if (!$guest) {
            return ['error' => 'Authorization not recognized.'];
        }
        try {
            $this->create($guest, $entryId);
        } catch (\Exception $e) {
            if ($e->getCode() === '23000') {
                return ['error' => 'Duplicate request.'];
            }
        }

        return ['success' => true];
    }

    public function deleteByGuestId(int $id)
    {
        $db = Database::getDB();
        $tbl = $db->addTable('storiesshare');
        $tbl->addFieldConditional('guestId', $id);
        return $db->delete();
    }

    public function addInaccessible(int $id)
    {
        $share = $this->load($id);
        $share->incrementInaccessible();
        if ($share->inaccessible >= 50) {
            $this->delete($id);
        } else {
            self::saveResource($share);
        }
    }

    public function getInaccessible()
    {
        $db = Database::getDB();
        $tbl = $db->addTable('storiesshare');
        $tbl->addFieldConditional('inaccessible', 0, '>');
        $tbl->addOrderBy('publishDate', 'asc');
        return $db->select();
    }

    public function delete(int $id)
    {
        $db = Database::getDB();
        $tbl = $db->addTable('storiesshare');
        $tbl->addFieldConditional('id', $id);
        $db->delete();

        $publishFactory = new PublishFactory;
        $publishFactory->deleteByShareId($id);
    }

}
