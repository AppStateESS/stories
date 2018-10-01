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

use stories\Resource\GuestResource as Resource;
use phpws2\Database;
use Canopy\Request;

class GuestFactory extends BaseFactory
{

    public function build($data = null)
    {
        $resource = new Resource;
        if ($data) {
            $resource->setVars($data);
        }
        return $resource;
    }

    public function getCurrentGuests()
    {
        $db = Database::getDB();
        $tbl = $db->addTable('storiesguest');
        $tbl->addFieldConditional('status', 1);
        $tbl->addOrderBy('siteName');
        return $db->select();
    }

    public function getGuestRequests()
    {
        $db = Database::getDB();
        $tbl = $db->addTable('storiesguest');
        $tbl->addFieldConditional('status', 0);
        $tbl->addOrderBy('siteName');
        return $db->select();
    }

}
