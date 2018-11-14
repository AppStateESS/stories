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

namespace stories\Controller\Entry;

use Canopy\Request;
use stories\Factory\EntryFactory as Factory;
use stories\View\EntryView as View;
use stories\View\publishedView;
use stories\Factory\StoryMenu;
use stories\Controller\RoleController;

class User extends RoleController
{

    /**
     * @var stories\Factory\EntryFactory Factory
     */
    protected $factory;

    /**
     * @var stories\View\EntryView View
     */
    protected $view;

    protected function loadFactory()
    {
        $this->factory = new Factory;
    }

    protected function loadView()
    {
        $this->view = new View;
    }

    protected function viewHtmlCommand(Request $request)
    {
        return $this->view->view($this->id);
    }

    protected function listHtmlCommand(Request $request)
    {
        $publishedView = new \stories\View\PublishedView;
        return $publishedView->listing($request);
    }

    protected function viewJsonCommand(Request $request)
    {
        try {
            $entry = $this->factory->load($this->id);
            return $this->factory->shareData($entry);
        } catch (\Exception $e) {
            return ['error' => 'Could not retrieve story.'];
        }
    }

}
