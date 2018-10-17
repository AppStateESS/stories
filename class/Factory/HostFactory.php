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

    public function getHosts()
    {
        $db = Database::getDB();
        $tbl = $db->addTable('storieshost');
        $tbl->addOrderBy('siteName');
        return $db->select();
    }
    
    public function getHostsSelect()
    {
        $result = $this->getHosts();
        if (empty($result)) {
            return [];
        }
        foreach ($result as $row) {
            $hosts[] = ['value'=>$row['id'], 'label'=>$row['siteName']];
        }
        return $hosts;
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
    
    public function share(int $id, Request $request) {
        $host = $this->load($id);
        $entryId = $request->pullPutString('entryId');
        
        $url = "{$host->url}/stories/Guest/share/?json=1&authkey={$host->authkey}&entryId=$entryId";
        $result = file_get_contents($url);
        return json_decode($result);
    }

}
