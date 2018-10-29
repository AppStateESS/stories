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

namespace stories\View;

use Canopy\Request;
use stories\Factory\GuestFactory as Factory;
use stories\Resource\GuestResource as Resource;
use phpws2\Template;

class GuestView extends View
{

    protected $factory;

    public function __construct()
    {
        $this->factory = new Factory;
    }

    public function requestError()
    {
        return $this->template('Guest/RequestError.html');
    }
    
    public function requestAccepted()
    {
        return $this->template('Guest/RequestAccepted.html');
    }
    
    private function template($filename)
    {
        $template = new Template;
        $template->setModuleTemplate('stories', $filename);
        return $template->get();
    }

}
