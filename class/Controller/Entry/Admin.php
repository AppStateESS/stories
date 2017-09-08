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

class Admin extends User
{

    /**
     *
     * @var \stories\Factory\EntryFactory factory
     */
    protected $factory;

    protected function createHtmlCommand(Request $request)
    {
        $entry = $this->factory->create();
        \Canopy\Server::forward('./stories/Entry/'. $entry->id . '/edit');
    }

    protected function editHtmlCommand(Request $request)
    {
        $entry = $this->factory->load($this->id);
        return $this->factory->form($entry);
    }

    protected function postCommand(Request $request)
    {
        return array('entryId' => $this->factory->post($request));
    }

    protected function putCommand(Request $request)
    {
        return array('entryId' => $this->factory->put($this->id, $request));
    }

    protected function viewJsonCommand(Request $request)
    {
        return array('entry' => $this->factory->view($this->id));
    }

}
