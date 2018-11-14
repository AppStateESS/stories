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

namespace stories\Controller\FeatureStory;
use stories\Factory\FeatureStory;
use Canopy\Request;

class Admin extends RoleController
{
    public function loadFactory() {
        $this->factory = FeatureStoryFactory;
    }
}
