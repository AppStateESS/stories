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

namespace stories\Controller\Embed;

use Canopy\Request;
use stories\Factory\EmbedFactory as Factory;
use stories\View\EmbedView as View;
use stories\Controller\RoleController;

class Admin extends RoleController
{

    /**
     * @var stories\Factory\EntryFactory Factory
     */
    protected $factory;

    /**
     * @var stories\View\EmbedView View
     */
    protected $view;

    protected function loadFactory()
    {
        //$this->factory = new Factory;
    }

    protected function loadView()
    {
        $this->view = new View;
    }

    public function getHtml(Request $request)
    {
        return $this->getJson($request);
    }

    /**
     * The embed result pulled by the url.
     * The medium script does not register as an ajax call for
     * some reason.
     * @param Request $request
     */
    protected function viewJsonCommand(Request $request)
    {
        return $this->view->embed($request);
    }

}
