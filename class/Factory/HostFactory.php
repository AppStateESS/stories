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

use stories\Resource\HostResource as Resource;
use phpws2\Database;
use Canopy\Request;

class HostFactory extends BaseFactory
{

    public function build(array $data = null)
    {
        $resource = new Resource;
        if ($data) {
            $resource->setVars($data);
        }
        return $resource;
    }

    public function getHosts($activeOnly = false)
    {
        $db = Database::getDB();
        $tbl = $db->addTable('storieshost');
        $tbl->addOrderBy('siteName');
        if ($activeOnly) {
            $tbl->addFieldConditional('authkey', '', '!=');
        }
        return $db->select();
    }

    /**
     * Returns an array of hosts that can used in a form for sending shares.
     * @param int $entryId
     * @return array
     */
    public function getHostsSelect()
    {
        $result = $this->getHosts(true);
        if (empty($result)) {
            return [];
        }
        foreach ($result as $row) {
            $hosts[] = [
                'value' => $row['id'],
                'label' => $row['siteName']
            ];
        }
        return $hosts;
    }

    public function getTrackedByEntry($entryId)
    {
        $columns = [];
        $db = Database::getDB();
        $tbl = $db->addTable('storiestrack');
        $tbl->addField('hostId');
        $tbl->addFieldConditional('entryId', $entryId);
        while ($col = $db->selectColumn()) {
            $columns[] = $col;
        }
        return $columns;
    }

    public function create(Request $request)
    {
        $host = $this->build();
        $host->siteName = $request->pullPostString('hostName');
        $host->url = $request->pullPostString('hostUrl');
        self::saveResource($host);
    }

    public function getByUrl(string $url)
    {
        //$urlObj = new \phpws2\Variable\Url($url);
        $urlObj = new \phpws2\Variable\StringVar($url);
        $db = Database::getDB();
        $tbl = $db->addTable('storieshost');
        $tbl->addFieldConditional('url', $urlObj->get());
        $row = $db->selectOneRow();
        if (empty($row)) {
            return null;
        } else {
            return $this->build($row);
        }
    }

    public function save(Resource $resource)
    {
        self::saveResource($resource);
    }

    public function delete(int $id)
    {
        if ($id <= 0) {
            throw new \Exception('Missing id');
        }
        $db = Database::getDB();
        $tbl = $db->addTable('storieshost');
        $tbl->addFieldConditional('id', $id);
        return $db->delete();
    }

    public function removeShareFromHost(int $hostId)
    {
        $host = $this->load($hostId);
        if (empty($host->authkey)) {
            return array('error' => 'Authkey not set for ' . $host->siteName);
        }
        $url = "{$host->url}stories/Share/unsubscribe/?json=1&authkey={$host->authkey}";
        if (!$this->sendCurl($url)) {
            throw new \Exception('Cannot to connect to host.');
        }
    }

    public function submit(int $hostId, Request $request)
    {
        $entryId = $request->pullPutString('entryId');
        if ($this->getTrack($entryId, $hostId)) {
            return array('error' => 'You have already submitted to this host.');
        }

        $host = $this->load($hostId);
        if (empty($host->authkey)) {
            return array('error' => 'Authkey not set for ' . $host->siteName);
        }

        $url = "{$host->url}stories/Share/submit/?json=1&authkey={$host->authkey}&entryId=$entryId";
        $result = $this->sendCurl($url);
        if (!$result) {
            throw new \Exception('Cannot to connect to host.');
        }

        $this->trackRequest($entryId, $hostId);

        return json_decode($result);
    }

    private function getTrack(int $entryId, int $hostId)
    {
        $db = Database::getDB();
        $tbl = $db->addTable('storiestrack');
        $tbl->addFieldConditional('entryId', $entryId);
        $tbl->addFieldConditional('hostId', $hostId);
        return $db->selectOneRow();
    }

    private function trackRequest(int $entryId, int $hostId)
    {
        $db = Database::getDB();
        $tbl = $db->addTable('storiestrack');
        $tbl->addValue('entryId', $entryId);
        $tbl->addValue('hostId', $hostId);
        $db->insert();
    }

    public function removeTrack(int $entryId, int $hostId)
    {
        $db = Database::getDB();
        $tbl = $db->addTable('storiestrack');
        $tbl->addFieldConditional('entryId', $entryId);
        $tbl->addFieldConditional('hostId', $hostId);
        $db->delete();
    }

    public function getByAuthkey(string $authkey)
    {
        $db = Database::getDB();
        $tbl = $db->addTable('storieshost');
        $tbl->addFieldConditional('authkey', $authkey);
        $host = $db->selectOneRow();
        if (empty($host)) {
            throw new \Exception('Invalid host');
        }
        $hostObj = $this->build($host);
        return $hostObj;
    }

}
