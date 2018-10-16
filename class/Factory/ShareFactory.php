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
        self::saveResource($share);
    }
    
}
