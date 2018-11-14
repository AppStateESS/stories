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
use stories\Factory\StoryMenu;
use stories\View\EntryView as View;

class Admin extends User
{

    /**
     *
     * @var \stories\Factory\EntryFactory factory
     */
    protected $factory;

    protected function loadView()
    {
        $this->view = new View(true);
    }

    protected function createHtmlCommand(Request $request)
    {
        $entry = $this->factory->create();
        \Canopy\Server::forward('./stories/Entry/' . $entry->id . '/edit/?new=1');
    }

    protected function editHtmlCommand(Request $request)
    {
        \Menu::disableMenu();
        $entry = $this->factory->load($this->id);
        StoryMenu::viewStoryLink($entry);
        return $this->view->form($entry, $request->pullGetBoolean('new', true));
    }

    protected function postCommand(Request $request)
    {
        return array('entryId' => $this->factory->post($request));
    }

    protected function putCommand(Request $request)
    {
        $json = array('entryId' => $this->factory->put($this->id, $request));
        $featureStoryFactory = new \stories\Factory\FeatureStoryFactory;
        $featureStoryFactory->refreshEntry($this->id);
        return $json;
    }

    protected function patchCommand(Request $request)
    {
        return array('entryId' => $this->factory->patch($this->id, $request));
    }

    protected function deleteCommand(Request $request)
    {
        $this->factory->delete($this->id);
        return array('success' => true);
    }

    protected function viewJsonCommand(Request $request)
    {
        $entry = $this->factory->load($this->id);
        return array('entry' => $this->factory->data($entry));
    }

    protected function viewHtmlCommand(Request $request)
    {
        StoryMenu::editStoryLink($this->id);
        return parent::viewHtmlCommand($request);
    }

}
