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

use stories\Factory\ShareFactory as Factory;
use phpws2\Template;

class ShareView extends View
{

    public function __construct()
    {
        $this->factory = new Factory;
    }
    
    public function view($id)
    {
        $data = $this->factory->pullShareData($id);
        if (isset($data->error)) {
            $this->factory->addInaccessible($id);
            return null;
        }
        $template = new Template(get_object_vars($data));
        $template->setModuleTemplate('stories', 'Share/View.html');
        return $template->get();
    }
    
}
