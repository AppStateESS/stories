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
use stories\View\PublishedView;
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
        $dataArray = get_object_vars($data);
        if (!empty($dataArray['tags'])) {
            array_walk($dataArray['tags'], function(&$i){
                $i = get_object_vars($i);
            });
        }
        $publishedView = new PublishedView;
        $dataArray['published'] = 1;
        $dataArray['shareId'] = $data->id;
        $dataArray['publishInfo'] = $publishedView->publishBlock($dataArray);
        $template = new Template($dataArray);
        $template->setModuleTemplate('stories', 'Share/View.html');
        return $template->get();
    }
    
}
