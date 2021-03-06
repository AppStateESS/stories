<?php

/*
 * Copyright (C) 2017 Matthew McNaney <mcnaneym@appstate.edu>.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301  USA
 */

namespace stories\Controller\Feature;

use Canopy\Request;
use stories\Factory\FeatureFactory as Factory;
use stories\View\FeatureView as View;
use stories\Controller\RoleController;

class Admin extends RoleController
{

    /**
     * @var \stories\Factory\FeatureFactory factory
     */
    protected $factory;

    protected function loadFactory()
    {
        $this->factory = new Factory;
    }
    
    protected function loadView()
    {
        $this->view = new View;
    }

    protected function listHtmlCommand()
    {
        \Menu::disableMenu();
        $this->view->addStoryCss();
        return $this->view->scriptView('Feature', true,
                        array('srcHttp' => PHPWS_SOURCE_HTTP));
    }

    protected function listJsonCommand()
    {
        $featureList = $this->factory->listing(false);
        return array('featureList' => $featureList);
    }

    protected function postCommand(Request $request)
    {
        return array('featureId' => $this->factory->post($request));
    }

    protected function deleteCommand(Request $request)
    {
        $this->factory->delete($this->id);
    }

    /**
     * @deprecated
     * @param Request $request
     * @return type
     */
    protected function putCommand(Request $request)
    {
        $feature = $this->factory->load($this->id);
        $this->factory->put($feature, $request);
        return array('featureId' => $this->id);
    }

}
