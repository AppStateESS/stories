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

namespace stories\Controller\Publish;

use Canopy\Request;
use stories\Controller\RoleController;
use stories\Factory\PublishFactory as Factory;

class Admin extends RoleController
{

    protected function loadFactory()
    {
        $this->factory = new Factory;
    }

    protected function loadView()
    {
        
    }

    protected function entryPatchCommand(Request $request)
    {
        return ['success' => $this->factory->patchByEntry($this->id,
                    $request->pullPatchString('varname'),
                    $request->pullPatchBoolean('value'))];
    }

    protected function sharePatchCommand(Request $request)
    {
        return ['success' => $this->factory->patchByShare($this->id,
                    $request->pullPatchString('varname'),
                    $request->pullPatchBoolean('value'))];
    }

}
