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
use stories\Factory\HostFactory;
use stories\Factory\PublishFactory;
use stories\Factory\FeatureStoryFactory;
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

    private function jsonShareData(Resource $share)
    {
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
            $json = $this->sendCurl($url);
        } catch (\Exception $e) {
            \phpws2\Error::log($e);
            return $error;
        }
        $jsonObject = json_decode($json);
        if (!is_object($jsonObject)) {
            return $error;
        }
        if (isset($jsonObject->error)) {
            return $jsonObject;
        }
        $jsonObject->showInList = (string)(int)$share->showInList;
        $jsonObject->approved = $share->approved;
        $jsonObject->entryId = $jsonObject->id;
        $jsonObject->id = $share->id;
        $jsonObject->url = $share->url . '/' . $jsonObject->urlTitle;
        $jsonObject->siteName = $guest->siteName;
        $jsonObject->siteUrl = $guest->url;
        if (!preg_match('/^http/', $jsonObject->thumbnail)) {
            $jsonObject->thumbnail = $jsonObject->siteUrl . $jsonObject->thumbnail;
        }
        if (!preg_match('/^http/', $jsonObject->leadImage)) {
            $jsonObject->leadImage = $jsonObject->siteUrl . $jsonObject->leadImage;
        }
        if (!empty($jsonObject->authorPic)) {
            $jsonObject->authorPic = $guest->url . $jsonObject->authorPic;
        }
        return $jsonObject;
    }

    public function pullShareData(int $shareId)
    {
        $share = $this->load($shareId);
        $shareData = $this->jsonShareData($share);
        return $shareData;
    }

    public function approve(int $shareId, int $list)
    {
        $share = $this->load($shareId);
        $share->approved = true;
        $share->stampPublish();
        self::saveResource($share);
        $publishFactory = new PublishFactory;
        $publishFactory->publishShare($shareId, time(), $list);
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

    /**
     * Deletes all shares associated with a guest.
     * @param int $guestId
     * @return type
     */
    public function deleteByGuestId(int $guestId)
    {
        $shares = $this->getSharesByGuestId($guestId);
        if (!empty($shares)) {
            foreach ($shares as $s) {
                $this->delete($s->id);
            }
        }
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

    /**
     * Delete a share on the host
     * @param int $id
     */
    public function delete(int $id)
    {
        $publishFactory = new PublishFactory;
        $publishFactory->unpublishShare($id);

        $db = Database::getDB();
        $tbl = $db->addTable('storiesshare');
        $tbl->addFieldConditional('id', $id);
        $db->delete();
    }

    /**
     * Pull the host by hostId and inform them that an entry was unpublished
     * or deleted.
     * @param int $entryId
     * @param int $hostId
     * @throws \Exception
     */
    public function removeFromHost(int $entryId, int $hostId)
    {
        $hostFactory = new HostFactory;
        $host = $hostFactory->load($hostId);

        $url = <<<EOF
{$host->url}stories/Share/removeGuestShare/?authkey={$host->authkey}&entryId={$entryId}&json=1
EOF;
        if (!$this->sendCurl($url)) {
            throw new \Exception('Cannot to connect to host');
        }
    }

    /**
     * Receive a share id and inform the guest that shared the entry it
     * is no longer in use.
     * @param int $shareId
     * @return type
     */
    public function removeFromGuest(int $shareId)
    {
        $share = $this->load($shareId);
        $guestFactory = new GuestFactory;
        $guest = $guestFactory->load($share->guestId);

        $url = <<<EOF
{$guest->url}stories/Share/removeHostShare/?authkey={$guest->authkey}&entryId={$share->entryId}&json=1
EOF;
        if (!$this->sendCurl($url)) {
            throw new \Exception('Cannot to connect to guest');
        }
    }

    public function getShareId(int $guestId, int $entryId)
    {
        $db = Database::getDB();
        $tbl = $db->addTable('storiesshare');
        $tbl->addField('id');
        $tbl->addFieldConditional('guestId', $guestId);
        $tbl->addFieldConditional('entryId', $entryId);
        $row = $db->selectOneRow();
        if (empty($row)) {
            return null;
        } else {
            return $row['id'];
        }
    }

    public function guestShareCount(int $guestId)
    {
        $db = Database::getDB();
        $tbl = $db->addTable('storiesshare');
        $tbl->addFieldConditional('guestId', $guestId);
        $exp = new \phpws2\Database\Expression('count(' . $tbl->getField('id') . ')');
        $tbl->addField($exp);
        return $db->selectColumn();
    }

    public function getSharesByGuestId(int $guestId)
    {
        $db = Database::getDB();
        $tbl = $db->addTable('storiesshare');
        $tbl->addOrderBy('publishDate', 'desc');
        $tbl->addFieldConditional('guestId', $guestId);
        
        $tbl2 = $db->addTable('storiespublish');
        $tbl2->addField('showInList');
        $db->joinResources($tbl, $tbl2,
                $db->createConditional($tbl->getField('id'),
                        $tbl2->getField('shareId')), 'left');
        $shares = $db->select();
        if (empty($shares)) {
            return;
        }
        foreach ($shares as $row) {
            $shareObject = $this->build($row);
            $json[] = $this->jsonShareData($shareObject);
        }
        return $json;
    }

}
