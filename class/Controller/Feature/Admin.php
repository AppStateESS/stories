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
use stories\Factory\EntryFactory;
use stories\Factory\StoryMenu;
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

    protected function listHtmlCommand(Request $request)
    {
        $this->factory->addStoryCss();
        return $this->factory->scriptView('Feature', true,
                        array('srcHttp' => PHPWS_SOURCE_HTTP));
    }

    protected function listJsonCommand(Request $request)
    {
        $featureList = $this->factory->listing($request, true);
        $entryFactory = new EntryFactory();
        $options = array(
            'vars' => array('id', 'title'),
            'includeContent' => false,
            'orderBy' => 'title',
            'mustHaveThumbnail'=> true,
            'asResource'=> false,
            
            'showTagLinks' => false);
        $stories = $entryFactory->pullList($options);
        return array('featureList' => $featureList, 'stories' => $stories);
    }

    protected function postCommand(Request $request)
    {
        return array('featureId' => $this->factory->post($request));
    }
    
    protected function putCommand(Request $request)
    {
        $this->factory->update($this->id, $request);
        return array('featureId'=>$this->id);
    }

}
